<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://fastcache.host.it/
 * @since             1.0.0
 * @package           FastCache
 *
 * @wordpress-plugin
 * Plugin Name:       FastCache by host.it
 * Plugin URI:        https://fastcache.host.it/wordpress/
 * Description:       Abilita il tuo sito Wordpress alla prima vera CDN realizzata PER Wordpress e configurata AD-HOC per il tuo sito. Il massimo della velocità senza difficoltà di setup.
 * Version:           1.7.0
 * Author:            Host.it
 * Author URI:        https://fastcache.host.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fastcache
 * Domain Path:       /languages
 * Tags:              cache , performance, speed, seo, google, cdn, varnish
 */

use FastCache\Platform\Plugin;
use FastCache\Platform\Utility;
use FastCache\Dispatcher;

// Requires PHP >= 7.2.0
if (! (PHP_VERSION_ID >= 70200)) {
	if (is_admin ()) {
		set_transient ( 'fastcache_notices', 'FastCache requires a PHP version ">= 7.2.0". You are running ' . PHP_VERSION . '.', 60 * 5 );
		if ($messages = get_transient ( 'fastcache_notices' )) {
			echo $messages;
		}
		delete_transient ( 'fastcache_notices' );
	}
	return;
}

define ( '_WP_EXEC', '1' );
define ( '_FASTCACHE_EXEC', 1 );
define ( 'FASTCACHE_VERSION', '1.7.0' );
define ( 'FASTCACHE_FILE_PATH', __FILE__ );

define ( 'FASTCACHE_URL', plugin_dir_url ( FASTCACHE_FILE_PATH ) );
define ( 'FASTCACHE_DIR', plugin_dir_path ( FASTCACHE_FILE_PATH ) );
define ( 'FASTCACHE_CACHE_DIR', WP_CONTENT_DIR . '/cache/fastcache/' );
define ( 'FASTCACHE_DEFAULTTTL' , 300);
define ( 'HOST_PLUGIN_DIR', plugin_dir_path ( __FILE__ ) );
define ( 'FASTCACHEHOST_HOST_PLUGINNAME', 'fastcache' );
define ( 'FASTCACHEHOST_HOST_PLUGINNAME_METHODS', 'fastcache_host' );
define ( 'FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS', FASTCACHEHOST_HOST_PLUGINNAME . '_settings' );
define ( 'FASTCACHEHOST_HOST_LICENCE_SERVER', 'https://lic.host.it' );
define ( 'FASTCACHEHOST_HOST_LICENCE_SERVER_PATH', '/v1/license/hook' );
define ( 'FASTCACHE_HOST_LICENSE_SERVER_URL', FASTCACHEHOST_HOST_LICENCE_SERVER . FASTCACHEHOST_HOST_LICENCE_SERVER_PATH );



require_once (FASTCACHE_DIR . '/vendor/autoload.php');
require_once (FASTCACHE_DIR . 'src/Dispatcher.php');

function mainFastCachePluginActivation() {
	Plugin::activate();
}
function mainFastCachePluginUpdate() {
	Plugin::update();
}
function mainFastCachePluginDeActivation() {
	Plugin::deactivate();
}
function mainFastCachePluginUninstall() {
	Plugin::uninstall();
	Dispatcher::runUninstallRoutines();
}
register_activation_hook(__FILE__, 'mainFastCachePluginActivation');
register_deactivation_hook(__FILE__, 'mainFastCachePluginDeActivation');
register_uninstall_hook(__FILE__, 'mainFastCachePluginUninstall');
add_action('upgrader_process_complete', function($upgrader, $options) {
	if ($options['action'] !== 'update' || $options['type'] !== 'plugin') {
		return;
	}

	$target_plugin = plugin_basename(__FILE__);
	$updated_plugins = [];

	if (isset($options['plugins']) && is_array($options['plugins'])) {
		$updated_plugins = $options['plugins'];
	} elseif (isset($options['plugin'])) {
		$updated_plugins = [ $options['plugin'] ];
	}

	if (in_array($target_plugin, $updated_plugins)) {
		mainFastCachePluginUpdate();
	}
}, 10, 2);

/**
 * Handle background automatic updates
 */
add_action('automatic_updates_complete', function($update_results) {
	$target_plugin = plugin_basename(__FILE__);

	if (!isset($update_results['plugin']) || !is_array($update_results['plugin'])) {
		return;
	}

	foreach ($update_results['plugin'] as $update_result) {
		// $update_result is often an object containing the result and item details
		$plugin_item = isset($update_result->item) ? $update_result->item : null;
		$plugin_path = '';

		if (is_object($plugin_item) && isset($plugin_item->plugin)) {
			$plugin_path = $plugin_item->plugin;
		} elseif (is_object($update_result) && isset($update_result->plugin)) {
			$plugin_path = $update_result->plugin;
		}

		if ($plugin_path === $target_plugin && $update_result->result === true) {
			mainFastCachePluginUpdate();
			break;
		}
	}
}, 10, 1);
/**
 * Initialize and run plugin
 */
FastCache\Dispatcher::init ();
