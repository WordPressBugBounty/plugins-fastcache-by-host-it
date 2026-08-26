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
	 *
	 * @return Settings
	 */
	public static function getPluginParams()
	{
		static $params = null;

		if ( is_null( $params ) )
		{
			$options = get_option( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
			$params  = Settings::getInstance( $options );
		}

		return $params;
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
