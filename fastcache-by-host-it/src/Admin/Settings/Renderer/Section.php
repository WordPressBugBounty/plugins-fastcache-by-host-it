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
namespace FastCache\Admin\Settings\Renderer;

use FastCache\Admin\Settings\TabContent;
use FastCache\Platform\Plugin;

class Section {
	private static function new() {
		return <<<HTML
		<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">New!</span>
HTML;
	}
	/**
	 * AUTO CONFIGURATION SECTION
	 */
	public static function autoconfigurationSection() {
		echo TabContent::addTab ( 'autoconfiguration-tab', true );
		
		$title = __ ( 'Auto configuration', 'fastcache' );
		
		echo TabContent::addSection ( $title );
	}
	
	/**
	 * ASSETS INCLUSIONS
	 */
	public static function javascriptAutomaticSettingsSection() {
		echo TabContent::addTab ( 'assets-inclusions' );
		
		$title = __ ( 'Include assets', 'fastcache' );
		$description = __ ( 'Choose assets to include in the optimization process.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * ASSETS EXCLUSIONS
	 */
	public static function excludeCssSection() {
		echo TabContent::addTab ( 'assets-exclusions' );

		$title = __ ( 'Exclude CSS', 'fastcache' );
		$description = __ ( 'Exclude CSS files to preserve the original ones and solve conflicts. It\'s possible to select a file name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache' );

		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * CSS
	 */
	public static function removeCssSection() {
		echo TabContent::addTab ( 'remove-tab' );
		
		$title = __ ( 'Remove CSS Files', 'fastcache' );
		$description = __ ( 'You can remove and prevent CSS files from loading on the page if they\'re optional or not being used at all to speed up page load and rendering. Pay attention that removing required files could break your pages.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * JAVASCRIPT
	 */
	public static function dontMoveSection() {
		$title = __ ( 'Keep Original Position', 'fastcache' );
		$description = __ ( 'Keep the original position for Javascript files and scripts.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * PAGE CACHE
	 */
	public static function pageCacheSection() {
		echo TabContent::addTab ( 'page-cache-tab' );
		
		$title = __ ( 'Page Cache', 'fastcache' );
		$description = __ ( 'The HTML source code of pages can be fully cached to significantly speed up the page load. Keep the page cache disabled or clear it while configuring the plugin or making changes to the site.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * COMBINE IMAGES
	 */
	public static function spriteGeneratorSection() {
		echo TabContent::addTab ( 'media-tab' );
		
		$title = __ ( 'Combine settings', 'fastcache' );
		$description = __ ( 'Images loaded through CSS background styles can be combined into one single image.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * LAZY LOAD
	 */
	public static function lazyLoadSection() {
		echo TabContent::addTab ( 'lazy-load-tab' );
		
		$title = __ ( 'Lazy-Load Images And Iframes', 'fastcache');
		$description = __ ( 'The lazy-load reduces the loading time of the page by displaying elements when the user scrolls down the page.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * HTTP/2
 	 */
	public static function http2PushSection() {
		echo TabContent::addTab ( 'http2-tab' );
		
		$title = __ ( 'HTTP/2 Settings', 'fastcache' );
		$description = __ ( 'HTTP/2 Server Push allows an HTTP/2-compliant server to send resources to a HTTP/2-compliant client before the client requests them.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * OPTIMIZE IMAGES
	 */
	public static function addImageAttributesSection() {
		echo TabContent::addTab ( 'optimize-image-tab' );
		
		$title = __ ( 'Optimize Heavy Images', 'fastcache' );
		$description = __ ( 'The images optimization allows to reduce the size of large images that can be converted, rescaled and resized on the fly.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * FAST CACHE CDN
	 */
	public static function addFastCacheCDNSectionEnable() {
		echo TabContent::addTab ( 'fastcache-cdn-tab' );
		
		$title = __ ( 'Enable Fastcache', 'fastcache' );
		$description = __ ( 'Enable the Fastcache CDN with your access token.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	/**
	 * DIAGNOSTIC TAB
	 */
	public static function diagnosticSection() {
		echo TabContent::addTab ( 'diagnostic-tab' );
		
		$title = __ ( 'Diagnostic', 'fastcache' );
		$description = __ ( 'This option detects if there are other caching or optimization plugins installed and active that could cause conflicts with FastCache. In such case, take care to disable them', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	/**
	 * SPEEDTEST TAB
	 */
	public static function speedtestSection() {
		echo TabContent::addTab ( 'speedtest-tab' );
		
		$title = __ ( 'Speed Test', 'fastcache' );
		$description = __ ( 'Execute the page speed test for your website to evaluate your Google PageSpeed score', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	public static function addFastCacheCDNTTL() {
		$params = Plugin::getPluginParams ();
		
		if($params->get('fastcache-enable', 0)) {
			$title = __ ( 'TTL settings', 'fastcache' );
			$description = __ ( 'Choose TTL settings.', 'fastcache' );
			
			echo TabContent::addSection ( $title, $description );
		}
	}
	public static function addFastCacheExclusion() {
		$params = Plugin::getPluginParams ();
		
		if($params->get('fastcache-enable', 0)) {
			$title = __ ( 'Exclusions', 'fastcache' );
			$description = __ ( 'Add exclusions for the CDN cache.', 'fastcache' );
			
			echo TabContent::addSection ( $title, $description );
		}
	}
	public static function addFastCacheLogDebug() {
		$params = Plugin::getPluginParams ();
		
		if($params->get('fastcache-enable', 0)) {
			$title = __ ( 'Debug', 'fastcache' );
			$description = __ ( 'Enable Log Debug.', 'fastcache' );
			
			echo TabContent::addSection ( $title, $description );
		}
	}
	public static function addFastCacheFaqs() {
		$params = Plugin::getPluginParams ();
		
		if($params->get('fastcache-enable', 0)) {
			$title = __ ( 'FAQs', 'fastcache' );
			$description = __ ( 'FAQs section.', 'fastcache' );
			
			echo TabContent::addSection ( $title, $description );
		}
	}
	/**
	 * CDN generic
	 */
	public static function cdnSection() {
		$title = __ ( 'Assets CDN', 'fastcache' );
		$description = __ ( 'Enter CDN domains to have the plugin load all static files from these external domains.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * ADVANCED
	 */
	public static function optimizeCssDeliverySection() {
		echo TabContent::addTab ( 'miscellaneous-tab' );
		
		$title = __ ( 'Extract Basic CSS Styles', 'fastcache' );
		$description = __ ( 'Extract basic CSS styles required to format the page above the fold and put this in a &lt;style&gt; element inside the &lt;head&gt; section of the HTML to prevent \'render-blocking\'.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function reduceDomSection() {
		$title = __ ( 'Reduce DOM Tree', 'fastcache' );
		$description = __ ( 'HTML5 DOM elements exceeding the limit of 600 below the fold will be removed and loaded asynchronously using Javascript after that the page has been fully rendered.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function minifyAssets() {
		$title = __ ( 'Minify Assets', 'fastcache' );
		$description = __ ( 'CSS, Javascript and HTML code can be minified and optimized.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function excludeMenuItemsSection() {
		$title = __ ( 'Exclude Urls', 'fastcache' );
		$description = __ ( 'Disable the plugin optimization for specific urls.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function combineCssJsSection() {
		echo TabContent::addTab ( 'general-tab' );
		
		$title = __ ( 'Combine Assets', 'fastcache' );
		$description = __ ( 'Manage the combining of CSS and Javascript files.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function combineCssJsAutoSection() {
		$title = __ ( 'Optimization Settings', 'fastcache' );
		$description = __ ( 'Manage optimizations settings such as cache, position of scripts and fonts loading.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function excludePeoSection() {
		$title = __ ( 'Exclude JS With Order', 'fastcache' );
		$description = __ ( 'If you experience conflicts, choose Javascript files to exclude from the combine functionality preserving their execution order as they appear on the page.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function excludeIeoSection() {
		$title = __ ( 'Exclude JS Without Order', 'fastcache' );
		$description = __ ( 'If you experience conflicts, choose Javascript files to exclude from the combine functionality without preserving their execution order as they appear on the page.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function removeJsSection() {
		$title = __ ( 'Remove Javascript Files', 'fastcache' );
		$description = __ ( 'You can remove and prevent Javascript files from loading on the page if they\'re optional or not being used at all to speed up page load and rendering. Pay attention that removing required files could break your pages.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	public static function addCustomJsSection() {
		$title = __ ( 'Add custom Javascript', 'fastcache' );
		$description = __ ( 'You can add custom Javascript code that will be included within the compiled file.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	public static function addCustomCssSection() {
		$title = __ ( 'Add custom CSS', 'fastcache' );
		$description = __ ( 'You can add custom CSS code that will be included within the compiled file.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	public static function autoApiSection() {
		$title = __ ( 'Optimize Images By Pages' );
		$description = __ ( 'The plugin will scan the pages of your website for you to find the images to optimize. (Currently only the Main Menu). You don\'t need to select them beforehand.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function manualApiSection() {
		$title = __ ( 'Optimize Images By Folders' );
		$description = __ ( 'Use the file tree to select the subfolders and files you want to optimize. Files will be optimized in subfolders recursively. If you want to rescale your images while optimizing, enter the new width and height in the respective columns beside each image on the right hand side.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function advancedSettings() {
		$title = __ ( 'Advanced Settings', 'fastcache' );
		$description = __ ( 'Manage advanced settings for the plugin', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function addImageSrcsetSection() {
		$title = __ ( 'Srcset management', 'fastcache' );
		$description = __ ( 'This settings make it possible to automatically generate a srcset starting from the original image. When this feature is enabled, it will replace the native Wordpress \'srcset\'.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function instantPageSection() {
		$title = __ ( 'Instant Page', 'fastcache' );
		$description = __ ( 'Enable the Instant Page preloading before a page is opened.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	public static function lazyLoadHtmlSection() {
		$title = __ ( 'Lazy-Load HTML tags', 'fastcache' );
		$description = __ ( 'Enable the lazy-load for user-defined HTML tags and parts of a page. They will be loaded when the user scrolls down the page or after a delay.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	/**
	 * TRANSFER CONFIGURATION
	 */
	public static function transferConfigSection() {
		echo TabContent::addTab ( 'transfer-config-tab' );
		
		$title = __ ( 'Transfer Configuration', 'fastcache' );
		$description = __ ( 'Download or upload the plugin configuration.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	/**
	 * OBJECT CACHE CONFIGURATION
	 */
	public static function objectCacheGeneralSection() {
		echo TabContent::addTab ( 'fastcache-objectcache-tab' );
		
		$title = __ ( 'Object Cache', 'fastcache' );
		$description = __ ( 'Manage object cache for DB queries and PHP objects.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	public static function objectCacheRedisSection() {
		$title = __ ( 'Redis configuration', 'fastcache' );
		$description = __ ( 'Manage Redis object cache configuration.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	public static function objectCacheMemcachedSection() {
		$title = __ ( 'MemCached configuration', 'fastcache' );
		$description = __ ( 'Manage MemCached object cache configuration.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}
	
	public static function objectCacheExclusionsSection() {
		$title = __ ( 'Exclusions configuration', 'fastcache' );
		$description = __ ( 'Exclude resources from object caching.', 'fastcache' );
		
		echo TabContent::addSection ( $title, $description );
	}

	public static function objectCacheDebuggingSection() {
		$title = __ ( 'Debugging', 'fastcache' );
		$description = __ ( 'Enable logging and statistics to monitor the object cache activity.', 'fastcache' );

		echo TabContent::addSection ( $title, $description );
	}
}
