<?php

namespace FastCache\Core\Compatibility;

use FastCache\Platform\Cache;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

/**
 * Disable Divi's disposable static CSS files whenever FastCache is active,
 * independently of the page cache setting.
 * Use Divi's options API, which also updates its in-memory options.
 */
class Divi {

	const SETTING = 'et_pb_static_css_file';
	const PENDING = 'fastcache_divi_page_purge_pending';
	const NOTICE = 'fastcache_divi_compatibility_notice';
	const RETRY = 'fastcache_divi_compatibility_retry';

	private static $running = false;
	private static $registered = false;

	public static function register() {
		if ( self::$registered || ! self::isConfigured() ) {
			return;
		}
		self::$registered = true;
		// Only register callbacks here: the theme has not necessarily loaded yet.
		// Migrate existing installations on the next authorized admin request, never
		// while rendering a visitor's page or handling a public AJAX/REST request.
		add_action( 'admin_init', [ __CLASS__, 'synchronize' ], 20 );
		register_activation_hook( FASTCACHE_FILE_PATH, [ __CLASS__, 'synchronize' ] );
		add_action( 'update_option_fastcache_settings', [ __CLASS__, 'settingsChanged' ], 20 );
		add_action( 'add_option_fastcache_settings', [ __CLASS__, 'settingsChanged' ], 20 );
		add_action( 'admin_notices', [ __CLASS__, 'showNotice' ] );
	}

	private static function isConfigured() {
		// FastCache can load before the theme or the Builder plugin. Read WordPress
		// configuration rather than relying on Divi functions/classes at this stage.
		// 'template' identifies the parent theme even when a child theme is active.
		if ( strtolower( (string) get_option( 'template', '' ) ) === 'divi'
			|| ( defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) && ET_BUILDER_PLUGIN_ACTIVE ) ) {
			return true;
		}
		$builder = 'divi-builder/divi-builder.php';
		if ( in_array( $builder, (array) get_option( 'active_plugins', [] ), true ) ) {
			return true;
		}
		$networkPlugins = is_multisite() ? (array) get_site_option( 'active_sitewide_plugins', [] ) : [];
		return isset( $networkPlugins[ $builder ] );
	}

	public static function settingsChanged() {
		if ( did_action( 'init' ) ) {
			self::synchronize();
		}
	}

	public static function synchronize() {
		if ( self::$running || ( ! is_admin() && ! ( defined( 'WP_CLI' ) && WP_CLI ) )
			|| ! current_user_can( 'manage_options' )
			|| ( is_multisite() && ms_is_switched() ) ) {
			return;
		}

		self::$running = true;
		try {
			if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
				return;
			}
			$theme = wp_get_theme();
			$isDivi = strtolower( $theme->get_template() ) === 'divi'
				|| ( defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) && ET_BUILDER_PLUGIN_ACTIVE );
			if ( ! $isDivi || ! function_exists( 'et_get_option' ) || ! function_exists( 'et_update_option' )
				|| ! function_exists( 'et_options_stored_in_one_row' ) || get_transient( self::RETRY ) ) {
				return;
			}

			global $shortname, $et_theme_options;
			$oneRow = et_options_stored_in_one_row();
			if ( $oneRow && ( ! is_string( $shortname ) || $shortname === '' ) ) {
				throw new \RuntimeException( 'Divi option storage is not initialized.' );
			}
			$optionName = $oneRow ? 'et_' . $shortname : self::SETTING;
			$stored = get_option( $optionName, $oneRow ? [] : 'on' );
			if ( $oneRow && ! is_array( $stored ) ) {
				throw new \RuntimeException( 'Unexpected Divi option format; settings were not changed.' );
			}
			$value = $oneRow ? ( isset( $stored[ self::SETTING ] ) ? $stored[ self::SETTING ] : 'on' ) : $stored;
			if ( $value !== 'off' && $value !== 'on' ) {
				throw new \RuntimeException( 'Unexpected Divi static CSS value; settings were not changed.' );
			}
			if ( $value !== 'off' ) {
				// Keep a durable retry marker in case writing settings or purging fails.
				update_option( self::PENDING, 1, false );
				if ( ! get_option( self::PENDING ) ) {
					throw new \RuntimeException( 'Cannot record the pending page cache purge.' );
				}
				if ( $oneRow ) {
					// Avoid overwriting newer theme settings with Divi's cached snapshot.
					$et_theme_options = $stored;
				}
				et_update_option( self::SETTING, 'off' );

				// et_update_option has no return value and updates its global cache even
				// if the DB write fails. Check persistent storage, not that global cache.
				if ( $oneRow ) {
					$options = get_option( $optionName, [] );
					$et_theme_options = $options;
					$saved = isset( $options[ self::SETTING ] ) ? $options[ self::SETTING ] : null;
				} else {
					$saved = get_option( self::SETTING );
				}
				if ( $saved !== 'off' ) {
					throw new \RuntimeException( 'Cannot disable Divi static CSS generation.' );
				}
			}

			if ( get_option( self::PENDING ) ) {
				$status = self::purgePages();
				delete_option( self::PENDING );
				set_transient( self::NOTICE, $status, DAY_IN_SECONDS );
			}
		} catch ( \Throwable $e ) {
			// Avoid retrying a failing filesystem/CDN on every admin request.
			set_transient( self::RETRY, 1, 5 * MINUTE_IN_SECONDS );
			set_transient( self::NOTICE, 'error', DAY_IN_SECONDS );
			error_log( 'FastCache Divi compatibility: ' . $e->getMessage() );
		} finally {
			self::$running = false;
		}
	}

	private static function purgePages() {
		// Do not use deleteCache('page'): its legacy delete_all_cache setting can
		// also remove other plugins' caches. Only this blog's HTML/WPC pages belong here.
		$prefix = is_multisite() ? (int) get_current_blog_id() . '/' : '';
		$directory = FASTCACHE_CACHE_DIR . $prefix . 'page';
		$filesystem = Cache::getWpFileSystem();
		if ( ! $filesystem ) {
			throw new \RuntimeException( 'Cannot access FastCache page files.' );
		}
		if ( $filesystem->exists( $directory ) ) {
			$root = realpath( FASTCACHE_CACHE_DIR );
			$resolved = realpath( $directory );
			$expected = $root === false ? '' : str_replace( '\\', '/', $root ) . '/' . $prefix . 'page';
			if ( $resolved === false || str_replace( '\\', '/', $resolved ) !== $expected ) {
				throw new \RuntimeException( 'Unexpected page cache path; files were not deleted.' );
			}
			// Page cache files are flat. Never traverse subdirectories or symlinks.
			foreach ( [ '*.html', '*.wpc' ] as $pattern ) {
				$files = glob( $directory . '/' . $pattern );
				if ( $files === false ) {
					throw new \RuntimeException( 'Cannot list FastCache page files.' );
				}
				foreach ( $files as $file ) {
					if ( is_link( $file ) || ! is_file( $file ) || ! $filesystem->delete( $file, false, 'f' ) ) {
						throw new \RuntimeException( 'Cannot clear FastCache page files; the purge will be retried.' );
					}
				}
			}
		}

		$settings = get_option( 'fastcache_settings', [] );
		if ( ! empty( $settings['fastcache-enable'] ) && ! empty( $settings['text-token'] ) ) {
			// A wildcard host purge can affect sibling sites on subdirectory networks.
			if ( is_multisite() ) {
				return 'manual_cdn';
			}
			if ( ! defined( 'FASTCACHEHOST_HOST_ENDPOINTCACHE' ) ) {
				throw new \RuntimeException( 'CDN configuration is not loaded yet.' );
			}
			$response = wp_remote_request( FASTCACHEHOST_HOST_ENDPOINTCACHE . '.*', [
				'method' => 'PURGE',
				'timeout' => 5,
				'headers' => [
					'X-HST-CACHE-PurgeKey' => $settings['text-token'],
					'host' => wp_parse_url( get_site_url(), PHP_URL_HOST ),
					'X-HST-CACHE-Purge-Method' => 'regexp',
				],
			] );
			if ( is_wp_error( $response ) ) {
				throw new \RuntimeException( 'CDN purge failed; it will be retried.' );
			}
			$code = wp_remote_retrieve_response_code( $response );
			if ( $code < 200 || $code >= 300 ) {
				throw new \RuntimeException( 'CDN purge failed; it will be retried.' );
			}
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! is_array( $body ) || empty( $body['nodes'] ) || ! is_array( $body['nodes'] ) ) {
				throw new \RuntimeException( 'CDN purge returned an unrecognized result.' );
			}
			foreach ( $body['nodes'] as $nodeCode ) {
				if ( ! in_array( $nodeCode, [ 200, 404, '200', '404' ], true ) ) {
					throw new \RuntimeException( 'One or more CDN nodes failed to purge.' );
				}
			}
		}
		return 'success';
	}

	public static function showNotice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$status = get_transient( self::NOTICE );
		if ( ! $status ) {
			return;
		}
		delete_transient( self::NOTICE );
		if ( $status === 'success' ) {
			$message = __( 'FastCache disabled Divi static CSS file generation and cleared the page cache to keep HTML and styles consistent.', 'fastcache' );
		} elseif ( $status === 'manual_cdn' ) {
			$message = __( 'FastCache disabled Divi static CSS file generation and cleared the local page cache. On Multisite, complete the site CDN cache purge from the Host.it control panel.', 'fastcache' );
		} else {
			$message = __( 'FastCache could not complete the Divi compatibility configuration or cache purge. The check will be retried; see the PHP error log for details.', 'fastcache' );
		}
		echo '<div class="notice notice-' . ( $status === 'success' ? 'success' : 'warning' ) . ' is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
	}
}
