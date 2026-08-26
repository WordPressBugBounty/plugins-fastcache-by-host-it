<?php

namespace FastCache\Core\Admin;

use FastCache\Platform\Plugin;
use FastCache\Platform\Utility;

/**
 * Dashboard Widget for FastCache
 *
 * Displays a summary status box on the WordPress admin dashboard
 * showing the current state of cache modules (PHP Cache, Static Site, CDN).
 *
 * @package FastCache
 * @since 1.5.18
 */
class DashboardWidget {

	/**
	 * Register the dashboard widget hook.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_dashboard_setup', [ __CLASS__, 'register' ] );
		add_action( 'admin_init', [ __CLASS__, 'handleToggle' ] );
	}

	/**
	 * Register the widget with WordPress.
	 *
	 * @return void
	 */
	public static function register() {
		wp_add_dashboard_widget(
			'fastcache_dashboard_widget',
			'FastCache ' . FASTCACHE_VERSION,
			[ __CLASS__, 'render' ]
		);
	}

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	public static function render() {
		$params  = Plugin::getPluginParams();
		$options = get_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );

		$phpCache    = (bool) $params->get( 'cache_enable', '1' );
		$staticSite  = (bool) $params->get( 'htaccess_cache_enable', '1' );
		$cdnActive   = ! empty( $options['fastcache-enable'] );
		$logoUrl     = FASTCACHE_URL . 'media/images/fastcache-logo-128x128.png';
		$version     = FASTCACHE_VERSION;

		// Cache stats — same algorithm as Configurations::getCacheSize()
		$cacheStats = self::getCacheStats();

		// Purge URLs
		$purgeLocalUrl = add_query_arg( [
			'page'     => 'fastcache',
			'task'     => 'cleancache',
			'_wpnonce' => wp_create_nonce( 'cleancache' ),
		], admin_url( 'options-general.php' ) );

		$purgeCdnUrl = wp_nonce_url(
			admin_url( 'admin-ajax.php?action=hstPurgeCache' ),
			'hstPurgeCache'
		);

		// Toggle URLs
		$togglePhpCache = wp_nonce_url(
			admin_url( 'index.php?fastcache_toggle=cache_enable' ),
			'fastcache_toggle'
		);
		$toggleStaticSite = wp_nonce_url(
			admin_url( 'index.php?fastcache_toggle=htaccess_cache_enable' ),
			'fastcache_toggle'
		);
		$toggleCdn = wp_nonce_url(
			admin_url( 'index.php?fastcache_toggle=fastcache-enable' ),
			'fastcache_toggle'
		);

		?>
		<div class="fastcache-dashboard-widget">
			<style>
				.fastcache-dashboard-widget {
					padding: 4px 0;
				}
				.fastcache-dw-header {
					display: flex;
					align-items: center;
					gap: 10px;
					margin-bottom: 14px;
					padding-bottom: 10px;
					border-bottom: 1px solid #e0e0e0;
				}
				.fastcache-dw-header img {
					width: 84px;
					height: 84px;
				}
				.fastcache-dw-header span {
					font-size: 15px;
					font-weight: 600;
					color: #1d2327;
				}
				.fastcache-dw-header .fastcache-dw-version {
					font-size: 12px;
					font-weight: 400;
					color: #787c82;
					margin-left: 2px;
				}
				.fastcache-dw-row {
					display: flex;
					justify-content: space-between;
					align-items: center;
					padding: 7px 0;
				}
				.fastcache-dw-row + .fastcache-dw-row {
					border-top: 1px solid #f0f0f1;
				}
				.fastcache-dw-label {
					font-size: 13px;
					color: #1d2327;
				}
				.fastcache-dw-badge {
					display: inline-block;
					padding: 2px 10px;
					border-radius: 10px;
					font-size: 11px;
					font-weight: 600;
					text-transform: uppercase;
					letter-spacing: 0.3px;
					text-decoration: none;
					cursor: pointer;
					transition: opacity 0.15s;
                    text-decoration: underline;
				}
				.fastcache-dw-badge:hover {
					opacity: 0.75;
				}
				.fastcache-dw-badge.active {
					background: #d4edda;
					color: #155724;
				}
				.fastcache-dw-badge.inactive {
					background: #f0f0f1;
					color: #787c82;
				}
				.fastcache-dw-right {
					display: flex;
					align-items: center;
					gap: 8px;
				}
				.fastcache-dw-purge {
					font-size: 11px;
					color: #b32d2e;
					text-decoration: none;
					cursor: pointer;
                    text-decoration: underline;
				}
				.fastcache-dw-purge:hover {
					color: #a00;
					text-decoration: underline;
				}
				.fastcache-dw-stats {
					display: flex;
					gap: 16px;
					padding: 10px 0;
					margin-bottom: 4px;
					border-bottom: 1px solid #e0e0e0;
				}
				.fastcache-dw-stat {
					flex: 1;
					text-align: center;
					padding: 8px;
					background: #f6f7f7;
					border-radius: 6px;
				}
				.fastcache-dw-stat-value {
					font-size: 18px;
					font-weight: 700;
					color: #1d2327;
					line-height: 1.3;
				}
				.fastcache-dw-stat-label {
					font-size: 11px;
					color: #787c82;
					text-transform: uppercase;
					letter-spacing: 0.3px;
				}
			</style>

			<div class="fastcache-dw-header">
				<img src="<?php echo esc_url( $logoUrl ); ?>" alt="FastCache">

                <?php if ( $phpCache || $staticSite ) : ?>
                        <div class="fastcache-dw-stat">
                            <div class="fastcache-dw-stat-value"><?php echo esc_html( $cacheStats['files'] ); ?></div>
                            <div class="fastcache-dw-stat-label"><?php esc_html_e( 'Cache files', 'fastcache' ); ?></div>
                        </div>
                        <div class="fastcache-dw-stat">
                            <div class="fastcache-dw-stat-value"><?php echo esc_html( $cacheStats['size'] ); ?></div>
                            <div class="fastcache-dw-stat-label"><?php esc_html_e( 'Cache size', 'fastcache' ); ?></div>
                        </div>
                <?php endif; ?>
			</div>

			<div class="fastcache-dw-row">
				<span class="fastcache-dw-label"><?php esc_html_e( 'PHP Cache', 'fastcache' ); ?></span>
				<div class="fastcache-dw-right">
					<?php self::renderBadge( $phpCache, $togglePhpCache ); ?>
					<?php if ( $phpCache ) : ?>
						<a href="<?php echo esc_url( $purgeLocalUrl ); ?>" class="fastcache-dw-purge"><?php esc_html_e( 'Purge', 'fastcache' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

			<div class="fastcache-dw-row">
				<span class="fastcache-dw-label"><?php esc_html_e( 'Ultra Fast Static Web Site', 'fastcache' ); ?></span>
				<div class="fastcache-dw-right">
					<?php self::renderBadge( $staticSite, $toggleStaticSite ); ?>
					<?php if ( $staticSite ) : ?>
						<a href="<?php echo esc_url( $purgeLocalUrl ); ?>" class="fastcache-dw-purge"><?php esc_html_e( 'Purge', 'fastcache' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

			<div class="fastcache-dw-row">
				<span class="fastcache-dw-label"><?php esc_html_e( 'CDN', 'fastcache' ); ?></span>
				<div class="fastcache-dw-right">
					<?php self::renderBadge( $cdnActive, $toggleCdn ); ?>
					<?php if ( $cdnActive ) : ?>
						<a href="<?php echo esc_url( $purgeCdnUrl ); ?>" class="fastcache-dw-purge"><?php esc_html_e( 'Purge', 'fastcache' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a clickable active/inactive badge.
	 *
	 * @param bool   $active
	 * @param string $toggleUrl URL to toggle the state
	 * @return void
	 */
	private static function renderBadge( $active, $toggleUrl ) {
		if ( $active ) {
			echo '<a href="' . esc_url( $toggleUrl ) . '" class="fastcache-dw-badge active" title="' . esc_attr__( 'Click to disable', 'fastcache' ) . '">' . esc_html__( 'Active', 'fastcache' ) . '</a>';
		} else {
			echo '<a href="' . esc_url( $toggleUrl ) . '" class="fastcache-dw-badge inactive" title="' . esc_attr__( 'Click to enable', 'fastcache' ) . '">' . esc_html__( 'Inactive', 'fastcache' ) . '</a>';
		}
	}

	/**
	 * Handle toggle requests from the dashboard widget.
	 *
	 * @return void
	 */
	public static function handleToggle() {
		if ( empty( $_GET['fastcache_toggle'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Not authorized', 'fastcache' ) );
		}

		check_admin_referer( 'fastcache_toggle' );

		$setting = sanitize_text_field( $_GET['fastcache_toggle'] );
		$allowed = [ 'cache_enable', 'htaccess_cache_enable', 'fastcache-enable' ];

		if ( ! in_array( $setting, $allowed, true ) ) {
			wp_die( __( 'Invalid setting', 'fastcache' ) );
		}

		$options = get_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
		if ( ! is_array( $options ) ) {
			$options = [];
		}

		// Toggle the value
		$current = isset( $options[ $setting ] ) ? (int) $options[ $setting ] : 0;
		$options[ $setting ] = $current ? 0 : 1;

		update_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, $options );

		// Handle htaccess side-effects for static site toggle
		if ( $setting === 'htaccess_cache_enable' ) {
			Utility::htaccessCacheManagement( $options );
		}

		// Redirect to plugin settings page
		wp_safe_redirect( admin_url( 'options-general.php?page=fastcache' ) );
		exit;
	}

	/**
	 * Get cache file count and total size.
	 * Uses the same algorithm as Configurations::getCacheSize()
	 *
	 * @return array{files: string, size: string}
	 */
	public static function getCacheStats() {
		$totalSize  = 0;
		$totalFiles = 0;
		$cachePath  = FASTCACHE_CACHE_DIR;

		if ( file_exists( $cachePath ) ) {
			$fi = new \FilesystemIterator( $cachePath, \FilesystemIterator::SKIP_DOTS );
			foreach ( $fi as $file ) {
				$totalSize += $file->getSize();
			}
			$totalFiles += iterator_count( $fi );
		}

		$imagesCachePath = $cachePath . 'images/';
		if ( file_exists( $imagesCachePath ) ) {
			$fim = new \FilesystemIterator( $imagesCachePath, \FilesystemIterator::SKIP_DOTS );
			foreach ( $fim as $image ) {
				$totalSize += $image->getSize();
			}
			$totalFiles += iterator_count( $fim );
		}

		return [
			'files'     => number_format( $totalFiles ),
			'size'      => size_format( $totalSize ),
			'files_raw' => $totalFiles,
			'size_raw'  => $totalSize,
		];
	}
}
