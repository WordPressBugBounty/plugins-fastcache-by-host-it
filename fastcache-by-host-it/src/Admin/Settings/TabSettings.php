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
namespace FastCache\Admin\Settings;

class TabSettings
{
	public static function getSettingsArray()
	{
		$aTabs = get_class_methods(__CLASS__);
		$aSettingsArray = [];

		foreach ($aTabs as $tab) {
			if ($tab == str_replace(__CLASS__ . '::', '', __METHOD__)) {
				continue;
			}

			$aSettingsArray = array_merge($aSettingsArray, self::$tab());
		}

		return $aSettingsArray;
	}
	public static function generalTab()
	{
		return [
			/**
			 * Combine CSS JS
			 */
			'combineCssJsSection' => [
				'combine_files_enable' => [
					__('Enable combining of CSS and Javascript files', 'fastcache'),
					__('If enabled, multiple CSS or Javascript files will be combined into a single file or grouped in as few files as possible. This feature allows to reduce the number of HTTP requests, the total page size and loading time.', 'fastcache')
				],
				'css' => [
					__('Combine CSS files', 'fastcache'),
					__('If enabled, multiple CSS files will be combined into a single file. The single file will replace all the original multiple files.', 'fastcache')
				],
				'javascript' => [
					__('Combine Javascript files', 'fastcache'),
					__('If enabled, multiple Javascript files will be combined into a single file. In the case that it is necessary to exclude certain files while preserving the execution order, the files will be grouped in as few files as possible. Combined files will replace all the original multiple files, unless they have been excluded from the combine functionality.', 'fastcache')
				]
			],
			/**
			 * Minify Assets
			 */
			'minifyAssets' => [
				'css_minify' => [
					__('Minify CSS', 'fastcache'),
					__('Whitespaces and comments will be removed from the combined CSS file, this helps to reduce the file size, save bandwidth and improve the loading time. NOTICE: this applies only to resulting combined CSS files and not to original files, thus if the \'combine\' feature is not enabled this setting won\'t have any effect.', 'fastcache')
				],
				'js_minify' => [
					__('Minify javascript', 'fastcache'),
					__('Whitespaces and comments will be removed from the combined JS file, this helps to reduce the file size, save bandwidth and improve the loading time. NOTICE: this applies only to resulting combined JS files and not to original files, thus if the \'combine\' feature is not enabled this setting won\'t have any effect.', 'fastcache')
				],
				'html_minify' => [
					__('Minify HTML', 'fastcache'),
					__('Whitespaces and comments will be removed from the HTML source code, this helps to save bandwidth and improve the loading time.', 'fastcache')
				],
				'html_minify_level' => [
					__('HTML Minification level', 'fastcache'),
					__('Choose the level of minification for HTML. Using the \'Low\' level, multiple whitespaces outside of elements will be reduced to a single one. Using the \'Normal\' level comments and all not needed whitespaces or carriage returns will be removed. Using the \'High\' level even quotes or double quotes from around attributes will be removed. CAUTION: using the \'High\' level could cause issues with third-party plugins; if you experience this kind of problems switch back to \'Normal\'.', 'fastcache')
				]
			],

			/**
			 * Combine Css Js Auto Settings
			 */
			'combineCssJsAutoSection' => [
				'cache_lifetime' => [
					__('Cache lifetime', 'fastcache'),
					__('You can set the cache lifetime in minutes for the combined files. If you\'re using a third-party Page Cache plugin, be sure to set this value higher than the lifetime set in the Page Cache plugin. If you experience issues with a huge amount of cache being generated, then decrease this value to reduce the cache size.', 'fastcache')
				],
				'bottom_js' => [
					__('Combined Javascript at closing body tag', 'fastcache'),
					__('If enabled, the combined Javascript file will be positioned at the bottom of the page just before the closing body tag. In the case that multiple combined Javascript files have been generated, only the last one will be placed at the bottom of the page. Additionally thanks to this option, even files positioned within the \'body\' section will be included in the combined file.', 'fastcache')
				],
				'loadAsynchronous' => [
					__('Load Javascript files asynchronously', 'fastcache'),
					__('If enabled, Javascript will be loaded asyncronously to avoid render blocking resources. If there is only one combined file placed at the bottom of the page without any excluded script it will be loaded using the \'async\' attribute. If instead there are excluded scripts and the order of execution must be preserved it will be loaded using the \'defer\' attribute.', 'fastcache')
				],
				'font_display_swap' => [
					__('Visible text during fonts load', 'fastcache'),
					__('If enabled, this function will fix the PageSpeed audit: \'Ensure text remains visible during webfont load\'. The easiest way to avoid showing invisible text while custom fonts load is to temporarily show a system font. When this option is enabled a special CSS rule \'font-display: swap\' will be included in each @font-face style, so that the flash of invisible text won\'t happen in most modern browsers. The font-display API specifies how a font is displayed, \'swap\' tells the browser that text using the font should be displayed immediately using a system font. Once the custom font is ready, it replaces the system font.', 'fastcache')
				],
				'defer_combined_styles' => [
					__('Defer combined styles', 'fastcache'),
					__('If enabled, the combined CSS file resulting from the merge of original ones will be loaded in a special \'preload\' mode to eliminate render-blocking resources and improve the First Contentful Paint (FCP).', 'fastcache')
				]
			]
		];
	}
	public static function removeTab()
	{
		return [
			/**
			 * Remove CSS
			 */
			'removeCssSection' => [
				'remove_css' => [
					__('Remove unused CSS files', 'fastcache'),
					__('Select or add the CSS files that you want to remove. Be sure that they are optional or not used on the site.', 'fastcache')
				]
			],
			/**
			 * Remove JS file
			 */
			'removeJsSection' => [
				'remove_js' => [
					__('Remove unused javascript files', 'fastcache'),
					__('Select or add the Javascript files that you want to remove. Be sure that they are optional or not used on the site.', 'fastcache')
				]
			],
			/**
			 * Add custom CSS code
			 */
			'addCustomJsSection' => [
				'add_custom_js_code' => [
					__('Add custom JS code', 'fastcache'),
					__('If enabled, it\'s possible to include custom JS code within the compiled JS file.', 'fastcache')
				],
				'custom_js_code' => [
					__('Custom JS code', 'fastcache'),
					__('Enter the custom JS code to be included within the compiled JS file.', 'fastcache')
				]
			],
			/**
			 * Add custom JS code
			 */
			'addCustomCssSection' => [
				'add_custom_css_code' => [
					__('Add custom CSS code', 'fastcache'),
					__('If enabled, it\'s possible to include custom CSS code within the compiled CSS file.', 'fastcache')
				],
				'custom_css_code' => [
					__('Custom CSS code', 'fastcache'),
					__('Enter the custom CSS code to be included within the compiled CSS file.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on Page Cache
	 *
	 * @return array
	 */
	public static function pageCacheTab()
	{
		return [
			/**
			 * Page Cache
			 */
			'pageCacheSection' => [
				'cache_enable' => [
					__('Enable PHP page caching', 'fastcache'),
					__('When the PHP page caching is enabled, pages are served up as static HTML versions of a page in order to avoid potentially time-consuming execution of your server and database. NOTE: this feature is enabled only for logged out users.', 'fastcache')
				],
				'htaccess_cache_enable' => [
					__('Enable .htaccess page caching', 'fastcache'),
					__('When the .htaccess page caching is enabled, pages are served up as static HTML versions of a page directly via the modified .htaccess completely bypassing the execution of Wordpress. This simulates having a website with static html pages and dramatically increases performance. NOTE: this feature is enabled only for logged out users and may not follow cache lifetime given that if all pages of the website are cached the PHP execution will be completely skipped. You can delete cached files using admin buttons. This cache system supports up to 10 segments in the URL path on Apache server version 2.2. If you need more segments, manually edit the .htaccess file or upgrade to Apache version 2.4', 'fastcache')
				],
				'page_cache_lifetime' => [
					__('Page cache lifetime', 'fastcache'),
					__('The lifetime of the page cache. This value must be lower than the cache lifetime of combined files under the optimization settings.', 'fastcache')
				],
				'pro_cache_platform' => [
					__('Platform specific cache', 'fastcache'),
					__('Page caching can be different for mobile and desktop devices. Enable this option if you need to serve different contents through 2 separate cache.', 'fastcache')
				],
				'cache_exclude' => [
					__('Exclude page cache by URL', 'fastcache'),
					__('Enter a substring of each url that you want to exclude from the page cache. It\'s not needed to enter the complete url for the matching, but only a part of it. Add a string and hit \'Enter\'.', 'fastcache')
				],
				'cache_cookie_exclude' => [
					__('Exclude page cache by cookie', 'fastcache'),
					__('Enter the name (or prefix) of each cookie whose presence in the browser should bypass the page cache entirely. Useful for GDPR/consent plugins (e.g. cmplz_ for Complianz) so that consent signals are forwarded correctly to ad networks. Add a string and hit \'Enter\'.', 'fastcache')
				],
				'enable_nonce_refresh' => [
					__('Enable late nonce refresh', 'fastcache'),
					__('When enabled, WordPress nonces in cached pages are replaced with placeholders and refreshed via REST API before AJAX requests. This prevents "nonce verification failed" errors when pages are cached longer than the nonce lifespan (12 hours). Works with both .htaccess static caching and PHP page caching.', 'fastcache')
				],
				'delete_all_cache' => [
					__('Delete all cache folders', 'fastcache'),
					__('If enabled, FastCache will delete every cache folder inside wp-content/cache/, including those created by other plugins. This may solve conflicts but can also cause issues with third-party caches. Disable to limit deletions only to the FastCache cache.', 'fastcache')
				]
			],
			'instantPageSection' => [
				'enable_instant_page' => [
					__('Enable Instant Page <br/>preloading', 'fastcache'),
					__('Instant Page uses just-in-time preloading. This feature, compatible with both desktop and mobile devices, preloads a page right before a user clicks on it. Pages are preloaded only when there\'s a good chance that a user will visit them, and only the HTML is preloaded, being respectful of your users and servers bandwidth and CPU. Preloading happens just a few milliseconds before than a user opens a page thanks to the special \'Prefetch\' metatag, as a result Instant Page makes you pages feel instant to the human brain even on 3G.', 'fastcache')
				],
				'instant_page_delay' => [
					__('Preload delay', 'fastcache'),
					__('By default, Instant Page preloads 65 ms fast after hovering a link and when a mobile user starts touching their display. For some sites with a lot of huge click targets, it\'s possible to increase the delay on hover to preload 150 ms slow.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on Media Tab
	 *
	 * @return array
	 */
	public static function lazyLoadTab()
	{
		return [
			/**
			 * Lazy-Load Images
			 */
			'lazyLoadSection' => [
				'lazyload_enable' => [
					__('Enable lazy-load for images and iframes', 'fastcache'),
					__('Enable the lazy-load for images and iframes when the user scrolls down to view them. This feature reduces the loading time of the page.', 'fastcache')
				],
				'lazyload_autosize' => [
					__('Autosize images', 'fastcache'),
					__('Enable this option if you experience an incorrect size of images or an empty space around them.', 'fastcache')
				],
				'pro_lazyload_effects' => [
					__('Enable effects', 'fastcache'),
					__('If enabled, a fade-in effects will be applied to lazy-loaded images when they are scrolled into view.', 'fastcache')
				],
				'pro_lazyload_iframe' => [
					__('Include iframes', 'fastcache'),
					__('If enabled, also iframes will be lazy-loaded.', 'fastcache')
				],
				'pro_lazyload_bgimages' => [
					__('Include CSS background <br/> images', 'fastcache'),
					__('If enabled, also CSS background images will be lazy-loaded in the same way of normal images.', 'fastcache')
				],
				'pro_lazyload_audiovideo' => [
					__('Include audio/video tags', 'fastcache'),
					__('If enabled, also audio/video tags will be lazy-loaded.', 'fastcache')
				],
				'disableCoreLazyload' => [
					__('Disable core lazy-load', 'fastcache'),
					__('If enabled, the WordPress core lazy-load will be disabled. This is helpful to improve the LCP by disabling the lazy-load of images displayed above the fold.', 'fastcache')
				],
				'excludeLazyLoad' => [
					__('Exclude images by URL', 'fastcache'),
					__('Choose URLs of the images that you want to exclude from being lazy-loaded. It\'s possible to select a file name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'pro_excludeLazyLoadFolders' => [
					__('Exclude images by folder', 'fastcache'),
					__('Choose folders of the images that you want to exclude from being lazy-loaded. It\'s possible to select a folder name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'pro_excludeLazyLoadClass' => [
					__('Exclude images by CSS class', 'fastcache'),
					__('Choose classes of the images that you want to exclude from being lazy-loaded. A class must be declared in the tag of an <span class="notranslate">\'&lt;img&gt;\'</span> element. It\'s possible to select a class name or attribute from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				]
			],
			/**
			 * Lazy-Load Images
			 */
			'lazyLoadHtmlSection' => [
				'lazyload_html_enable' => [
					__('Enable lazy-loading for HTML elements', 'fastcache'),
					__('Choose if enable the lazy-loading for HTML elements based on the CSS selectors parameter. This feature requires "Minify HTML" enabled in order to work.', 'fastcache')
				],
				'lazyload_html_css_selector' => [
					__('CSS selectors for lazy-loaded elements', 'fastcache'),
					__('Enter one or more CSS selectors comma separated to match certain HTML elements that should be lazy-loaded, for example using the selector \'img.myclass,div.myclass\'. This option could be useful for example to lazy-load the LCP element of a certain page.', 'fastcache')
				],
				'lazyload_method' => [
					__('Lazy-load method', 'fastcache'),
					__('Choose the preferred method used to reveal lazy-loaded elements. Hidden elements can be lazy-loaded using scrolling or a delay.', 'fastcache')
				],
				'lazyload_html_delay' => [
					__('Lazy-load delay', 'fastcache'),
					__('When the selected lazy-load method is \'Delay\' then the lazy-loading of selected HTML elements will be delayed of a certain amount of milliseconds, by default it\'s delayed of 3000 ms. You can change this value based on best performance reported in the PageSpeed test.', 'fastcache')
				],
				'lazyload_html_use_important_override' => [
					__('Use CSS override', 'fastcache'),
					__('If enabled, the special \'!important\' CSS override will be used to lazy-load a HTML elements. This may be needed if certain tags can\'t be lazy-loaded because of CSS styles included in the template that require to be overridden.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on Media Tab
	 *
	 * @return array
	 */
	public static function mediaTab()
	{
		return [
			/**
			 * Sprite Generator
			 */
			'spriteGeneratorSection' => [
				'csg_enable' => [
					__('Combine background images', 'fastcache'),
					__('If enabled, images loaded through CSS background styles will be combined into one single image in order to reduce HTTP requests. NOTICE: the option \'Combine CSS Files\' must be enabled to have this feature working".', 'fastcache')
				],
				'csg_direction' => [
					__('Image orientation', 'fastcache'),
					__('Choose the best orientation used for images positioned inside the combined one.', 'fastcache')
				],
				'csg_wrap_images' => [
					__('Auto-format combined image', 'fastcache'),
					__('If the size of the combined image exceeds 2000px, included images will be wrapped in a new row or column.', 'fastcache')
				],
				'csg_exclude_images' => [
					__('Exclude images', 'fastcache'),
					__('You can exclude certain images from the combine functionality in the case that they got broken or displayed incorrectly. It\'s possible to select a file name from the list of options or specify additional ones and hit \'Enter\'".', 'fastcache')
				],
				'csg_include_images' => [
					__('Include additional images', 'fastcache'),
					__('Images to be combined are crawled and selected automatically. In the case that you want to include additional images that have not been found automatically, you can enter them in this parameter. It\'s possible to select a file name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on Http/2 tab
	 *
	 * @return array
	 */
	public static function http2PushTab()
	{
		return [
			/**
			 * Http2 Push
			 */
			'http2PushSection' => [
				'http2_push_enable' => [
					__('Enable HTTP/2 Server Push', 'fastcache'),
					__('HTTP/2 Server Push allows an HTTP/2-compliant server to send resources to a HTTP/2-compliant client before the client requests them. It is, for the most part, a performance technique that can be helpful in loading resources preemptively. If this option is enabled, Fastcache will communicate to the server to operate with this technique, keep in mind that this only works on a HTTP/2 compliant server.', 'fastcache')
				],
				'pro_http2_exclude_deferred' => [
					__('Exclude asyncronous assets', 'fastcache'),
					__('If enabled, assets that are deferred, loaded asynchronously or lazy-loaded will be excluded.', 'fastcache')
				],
				'pro_http2_push_cdn' => [
					__('Include CDN files', 'fastcache'),
					__('Files loaded over your CDN domains will also be included.', 'fastcache')
				],
				'pro_http2_file_types' => [
					__('File types', 'fastcache'),
					__('Select the type of files to preload through HTTP/2.', 'fastcache')
				],
				'pro_http2_include' => [
					__('Add custom files', 'fastcache'),
					__('It\'s possible to preload custom files adding their full path in this field. supported file types are: .js, .css, .webp, .gif, .png, .jpg, .woff, .woff2', 'fastcache')
				],
				'pro_http2_exclude' => [
					__('Exclude files', 'fastcache'),
					__('If you receive warnings in the browser console about preloaded files not used within a few seconds, you can exclude these files here.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on Optimize Image tab
	 *
	 * @return array
	 */
	public static function optimizeImageTab()
	{
		return [
			/**
			 * Add image Attributes
			 */
			'addImageAttributesSection' => [
				'lightimgs_status' => [
					__('Enable image optimization', 'fastcache'),
					__('If images optimization is enabled, images will be compressed on the fly to reduce the bandwidth usage and speed up your site. NOTICE: the quality of images may be significantly decreased on large screens.', 'fastcache')
				],
				'img_processing_minwidth' => [
					__('Minimum width for image optimization', 'fastcache'),
					__('Choose the minimum width that images must have to be optimized. You should ignore small images that are already lightweight and don\'t need any kind of optimization.', 'fastcache')
				],
				'img_quality' => [
					__('Quality for compressed <br/>images', 'fastcache'),
					__('You can choose the image quality for resulting optimized images. High value will mean a higher quality but also higher bandwidth usage.', 'fastcache')
				],
				'img_resizing_switcher' => [
					__('Enable image resizing', 'fastcache'),
					__('You can choose if images should be resized to a lower resolution to reduce size.', 'fastcache')
				],
				'img_resizing' => [
					__('Resizing percentage', 'fastcache'),
					__('Choose the percentage to which you want to down-size your images. For example if you have an image 1500px wide and apply a resizing percentage of 50%, the image will be resized to 750px thus saving bandwidth.', 'fastcache')
				],
				'img_resizing_minwidth' => [
					__('Minimum width for resizing images', 'fastcache'),
					__('Choose the minimum width that images must have to be resized to a lower resolution. You should avoid to resize small images that are already lightweight and apply down-sizing only to large and heavyweight images.', 'fastcache')
				],
				'convert_all_images_to_webp' => [
					__('Convert all images to WebP', 'fastcache'),
					__('If enabled, all optimized images will be converted to the next generation WebP format. NOTICE: for this function to work the server PHP must include the GD library with the WebP support enabled and the \'imagewebp\' function must be available, if this is missing on your server you can contact the hosting provider.', 'fastcache')
				],
				'convert_all_images_to_avif' => [
					__('Convert all images to AVIF', 'fastcache'),
					__('If enabled, all optimized images will be converted to the next generation AVIF format. NOTICE: for this function to work the server PHP must include the GD library with the AVIF support enabled and the \'imageavif\' function must be available, if this is missing on your server you can contact the hosting provider. Commonly this feature is supported by PHP 8.1 and later.', 'fastcache')
				],
				'exclude_light_images_safari' => [
					__('Exclude images conversion for Safari', 'fastcache'),
					__('If enabled, all optimized images will not be converted to the next generation WebP/AVIF format for Safari browser on iOS devices. Safari supports WebP images since version 16.0 and AVIF images since version 16.4. Keep this option enabled for maximum compatibility with older versions of the Safari browser.', 'fastcache')
				],
				'optimize_css_background_images' => [
					__('Optimize CSS background <br/>images', 'fastcache'),
					__('If enabled, all images that are loaded through CSS files will also be optimized. The compiled CSS files will include new links to optimized images. This features requires CSS optimization to be enabled.', 'fastcache')
				],
				'optimize_html_background_images' => [
					__('Optimize HTML background images', 'fastcache'),
					__('If enabled, all images that are loaded through HTML inline styles for background will also be optimized. The minified HTML code will include new links to optimized images. This features requires HTML optimization to be enabled and set to \'High\' level.', 'fastcache')
				],
				'img_support_gif' => [
					__('GIF images optimization', 'fastcache'),
					__('Choose to optmize also GIF images on your site. Note that if this feature is enabled for animated GIF all animation frames will be lost.', 'fastcache')
				],
				'webservice_processing' => [
					__('Lossless optimization', 'fastcache'),
					__('If enabled, all images will be converted using a lossless compression provided by the third-party Resmush WebService https://resmush.it. NOTICE: using this system images won\'t never be resized, the parameter \'Quality for compressed images\' is applied only to JPG images and the conversion to the WebP format won\'t have any effect. Although it\'s possible to generate a srcset using this method it doesn\'t make much sense, indeed all images of the srcset will have the same size and quality.', 'fastcache')
				],
				'hash_images_algo' => [
					__('Hash method of images name', 'fastcache'),
					__('Choose the preferred method for the hash generation of the image name. The \'full\' method totally replaces the name of an image with a full hash. The \'partial\' method concatenates a hash to the original name of an image. The \'none\' method leaves the original name of an image unaltered, but pay attention that not using a hash could cause names collision if you have different images with the same name.', 'fastcache')
				],
				'preserve_cached_images' => [
					__('Preserve cached images', 'fastcache'),
					__('If enabled, images stored in the cache will not be deleted when clearing or regenerating the cache and will persist indefinitely. The first time a page is visited, optimized images must be created and this may slightly increase the loading time. By preserving cached images forever, once they are generated the following visits will be much faster since the optimized images are already available', 'fastcache')
				],
				'img_exts_excluded' => [
					__('Exclude image extensions', 'fastcache'),
					__('Choose by file extension if some image types should be excluded from the resizing process.', 'fastcache')
				],
				'img_files_excluded' => [
					__('Exclude images by name/path', 'fastcache'),
					__('If you need to exclude certain images from the optimization process you can enter a substring of the name or path to keep the original one. For example if you want to exclude an image at the following path: \'images/socials/mycustomimage.png\' it\'s enough to enter \'socials\' to exclude the entire folder or \'custom\' to exclude an image named \'mycustomname.png\'.', 'fastcache')
				],
				'img_class_excluded' => [
					__('Exclude images by class', 'fastcache'),
					__('Choose classes of the images that you want to exclude from being optimized. A class must be declared in the attribute of an \'&lt;img&gt;\' element. It\'s possible to select a class name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'img_menu_excluded' => [
					__('Exclude images optimization by page URL', 'fastcache'),
					__('Enter a substring of each url that you want to exclude from the images optimization. It\'s not needed to enter the complete url for the matching, but only a part of it. Add a string and hit \'Enter\'.', 'fastcache')
				],
				'img_processing_datasrc' => [
					__('Process \'data-src\' attribute', 'fastcache'),
					__('If enabled, when the special \'data-src\' attribute of an image is present, it will be set to point to the optimized image. This can be useful for example for galleries that use this special attribute in order to load the optimized image instead that the original one".', 'fastcache')
				],
				'img_processing_datacustom' => [
					__('Process custom "data-" attributes', 'fastcache'),
					__('If enabled, custom "data-" attributes containing image URLs will be processed to point to optimized images. This is useful for elements that store image sources in custom attributes instead of the standard "src" or "data-src"', 'fastcache')
				],
				'img_attributes_enable' => [
					__('Add size attributes', 'fastcache')
				],
				'use_simplehtmldom' => [
					__('Alternative HTML parsing mode', 'fastcache'),
					__('Enable this option only if you notice layout issues, wrong UTF-8 characters encoding or conflicts with the default processing. It provides an alternative way to analyze and optimize your page output, which may improve compatibility with certain templates or extensions', 'fastcache')
				],
				'img_processing_entity_decode' => [
					__('Decode HTML entities', 'fastcache'),
					__('If enabled, UTF-8 characters will be decoded and preserved without encoding entities.', 'fastcache')
				],
				'img_processing_utf8_entity_decode' => [
					__('Decode UTF-8 HTML entities', 'fastcache'),
					__('If enabled, UTF-8 characters will be decoded into HTML entities. Enable this option if you experience characters on the page that are wrongly encoded.', 'fastcache')
				],
				'purify_string' => [
					__('Purify string', 'fastcache'),
					__('If you experience weird errors such as javascript code inside the page content caused by a certain script word or tag, you can specify that string in this parameter in order to remove it and avoid the conflict. This field supports regular expressions and automatically adds the escape slash to the \'/\' special character.', 'fastcache')
				],
				'purify_string_replacement' => [
					__('Replacement string', 'fastcache'),
					__('You can specify an alternative string used to replace the purified string. By default it will be completely removed and replaced with an empty string.', 'fastcache')
				],
				'img_processing_simplehtmldom_entity_decode' => [
					__('Decode text entities', 'fastcache'),
					__('Convert encoded characters into normal characters in the visible text of your pages. Attributes such as links will remain safe. Enable only if you prefer cleaner source code or need it for validation', 'fastcache')
				]
			],
			'addImageSrcsetSection' => [
				'img_processing_srcset' => [
					__('Create srcset', 'fastcache'),
					__('If enabled, an additional HTML5 \'srcset\' attribute will be added for each image that need to be optimized and Fastcache will automatically create up to 4 differently-sized images as variations to the base image. The browser will pick up the most correct image based on the device screen size and resolution. IMPORTANT: if you activate this feature, images may be enlarged to full width because the srcset doesn\'t render images based on its own width but based on the total width of the container element; as a result you may need to rework your CSS and apply a \'max-width\' to the various images displayed via a srcset.', 'fastcache')
				],
				'img_processing_srcset_starting_quality' => [
					__('Initial quality value', 'fastcache'),
					__('Choose the value of the initial quality used for the image with the highest resolution of the srcset and for the regular fallback img tag. Clear the plugin cache after that you have changed this setting.', 'fastcache')
				],
				'img_processing_srcset_quality_decrease_step' => [
					__('Quality decrease', 'fastcache'),
					__('Choose the amount of quality decrease that each srcset image should have. Starting from the highest quality image there will be 3 more images with a lower quality reduced by this value. Clear the plugin cache after that you have changed this setting.', 'fastcache')
				],
				'img_processing_srcset_starting_resize' => [
					__('Initial resizing value', 'fastcache'),
					__('Choose the value of the initial resizing used for the image with the largest size of the srcset and for the regular fallback img tag. Clear the plugin cache after that you have changed this setting.', 'fastcache')
				],
				'img_processing_srcset_resize_decrease_step' => [
					__('Resizing decrease', 'fastcache'),
					__('Choose the amount of resizing decrease that each srcset image should have. Starting from the biggest image there will be 3 more smaller images reduced by this value. Clear the plugin cache after that you have changed this setting.', 'fastcache')
				],
				'img_processing_srcset_original_image' => [
					__('Standard image \'src\'', 'fastcache'),
					__('You can choose which image to use for the standard \'src\' attribute used as a fallback for browsers that don\'t support srcset. Choosing a lower resolution image for the \'src\' attribute could be helpful to reach a higher score during the PageSpeed test.', 'fastcache')
				],
				'img_processing_srcset_datasrc' => [
					__('Create \'srcset\' for \'data-src\' images', 'fastcache'),
					__('If enabled, Fastcache will generate a \'srcset\' attribute for images using \'data-src\' or custom \'data-\' attributes, ensuring optimized responsive images for different screen sizes and improving loading performance.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on Miscellanous tab
	 *
	 * @return array
	 */
	public static function miscellaneous()
	{
		return [
			/**
			 * Optimize CSS Delivery
			 */
			'optimizeCssDeliverySection' => [
				'optimizeCssDelivery_enable' => [
					__('Enable extraction', 'fastcache'),
					__('Enable the extraction of basic CSS styles and load them above the fold to avoid render blocking resources.', 'fastcache')
				],
				'optimizeCssDelivery' => [
					__('Number of elements', 'fastcache'),
					__('Select the number of HTML elements from the top of the page that you want to analyze to find the basic CSS to be extracted from.', 'fastcache')
				],
				'pro_remove_unused_css' => [
					__('Remove unused CSS', 'fastcache'),
					__('If enabled, the plugin will \'lazy-load\' the unused CSS to prevent unnecessary processing before than the page is loaded.', 'fastcache')
				],
				'optimizeCssDeliveryExcludeFontface' => [
					__('Exclude @font-face', 'fastcache'),
					__('If enabled, the extracted \'Above-the-fold\' CSS code will not include any external font, futher improving performance.', 'fastcache')
				],
				'pro_dynamic_selectors' => [
					__('CSS selectors', 'fastcache'),
					__('It could be necessary to extract the basic CSS for some custom elements. Add any substring from the CSS declaration here to have them included in the extracted CSS code and hit \'Enter\'.', 'fastcache')
				],
				'extractcss_menu_excluded' => [
					__('Exclude extraction by page URL', 'fastcache'),
					__('Enter a substring of each url that you want to exclude from the CSS extraction. It\'s not needed to enter the complete url for the matching, but only a part of it. Add a string and hit \'Enter\'.', 'fastcache')
				]
			],
			/**
			 * Reduce Dom Section
			 */
			'reduceDomSection' => [
				'pro_reduce_dom' => [
					__('Enable DOM reduction', 'fastcache'),
					__('Enable the reduction of DOM tree. If you experience conflicts or malfuctioning keep this feature disabled.', 'fastcache')
				],
				'pro_html_sections' => [
					__('HTML tags to load asynchronously', 'fastcache'),
					__('Select which HTML elements you want to load asynchronously.', 'fastcache')
				]
			],
			'advancedSettings' => [
				'clear_server_cache' => [
					__('Clear server cache (Varnish)', 'fastcache'),
					__('If enabled, when the plugin clears its own cache it will also try to invalidate a server side caching system, such as Varnish, by making a PURGE request. This could be needed to keep synced the cache cleaning between the static files generated by the plugin and the server side cache.', 'fastcache')
				],
				'base64encode' => [
					__('Cache encryption', 'fastcache'),
					__('If enabled, all cached files will be encrypted by PHP and it won\'t be possible to read contents. This option could slow down performance and increase the cache size.', 'fastcache')
				],
				'debug' => [
					__('Debug Plugin', 'fastcache'),
					__('This option will enable the debug mode that adds the url of the original files inside the combined and optimized file generated by FastCache and enable the log system at the path "root/wp-content/plugins/fastcache/logs". This is useful to identify and resolve conflicts.', 'fastcache')
				],
				'order_plugin' => [
					__('Order plugins', 'fastcache'),
					__('If enabled, the plugin automatically ensures the correct execution order with other popular caching plugins.', 'fastcache')
				],
				'try_catch' => [
					__('Handle Javascript errors', 'fastcache'),
					__('If you get Javascript errors in the browser console, you can try to enable this option to execute each combined Javascript file within a try-catch block.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on assetsInclusions tab
	 *
	 * @return array
	 */
	public static function assetsInclusions()
	{
		return [
			/**
			 * Javascript Automatic Settings
			 */
			'javascriptAutomaticSettingsSection' => [
				'includeAllExtensions' => [
					__('Include all assets', 'fastcache'),
					__('If enabled, all files managed by third-party plugins and external domains will be included in the combined file.', 'fastcache')
				],
				'replaceImports' => [
					__('Include imported CSS files', 'fastcache'),
					__('If you have CSS files that use @import statements, when this option is enabled the contents of each referenced resource will be fetched and directly included in the containing file to speed up delivering of all styles.', 'fastcache')
				],
				'phpAndExternal' => [
					__('Include PHP and external files', 'fastcache'),
					__('If enabled, dynamic Javascript and CSS files having a \'.php\' file extension and files pointing to external domains will be included in the combined file. This feature requires the \'PHP CURL\' library or \'allow_url_fopen\' enabled on your server for PHP.', 'fastcache')
				],
				'inlineStyle' => [
					__('Include inline CSS styles', 'fastcache'),
					__('If enabled, inline CSS styles will be included in the combined CSS file in the same order that they appear in the page.', 'fastcache')
				],
				'inlineScripts' => [
					__('Include inline scripts', 'fastcache'),
					__('If enabled, inline Javascript scripts will be included in the combined JS files in the same order that they appear in the page.', 'fastcache')
				]
			]
		];
	}

	/**
	 * Settings on assetsExclusions tab
	 *
	 * @return array
	 */
	public static function assetsExclusions()
	{
		return [
			/**
			 * Exclude CSS Settings
			 */
			'excludeCssSection' => [
				'excludeCSS' => [
					__('Exclude CSS files', 'fastcache'),
					__('Select the CSS files that you want to exclude from the combine functionality. It\'s possible to select a file name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'excludeCssComponents' => [
					__('Exclude CSS files by plugin', 'fastcache'),
					__('All CSS files that belong to the chosen extensions will be excluded from the combine functionality. It\'s possible to select an extension name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'excludeStyles' => [
					__('Exclude inline "style" <br/>declarations', 'fastcache'),
					__('Select an inline \'style\' declaration that you want to exclude from the combine functionality, you can type in a substring of the style tag or content that you want to exclude.', 'fastcache')
				],
				'excludeAllStyles' => [
					__('Exclude all inline "style" declarations', 'fastcache'),
					__('Exclude all inline \'style\' declaration from the combine functionality. This could reduce the amount of cache if there are inline styles that change continuously.', 'fastcache')
				]
			],
			/**
			 * Exclude Preserving Execution Order
			 */
			'excludePeoSection' => [
				'excludeJs_peo' => [
					__('Exclude Javascript files', 'fastcache'),
					__('Select the Javascript files that you want to exclude from the combine functionality preserving the execution order as they appear on the page. It\'s possible to select a file name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'excludeJsComponents_peo' => [
					__('Exclude Javascript files <br/>by plugin', 'fastcache'),
					__('All Javascript files that belong to the chosen plugin will be excluded from the combine functionality preserving the execution order as they appear on the page. It\'s possible to select a plugin name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'excludeScripts_peo' => [
					__('Exclude inline "script" <br/>declarations', 'fastcache'),
					__('Select an inline \'script\' declarations that you want to exclude from the combine functionality, you can type in a substring of the script tag or content that you want to exclude.', 'fastcache')
				],
				'excludeAllScripts' => [
					__('Exclude all inline "script" declarations', 'fastcache'),
					__('Exclude all inline \'script\' declaration from the combine functionality. This could reduce the amount of cache if there are inline styles that change continuously.', 'fastcache')
				]
			],
			/**
			 * Exclude Ignoring Excecution Order
			 */
			'excludeIeoSection' => [
				'excludeJs' => [
					__('Exclude javascript files', 'fastcache'),
					__('Select the Javascript files that you want to exclude from the combine functionality without preserving the execution order as they appear on the page. It\'s possible to select a file name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'excludeJsComponents' => [
					__('Exclude Javascript files <br/>by plugin', 'fastcache'),
					__('All Javascript files that belong to the chosen plugin will be excluded from the combine functionality without preserving the execution order as they appear on the page. It\'s possible to select a plugin name from the list of options or specify additional ones and hit \'Enter\'.', 'fastcache')
				],
				'excludeScripts' => [
					__('Exclude inline "script" <br/>declarations', 'fastcache'),
					__('Select an inline \'script\' declarations that you want to exclude from the combine functionality, you can type in a substring of the script tag or content that you want to exclude.', 'fastcache')
				]
			],
			/**
			 * Exclude Menu Items
			 */
			'excludeMenuItemsSection' => [
				'menuexcludedurl' => [
					__('Exclude urls', 'fastcache'),
					__('Enter a substring of each url that you want to exclude from the plugin optimization. It\'s not needed to enter the complete url for the matching, but only a part of it. Add a string and hit \'Enter\'.', 'fastcache')
				],
				'disable_logged_in_users' => [
					__('Exclude logged in users', 'fastcache'),
					__('If enabled, the plugin will be disabled for all users that are logged in.', 'fastcache')
				]
			],
			/**
			 * Don't Move Files To Bottom
			 */
			'dontMoveSection' => [
				'dontmoveJs' => [
					__('Javascript files', 'fastcache'),
					__('Excluded Javascript files are commonly moved to the bottom of the page. In the case of conflicts, it\'s possible to enter them in this setting to keep the original position in the page.', 'fastcache')
				],
				'dontmoveScripts' => [
					__('Inline scripts', 'fastcache'),
					__('Excluded scripts are commonly moved to the bottom of the page. In the case of conflicts, it\'s possible to enter them in this setting to keep the original position in the page.', 'fastcache')
				]
			]
		];
	}
	/**
	 * Settings on assetsExclusions tab
	 *
	 * @return array
	 */
	public static function fastCacheCDNTab()
	{
		return [
			'addFastCacheCDNSectionEnable' => [
				'fastcache_cdn_enable' => [
					__('Enable FastCache CDN', 'fastcache'),
					__('If enabled, it activates the communication with the FastCache CDN by host.it', 'fastcache')
				],
				'fastcache_cdn_texttoken' => [
					__('Access token', 'fastcache'),
					__('Enter your access token obtained in the host.it customer area', 'fastcache')
				]
			],
			'addFastCacheCDNTTL' => [
				'fastcache_cdn_enable_ttl' => [
					__('Enable TTL', 'fastcache'),
					__('By default, the TTL is set to 300 seconds. If you need to use a different value, you can enter it as the global default or set it per post type by enabling this option.', 'fastcache')
				],
				'fastcache_cdn_default_ttl_notfound_set' => [
					__('Set not found default TTL', 'fastcache'),
					__('If a ttl value for a given post type is not found, you can choose to set the default ttl value or not set a ttl at all', 'fastcache')
				],
				'fastcache_cdn_default_ttl' => [
					__('Default TTL', 'fastcache'),
					__('Default TTL (generic cache duration in seconds)', 'fastcache')
				],
				'fastcache_cdn_types_ttl' => [
					__('Post type TTL', 'fastcache')
				]
			],
			'addFastCacheExclusion' => [
				'fastcache_url_exclusion' => [
					__('Exclude pages', 'fastcache'),
					__('Enter the list of URLs to be excluded from the cache', 'fastcache')
				],
				'fastcache_posttype_exclusion' => [
					__('Exclude post type', 'fastcache'),
					__('Enter the post type to be excluded from the cache', 'fastcache')
				]
			],
			'addFastCacheLogDebug' => [
				'fastcache_cdn_logdebug' => [
					__('Enable Log Debug', 'fastcache'),
					__('Once activated, the log file inside the folder wp-content/plugins/fastcache/logs/log.txt is updated', 'fastcache')
				]
			],
			'addFastCacheFaqs' => [
				'fastcache_cdn_helpSection' => [
					__('FAQs', 'fastcache'),
					__('FAQs section', 'fastcache')
				]
			]
		];
	}
	public static function autoconfigurationTab()
	{
		return [
			/**
			 * Auto configuration
			 */
			'autoconfigurationSection' => [
				'autoconfiguration' => [
					__('Choose optimization level', 'fastcache'),
					__('Select an optimization level for the auto configuration', 'fastcache'),
					true
				]
			]
		];
	}
	public static function diagnosticTab()
	{
		return [
			/**
			 * Plugins Conflict Diagnosis
			 */
			'diagnosticSection' => [
				'pluginsDiagnosis' => [
					__('Plugins conflict diagnosis', 'fastcache'),
					__('This option detects if there are other caching or optimization plugins installed and active that could cause conflicts with FastCache. In such case, take care to disable them', 'fastcache'),
					true
				]
			]
		];
	}
	public static function speedtestTab()
	{
		return [
			/**
			 * Plugins Conflict Diagnosis
			 */
			'speedtestSection' => [
				'pagespeed' => [
					__('Google PageSpeed score', 'fastcache'),
					__('Test your website to evaluate your Google PageSpeed score', 'fastcache'),
					true
				],
				'pagespeedtest_domain_url' => [
					__('Page URL to test', 'fastcache'),
					__('Enter the URL of the page to test, if the field is left empty then the home page URL will be used', 'fastcache'),
					true
				],
				'google_pagespeed_api_key' => [
					__('Custom Google PageSpeed ApiKey', 'fastcache'),
					__('It\'s possible to enter a custom Google PageSpeed ApiKey, if the field is left empty then the built in ApiKey will be used', 'fastcache'),
					true
				]
			]
		];
	}
	public static function transferConfigTab()
	{
		return [
			/**
			 * Transfer Configuration
			 */
			'transferConfigSection' => [
				'download_config' => [
					__('Download configuration', 'fastcache'),
					__('Download a JSON file with the current plugin configuration.', 'fastcache'),
					true
				],
				'upload_config' => [
					__('Upload configuration', 'fastcache'),
					__('Upload a JSON file with a previously dumped configuration file. CAUTION: this will overwrite all current settings.', 'fastcache'),
					true
				]
			]
		];
	}

	/**
	 * Settings on Object Cache tab
	 *
	 * @return array
	 */
	public static function objectCacheTab()
	{
		return [
			/**
			 * Object Cache General
			 */
			'objectCacheGeneralSection' => [
				'object_cache_enable' => [
					__('Enable Object Cache', 'fastcache'),
					__('When enabled, WordPress database query results and objects are cached persistently using the selected backend (Redis, Memcached, or File System). This significantly reduces database queries and improves performance.', 'fastcache')
				],
				'object_cache_backend' => [
					__('Cache backend', 'fastcache'),
					__('Choose the cache backend. "Auto" will automatically detect the best available backend in order: Redis → Memcached → File System.', 'fastcache')
				],
				'object_cache_default_ttl' => [
					__('Default TTL (seconds)', 'fastcache'),
					__('Default time-to-live for cached objects in seconds. Set to 0 for no expiration (recommended for Redis/Memcached). For file-based backend, a value like 3600 (1 hour) is recommended.', 'fastcache')
				],
				'object_cache_key_prefix' => [
					__('Key prefix', 'fastcache'),
					__('A prefix added to all cache keys. Useful when multiple WordPress installations share the same Redis/Memcached server.', 'fastcache')
				],

			],
			/**
			 * Redis Configuration
			 */
			'objectCacheRedisSection' => [
				'object_cache_redis_host' => [
					__('Redis host', 'fastcache'),
					__('The hostname or IP address of the Redis server. Default: 127.0.0.1', 'fastcache')
				],
				'object_cache_redis_port' => [
					__('Redis port', 'fastcache'),
					__('The port number of the Redis server. Default: 6379', 'fastcache')
				],
				'object_cache_redis_password' => [
					__('Redis password', 'fastcache'),
					__('The authentication password for the Redis server. Leave empty if no authentication is required.', 'fastcache')
				],
				'object_cache_redis_database' => [
					__('Redis database', 'fastcache'),
					__('The Redis database index to use (0-15). Default: 0. Use different indexes for different WordPress installations on the same server.', 'fastcache')
				]
			],
			/**
			 * Memcached Configuration
			 */
			'objectCacheMemcachedSection' => [
				'object_cache_memcached_host' => [
					__('Memcached host', 'fastcache'),
					__('The hostname or IP address of the Memcached server. Default: 127.0.0.1', 'fastcache')
				],
				'object_cache_memcached_port' => [
					__('Memcached port', 'fastcache'),
					__('The port number of the Memcached server. Default: 11211', 'fastcache')
				]
			],
			/**
			 * Exclusions
			 */
			'objectCacheExclusionsSection' => [
				'object_cache_exclude_logged_in' => [
					__('Exclude logged-in users', 'fastcache'),
					__('Disable object caching for logged-in users. This is recommended to avoid displaying stale or private data.', 'fastcache')
				],
				'object_cache_exclude_admin' => [
					__('Exclude admin area', 'fastcache'),
					__('Disable object caching for the WordPress admin dashboard (wp-admin). This ensures that admin actions always reflect the latest database state.', 'fastcache')
				],
				'object_cache_exclude_frontend' => [
					__('Exclude frontend', 'fastcache'),
					__('Disable persistent object caching for all frontend requests (non-admin). Useful for testing: keep object cache active only in wp-admin while the frontend always reads from the database.', 'fastcache')
				],
				'object_cache_non_persistent_groups' => [
					__('Non-persistent groups', 'fastcache'),
					__('Cache groups stored only in memory (not persisted to backend). One group per line. Note: wc_session_data and session-tokens are always excluded from persistence regardless of this setting.', 'fastcache')
				]
			],
			/**
			 * Debugging
			 */
			'objectCacheDebuggingSection' => [
				'object_cache_logging_enable' => [
					__('Enable activity log', 'fastcache'),
					__('Log cache operations (HIT, MISS, SET, DELETE, GC) to wp-content/cache/fastcache/object-cache.log. The log is automatically cleared when the cache is flushed.', 'fastcache')
				],
				//'object_cache_logging_types' => [
				//	__('Enable logging for message of type:', 'fastcache'),
				//	__('Select which cache operation types will be written to the activity log.', 'fastcache')
				//],
				'object_cache_stats_enable' => [
					__('Enable statistics', 'fastcache'),
					__('When enabled, cache hit/miss statistics are tracked and displayed in the admin dashboard.', 'fastcache')
				],
				'object_cache_dropin_status' => [
					__('Drop-in status', 'fastcache'),
					__('Shows the current status of the object-cache.php drop-in file and detected backend.', 'fastcache'),
					true
				]
			]
		];
	}
}