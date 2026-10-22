<?php

/**
 * FastCache - Performs several front-end optimizations for fast downloads
 *
 * @package   FastCache
 * @author    Host.it <info@host.it>
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace FastCache\Platform;

use FastCache\Core\Interfaces\Plugin as PluginInterface;
use FastCache\Core\Admin\Tasks;
use FastCache\Dispatcher;

defined( '_WP_EXEC' ) or die( 'Restricted access' );

class Plugin implements PluginInterface
{

	protected static $plugin = null;

	/**
	 *
	 * @return void
	 */
	public static function getPluginId()
	{
		return;
	}

	/**
	 *
	 * @return void
	 */
	public static function getPlugin()
	{
		return;
	}

	/**
	 *
	 * @param   Settings  $params
	 */
	public static function saveSettings( Settings $params )
	{
		$options = $params->getOptions();

		update_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, $options );
	}

	/**
	 * Returns the CDN token for the current site.
	 *
	 * In multisite each subsite has its own CDN account and token (per-site setting).
	 * Centralizes the read so all callers use the same path.
	 *
	 * @return string
	 */
	public static function getCdnToken(): string
	{
		$settings = get_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, [] );
		return isset( $settings['text-token'] ) ? (string) $settings['text-token'] : '';
	}

	/**
	 *
	 * @return Settings
	 */
	/**
	 * Request-lifetime cache for getPluginParams(). A class property (rather than a
	 * function-local static) so resetPluginParamsCache() can invalidate it from outside.
	 */
	private static $paramsCache = null;

	public static function getPluginParams()
	{
		if ( is_null( self::$paramsCache ) )
		{
			$options = get_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
			self::$paramsCache = Settings::getInstance( $options );
		}

		return self::$paramsCache;
	}

	/**
	 * Force the next getPluginParams() call to re-read from the database instead of
	 * returning the snapshot cached earlier in this same PHP request. Needed right before
	 * regenerating the .htaccess block during a settings save: if anything earlier in the
	 * request already called getPluginParams() (very likely, it's used throughout the
	 * plugin), the cache would otherwise still hold the pre-save values even though
	 * update_option() already persisted the new ones -- producing .htaccess rules one save
	 * behind the setting actually being toggled (Bug#34278 follow-up).
	 */
	public static function resetPluginParamsCache()
	{
		self::$paramsCache = null;
	}

	public static function activate()
	{
		$currentSettings = Plugin::getPluginParams()->toArray();
		if(!isset($currentSettings['htaccess_cache_enable']) || (isset($currentSettings['htaccess_cache_enable']) && (int)$currentSettings['htaccess_cache_enable'] == 1)) {
			self::setHtaccessCacheEnableSwitch(true);
		}
		$settings = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);
		if (!$settings) {
			self::hookToLicense('install');
		} else {
			self::hookToLicense('activate');
		}
	}
	public static function update(): void {
		self::hookToLicense('update');
	}
	public static function deactivate()
	{
		self::setHtaccessCacheEnableSwitch(false);
		self::hookToLicense('deactivate');
		// Clean the htaccess file to remove the old rules, the new ones will be added by the activation hook of the plugin
		Tasks::cleanHtaccess();
	}

	public static function uninstall()
	{
		self::hookToLicense('uninstall');
		// Clean the htaccess file to remove the old rules, the new ones will be added by the activation hook of the plugin
		Tasks::cleanHtaccess();
	}
	public static function setHtaccessCacheEnableSwitch($enable){
		$currentSettings = Plugin::getPluginParams()->toArray();
		// Reactivate simulate the enable of htaccess_cache_enable
		$postedSettings = [];
		$postedSettings ['htaccess_cache_enable'] = 'auto';
		Utility::htaccessCacheManagement($postedSettings, $enable);
	}
	const UPGRADE_VERSION = '1.7.8';

	/**
	 * One-time upgrade routine, run from admin_init. The WordPress updater still executes the
	 * previous version's code, so this cannot run at update time: it runs on the first admin request after it.
	 */
	public static function maybeUpgrade()
	{
		if ( version_compare( (string) get_option( 'fastcache_upgrade_version', '0' ), self::UPGRADE_VERSION, '>=' ) ) {
			return;
		}

		// add_option() fails when the key exists, so only one request runs the upgrade at a time.
		if ( ! add_option( 'fastcache_upgrade_lock', time(), '', 'no' ) ) {
			if ( time() - (int) get_option( 'fastcache_upgrade_lock', 0 ) < 120 ) {
				return;
			}
			update_option( 'fastcache_upgrade_lock', time(), false );
		}

		$settings = get_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
		$settings_changed = false;
		if ( is_array( $settings ) ) {
			// Re-sanitize the stored cookie exclusions and keep track of what was rejected, for the admin notice.
			if ( isset( $settings['cache_cookie_exclude'] ) ) {
				$split = Utility::splitCookieExcludeList( $settings['cache_cookie_exclude'] );
				if ( $split['clean'] !== (array) $settings['cache_cookie_exclude'] ) {
					$settings['cache_cookie_exclude'] = $split['clean'];
					$settings_changed = true;
				}
				Utility::recordDroppedCookies( $split['dropped'] );
			}

			// Seed ignored tracking query args (1.7.8) if absent; re-sanitize if present.
			if ( ! array_key_exists( 'cache_query_ignore', $settings ) ) {
				$settings['cache_query_ignore'] = Utility::defaultIgnoredTrackingQueryArgs();
				$settings_changed = true;
			} else {
				$split = Utility::splitIgnoredQueryArgList( $settings['cache_query_ignore'] );
				if ( $split['clean'] !== (array) $settings['cache_query_ignore'] ) {
					$settings['cache_query_ignore'] = $split['clean'];
					$settings_changed = true;
				}
				Utility::recordDroppedQueryArgs( $split['dropped'] );
			}

			if ( ! array_key_exists( 'cache_warmup_on_publish', $settings ) ) {
				$settings['cache_warmup_on_publish'] = '1';
				$settings_changed = true;
			}

			if ( $settings_changed ) {
				update_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, $settings );
				self::resetPluginParamsCache();
			}

			// Rewrite an existing FastCache .htaccess block from the sanitized settings
			// (cookie allow-list + ignored QS RewriteCond for 1.7.8).
			$htaccess = Paths::rootPath() . '/.htaccess';
			$enabled  = ! isset( $settings['htaccess_cache_enable'] ) || (int) $settings['htaccess_cache_enable'] == 1;
			if ( $enabled && is_readable( $htaccess ) && false !== strpos( (string) file_get_contents( $htaccess ), '## BEGIN HTACCESS PAGE CACHING - FASTCACHE' ) ) {
				self::setHtaccessCacheEnableSwitch( true );
			}
		}

		// Remove page-cache files polluted with tracking QS in the filename (pre-1.7.8).
		Utility::cleanupPollutedPageCacheFiles();

		// Drop the page cache so it is rebuilt (Bug#34278): on https sites it may hold http:// assets written by an http request
		// (blocked by browsers), and with platform specific cache on it may hold pages generated for the wrong device, because
		// older versions did not purge the cache when that option was switched.
		// Keep this for upgrades that still carry the 1.7.7 condition when upgrading from older builds.
		$from = (string) get_option( 'fastcache_upgrade_version', '0' );
		if ( version_compare( $from, '1.7.7', '<' ) ) {
			if ( 0 === stripos( (string) get_option( 'home' ), 'https://' ) || ( is_array( $settings ) && ! empty( $settings['pro_cache_platform'] ) ) ) {
				Cache::deletePageCacheDir();
			}
		}

		update_option( 'fastcache_upgrade_version', self::UPGRADE_VERSION );
		delete_option( 'fastcache_upgrade_lock' );
	}

	public static function hookToLicense($action){
		// check if lic.host.it/index.html is reachable and reply with text/html "OK"
		// with 1 second of timeout
		
		$url = FASTCACHE_HOST_LICENSE_SERVER_URL ;
		// fwrite($h, $url . PHP_EOL);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		$response = curl_exec($ch);
		// fwrite($h, "2 - " .  $response. PHP_EOL);

		curl_close($ch);
		if($response != 'OK') {
			return;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,  FASTCACHE_HOST_LICENSE_SERVER_URL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
			'action' => $action,
			'site_url' => get_site_url(),
			'home_url'=> get_home_url(),
			'plugin_version'=> FASTCACHE_VERSION,
			'plugin_name'=> FASTCACHEHOST_HOST_PLUGINNAME,
			'plugin_slug'=> FASTCACHEHOST_HOST_PLUGINNAME,
			'plugin_type'=> 'wordpress',
			'admin_url'=> get_admin_url(),
			'admin_email'=> get_option('admin_email')
		]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

	}
}
