<?php

namespace FastCache;

/**
 * FastCache - Performs several front-end optimizations for fast downloads
 *
 * @package FastCache
 * @author Host.it <info@host.it>
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license GNU/GPLv3, or later. See LICENSE file
 *         
 *          If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
use FastCache\Core\Admin\Tasks;
use FastCache\Core\Compatibility\Divi;
use FastCache\Core\Helper;
use FastCache\Core\Logger;
use FastCache\Core\Optimize;
use FastCache\Core\PageCache;
use FastCache\Core\Exception;
use FastCache\Platform\Cache;
use FastCache\Platform\Plugin;
use FastCache\Platform\Settings;
use FastCache\Platform\Uri;
use FastCache\Platform\Utility;
use FastCache\Core\Admin\DashboardWidget;

abstract class Dispatcher {
	/**
	 *
	 * @var Settings|null
	 */
	private static $oParams;
	protected static function runActivationRoutines() {
		// Handles activation routines
		if (! file_exists ( FASTCACHE_DIR . 'root.php' )) {
			try {
				$wp_filesystem = Cache::getWpFileSystem ();
			} catch ( Exception $e ) {
				return false;
			}

			if ($wp_filesystem === false) {
				return false;
			}

			$file = FASTCACHE_DIR . 'root.php';
			$abspath = ABSPATH;
			$code = <<<PHPCODE
			<?php	
			\$FASTCACHE_ROOT = '$abspath';
			PHPCODE;
			$wp_filesystem->put_contents ( $file, $code, FS_CHMOD_FILE );
		}
	}
	public static function init() {
		self::$oParams = Plugin::getPluginParams ();
		Divi::register();
		self::runActivationRoutines ();
		$active_plugins = ( array ) get_option ( 'active_plugins', [ ] );

		// Register REST endpoint for late nonce refresh (v1.6.9)
		if ( file_exists( FASTCACHE_DIR . '/src/Core/NonceEndpoint.php' ) ) {
			require_once FASTCACHE_DIR . '/src/Core/NonceEndpoint.php';
			\FastCache\Core\NonceEndpoint::register();
		}

		// Include the Fastcache CDN lib
		require_once FASTCACHE_DIR . '/src/Host/Main.php';

		if (is_admin ()) {
			require_once FASTCACHE_DIR . '/src/Admin.php';
			add_action ( 'admin_menu', [ 
					'FastCache\\Admin',
					'addAdminMenu'
			] );
			add_action ( 'admin_init', [ 
					'FastCache\\Admin',
					'registerOptions'
			] );
			add_filter ( 'plugin_action_links', [ 
					'FastCache\\Admin',
					'loadActionLinks'
			], 10, 2 );
			DashboardWidget::init ();
		} else {
			if (self::$oParams->get ( 'disableCoreLazyload', 0 )) {
				add_filter ( 'wp_lazy_loading_enabled', '__return_false' );
			}

			$url_exclude = self::$oParams->get ( 'menuexcludedurl', [ ] );
			$isBackendRequest = Utility::get ( 'fastcacherunbackend' );

			if (defined ( 'WP_USE_THEMES' ) && WP_USE_THEMES && $isBackendRequest != 1 && version_compare ( PHP_VERSION, '5.3.0', '>=' ) && ! defined ( 'DOING_AJAX' ) && ! defined ( 'DOING_CRON' ) && ! defined ( 'APP_REQUEST' ) && ! defined ( 'XMLRPC_REQUEST' ) && (! defined ( 'SHORTINIT' ) || (defined ( 'SHORTINIT' ) && ! SHORTINIT)) && ! Helper::findExcludes ( $url_exclude, Uri::getInstance ()->toString () )) {
				// Disable NextGen Resource Manager; incompatible with plugin
				// add_filter( 'run_ngg_resource_manager', '__return_false' );
				add_action ( 'init', [ 
						__CLASS__,
						'initializeCache'
				], 0 );

				ob_start ( [ 
						__CLASS__,
						'runOptimize'
				] );
			}
		}

		add_action ( 'plugins_loaded', [ 
				__CLASS__,
				'pluginsLoaded'
		] );
		// register_uninstall_hook ( FASTCACHE_FILE_PATH, [
		// 'FastCache\\Dispatcher',
		// 'runUninstallRoutines'
		// ] );

		if (self::$oParams->get ( 'order_plugin', '1' )) {
			add_action ( 'activated_plugin', [ 
					__CLASS__,
					'orderPlugin'
			] );
			add_action ( 'deactivated_plugin', [ 
					__CLASS__,
					'orderPlugin'
			] );
		}

		if (self::$oParams->get ( 'lazyload_enable', '0' )) {
			add_action ( 'wp_head', [ 
					__CLASS__,
					'enqueueLazyLoad'
			] );
		}

		if (self::$oParams->get ( 'enable_instant_page', '0' )) {
			add_action ( 'wp_head', [ 
					__CLASS__,
					'enqueueInstantPage'
			] );
		}

		if (self::$oParams->get ( 'pro_cache_platform', '0' )) {
			add_filter ( 'fastcache_get_page_cache_id', [ 
					__CLASS__,
					'getPageCacheHash'
			], 10, 2 );
		}

		// Ajax functions
		add_action ( 'wp_ajax_multiselect', [ 
				'FastCache\\Admin',
				'doAjaxMultiSelect'
		] );

		add_action ( 'wp_ajax_fastcache_hook_pagespeed', [ 
				'FastCache\\Admin',
				'executePageSpeedTest'
		] );

		// Helper functions for encoding urls
		function base64_encode_url($string) {
			return strtr ( base64_encode ( $string ), '+/=', '._-' );
		}
		function base64_decode_url($string) {
			return base64_decode ( strtr ( $string, '._-', '+/=' ) );
		}

		// Object Cache: register invalidation hooks if enabled
		$objCacheSettings = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, [ ] );
		if (! empty ( $objCacheSettings ['object_cache_enable'] )) {
			\FastCache\ObjectCache\Invalidation::register ();
		}

		// Ajax handler for object cache drop-in management
		add_action ( 'wp_ajax_fastcache_object_cache_action', [
				__CLASS__,
				'handleObjectCacheAjax'
		] );

		// Test Redis and Memcache connections
		add_action ( 'wp_ajax_fastcache_test_redis', [
				__CLASS__,
				'testRedisConnection'
		] );
		add_action ( 'wp_ajax_fastcache_test_memcache', [
				__CLASS__,
				'testMemcacheConnection'
		] );

		// Auto-manage drop-in when settings are saved
		add_action ( 'update_option_' . FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, [ 
				__CLASS__,
				'onSettingsUpdate'
		], 10, 2 );
	}
	public static function initializeCache() {
		PageCache::initialize ();
	}
	public static function runUninstallRoutines() {
		// Remove object cache drop-in
		\FastCache\ObjectCache\DropinManager::uninstall ();

		delete_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
		try {
			Cache::deleteCache ();
		} catch ( Exception $e ) {
		}

		Tasks::cleanHtaccess ();
	}
	public static function runOptimize($sHtml) {
		if (! Helper::validateHtml ( $sHtml )) {
			return $sHtml;
		}

		// Divi Visual Builder sessions skip all optimization and caching
		if ( isset( $_GET['et_fb'] ) || isset( $_GET['et_pb_preview'] ) ) {
			return $sHtml;
		}

		$disable_logged_in = self::$oParams->get ( 'disable_logged_in_users', '0' );

		// Need to call Utility::isGuest after init has been called
		if ($disable_logged_in && ! Utility::isGuest ()) {
			return $sHtml;
		}

		try {
			$sOptimizedHtml = Optimize::optimize ( self::$oParams, $sHtml );

			if (self::$oParams->get ( 'enable_instant_page', '0' ) && self::$oParams->get ( 'instant_page_delay', 'fast' ) == 'slow') {
				$sOptimizedHtml = preg_replace ( '/<body/i', '<body data-instant-intensity="150"', $sOptimizedHtml );
			}

			Pagecache::store ( $sOptimizedHtml );
		} catch ( Exception $e ) {
			Logger::log ( $e->getMessage (), self::$oParams );

			$sOptimizedHtml = $sHtml;
		}

		return $sOptimizedHtml;
	}
	public static function pluginsLoaded() {
		self::loadPluginTextDomain ();
		self::migration ();
		self::cronActivation ();
	}
	public static function migration() {
		$installed = get_option ( 'fastcache_version' );

		if ($installed === false) {
			update_option ( 'fastcache_version', FASTCACHE_VERSION );
			$installed = "1.0.0";
		}

		$upgrades = [ 
				'1.5.5' => 'fastcache_upgrade_to_155'
		];

		foreach ( $upgrades as $version => $method ) {
			if (version_compare ( $installed, $version, '<' )) {
				if (method_exists ( __CLASS__, $method )) {
					call_user_func ( [ 
							__CLASS__,
							$method
					] );
				}
			}
		}

		update_option ( 'fastcache_version', FASTCACHE_VERSION );
	}
	public static function cronActivation() {
		if (! wp_next_scheduled ( 'fastcache_cron_prune' )) {
			wp_schedule_event ( time (), 'hourly', 'fastcache_cron_prune' );
		}

		add_action ( 'fastcache_cron_prune', [
				__CLASS__,
				'cronPrune'
		] );
	}
	public static function cronPrune() {
		$params       = Plugin::getPluginParams ();
		$ttl          = ( int ) $params->get ( 'page_cache_lifetime', '86400' );
		$current_time = time ();

		// Build the list of page-cache directories to prune.
		// Single-site: FASTCACHE_CACHE_DIR/page/
		// Multisite:   FASTCACHE_CACHE_DIR/{blog_id}/page/ for every registered site.
		if ( is_multisite() ) {
			$page_dirs = [];
			foreach ( get_sites( [ 'number' => 500 ] ) as $site ) {
				$page_dirs[] = FASTCACHE_CACHE_DIR . (int) $site->blog_id . '/page/';
			}
		} else {
			$page_dirs = [ FASTCACHE_CACHE_DIR . 'page/' ];
		}

		foreach ( $page_dirs as $dir ) {
			$files = glob( rtrim( $dir, '/' ) . '/*' );
			if ( ! $files ) {
				continue;
			}
			foreach ( $files as $file ) {
				$diff = $current_time - filemtime ( $file );
				if ( $diff > $ttl || filesize ( $file ) == 0 ) {
					unlink ( $file );
				}
			}
		}

		// Object Cache GC: delete expired object cache files
		$settings = get_option( 'fastcache_settings', [] );
		if ( !empty( $settings['object_cache_enable'] ) ) {
			$objectCache = \FastCache\ObjectCache\ObjectCache::getInstance();
			if ( $objectCache->isInitialized() ) {
				$objectCache->garbageCollection();
			}
		}
	}
	public static function fastcache_upgrade_to_155() {
		Tasks::cleanHtaccess ();
	}
	public static function loadPluginTextDomain() {
		load_plugin_textdomain ( 'fastcache', false, basename ( dirname ( FASTCACHE_FILE_PATH ) ) . '/languages' );
	}
	public static function orderPlugin() {
		$active_plugins = ( array ) get_option ( 'active_plugins', [ ] );
		$order = [ 
				'wp-rocket/wp-rocket.php',
				'w2-total-cache/w3-total-cache.php',
				'litespeed-cache/litespeed-cache.php',
				'wp-fastest-cache/wpFastestCache.php',
				'wp-optimize/wp-optimize.php',
				'comet-cache/comet-cache.php',
				'hyper-cache/plugin.php',
				'swift-performance/performance.php',
				'fastcache/fastcache.php',
				'wp-super-cache/wp-cache.php',
				'akeebabackupwp/akeebabackupwp.php'
		];

		// Get the plugins in $order that are currently activated
		$order_short_list = array_intersect ( $order, $active_plugins );
		// Remove plugins in $order_short_list from list of activated plugins
		$active_plugins_slist = array_diff ( $active_plugins, $order_short_list );
		// Merge $order with $active_plugins_list
		$ordered_active_plugins = array_merge ( $order_short_list, $active_plugins_slist );

		update_option ( 'active_plugins', $ordered_active_plugins );

		return true;
	}
	public static function enqueueLazyLoad() {
		wp_register_script ( 'fastcache-lazyloader-js', FASTCACHE_URL . 'media/core/js/ls.loader.js', [ ], FASTCACHE_VERSION );
		wp_enqueue_script ( 'fastcache-lazyloader-js' );

		if (self::$oParams->get ( 'pro_lazyload_effects', '0' )) {
			wp_enqueue_style ( 'fastcache-lazyload-css', FASTCACHE_URL . 'media/core/css/ls.effects.css', [ ], FASTCACHE_VERSION );

			wp_register_script ( 'fastcache-lseffects-js', FASTCACHE_URL . 'media/core/js/ls.loader.effects.js', [ 
					'fastcache-lazyloader-js'
			], FASTCACHE_VERSION );
			wp_enqueue_script ( 'fastcache-lseffects-js' );
		}

		if (self::$oParams->get ( 'pro_lazyload_bgimages', '0' ) || self::$oParams->get ( 'pro_lazyload_audiovideo', '0' )) {
			wp_register_script ( 'fastcache-unveilhooks-js', FASTCACHE_URL . 'media/lazysizes/ls.unveilhooks.js', [ 
					'fastcache-lazyloader-js'
			], FASTCACHE_VERSION );
			wp_enqueue_script ( 'fastcache-unveilhooks-js' );
		}

		wp_register_script ( 'fastcache-lazyload-js', FASTCACHE_URL . 'media/lazysizes/lazysizes.js', [ 
				'fastcache-lazyloader-js'
		], FASTCACHE_VERSION );
		wp_enqueue_script ( 'fastcache-lazyload-js' );
	}
	public static function enqueueInstantPage() {
		wp_register_script ( 'fastcache-instantpage-js', FASTCACHE_URL . 'media/core/js/instantpage-5.2.0.js', [ ], FASTCACHE_VERSION );
		wp_enqueue_script ( 'fastcache-instantpage-js' );
	}
	public static function getPageCacheHash($parts) {
		if (wp_is_mobile ()) {
			$parts [] = '_MOBILE_';
		}

		return $parts;
	}

	/**
	 * Handle AJAX requests for Object Cache management.
	 */
	public static function handleObjectCacheAjax() {
		if (! current_user_can ( 'manage_options' )) {
			wp_send_json_error ( __ ( 'Unauthorized', 'fastcache' ) );
		}

		check_ajax_referer ( 'fastcache_object_cache_nonce', 'nonce' );

		$action = isset ( $_POST ['cache_action'] ) ? sanitize_text_field ( $_POST ['cache_action'] ) : '';

		switch ($action) {
			case 'enable' :
				$result = \FastCache\ObjectCache\DropinManager::install ();
				if ($result === true) {
					wp_send_json_success ( __ ( 'Object cache drop-in installed successfully.', 'fastcache' ) );
				} else {
					wp_send_json_error ( $result );
				}
				break;

			case 'disable' :
				$result = \FastCache\ObjectCache\DropinManager::uninstall ();
				if ($result === true) {
					wp_send_json_success ( __ ( 'Object cache drop-in removed successfully.', 'fastcache' ) );
				} else {
					wp_send_json_error ( $result );
				}
				break;

			case 'flush' :
				if (function_exists ( 'wp_cache_flush' )) {
					wp_cache_flush ();
				}
				// Always delete the log file directly — no dependencies on adapters or results
				$logFile = WP_CONTENT_DIR . '/cache/fastcache/object-cache.log';
				if (file_exists($logFile)) {
					@unlink($logFile);
				}
				wp_send_json_success ( __ ( 'Object cache flushed successfully.', 'fastcache' ) );
				break;

			case 'status' :
				$status = \FastCache\ObjectCache\DropinManager::getStatus ();
				$availableBackends = \FastCache\ObjectCache\BackendDetector::getAvailable ();

				$oc = \FastCache\ObjectCache\ObjectCache::getInstance ();
				$stats = $oc !== null ? $oc->getStats () : [ ];

				wp_send_json_success ( [ 
						'dropin' => $status,
						'backends' => $availableBackends,
						'stats' => $stats
				] );
				break;

			default :
				wp_send_json_error ( __ ( 'Unknown action.', 'fastcache' ) );
		}
	}

	/**
	 * Handle settings update - auto-manage object cache drop-in.
	 *
	 * @param mixed $oldValue
	 *        	Old settings value
	 * @param mixed $newValue
	 *        	New settings value
	 */
	public static function onSettingsUpdate($oldValue, $newValue) {
		if (! is_array ( $newValue )) {
			return;
		}

		// Regenerate the .htaccess FASTCACHE block here, not via admin_action_update: the
		// settings form posts to options.php, which WordPress's own core never routes through
		// admin_action_{$action} -- so Admin::updateSettings() (hooked there) never actually
		// runs on a real save. update_option_{option} is the one hook WordPress guarantees
		// fires on every save regardless of how it was triggered, and it hands us the
		// authoritative old/new arrays directly, with no caching involved (Bug#34278 follow-up).
		if ( ! is_array( $oldValue ) ) {
			$oldValue = [];
		}
		Utility::htaccessCacheManagement( $newValue, null, $oldValue );

		$wasEnabled = ! empty ( $oldValue ['object_cache_enable'] );
		$isEnabled = ! empty ( $newValue ['object_cache_enable'] );

		// Enabling object cache
		if ($isEnabled && ! $wasEnabled) {
			\FastCache\ObjectCache\DropinManager::install ();
		}

		// Disabling object cache
		if (! $isEnabled && $wasEnabled) {
			\FastCache\ObjectCache\DropinManager::uninstall ();
		}

		// Settings changed while enabled - update the drop-in
		if ($isEnabled && $wasEnabled) {
			// Check if backend-related settings changed
			$backendSettings = [ 
					'object_cache_backend',
					'object_cache_redis_host',
					'object_cache_redis_port',
					'object_cache_redis_password',
					'object_cache_redis_database',
					'object_cache_memcached_host',
					'object_cache_memcached_port',
					'object_cache_key_prefix'
			];

			foreach ( $backendSettings as $key ) {
				$old = isset ( $oldValue [$key] ) ? $oldValue [$key] : '';
				$new = isset ( $newValue [$key] ) ? $newValue [$key] : '';

				if ($old !== $new) {
					\FastCache\ObjectCache\DropinManager::update ();
					break;
				}
			}
		}
	}

	/**
	 * Test Redis connection
	 */
	public static function testRedisConnection() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', 'fastcache' ) );
		}
		check_ajax_referer( 'fastcache_test_redis', 'nonce' );

		try {
			$settings = get_option( 'fastcache_settings', [] );
			$host = isset( $settings['object_cache_redis_host'] ) ? $settings['object_cache_redis_host'] : '127.0.0.1';
			$port = isset( $settings['object_cache_redis_port'] ) ? (int) $settings['object_cache_redis_port'] : 6379;
			$password = isset( $settings['object_cache_redis_password'] ) ? $settings['object_cache_redis_password'] : '';
			$database = isset( $settings['object_cache_redis_database'] ) ? (int) $settings['object_cache_redis_database'] : 0;

			if ( ! extension_loaded( 'redis' ) ) {
				wp_send_json_error( __( 'Redis PHP extension not loaded', 'fastcache' ) );
			}

			$redis = new \Redis();
			$redis->connect( $host, $port, 3 );

			if ( ! empty( $password ) ) {
				$redis->auth( $password );
			}

			$redis->select( $database );

			// Test set/get
			$testKey = 'fastcache_test_' . time();
			$testValue = 'test_value';
			$redis->set( $testKey, $testValue, 10 );
			$retrieved = $redis->get( $testKey );
			$redis->delete( $testKey );

			if ( $retrieved === $testValue ) {
				wp_send_json_success( __( 'Redis connection OK', 'fastcache' ) );
			} else {
				wp_send_json_error( __( 'Redis test failed: data mismatch', 'fastcache' ) );
			}
		} catch ( Exception $e ) {
			wp_send_json_error( 'Redis error: ' . $e->getMessage() );
		}
	}

	/**
	 * Test Memcache connection
	 */
	public static function testMemcacheConnection() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Unauthorized', 'fastcache' ) );
		}
		check_ajax_referer( 'fastcache_test_memcache', 'nonce' );

		try {
			$settings = get_option( 'fastcache_settings', [] );
			$host = isset( $settings['object_cache_memcached_host'] ) ? $settings['object_cache_memcached_host'] : '127.0.0.1';
			$port = isset( $settings['object_cache_memcached_port'] ) ? (int) $settings['object_cache_memcached_port'] : 11211;

			if ( ! extension_loaded( 'memcached' ) ) {
				wp_send_json_error( __( 'Memcached PHP extension not loaded', 'fastcache' ) );
			}

			$memcached = new \Memcached();
			$memcached->addServer( $host, $port );

			// Test set/get
			$testKey = 'fastcache_test_' . time();
			$testValue = 'test_value';
			$memcached->set( $testKey, $testValue, 10 );
			$retrieved = $memcached->get( $testKey );
			$memcached->delete( $testKey );

			if ( $retrieved === $testValue ) {
				wp_send_json_success( __( 'Memcached connection OK', 'fastcache' ) );
			} else {
				wp_send_json_error( __( 'Memcached test failed: data mismatch', 'fastcache' ) );
			}
		} catch ( Exception $e ) {
			wp_send_json_error( 'Memcached error: ' . $e->getMessage() );
		}
	}
}