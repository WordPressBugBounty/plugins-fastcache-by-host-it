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

namespace FastCache\Admin\Controller;

use FastCacheFramework\Container\Container;
use FastCacheFramework\Mvc\Controller;
use FastCache\Dispatcher;
use FastCache\Core\Admin\Tasks;
use FastCache\Platform\Cache;
use FastCache\Platform\Plugin;
use FastCache\Core\Admin\InOutConfig;

// Ensure InOutConfig is loaded
require_once dirname( dirname( __DIR__ ) ) . '/Core/Admin/InOutConfig.php';

class Utility extends Controller
{
	public function __construct( ?Container $container = null )
	{
		parent::__construct( $container );
	}

	public function browsercaching()
	{
		if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'browsercaching')) {
			wp_die(__('Not authorized'));
		}
		
		$expires = Tasks::leverageBrowserCaching();

		if ( $expires === false )
		{
			$this->setMessage( __( 'Failed trying to add browser caching codes to the .htaccess file', 'fastcache' ), 'error' );
		}
		elseif ( $expires === 'FILEDOESNTEXIST' )
		{
			$this->setMessage( __( 'No .htaccess file were found in the root of this site', 'fastcache' ), 'warning' );
		}
		elseif ( $expires === 'CODEUPDATEDSUCCESS' )
		{
			$this->setMessage( __( 'The .htaccess file was updated successfully', 'fastcache' ), 'success' );
		}
		elseif ( $expires === 'CODEUPDATEDFAIL' )
		{
			$this->setMessage( __( 'Failed to update the .htaccess file', 'fastcache' ), 'warning' );
		}
		elseif ( $expires === 'CODEALREADYINFILE' )
		{
			$this->setMessage( __( 'Optimizations already added to the .htaccess', 'fastcache' ), 'warning' );
		}
		else
		{
			$this->setMessage( __( 'Optimizations for the htaccess file have been successfully added, browser caching is now enabled for all assets', 'fastcache' ), 'success' );
		}

		$this->setRedirect( 'options-general.php?page=fastcache' );

		$this->redirect();
	}

	public function restorehtaccess()
	{
		if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'restorehtaccess')) {
			wp_die(__('Not authorized'));
		}
		
		$cleanedHtaccess = Tasks::cleanHtaccess();
		
		if ( $cleanedHtaccess === false )
		{
			$this->setMessage( __( 'Failed trying to restore the original .htaccess file', 'fastcache' ), 'error' );
		}
		else
		{
			$this->setMessage( __( 'Optimizations for the htaccess file have been successfully removed, browser caching is now disabled for all assets', 'fastcache' ), 'success' );
		}
		
		$this->setRedirect( 'options-general.php?page=fastcache' );
		
		$this->redirect();
	}
	
	public function orderplugins()
	{
		if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'orderplugins')) {
			wp_die(__('Not authorized'));
		}
		
		Dispatcher::orderPlugin();

		$this->setMessage( __( 'Plugins ordered successfully', 'fastcache' ), 'success' );
		$this->setRedirect( 'options-general.php?page=fastcache' );

		$this->redirect();
	}

	public function keycache()
	{
		if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'keycache')) {
			wp_die(__('Not authorized'));
		}
		
		Tasks::generateNewCacheKey();

		$this->setMessage( __( 'New cache key generated!', 'fastcache' ), 'success' );
		$this->setRedirect( 'options-general.php?page=fastcache' );

		$this->redirect();
	}

	public function cleancache()
	{
		if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'cleancache')) {
			wp_die(__('Not authorized'));
		}
		
		if ( Cache::deleteCache() )
		{
			$this->setMessage( __( 'Cache deleted successfully!', 'fastcache' ), 'success' );
			if(Plugin::getPluginParams()->get('clear_server_cache', 0)) {
				$this->setMessage( __( 'The cache has been deleted and server cache has been successfully purged!', 'fastcache' ), 'success' );
			}
		}
		else
		{
			$this->setMessage( __( 'Error cleaning cache!', 'fastcache' ), 'error' );
		}

		if ( ( $return = $this->input->get( 'return', '' ) ) != '' )
		{
			$this->setRedirect( base64_decode_url( $return ) );
		}
		else
		{
			$this->setRedirect( 'options-general.php?page=fastcache' );
		}

		$this->redirect();
	}

	public function downloadconfig()
	{
		if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'downloadconfig')) {
			wp_die(__('Not authorized'));
		}

		InOutConfig::export();
	}

	public function uploadconfig()
	{
		if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'uploadconfig')) {
			wp_die(__('Not authorized'));
		}

		if (isset($_FILES['fc_config_file']) && $_FILES['fc_config_file']['error'] === UPLOAD_ERR_OK) {
			$jsonContent = file_get_contents($_FILES['fc_config_file']['tmp_name']);
			if (InOutConfig::import($jsonContent)) {
				$this->setMessage(__( 'Configuration imported successfully!', 'fastcache' ), 'success');
			} else {
				$this->setMessage(__( 'Error importing configuration. Please ensure it\'s a valid JSON file.', 'fastcache' ), 'error');
			}
		} else {
			$this->setMessage(__( 'No file uploaded or upload error.', 'fastcache' ), 'error');
		}

		$this->setRedirect('options-general.php?page=fastcache#transfer-config-tab');
		$this->redirect();
	}
}