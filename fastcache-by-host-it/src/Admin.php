<?php
namespace FastCache;

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

use FastCacheFramework\Application\Application;
use FastCacheFramework\Container\Container;
use FastCacheFramework\Uri\Uri;
use FastCache\Core\Admin\Ajax\Ajax;
use FastCache\Admin\Settings\Html;
use FastCache\Admin\Settings\TabSettings;
use FastCache\Platform\Plugin;
use FastCache\Platform\Utility;
use FastCache\Core\FileRetriever;
use FastCache\Core\Admin\InOutConfig;

abstract class Admin
{
	
	public static function addAdminMenu()
	{
		$hook_suffix = add_options_page( __( 'FastCache Settings', 'fastcache' ), 'FastCache Settings', 'manage_options', 'fastcache', [
				__CLASS__,
				'loadAdminPage'
		] );
		
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'loadResourceFiles' ] );
		add_action( 'admin_head-' . $hook_suffix, [ __CLASS__, 'addScriptsToHead' ] );
		add_action( 'load-' . $hook_suffix, [ __CLASS__, 'initializeSettings' ] );
		
		add_action( 'admin_bar_menu', [ __CLASS__, 'addMenuToAdminBar' ], 100 );
		add_action( 'admin_init', [ __CLASS__, 'checkMessages' ] );
		add_action( 'admin_init', [ __CLASS__, 'handleEarlyTasks' ] );
		
		add_action ( 'admin_action_update',  [ __CLASS__, 'updateSettings' ]);
		
		add_filter( 'plugin_action_links_' . plugin_basename(FASTCACHE_FILE_PATH), [ static::class, 'add_action_link' ], 10, 2 );
	}
	
	public static function loadAdminPage()
	{
		try
		{
			$oContainer   = new Container();
			$oApplication = Application::getInstance( 'FastCacheApplication', $oContainer );
			$oApplication->initialise();
			$oApplication->route();
			$oApplication->dispatch();
			$oApplication->render();
		}
		catch ( \Exception $e )
		{
			$class = get_class( $e );
			echo <<<HTML
<h1>Error initializing the FastCache plugin</h1>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">  {$class} &mdash;  {$e->getMessage()}  </div>
<pre class="bg-gray-100 p-4 rounded border border-gray-200 overflow-x-auto"> {$e->getTraceAsString()} </pre>
HTML;
		}
	}
	
	public static function registerOptions()
	{
		register_setting( 'fastcacheOptionsPage', FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, [ 'type' => 'array' ] );
		
		// Controlla se esistono i vecchi settings
		$old_settings = get_option('fastcache-host-settings');
		
		// Se esistono vecchi settings e non esistono ancora i nuovi
		if ($old_settings !== false && get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS) === false) {
			
			// Copia i settings nel nuovo formato
			update_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, $old_settings);
			
			// Elimina i vecchi settings
			delete_option('fastcache-host-settings');
		}
	}
	
	/**
	 * Adds links to Premium Support and FAQ under the plugin in the plugin overview page.
	 *
	 * @param array  $links Array of links for the plugins, adapted when the current plugin is found.
	 * @param string $file  The filename for the current plugin, which the filter loops through.
	 *
	 * @return array
	 */
	public static function add_action_link( $links, $file ) {
		// Add link to docs.
		$faq_link = '<a href="http://assistenza.host.it/area-tecnica/fastcache/pluginwordpress" target="_blank">' . __( 'FAQ', 'fastcache' ) . '</a>';
		array_push( $links, $faq_link );
		
		return $links;
	}
	
	public static function loadResourceFiles( $hook )
	{
		if ( 'settings_page_fastcache' != $hook )
		{
			return;
		}
		
		wp_enqueue_style( 'fastcache-bootstrap-css' );
		wp_enqueue_style( 'fastcache-verticaltabs-css' );
		wp_enqueue_style( 'fastcache-admin-css' );
		wp_enqueue_style( 'fastcache-fonts-css' );
		wp_enqueue_style( 'fastcache-select2-css' );
		wp_enqueue_style( 'fastcache-wordpress-css' );
		wp_enqueue_style( 'fastcache-fastcache-css' );
		wp_enqueue_style( 'fastcache-tailwind-css' );

		wp_enqueue_script( 'fastcache-bootstrap-js' );
		wp_enqueue_script( 'fastcache-adminutilities-js' );

		wp_enqueue_script( 'fastcache-adminutility-js' );
		wp_enqueue_script( 'fastcache-multiselect-js' );
		wp_enqueue_script( 'fastcache-fastcache-js' );
		
		wp_enqueue_script( 'fastcache-select2-js' );
		wp_enqueue_script( 'fastcache-collapsible-js' );
		wp_enqueue_script( 'fastcache-tabsstate-js' );
	}
	
	public static function initializeSettings()
	{
		try
		{
			$oContainer   = new Container();
			$oApplication = Application::getInstance( 'FastCacheApplication', $oContainer );
		}
		catch ( \Exception $e )
		{
			// Exceptions will be caught again in loadAdminPage
		}

		//Css files
		wp_register_style( 'fastcache-bootstrap-css', FASTCACHE_URL . 'media/bootstrap/css/bootstrap.css', [], FASTCACHE_VERSION );
		
		wp_register_style( 'fastcache-fonts-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' );
		wp_register_style( 'fastcache-select2-css', FASTCACHE_URL . 'media/select2/select2.min.css', [], FASTCACHE_VERSION );
		wp_register_style( 'fastcache-wordpress-css', FASTCACHE_URL . 'media/css/admin.css', [], FASTCACHE_VERSION );
		wp_register_style( 'fastcache-admin-css', FASTCACHE_URL . 'media/css/admin.css', [], FASTCACHE_VERSION );
		wp_register_style( 'fastcache-verticaltabs-css', FASTCACHE_URL . 'media/css/admin.css', [], FASTCACHE_VERSION );
		wp_register_style( 'fastcache-fastcache-css', FASTCACHE_URL . 'media/css/fastcache-admin.css', [], FASTCACHE_VERSION );
		// tailwind 2026 4.1 
		wp_register_style( 'fastcache-tailwind-css', FASTCACHE_URL . 'media/css/fastcache-tailwind.css', [], FASTCACHE_VERSION );
		
		//Javascript files
		wp_register_script( 'fastcache-bootstrap-js', FASTCACHE_URL . 'media/bootstrap/js/bootstrap.bundle.min.js', [ 'jquery' ], FASTCACHE_VERSION, true );
		wp_register_script( 'fastcache-adminutilities-js', FASTCACHE_URL . 'media/js/adminutilities.js', [ 'jquery' ], FASTCACHE_VERSION, true );
		wp_register_script( 'fastcache-adminutility-js', FASTCACHE_URL . 'media/core/js/admin-utility.js', [ 'jquery' ], FASTCACHE_VERSION, true );
		wp_register_script( 'fastcache-multiselect-js', FASTCACHE_URL . 'media/core/js/multiselect.js', [
				'jquery',
				'fastcache-adminutility-js',
				'fastcache-adminutilities-js'
		], FASTCACHE_VERSION, true );
		wp_register_script( 'fastcache-select2-js', FASTCACHE_URL . 'media/select2/select2.min.js', [ 'jquery' ], FASTCACHE_VERSION, true );
		wp_register_script( 'fastcache-collapsible-js', FASTCACHE_URL . 'media/js/adminutilities.js', [ 'jquery' ], FASTCACHE_VERSION, true );
		wp_register_script( 'fastcache-tabsstate-js', FASTCACHE_URL . 'media/js/tabs-state.js', [ 'jquery' ], FASTCACHE_VERSION, true );
		
		$aSettingsArray = TabSettings::getSettingsArray();
		
		foreach ( $aSettingsArray as $section => $aSettings )
		{
			add_settings_section( 'fastcache_' . $section . '_section', '', [
					'\\FastCache\\Admin\\Settings\\Renderer\\Section',
					$section
			], 'fastcacheOptionsPage' );
			
			/**
			 * $aArgs = [
			 *        0 => title,
			 *        1 => description,
			 *        2 => new
			 * ]
			 */
			foreach ( $aSettings as $setting => $aArgs )
			{
				list( $title, $description, $new ) = array_pad( $aArgs, 3, 0 );
				
				$id    = 'fastcache_' . $setting;
				$title = Html::description( $title, $description, $new );
				$args  = [];
				
				$aClasses = self::getSettingsClassMap();
				
				if ( isset( $aClasses[$setting] ) )
				{
					$args['class'] = $aClasses[$setting];
				}
				
				add_settings_field( $id, $title, [
						'\\FastCache\\Admin\\Settings\\Renderer\\Setting',
						$setting
				], 'fastcacheOptionsPage', 'fastcache_' . $section . '_section', $args );
			}
		}
	}
	
	public static function addScriptsToHead()
	{
		
		echo <<<HTML
		<script type="text/javascript">
		(function($){
			$(document).ready(function () {
					$(".select2-ctrl").select2({
						tags: true,
						tokenSeparators: [',']
					})

						$('.hasPopover').popover({
								container: 'body',
								placement: 'bottom',
								trigger: 'hover focus',
								html: true
					})
				});
			})(jQuery);
		 </script>
		 
HTML;
	}
	
	private static function getSettingsClassMap()
	{
		return [
				'pro_http2_file_types'       => 'fastcache-wp-checkboxes-grid-wrapper columns-4',
				'staticfiles'                => 'fastcache-wp-checkboxes-grid-wrapper columns-5',
				'pro_staticfiles_2'          => 'fastcache-wp-checkboxes-grid-wrapper columns-5',
				'pro_staticfiles_3'          => 'fastcache-wp-checkboxes-grid-wrapper columns-5',
				'pro_html_sections'          => 'fastcache-wp-checkboxes-grid-wrapper columns-5 width-400',
				'object_cache_logging_types' => 'fastcache-wp-checkboxes-grid-wrapper columns-4',
		];
	}
	
	public static function addMenuToAdminBar( $admin_bar )
	{
		if ( ! current_user_can( 'manage_options' ) )
		{
			return;
		}
// ... (rest of the changes for this method)

		$params = Plugin::getPluginParams();

		// Parent "FastCache" menu
		$admin_bar->add_node( [
			'id'    => 'fastcache-main',
			'title' => __( '<span class="ab-icon dashicons-performance"></span>FastCache', 'fastcache' ),
			'href'  => '#'
		] );
		
		$aArgs = [
				'id'     => FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS,
				'parent' => 'fastcache-main',
				'title'  => __( 'Settings', 'fastcache' ),
				'href'   => add_query_arg( [
						'page' => 'fastcache',
				], admin_url( 'options-general.php' ) )
		];
		
		$admin_bar->add_node( $aArgs );
		
		if ($params->get('htaccess_cache_enable', 1) == 1)
		{
			$aArgs = [
					'id'     => 'fastcache-clear-cache',
					'parent' => 'fastcache-main',
					'title'  => __( 'Local Cache Purge All', 'fastcache' ),
					'href'   => add_query_arg( [
							'page' => 'fastcache',
							'task' => 'cleancache',
							'_wpnonce' => wp_create_nonce('cleancache')
					], admin_url( 'options-general.php' ) )
			];
			
			$admin_bar->add_node( $aArgs );
		}
	}
	
	public static function getCurrentAdminUri()
	{
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );
		
		if ( ! $uri )
		{
			return '';
		}
		
		return $uri;
	}

	public static function updateSettings()
	{
		$postedSettings = $_POST[FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS];
		
		// Manage settings for activation
		Utility::htaccessCacheManagement($postedSettings);
	}
	
	public static function handleEarlyTasks()
	{
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		$task = isset($_GET['task']) ? $_GET['task'] : '';
		
		if ($page === 'fastcache' && $task === 'downloadconfig') {
			if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'downloadconfig')) {
				wp_die(__('Not authorized'));
			}
			InOutConfig::export();
		}
	}
	
	public static function checkMessages()
	{
		if ( get_transient( 'fastcache_notices' ) )
		{
			add_action( 'admin_notices', [ __CLASS__, 'publishAdminNotices' ] );
		}
	}
	
	public static function publishAdminNotices()
	{
		$oContainer = new Container();
		
		try
		{
			/** @var \FastCache\Admin\Application $oApplication */
			$oApplication = Application::getInstance( 'FastCacheApplication', $oContainer );
			$oApplication->publishMessages( $oApplication->getMessageQueue() );
		}
		catch ( FastCacheFramework\Exception\App $e )
		{
		}
	}
	
	public static function loadActionLinks( $links, $file )
	{
		static $this_plugin;
		
		if ( ! $this_plugin )
		{
			$this_plugin = plugin_basename( FASTCACHE_FILE_PATH );
		}
		
		if ( $file == $this_plugin )
		{
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=fastcache' ) . '">' . __( 'Settings' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		
		return $links;
	}
	
	public static function doAjaxMultiSelect()
	{
		echo Ajax::getInstance( 'MultiSelect' )->run();
		die();
	}
	
	public static function executePageSpeedTest()
	{
		$params = Plugin::getPluginParams ();
		$container = new Container();
		$application = Application::getInstance( 'FastCacheApplication', $container );
		
		// Ensure that at least a language is available for the backend and locale sent to Google otherwise the API fails
		$langTag = get_bloginfo ( 'language' );
		if($langTag) {
			$explodedLangTag = explode ( '-', $langTag );
			$locale = array_shift ( $explodedLangTag );
		} else {
			$locale = 'en';
		}
		
		$response = new \stdClass();
		
		// Build the purified domain to scrape using the host only
		$linkUrl = trim($params->get('pagespeedtest_domain_url', get_site_url()));
		if(!$linkUrl) {
			$linkUrl = get_site_url();
		}
		$hostDomain = rawurlencode ( $linkUrl );
		$customApiKey = trim($params->get ( 'google_pagespeed_api_key', ''));
		$apiKey = $customApiKey ? $customApiKey : 'AIzaSyDBN6utYmIBNQ2IVlLcY7-42S9GuKHBIfQ';
		
		$urlDesktop = "https://content.googleapis.com/pagespeedonline/v5/runPagespeed?url=$hostDomain&key=$apiKey&strategy=desktop&category=performance&locale=" . $locale;
		$urlMobile = "https://content.googleapis.com/pagespeedonline/v5/runPagespeed?url=$hostDomain&key=$apiKey&strategy=mobile&category=performance&locale=" . $locale;
		
		try {
			// Fetch remote data to scrape
			$httpResponseDesktop = wp_remote_get( $urlDesktop, array('timeout'=>120));
			$httpResponseMobile = wp_remote_get ( $urlMobile, array('timeout'=>120));
			
			// Check if HTTP status code is 200 OK
			if (!$httpResponseDesktop instanceof \WP_Error) {
				$decodedApiResponse = json_decode($httpResponseDesktop['body'], true);
				if(is_array($decodedApiResponse)) {
					// Calculate the score category, range, colors for labels and sliders
					$response->pagespeedDesktop = isset($decodedApiResponse['lighthouseResult']['categories']['performance']) ? (int)($decodedApiResponse['lighthouseResult']['categories']['performance']['score'] * 100) : -1;
					
					// Throw exception on failure results
					if($response->pagespeedDesktop == -1) {
						throw new \Exception();
					}
					
					$response->fcpDesktop = number_format($decodedApiResponse['lighthouseResult']['audits']['first-contentful-paint']['numericValue'] / 1000, 1);
					$response->siDesktop = number_format($decodedApiResponse['lighthouseResult']['audits']['speed-index']['numericValue'] / 1000, 1);
					$response->lcpDesktop = number_format($decodedApiResponse['lighthouseResult']['audits']['largest-contentful-paint']['numericValue'] / 1000, 1);
					
					$response->interactiveDesktop = number_format($decodedApiResponse['lighthouseResult']['audits']['interactive']['numericValue'] / 1000, 1);
					$response->tbtDesktop = intval($decodedApiResponse['lighthouseResult']['audits']['total-blocking-time']['numericValue']);
					$response->clsDesktop = number_format($decodedApiResponse['lighthouseResult']['audits']['cumulative-layout-shift']['numericValue'], 3);
					
					$response->screenShotDesktop = $decodedApiResponse['lighthouseResult']['audits']['final-screenshot']['details']['data'];
				}
			}
			// Check if HTTP status code is 200 OK
			if (!$httpResponseMobile instanceof \WP_Error) {
				$decodedApiResponse = json_decode($httpResponseMobile['body'], true);
				if(is_array($decodedApiResponse)) {
					// Calculate the score category, range, colors for labels and sliders
					$response->pagespeedMobile = isset($decodedApiResponse['lighthouseResult']['categories']['performance']) ? (int)($decodedApiResponse['lighthouseResult']['categories']['performance']['score'] * 100) : -1;
					
					// Throw exception on failure results
					if($response->pagespeedMobile == -1) {
						throw new \Exception();
					}
					
					$response->fcpMobile = number_format($decodedApiResponse['lighthouseResult']['audits']['first-contentful-paint']['numericValue'] / 1000, 1);
					$response->siMobile = number_format($decodedApiResponse['lighthouseResult']['audits']['speed-index']['numericValue'] / 1000, 1);
					$response->lcpMobile = number_format($decodedApiResponse['lighthouseResult']['audits']['largest-contentful-paint']['numericValue'] / 1000, 1);
					
					$response->interactiveMobile = number_format($decodedApiResponse['lighthouseResult']['audits']['interactive']['numericValue'] / 1000, 1);
					$response->tbtMobile = intval($decodedApiResponse['lighthouseResult']['audits']['total-blocking-time']['numericValue']);
					$response->clsMobile = number_format($decodedApiResponse['lighthouseResult']['audits']['cumulative-layout-shift']['numericValue'], 3);
					
					$response->screenShotMobile = $decodedApiResponse['lighthouseResult']['audits']['final-screenshot']['details']['data'];
				}
			}
			
			$response->analyzedUrl = $linkUrl;
		} catch ( \Exception $e ) {
			// Go on with the next API without blocking exception
			$response->success = false;
		}
		
		$response->success = true;
		
		echo json_encode($response);
		die();
	}
}