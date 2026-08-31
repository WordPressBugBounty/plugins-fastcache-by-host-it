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

use FastCache\Admin\Settings\Html;
use FastCache\Platform\Utility;

class Setting
{
	public static function object_cache_exclude_logged_in()
	{
		echo Html::_('radio', 'object_cache_exclude_logged_in', '1');
	}

	public static function object_cache_exclude_admin()
	{
		echo Html::_('radio', 'object_cache_exclude_admin', '1');
	}

	public static function object_cache_exclude_frontend()
	{
		echo Html::_('radio', 'object_cache_exclude_frontend', '0');
	}

	public static function object_cache_logging_enable()
	{
		echo Html::_('radio', 'object_cache_logging_enable', '0');
	}

	public static function object_cache_logging_types()
	{
		$aOptions = [
			'hit'    => __('HIT', 'fastcache'),
			'miss'   => __('MISS', 'fastcache'),
			'delete' => __('DELETE', 'fastcache'),
			'set'    => __('SET', 'fastcache'),
		];

		// Default: tutti e 4 i tipi selezionati
		$aDefault = ['hit', 'miss', 'delete', 'set'];

		// echo Html::_('checkboxes', 'object_cache_logging_types', $aDefault, $aOptions);
		
	}

	public static function registration_email()
	{
		echo Html::_('text', 'registration_email', '');
	}

	public static function autoconfiguration()
	{
		echo Html::_('rangeSlider', 'rangeSlider', '0');
	}

	public static function pluginsDiagnosis()
	{
		echo Html::_('pluginsDiagnosis', 'all', '');
	}

	public static function pagespeed()
	{
		echo Html::_('pageSpeed', 'pageSpeed', '0');
	}

	public static function pagespeedtest_domain_url()
	{
		echo Html::_('text', 'pagespeedtest_domain_url', '', '100');
	}

	public static function google_pagespeed_api_key()
	{
		echo Html::_('text', 'google_pagespeed_api_key', '', '100');
	}
	public static function clear_server_cache()
	{
		echo Html::_('radio', 'clear_server_cache', '0');
	}
	public static function debug()
	{
		echo Html::_('radio', 'debug', '0');
	}
	public static function order_plugin()
	{
		echo Html::_('radio', 'order_plugin', '1');
	}
	public static function disable_logged_in_users()
	{
		echo Html::_('radio', 'disable_logged_in_users', '0');
	}
	public static function menuexcludedurl()
	{
		echo Html::_('multiselect', 'menuexcludedurl', [], 'url', 'file');
	}
	public static function combine_files_enable()
	{
		echo Html::_('radio', 'combine_files_enable', '0');
	}
	public static function cache_lifetime()
	{
		$aOptions = [
			'60' => __('1 min', 'fastcache'),
			'300' => __('5 min', 'fastcache'),
			'600' => __('10 min', 'fastcache'),
			'900' => __('15 min', 'fastcache'),
			'1800' => __('30 min', 'fastcache'),
			'3600' => __('1 hour', 'fastcache'),
			'10800' => __('3 hours', 'fastcache'),
			'21600' => __('6 hours', 'fastcache'),
			'43200' => __('12 hours', 'fastcache'),
			'86400' => __('1 day', 'fastcache'),
			'172800' => __('2 days', 'fastcache'),
			'604800' => __('7 days', 'fastcache'),
			'1209600' => __('2 weeks', 'fastcache'),
			'1814400' => __('3 weeks', 'fastcache'),
			'2592000' => __('1 month', 'fastcache'),
			'7776000' => __('3 months', 'fastcache'),
			'31536000' => __('No expiration', 'fastcache')
		];

		echo Html::_('select', 'cache_lifetime', '300', $aOptions);
	}
	public static function html_minify_level()
	{
		$aOptions = [
			'0' => __('Low', 'fastcache'),
			'1' => __('Normal', 'fastcache'),
			'2' => __('High', 'fastcache')
		];

		echo Html::_('select', 'html_minify_level', '1', $aOptions);
	}
	public static function try_catch()
	{
		echo Html::_('radio', 'try_catch', '1');
	}
	public static function html_minify()
	{
		echo Html::_('radio', 'html_minify', '0');
	}
	public static function includeAllExtensions()
	{
		echo Html::_('radio', 'includeAllExtensions', '1');
	}
	public static function phpAndExternal()
	{
		echo Html::_('radio', 'phpAndExternal', '1');
	}
	public static function css()
	{
		echo Html::_('radio', 'css', '0');
	}
	public static function css_minify()
	{
		echo Html::_('radio', 'css_minify', '0');
	}
	public static function replaceImports()
	{
		echo Html::_('radio', 'replaceImports', '1');
	}
	public static function inlineStyle()
	{
		echo Html::_('radio', 'inlineStyle', '1');
	}
	public static function excludeCss()
	{
		echo Html::_('multiselect', 'excludeCss', [], 'css', 'file');
	}
	public static function excludeCssComponents()
	{
		echo Html::_('multiselect', 'excludeCssComponents', [], 'css', 'extension');
	}
	public static function excludeStyles()
	{
		echo Html::_('multiselect', 'excludeStyles', [], 'css', 'style');
	}
	public static function excludeAllStyles()
	{
		echo Html::_('radio', 'excludeAllStyles', '0');
	}
	public static function remove_css()
	{
		echo Html::_('multiselect', 'remove_css', [], 'css', 'file');
	}
	public static function font_display_swap()
	{
		echo Html::_('radio', 'font_display_swap', '0');
	}
	public static function defer_combined_styles()
	{
		echo Html::_('radio', 'defer_combined_styles', '0');
	}
	public static function optimizeCssDelivery_enable()
	{
		echo Html::_('radio', 'optimizeCssDelivery_enable', '0');
	}
	public static function optimizeCssDelivery()
	{
		$aOptions = [
			'400' => '400',
			'600' => '600',
			'800' => '800',
			'1000' => '1000',
			'1200' => '1200',
			'1500' => '1500',
			'2000' => '2000',
			'3000' => '3000',
			'4000' => '4000',
			'5000' => '5000',
			'6000' => '6000',
			'7000' => '7000',
			'8000' => '8000',
			'9000' => '9000',
			'10000' => '10000',
			'15000' => '15000',
			'20000' => '20000'
		];

		echo Html::_('select', 'optimizeCssDelivery', '800', $aOptions);
	}
	public static function pro_remove_unused_css()
	{
		echo Html::_('radio', 'pro_remove_unused_css', '0');
	}
	public static function optimizeCssDeliveryExcludeFontface()
	{
		echo Html::_('radio', 'optimizeCssDeliveryExcludeFontface', '1');
	}
	public static function pro_dynamic_selectors()
	{
		echo Html::_('multiselect', 'pro_dynamic_selectors', [], 'selectors', 'style');
	}
	public static function javascript()
	{
		echo Html::_('radio', 'javascript', '0');
	}
	public static function js_minify()
	{
		echo Html::_('radio', 'js_minify', '0');
	}
	public static function inlineScripts()
	{
		echo Html::_('radio', 'inlineScripts', '1');
	}
	public static function bottom_js()
	{
		echo Html::_('radio', 'bottom_js', '0');
	}
	public static function loadAsynchronous()
	{
		echo Html::_('radio', 'loadAsynchronous', '0');
	}
	public static function excludeJs_peo()
	{
		echo Html::_('multiselect', 'excludeJs_peo', [], 'js', 'file');
	}
	public static function excludeJsComponents_peo()
	{
		echo Html::_('multiselect', 'excludeJsComponents_peo', [], 'js', 'extension');
	}
	public static function excludeScripts_peo()
	{
		echo Html::_('multiselect', 'excludeScripts_peo', [], 'js', 'script');
	}
	public static function excludeAllScripts()
	{
		echo Html::_('radio', 'excludeAllScripts', '0');
	}
	public static function excludeJs()
	{
		echo Html::_('multiselect', 'excludeJs', [], 'js', 'file');
	}
	public static function excludeJsComponents()
	{
		echo Html::_('multiselect', 'excludeJsComponents', [], 'js', 'extension');
	}
	public static function excludeScripts()
	{
		echo Html::_('multiselect', 'excludeScripts', [], 'js', 'script');
	}
	public static function dontmoveJs()
	{
		echo Html::_('multiselect', 'dontmoveJs', [], 'js', 'file');
	}
	public static function dontmoveScripts()
	{
		echo Html::_('multiselect', 'dontmoveScripts', [], 'js', 'script');
	}
	public static function remove_js()
	{
		echo Html::_('multiselect', 'remove_js', [], 'js', 'file');
	}
	public static function cache_enable()
	{
		echo Html::_('radio', 'cache_enable', '1');
	}
	public static function htaccess_cache_enable()
	{
		echo Html::_('radio', 'htaccess_cache_enable', '1');
	}
	public static function pro_cache_platform()
	{
		echo Html::_('radio', 'pro_cache_platform', '0');
	}
	public static function page_cache_lifetime()
	{
		$aOptions = [
			'60' => __('1 min', 'fastcache'),
			'300' => __('5 min', 'fastcache'),
			'600' => __('10 min', 'fastcache'),
			'900' => __('15 min', 'fastcache'),
			'1800' => __('30 min', 'fastcache'),
			'3600' => __('1 hour', 'fastcache'),
			'10800' => __('3 hours', 'fastcache'),
			'21600' => __('6 hours', 'fastcache'),
			'43200' => __('12 hours', 'fastcache'),
			'86400' => __('1 day', 'fastcache'),
			'172800' => __('2 days', 'fastcache'),
			'604800' => __('1 week', 'fastcache')
		];

		echo Html::_('select', 'page_cache_lifetime', '86400', $aOptions);
	}
	public static function cache_cookie_exclude()
	{
		$default_cookie_exclude = [ 'cmplz_' ];
		echo Html::_('multiselect', 'cache_cookie_exclude', $default_cookie_exclude, '', 'value');
	}
	public static function cache_exclude()
	{
		$default_cache_exclude = [
			'cart',
			'carrello',
			'checkout',
			'ordine',
			'ordini',
			'order',
			'orders',
			'account',
			'mio-account',
			'my-account',
			'thankyou',
			'grazie',
			'pagamento',
			'payment',
			'wishlist',
			'lista-desideri'
		];
		echo Html::_('multiselect', 'cache_exclude', $default_cache_exclude, '', 'value');
	}
	public static function img_attributes_enable()
	{
		echo Html::_('radio', 'img_attributes_enable', '0');
	}
	public static function enable_nonce_refresh()
	{
		echo Html::_('radio', 'enable_nonce_refresh', '1');
	}
	public static function delete_all_cache()
	{
		echo Html::_('radio', 'delete_all_cache', '0');
	}
	public static function use_simplehtmldom()
	{
		echo Html::_('radio', 'use_simplehtmldom', '0');
	}
	public static function img_processing_simplehtmldom_entity_decode()
	{
		echo Html::_('radio', 'img_processing_simplehtmldom_entity_decode', '0');
	}
	public static function csg_enable()
	{
		echo Html::_('radio', 'csg_enable', '0');
	}
	public static function csg_direction()
	{
		$aOptions = [
			'vertical' => __('Vertical direction', 'fastcache'),
			'horizontal' => __('Horizontal direction', 'fastcache')
		];

		echo Html::_('select', 'csg_drection', 'vertical', $aOptions);
	}
	public static function csg_wrap_images()
	{
		echo Html::_('radio', 'csg_wrap_images', '0');
	}
	public static function csg_exclude_images()
	{
		echo Html::_('multiselect', 'csg_exclude_images', [], 'images', 'file');
	}
	public static function csg_include_images()
	{
		echo Html::_('multiselect', 'csg_include_images', [], 'images', 'file');
	}
	public static function lazyload_enable()
	{
		echo Html::_('radio', 'lazyload_enable', '0');
	}
	public static function lazyload_autosize()
	{
		echo Html::_('radio', 'lazyload_autosize', '0');
	}
	public static function pro_lazyload_effects()
	{
		echo Html::_('radio', 'pro_lazyload_effects', '0');
	}
	public static function pro_lazyload_iframe()
	{
		echo Html::_('radio', 'pro_lazyload_iframe', '0');
	}
	public static function pro_lazyload_bgimages()
	{
		echo Html::_('radio', 'pro_lazyload_bgimages', '0');
	}
	public static function pro_lazyload_audiovideo()
	{
		echo Html::_('radio', 'pro_lazyload_audiovideo', '0');
	}
	public static function disableCoreLazyload()
	{
		echo Html::_('radio', 'disableCoreLazyload', '0');
	}
	public static function excludeLazyLoad()
	{
		echo Html::_('multiselect', 'excludeLazyLoad', [], 'lazyload', 'file');
	}
	public static function pro_excludeLazyLoadFolders()
	{
		echo Html::_('multiselect', 'pro_excludeLazyLoadFolders', [], 'lazyload', 'folder');
	}
	public static function pro_excludeLazyLoadClass()
	{
		echo Html::_('multiselect', 'pro_excludeLazyLoadClass', [], 'lazyload', 'class');
	}
	public static function http2_push_enable()
	{
		echo Html::_('radio', 'http2_push_enable', '0');
	}
	public static function pro_http2_exclude_deferred()
	{
		echo Html::_('radio', 'pro_http2_exclude_deferred', '0');
	}
	public static function pro_http2_push_cdn()
	{
		echo Html::_('radio', 'pro_http2_push_cdn', '0');
	}
	public static function pro_http2_file_types()
	{
		$aOptions = [
			'style' => 'style',
			'script' => 'script',
			'font' => 'font',
			'image' => 'image'
		];

		echo Html::_('multiselect', 'pro_http2_file_types', $aOptions, '', 'value');
	}
	public static function pro_http2_include()
	{
		echo Html::_('multiselect', 'pro_http2_include', [], 'http2', 'file');
	}
	public static function pro_http2_exclude()
	{
		echo Html::_('multiselect', 'pro_http2_exclude', [], 'http2', 'file');
	}
	public static function ignore_optimized()
	{
		echo Html::_('radio', 'ignore_optimized', '1');
	}
	public static function pro_next_gen_images()
	{
		echo Html::_('radio', 'pro_next_gen_images', '1');
	}
	public static function pro_web_old_browsers()
	{
		echo Html::_('radio', 'pro_web_old_browsers', '0');
	}
	public static function pro_api_resize_mode()
	{
		echo Html::_('radio', 'pro_api_resize_mode', '1');
	}
	public static function recursive()
	{
		echo Html::_('radio', 'recursive', '1');
	}
	public static function pro_reduce_dom()
	{
		echo Html::_('radio', 'pro_reduce_dom', '0');
	}
	public static function pro_html_sections()
	{
		$options = [
			'section' => 'section',
			'header' => 'header',
			'footer' => 'footer',
			'aside' => 'aside',
			'nav' => 'nav'
		];

		echo Html::_('multiselect', 'pro_html_sections', $options, '', 'value');
	}
	public static function lightimgs_status()
	{
		echo Html::_('radio', 'lightimgs_status', '0');
	}
	public static function img_processing_minwidth()
	{
		echo Html::_('text', 'img_processing_minwidth', '50');
	}
	public static function img_quality()
	{
		$aOptions = [
			'40' => __('40%', 'fastcache'),
			'45' => __('45%', 'fastcache'),
			'50' => __('50%', 'fastcache'),
			'55' => __('55%', 'fastcache'),
			'60' => __('60%', 'fastcache'),
			'65' => __('65%', 'fastcache'),
			'70' => __('70%', 'fastcache'),
			'75' => __('75%', 'fastcache'),
			'80' => __('80%', 'fastcache'),
			'85' => __('85%', 'fastcache'),
			'90' => __('90%', 'fastcache')
		];

		echo Html::_('select', 'img_quality', '70', $aOptions);
	}
	public static function img_resizing_switcher()
	{
		echo Html::_('radio', 'img_resizing_switcher', '0');
	}
	public static function img_resizing()
	{
		$aOptions = [
			'20' => __('20%', 'fastcache'),
			'25' => __('25%', 'fastcache'),
			'30' => __('30%', 'fastcache'),
			'35' => __('35%', 'fastcache'),
			'40' => __('40%', 'fastcache'),
			'45' => __('45%', 'fastcache'),
			'50' => __('50%', 'fastcache'),
			'55' => __('55%', 'fastcache'),
			'60' => __('60%', 'fastcache'),
			'65' => __('65%', 'fastcache'),
			'70' => __('70%', 'fastcache'),
			'75' => __('75%', 'fastcache'),
			'80' => __('80%', 'fastcache'),
			'85' => __('85%', 'fastcache'),
			'90' => __('90%', 'fastcache')
		];

		echo Html::_('select', 'img_resizing', '60', $aOptions);
	}
	public static function img_resizing_minwidth()
	{
		echo Html::_('text', 'img_resizing_minwidth', '300');
	}
	public static function convert_all_images_to_webp()
	{
		echo Html::_('radio', 'convert_all_images_to_webp', '0');

		if (function_exists('imagewebp')) {
			echo '<span data-bs-content="' . __('Your server and PHP version support the conversion to the WebP format', 'fastcache') . '" class="badge bg-success jspeed-checker hasPopover">' .
				'<span class="fas fa-check" aria-hidden="true"></span> ' . __('WebP conversion supported', 'fastcache') . '</span>';
		} else {
			echo '<span data-bs-content="' . __('Your server and PHP version don\'t support the conversion to the WebP format. Contact your hosting provider in order to activate it via the PHP GD library', 'fastcache') . '" class="badge bg-danger jspeed-checker hasPopover">' .
				'<span class="fas fa-exclamation-triangle" aria-hidden="true"></span> ' . __('WebP conversion not supported', 'fastcache') . '</span>';
		}
	}
	public static function convert_all_images_to_avif()
	{
		echo Html::_('radio', 'convert_all_images_to_avif', '0');

		if (version_compare(PHP_VERSION, '8.1', '>=') && function_exists('imageavif')) {
			// Convert a dummy image to check if the size is > 0 bytes
			$imageFileSize = 0;
			$mediaFilePath = FASTCACHE_DIR . 'media/images/';
			$convertedImage = $mediaFilePath . 'output.avif';
			if (!file_exists($convertedImage)) {
				$originalImage = imagecreatefrompng($mediaFilePath . 'dummyimage.png');
				$result = imageavif($originalImage, $convertedImage);
				if ($result) {
					$imageFileSize = filesize($convertedImage);
				}
			} else {
				$imageFileSize = filesize($convertedImage);
			}

			if ($imageFileSize > 0) {
				echo '<span data-bs-content="' . __('Your server and PHP version support the conversion to the AVIF format', 'fastcache') . '" class="badge bg-success jspeed-checker hasPopover">' .
					'<span class="fas fa-check" aria-hidden="true"></span> ' . __('AVIF conversion supported', 'fastcache') . '</span>';
			} else {
				echo '<span data-bs-content="' . __('Your server and PHP version don\'t support the conversion to the AVIF format. Upgrade at least to PHP 8.1 and contact your hosting provider in order to activate it via the PHP GD library', 'fastcache') . '" class="badge bg-danger jspeed-checker hasPopover">' .
					'<span class="fas fa-exclamation-triangle" aria-hidden="true"></span> ' . __('AVIF conversion not supported', 'fastcache') . '</span>';
			}
		} else {
			echo '<span data-bs-content="' . __('Your server and PHP version don\'t support the conversion to the AVIF format. Upgrade at least to PHP 8.1 and contact your hosting provider in order to activate it via the PHP GD library', 'fastcache') . '" class="badge bg-danger jspeed-checker hasPopover">' .
				'<span class="fas fa-exclamation-triangle" aria-hidden="true"></span> ' . __('AVIF conversion not supported', 'fastcache') . '</span>';
		}
	}
	public static function exclude_light_images_safari()
	{
		echo Html::_('radio', 'exclude_light_images_safari', '1');
	}
	public static function optimize_css_background_images()
	{
		echo Html::_('radio', 'optimize_css_background_images', '0');
	}
	public static function optimize_html_background_images()
	{
		echo Html::_('radio', 'optimize_html_background_images', '0');
	}
	public static function img_support_gif()
	{
		echo Html::_('radio', 'img_support_gif', '0');
	}
	public static function webservice_processing()
	{
		echo Html::_('radio', 'webservice_processing', '0');
	}
	public static function hash_images_algo()
	{
		$aOptions = [
			'full' => __('Full', 'fastcache'),
			'partial' => __('Partial', 'fastcache'),
			'none' => __('None', 'fastcache')
		];

		echo Html::_('select', 'hash_images_algo', 'full', $aOptions);
	}
	public static function preserve_cached_images()
	{
		echo Html::_('radio', 'preserve_cached_images', '0');
	}
	public static function img_exts_excluded()
	{
		$aOptions = [
			'jpeg' => __('jpeg', 'fastcache'),
			'jpg' => __('jpg', 'fastcache'),
			'png' => __('png', 'fastcache'),
			'gif' => __('gif', 'fastcache'),
			'bmp' => __('bmp', 'fastcache')
		];
		echo Html::_('multiselect', 'img_exts_excluded', $aOptions, '', 'value');
	}
	public static function img_files_excluded()
	{
		echo Html::_('multiselect', 'img_files_excluded', [], 'none', 'file');
	}
	public static function img_class_excluded()
	{
		echo Html::_('multiselect', 'img_class_excluded', [], 'none', 'file');
	}
	public static function img_menu_excluded()
	{
		echo Html::_('multiselect', 'img_menu_excluded', [], 'none', 'file');
	}
	public static function extractcss_menu_excluded()
	{
		echo Html::_('multiselect', 'extractcss_menu_excluded', [], 'none', 'file');
	}
	public static function img_processing_datasrc()
	{
		echo Html::_('radio', 'img_processing_datasrc', '0');
	}
	public static function img_processing_datacustom()
	{
		echo Html::_('text', 'img_processing_datacustom', '', '');
	}
	public static function img_processing_entity_decode()
	{
		echo Html::_('radio', 'img_processing_entity_decode', '1');
	}
	public static function img_processing_utf8_entity_decode()
	{
		echo Html::_('radio', 'img_processing_utf8_entity_decode', '0');
	}
	public static function purify_string()
	{
		echo Html::_('textarea', 'purify_string', '');
	}
	public static function purify_string_replacement()
	{
		echo Html::_('textarea', 'purify_string_replacement', '');
	}
	public static function img_processing_srcset()
	{
		echo Html::_('radio', 'img_processing_srcset', '0');
	}
	public static function img_processing_srcset_starting_quality()
	{
		$aOptions = [
			'40' => __('40%', 'fastcache'),
			'45' => __('45%', 'fastcache'),
			'50' => __('50%', 'fastcache'),
			'55' => __('55%', 'fastcache'),
			'60' => __('60%', 'fastcache'),
			'65' => __('65%', 'fastcache'),
			'70' => __('70%', 'fastcache'),
			'75' => __('75%', 'fastcache'),
			'80' => __('80%', 'fastcache'),
			'85' => __('85%', 'fastcache'),
			'90' => __('90%', 'fastcache'),
			'95' => __('95%', 'fastcache')
		];

		echo Html::_('select', 'img_processing_srcset_starting_quality', '90', $aOptions);
	}
	public static function img_processing_srcset_quality_decrease_step()
	{
		$aOptions = [
			'5' => __('5%', 'fastcache'),
			'10' => __('10%', 'fastcache'),
			'15' => __('15%', 'fastcache'),
			'20' => __('20%', 'fastcache'),
			'25' => __('25%', 'fastcache')
		];

		echo Html::_('select', 'img_processing_srcset_quality_decrease_step', '15', $aOptions);
	}
	public static function img_processing_srcset_starting_resize()
	{
		$aOptions = [
			'40' => __('40%', 'fastcache'),
			'45' => __('45%', 'fastcache'),
			'50' => __('50%', 'fastcache'),
			'55' => __('55%', 'fastcache'),
			'60' => __('60%', 'fastcache'),
			'65' => __('65%', 'fastcache'),
			'70' => __('70%', 'fastcache'),
			'75' => __('75%', 'fastcache'),
			'80' => __('80%', 'fastcache'),
			'85' => __('85%', 'fastcache'),
			'90' => __('90%', 'fastcache'),
			'95' => __('95%', 'fastcache'),
			'100' => __('100%', 'fastcache')
		];

		echo Html::_('select', 'img_processing_srcset_starting_resize', '100', $aOptions);
	}
	public static function img_processing_srcset_resize_decrease_step()
	{
		$aOptions = [
			'5' => __('5%', 'fastcache'),
			'10' => __('10%', 'fastcache'),
			'15' => __('15%', 'fastcache'),
			'20' => __('20%', 'fastcache'),
			'25' => __('25%', 'fastcache')
		];

		echo Html::_('select', 'img_processing_srcset_resize_decrease_step', '20', $aOptions);
	}
	public static function img_processing_srcset_original_image()
	{
		$aOptions = [
			'-1' => __('Original image', 'fastcache'),
			'0' => __('4x', 'fastcache'),
			'1' => __('3x', 'fastcache'),
			'2' => __('2x', 'fastcache'),
			'3' => __('1x', 'fastcache')
		];

		echo Html::_('select', 'img_processing_srcset_original_image', '0', $aOptions);
	}
	public static function img_processing_srcset_datasrc()
	{
		echo Html::_('radio', 'img_processing_srcset_datasrc', '0');
	}
	public static function fastcache_cdn_enable()
	{
		$aSavedSettings = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);
		$fastcacheEnableChecked = isset($aSavedSettings['fastcache-enable']) ? 'checked="checked"' : '';
		$defaultTTLValue = isset($aSavedSettings['default-ttl']) ? $aSavedSettings['default-ttl'] : FASTCACHE_DEFAULTTTL;
		$fastCacheTextDescription = __('If selected, activates communication with the CDN', 'fastcache');
		if ($fastcacheEnableChecked) {
			$fastCacheTextWarning = '<div class="col-span-3 w-full text-sm notice-fastcache">' . __('The caching system automatically regenerates the cache every "' . $defaultTTLValue . '" seconds or according to the "ttl" value entered in the appropriate fields', 'fastcache') . '</div>';
			$fastCacheTextWarning = sprintf(
				'<div class="col-span-3 w-full text-sm notice-fastcache">%s</div>',
				sprintf(
					__('The caching system automatically regenerates the cache every "%s" seconds or according to the "ttl" value entered in the appropriate fields', 'fastcache'),
					$defaultTTLValue
				)
			);
		} else {
			$fastCacheTextWarning = '';
		}

		$fastCacheCDNEnable = <<<HTML
		<div class="group cursor-pointer" id="attivazionefastcache">
			<input type="checkbox" class="!hidden peer checked" name="fastcache_settings[fastcache-enable]" id="fastcache_settings[fastcache-enable]" $fastcacheEnableChecked value="1">
			<label for="fastcache_settings[fastcache-enable]" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-blue-600 hover:text-gray-600  peer-checked:text-gray-600 hover:bg-gray-50 ">
                <div class="w-full grid grid-cols-[50px_1fr_50px]">
                    <div class="fastcache-enable col-span-3 w-full text-lg font-semibold">FastCache</div>
                    <div>
                        <div class="
                            relative
                            w-11
                            h-6
                            bg-gray-200
                            rounded-full
                            after:content-['']
                            after:absolute
                            after:top-[2px]
                            after:start-[2px]
                            after:bg-white
                            after:border-gray-300
                            after:border
                            after:rounded-full
                            after:w-5
                            after:h-5
                            after:transition-all
                            group-has-[:checked]:bg-blue-600
                            group-has-[:checked]:after:left-[22px]
                       ">
                        </div>
                    </div>
					$fastCacheTextWarning
                </div>
            </label>
		</div>
HTML;

		echo $fastCacheCDNEnable;
	}
	public static function fastcache_cdn_texttoken()
	{
		$aSavedSettings = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);
		$accessTokenInsert = __('Insert here the access token', 'fastcache');
		$accessTokenInstructions = __('You can get the access token in the host.it customer area', 'fastcache');
		$aSavedSettings['text-token'] = isset($aSavedSettings['text-token']) ? $aSavedSettings['text-token'] : '';

		$fastcacheCdnTexttoken = <<<HTML
		<label for="fastcache_settings[text-token]" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 hover:text-gray-600 peer-checked:text-gray-600 hover:bg-gray-50">
                <div class="w-full grid grid-cols-[50px_1fr_50px]">
                    <div>
                    </div>
                    <div></div>
                    <div></div>
                    <div class="fastcache-token col-span-3 w-full text-lg font-semibold">$accessTokenInsert</div>
                    <div class="col-span-3">
                        <input type="text" name="fastcache_settings[text-token]" id="fastcache_settings-text-token" value="{$aSavedSettings['text-token']}" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
                    </div>
                </div>
            </label>
HTML;

		echo $fastcacheCdnTexttoken;
	}


	public static function fastcache_cdn_logdebug()
	{
		$aSavedSettings = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);
		$fastcacheEnableDebugChecked = isset($aSavedSettings['button-checkbox-activate-log']) ? 'checked="checked"' : '';
		$enableLogDebug = __('Enable Log Debug', 'fastcache');
		$enableLogDebugDescription = __('Once activated, the log file inside the folder wp-content/plugins/fastcache/logs/log.txt will be updated', 'fastcache');
		$checkConfiguration = __('Check the configuration!', 'fastcache');
		$settingsCorrect = __('Check that settings are correct', 'fastcache');

		$fastcacheCdnTexttoken = <<<HTML
		<div class="group cursor-pointer" id="attivazionefastcachelogs">
			<input type="checkbox" class="!hidden peer unchecked" name="fastcache_settings[button-checkbox-activate-log]" id="fastcache_settings[button-checkbox-activate-log]" $fastcacheEnableDebugChecked value="1">
			<label for="fastcache_settings[button-checkbox-activate-log]" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-blue-600 hover:text-gray-600  peer-checked:text-gray-600 hover:bg-gray-50 ">
			<div class="w-full grid grid-cols-[50px_1fr_50px]">
			
			<div>
			<div class="
                            relative
                            w-11
                            h-6
                            bg-gray-200
                            rounded-full
                            after:content-['']
                            after:absolute
                            after:top-[2px]
                            after:start-[2px]
                            after:bg-white
                            after:border-gray-300
                            after:border
                            after:rounded-full
                            after:w-5
                            after:h-5
                            after:transition-all
                            group-has-[:checked]:bg-blue-600
                            group-has-[:checked]:after:left-[22px]
                       ">
                       </div>
                       </div>
                </div>
            </label>
        </div>

		<ul>        <li class="group">
		            <label for="fastcache_settings[button-test]" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer  peer-checked:border-blue-600 hover:text-gray-600  peer-checked:text-gray-600 hover:bg-gray-50 ">
		                <div class="w-full grid grid-cols-[50px_1fr_50px]">
		                    <div class="hidden col-span-3 w-full text-lg font-semibold">TEST!</div>
		                    <div class="col-span-3 text-center">
		                        <span id="button-test" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center ">
		                            $checkConfiguration
		                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" fill="none" viewBox="0 0 14 10">
									<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"></path>
								</svg>
                        </span>
                    </div>
                    <div class="col-span-3 w-full text-sm text-center">$settingsCorrect</div>
                    <div id="outputreporthtml" class="col-span-3 w-full"></div>
                </div>
            </label>
        </li></ul>
HTML;

		echo $fastcacheCdnTexttoken;
	}


	public static function fastcache_cdn_helpSection()
	{
		$faqText = __('Frequently Asked Question', 'fastcache');
		$html = <<<HTML
<section id="help" class="togglerTabs">
	<ul>--lis--
	</ul>
</section>
HTML;
		function faqs()
		{
			require_once(FASTCACHE_DIR . 'src/Host/admin/partials/faq.php');
			$t = file_get_contents(FASTCACHE_DIR . 'src/Host/admin/partials/faq.html');
			$html = "";
			$fqt = new \faqText();
			$faqsArr = $fqt->faqs();
			foreach ($faqsArr as $k => $v) {
				$t2 = str_replace("--name--", $v['name'], $t);
				$t2 = str_replace("--question--", $v['question'], $t2);
				$t2 = str_replace("--answer--", $v['answer'], $t2);
				$html .= $t2;
			}
			return $html;
		}

		$html = str_replace("--lis--", faqs(), $html);
		echo $html;
	}

	public static function fastcache_cdn_enable_ttl()
	{
		echo Html::_('radio', 'enable-ttl', '0');
	}

	public static function fastcache_cdn_default_ttl()
	{
		echo Html::_('text', 'default-ttl', '300', '');
	}
	public static function fastcache_cdn_types_ttl()
	{
		echo Html::fieldPostTypes('ttl-post-types');
	}
	public static function fastcache_url_exclusion()
	{
		echo Html::_('multiselect', 'url_exclusion', [], 'url', 'file');
	}
	public static function fastcache_posttype_exclusion()
	{
		$aOptions = [
			'post' => 'post',
			'page' => 'page'
		];

		echo Html::_('multiselect', 'posttype_exclusion', $aOptions, '', 'value');
	}
	public static function enable_instant_page()
	{
		echo Html::_('radio', 'enable_instant_page', '0');
	}
	public static function fastcache_cdn_default_ttl_notfound_set()
	{
		echo Html::_('radio', 'fastcache_cdn_default_ttl_notfound_set', '0');
	}
	public static function instant_page_delay()
	{
		$aOptions = [
			'fast' => __('Fast delay', 'fastcache'),
			'slow' => __('Slow delay', 'fastcache')
		];

		echo Html::_('select', 'instant_page_delay', 'fast', $aOptions);
	}
	public static function add_custom_js_code()
	{
		echo Html::_('radio', 'add_custom_js_code', '0');
	}
	public static function custom_js_code()
	{
		echo Html::_('textarea', 'custom_js_code', '');
	}
	public static function add_custom_css_code()
	{
		echo Html::_('radio', 'add_custom_css_code', '0');
	}
	public static function custom_css_code()
	{
		echo Html::_('textarea', 'custom_css_code', '');
	}
	public static function base64encode()
	{
		echo Html::_('radio', 'base64encode', '0');
	}
	public static function lazyload_html_enable()
	{
		echo Html::_('radio', 'lazyload_html_enable', '0');
	}
	public static function lazyload_html_css_selector()
	{
		echo Html::_('text', 'lazyload_html_css_selector', '', '100');
	}
	public static function lazyload_method()
	{
		$aOptions = [
			'scroll' => __('Scroll', 'fastcache'),
			'delay' => __('Delay', 'fastcache')
		];

		echo Html::_('select', 'lazyload_method', 'scroll', $aOptions);
	}
	public static function lazyload_html_delay()
	{
		$aOptions = [
			'100' => __('100 ms', 'fastcache'),
			'200' => __('200 ms', 'fastcache'),
			'300' => __('300 ms', 'fastcache'),
			'500' => __('500 ms', 'fastcache'),
			'600' => __('600 ms', 'fastcache'),
			'700' => __('700 ms', 'fastcache'),
			'800' => __('800 ms', 'fastcache'),
			'900' => __('900 ms', 'fastcache'),
			'1000' => __('1000 ms', 'fastcache'),
			'1500' => __('1500 ms', 'fastcache'),
			'2000' => __('2000 ms', 'fastcache'),
			'2500' => __('2500 ms', 'fastcache'),
			'3000' => __('3000 ms', 'fastcache'),
			'3500' => __('3500 ms', 'fastcache'),
			'4000' => __('4000 ms', 'fastcache'),
			'4500' => __('4500 ms', 'fastcache'),
			'5000' => __('5000 ms', 'fastcache'),
			'5500' => __('5500 ms', 'fastcache'),
			'6000' => __('6000 ms', 'fastcache')
		];

		echo Html::_('select', 'lazyload_html_delay', '3000', $aOptions);
	}
	public static function lazyload_html_use_important_override()
	{
		echo Html::_('radio', 'lazyload_html_use_important_override', '0');
	}

	public static function download_config()
	{
		$url = add_query_arg([
			'page' => 'fastcache',
			'task' => 'downloadconfig',
			'_wpnonce' => wp_create_nonce('downloadconfig')
		], admin_url('options-general.php'));

		echo '<a href="' . esc_url($url) . '" class="btn btn-primary"><span class="fas fa-download"></span> ' . __('Download', 'fastcache') . '</a>';
	}

	public static function upload_config()
	{
		$nonce = wp_create_nonce('uploadconfig');
		$uploadUrl = admin_url('options-general.php?page=fastcache&task=uploadconfig&_wpnonce=' . $nonce);
		$confirmMsg = __('Are you sure? This will overwrite all current settings.', 'fastcache');
		$selectMsg = __('Please select a JSON file first.', 'fastcache');

		echo <<<HTML
		<div class="upload-config-wrapper" style="max-width: 400px;">
			<div class="input-group">
				<input type="file" id="fc_config_file_input" class="form-control" accept=".json">
				<button type="button" id="fc_upload_config_btn" class="btn btn-warning">
					<span class="fas fa-upload"></span>
				</button>
			</div>
		</div>
		<script>
		(function($){
			$(document).ready(function(){
				$(document).off('click', '#fc_upload_config_btn').on('click', '#fc_upload_config_btn', function(){
					var file_data = $('#fc_config_file_input').prop('files')[0];
					if(!file_data) {
						alert('{$selectMsg}');
						return;
					}
					if(!confirm('{$confirmMsg}')) {
						return;
					}
					
					var formData = new FormData();
					formData.append('fc_config_file', file_data);
					
					$.ajax({
						url: '{$uploadUrl}',
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function(response){
							window.location.reload();
						},
						error: function(){
							alert('Upload failed');
						}
					});
				});
			});
		})(jQuery);
		</script>
HTML;
	}

	public static function object_cache_enable()
	{
		echo Html::_('radio', 'object_cache_enable', '0');
	}

	public static function object_cache_backend()
	{
		$aOptions = [
			'auto' => __('Auto-detect (Redis -> Memcached -> File)', 'fastcache'),
			'redis' => __('Redis', 'fastcache'),
			'memcached' => __('Memcached', 'fastcache'),
			'file' => __('File System', 'fastcache')
		];
		echo Html::_('select', 'object_cache_backend', 'auto', $aOptions);
	}

	public static function object_cache_default_ttl()
	{
		echo Html::_('text', 'object_cache_default_ttl', '0', '10');
	}

	public static function object_cache_key_prefix()
	{
		echo Html::_('text', 'object_cache_key_prefix', 'fc_', '20');
	}

	public static function object_cache_stats_enable()
	{
		echo Html::_('radio', 'object_cache_stats_enable', '0');
	}

	public static function object_cache_redis_host()
	{
		echo Html::_('text', 'object_cache_redis_host', '127.0.0.1', '50');
	}

	public static function object_cache_redis_port()
	{
		echo Html::_('text', 'object_cache_redis_port', '6379', '6');
	}

	public static function object_cache_redis_password()
	{
		echo Html::_('text', 'object_cache_redis_password', '', '50');
	}

	public static function object_cache_redis_database()
	{
		echo Html::_('text', 'object_cache_redis_database', '0', '3');
		?>
		<button type="button" class="button button-secondary" id="fc-test-redis">
			<span class="dashicons dashicons-yes"></span><?php _e('Test Connection', 'fastcache'); ?>
		</button>
		<span id="fc-redis-test-result"></span>
		<?php
	}

	public static function object_cache_memcached_host()
	{
		echo Html::_('text', 'object_cache_memcached_host', '127.0.0.1', '50');
	}

	public static function object_cache_memcached_port()
	{
		echo Html::_('text', 'object_cache_memcached_port', '11211', '6');
		?>
		<button type="button" class="button button-secondary" id="fc-test-memcache">
			<span class="dashicons dashicons-yes"></span><?php _e('Test Connection', 'fastcache'); ?>
		</button>
		<span id="fc-memcache-test-result"></span>
		<?php
	}

	public static function object_cache_non_persistent_groups()
	{
		echo Html::_('textarea', 'object_cache_non_persistent_groups', "counts\nplugins\nthemes\nwc_session_data\nsession-tokens\noptions\nalloptions\nwc_cache\nproduct");
	}


	public static function object_cache_dropin_status()
	{
		if (!class_exists('\FastCache\ObjectCache\DropinManager')) {
			echo '<span class="badge bg-warning">Object Cache module not loaded.</span>';
			return;
		}

		$status = \FastCache\ObjectCache\DropinManager::getStatus();
		$availableBackends = \FastCache\ObjectCache\BackendDetector::getAvailable();
		$settings = get_option('fastcache_settings', []);
		$detectedBackendName = \FastCache\ObjectCache\BackendDetector::getDetectedBackendName($settings);

		$ajaxUrl = admin_url('admin-ajax.php');
		$nonce = wp_create_nonce('fastcache_object_cache_nonce');

		wp_enqueue_script('jquery');

		?>
		<div id="fastcache-object-cache-status-container" class="group cursor-auto border rounded-lg p-5">

			<div style="margin-bottom: 20px;">
				<div id="fastcache-object-cache-status-subcontainer">
					<h4 style="margin-top:0;"><strong><?php _e('Drop-in File Status:', 'fastcache'); ?></strong></h4>
					<?php if (!$status['dropin_exists']): ?>
						<span class="badge bg-secondary badge-status-drop-in"><?php _e('Not Installed', 'fastcache'); ?></span>
					<?php elseif ($status['is_ours']): ?>
						<span class="badge bg-success badge-status-drop-in"><?php _e('Installed & Active', 'fastcache'); ?></span>
					<?php elseif ($status['is_foreign']): ?>
						<span class="badge bg-warning text-dark badge-status-drop-in"><?php _e('Foreign Drop-in Detected', 'fastcache'); ?></span>
					<?php endif; ?>
				</div>
				
				<?php if (!$status['dropin_exists']): ?>
					<p class="description"><?php _e('The object-cache.php drop-in is not installed.', 'fastcache'); ?></p>
				<?php elseif ($status['is_ours']): ?>
					<p class="description">
						<?php _e('The FastCache object-cache.php drop-in is installed successfully.', 'fastcache'); ?>
					</p>
				<?php elseif ($status['is_foreign']): ?>
					<p class="description">
						<?php printf(__('Another plugin (%s) has installed the object-cache.php drop-in.', 'fastcache'), esc_html($status['foreign_info']['name'] ?? 'Unknown')); ?>
						<?php _e('You must disable or uninstall that plugin\'s object cache before FastCache can manage the drop-in.', 'fastcache'); ?>
					</p>
				<?php endif; ?>
				
				<?php if ($status['is_ours'] && class_exists('\FastCache\ObjectCache\ObjectCache')): ?>
					<?php $isExcluded = \FastCache\ObjectCache\ObjectCache::getInstance()->isExcludedRequest(); ?>
					<div style="margin-top: 10px;">
						<strong><?php _e('Current Request Status:', 'fastcache'); ?></strong>
						<?php if ($isExcluded): ?>
							<span class="badge bg-info badge-drop-in"
								style="background-color: #0dcaf0; color: #000; padding: 2px 8px; border-radius: 4px; font-size: 0.8em;"><?php _e('Exclusion Active (No Persistent Cache)', 'fastcache'); ?></span>
							<p class="description" style="font-size: 0.85em; margin-top: 5px; color: #666;">
								<?php _e('Your session is currently excluded from persistent object caching (e.g., because you are an admin or logged in).', 'fastcache'); ?>
							</p>
						<?php else: ?>
							<span class="badge bg-success"
								style="background-color: #198754; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.8em;"><?php _e('Caching Enabled', 'fastcache'); ?></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if (!$status['wp_content_writable']): ?>
					<div class="notice notice-error inline">
						<p><?php _e('Warning: Your wp-content directory is not writable. Automatic drop-in management might fail.', 'fastcache'); ?>
						</p>
					</div>
				<?php endif; ?>
			</div>

			<div style="margin-bottom: 20px;">
				<h4><strong><?php _e('Detected Backend:', 'fastcache'); ?></strong></h4>
				<ul style="list-style-type: none; padding-left: 0;">
					<?php foreach ($availableBackends as $key => $backend): ?>
						<li style="margin-bottom: 5px;">
							<?php if ($key === $detectedBackendName): ?>
								<span class="badge bg-success badge-active-backend" title="Currently Detected">✓</span>
								<strong><?php echo esc_html($backend['name']); ?></strong> (<?php _e('Active', 'fastcache'); ?>)
							<?php else: ?>
								<?php if ($backend['available']): ?>
									<span style="color: green;">✓ <?php echo esc_html($backend['name']); ?>
										(<?php _e('Available', 'fastcache'); ?>)</span>
								<?php else: ?>
									<span style="color: red;">✗ <?php echo esc_html($backend['name']); ?>
										(<?php _e('Not Available', 'fastcache'); ?>)</span>
								<?php endif; ?>
							<?php endif; ?>
							<span style="color: #666; font-size: 0.9em;">- <?php echo esc_html($backend['description']); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<?php
			// Check if logging is enabled first — both stats and log depend on it
			$activityLogEnabled = isset($settings['object_cache_logging_enable']) ? (bool)$settings['object_cache_logging_enable'] : false;
			?>
			<?php if (isset($settings['object_cache_stats_enable']) && $settings['object_cache_stats_enable'] && $status['is_ours'] && class_exists('\FastCache\ObjectCache\ObjectCache')): ?>
				<?php
				$ocInstance = \FastCache\ObjectCache\ObjectCache::getInstance();
				$stats = $ocInstance->getStats();
				$globalStats = $ocInstance->getGlobalStats();
				$isExcluded = $ocInstance->isExcludedRequest();
				$totalRequests = $stats['hits'] + $stats['misses'];
				$hitRatio = $totalRequests > 0 ? round(($stats['hits'] / $totalRequests) * 100, 2) : 0;
				?>
				<div class="fc-cache-status-container">
					<!-- SEZIONE 1: Stats Globali -->
					<div class="fc-status-section">
						<h3 class="fc-status-section-title"><?php _e('Stato Globale Object Cache (su disco)', 'fastcache'); ?></h3>
						<div class="fc-stats-row">
							<div class="fc-stat-item">
								<span class="fc-stat-item-label"><?php _e('File cache totali:', 'fastcache'); ?></span>
								<span id="fc-oc-global-count" class="fc-stat-item-value"><?php echo $globalStats['count']; ?></span>
							</div>
							<div class="fc-stat-item">
								<span class="fc-stat-item-label"><?php _e('Dimensione cache totale:', 'fastcache'); ?></span>
								<span id="fc-oc-global-size" class="fc-stat-item-value"><?php echo $globalStats['size']; ?></span>
							</div>
						</div>
					</div>

					<!-- SEZIONE 2: Stats da log (admin escluso) o richiesta corrente — SOLO se logging abilitato -->
					<?php if ($activityLogEnabled): ?>
					<div class="fc-status-section">
						<?php if ($isExcluded): ?>
							<?php
							$globalHits = 0; $globalMisses = 0; $logIsPartial = false;
							$logFile = WP_CONTENT_DIR . '/cache/fastcache/object-cache.log';
							if (file_exists($logFile)) {
								// Legge al massimo 5MB dall'ultima parte del file per evitare memory exhaustion
								$maxRead  = 5 * 1024 * 1024;
								$fileSize = @filesize($logFile);
								if ($fileSize !== false && $fileSize > $maxRead) {
									$logContent  = @file_get_contents($logFile, false, null, $fileSize - $maxRead, $maxRead);
									$logIsPartial = true;
								} else {
									$logContent = @file_get_contents($logFile);
								}
								if ($logContent) {
									$globalHits   = substr_count($logContent, 'HIT_PERSISTENT') + substr_count($logContent, 'HIT_MEMORY');
									$globalMisses = substr_count($logContent, 'MISS |') + substr_count($logContent, 'MISS_EXCLUDED');
									unset($logContent); // libera subito la memoria
								}
							}
							$totalOps = $globalHits + $globalMisses;
							$globalHitRate = $totalOps > 0 ? round(($globalHits / $totalOps) * 100, 2) : 0;
							?>
							<h3 class="fc-status-section-title"><?php _e('Global Statistics (Log Analysis)', 'fastcache'); ?></h3>
							<div class="fc-stats-row">
								<div class="fc-stat-item">
									<span class="fc-stat-item-label"><?php _e('Total Hits:', 'fastcache'); ?></span>
									<span id="fc-oc-local-hits" class="fc-stat-item-value" style="color:#198754;font-weight:bold;"><?php echo number_format($globalHits); ?></span>
								</div>
								<div class="fc-stat-item">
									<span class="fc-stat-item-label"><?php _e('Total Misses:', 'fastcache'); ?></span>
									<span id="fc-oc-local-misses" class="fc-stat-item-value" style="color:#dc3545;font-weight:bold;"><?php echo number_format($globalMisses); ?></span>
								</div>
								<div class="fc-stat-item">
									<span class="fc-stat-item-label"><?php _e('Hit Rate:', 'fastcache'); ?></span>
									<span id="fc-oc-local-ratio" class="fc-stat-item-value" style="color:#029aff;font-weight:bold;"><?php echo $globalHitRate; ?>%</span>
								</div>
							</div>
						<?php else: ?>
							<h3 class="fc-status-section-title"><?php _e('Current Request Statistics', 'fastcache'); ?></h3>
							<div class="fc-stats-row">
								<div class="fc-stat-item">
									<span class="fc-stat-item-label">Hit:</span>
									<span id="fc-oc-local-hits" class="fc-stat-item-value"><?php echo number_format($stats['hits']); ?></span>
								</div>
								<div class="fc-stat-item">
									<span class="fc-stat-item-label">Miss:</span>
									<span id="fc-oc-local-misses" class="fc-stat-item-value"><?php echo number_format($stats['misses']); ?></span>
								</div>
								<div class="fc-stat-item">
									<span class="fc-stat-item-label">Hit rate:</span>
									<span id="fc-oc-local-ratio" class="fc-stat-item-value"><?php echo $hitRatio; ?>%</span>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
			<?php
			$logFile = WP_CONTENT_DIR . '/cache/fastcache/object-cache.log';
			if ($activityLogEnabled && file_exists($logFile)):
				$lines2 = [];
				$fp = @fopen($logFile, 'r');
				if ($fp) {
					// Legge solo gli ultimi 50KB per evitare memory exhaustion su file grandi
					$chunkSize = 50 * 1024;
					@fseek($fp, 0, SEEK_END);
					$fileEnd = ftell($fp);
					$offset  = max(0, $fileEnd - $chunkSize);
					@fseek($fp, $offset);
					$chunk = @fread($fp, $chunkSize);
					fclose($fp);
					if ($chunk) {
						$allLines = explode("\n", $chunk);
						if ($offset > 0) { array_shift($allLines); } // scarta eventuale prima riga incompleta
						$lines2 = array_map('trim', $allLines);
						unset($chunk, $allLines);
					}
				}
				$recentLines = array_slice($lines2, -100);
			?>
			<div style="margin-top:20px;padding-top:15px;border-top:1px solid #ddd;">
				<h4 style="margin-top:0;font-weight:700;font-size:13px;"><?php _e('Activity Log (last 100 entries)', 'fastcache'); ?></h4>
				<?php if (!empty($recentLines)): ?>
				<div id="fc-oc-activity-log" style="background:#f8f9fa;border:1px solid #dee2e6;border-radius:4px;padding:10px;max-height:200px;overflow-y:auto;font-family:monospace;font-size:11px;line-height:1.4;">
					<?php foreach (array_reverse($recentLines) as $line): if (empty($line)) continue;
						$color = '#666';
						if (strpos($line, 'HIT') !== false)     $color = '#198754';
						if (strpos($line, 'MISS') !== false)    $color = '#dc3545';
						if (strpos($line, 'SET') !== false)     $color = '#029aff';
						if (strpos($line, 'DELETE') !== false)  $color = '#fd7e14';
						if (strpos($line, 'FLUSH') !== false || strpos($line, 'GARBAGE') !== false) $color = '#6f42c1';
					?>
					<div style="color:<?php echo $color; ?>"><?php echo esc_html($line); ?></div>
					<?php endforeach; ?>
				</div>
				<?php else: ?>
					<div id="fc-oc-activity-log" style="color:#999;font-size:12px;"><?php _e('No activity recorded yet.', 'fastcache'); ?></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

				</div>

			<?php endif; ?>


			<div style="margin-top: 15px;">
				<button type="button" class="button button-secondary" id="fc-flush-object-cache" <?php echo (!$status['is_ours'] ? 'disabled' : ''); ?>>
					<span class="fas fa-trash-alt"></span> <?php _e('Flush Object Cache', 'fastcache'); ?>
				</button>
				<span id="fc-oc-spinner" class="spinner"></span>
				<span id="fc-oc-msg" style="margin-left: 10px;"></span>
			</div>
		</div>

		<script>
			jQuery(document).ready(function ($) {
				$('#fc-flush-object-cache').on('click', function (e) {
					e.preventDefault();
					if (!confirm('<?php _e('Are you sure you want to flush the object cache?', 'fastcache'); ?>')) {
						return;
					}

					var $btn = $(this);
					var $spinner = $('#fc-oc-spinner');
					var $msg = $('#fc-oc-msg');

					$btn.prop('disabled', true);
					$spinner.addClass('is-active');
					$msg.text('').removeClass('text-success text-danger');

					$.ajax({
						url: '<?php echo esc_url($ajaxUrl); ?>',
						type: 'POST',
						data: {
							action: 'fastcache_object_cache_action',
							cache_action: 'flush',
							nonce: '<?php echo esc_attr($nonce); ?>'
						},
						success: function (response) {
							if (response.success) {
								$msg.text(response.data).addClass('text-success');
								// Reset stats in the UI immediately
								$('#fc-oc-global-count').text('0');
								$('#fc-oc-global-size').text('0 B');
								$('#fc-oc-local-hits').text('0');
								$('#fc-oc-local-misses').text('0');
								$('#fc-oc-local-ratio').text('0%');
								// Clear the activity log panel immediately
								var $logWrap = $('#fc-oc-activity-log');
								if ($logWrap.length) {
									$logWrap.html('<em style="color:#999;font-size:11px;"><?php _e('Log cleared.', 'fastcache'); ?></em>');
								}
							} else {
								$msg.text(response.data || 'Error').addClass('text-danger');
							}
						},
						error: function () {
							$msg.text('AJAX Request Failed').addClass('text-danger');
						},
						complete: function () {
							$btn.prop('disabled', false);
							$spinner.removeClass('is-active');
							setTimeout(function () {
								$msg.fadeOut(function () {
									$(this).text('').removeClass('text-success text-danger').show();
								});
							}, 3000);
						}
					});
				});

				// Redis connection test
				$('#fc-test-redis').on('click', function (e) {
					e.preventDefault();
					var $btn = $(this);
					var $result = $('#fc-redis-test-result');
					$btn.prop('disabled', true);
					$result.text('<?php _e('Testing...', 'fastcache'); ?>').css('color', '#999');

					$.ajax({
						url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
						type: 'POST',
						data: {
							action: 'fastcache_test_redis',
							nonce: '<?php echo wp_create_nonce('fastcache_test_redis'); ?>'
						},
						success: function (response) {
							if (response.success) {
								$result.text('✓ ' + response.data).css('color', '#198754');
							} else {
								$result.text('✗ ' + response.data).css('color', '#dc3545');
							}
						},
						error: function (xhr) {
							$result.text('✗ Connection failed').css('color', '#dc3545');
						},
						complete: function () {
							$btn.prop('disabled', false);
						}
					});
				});

				// Memcache connection test
				$('#fc-test-memcache').on('click', function (e) {
					e.preventDefault();
					var $btn = $(this);
					var $result = $('#fc-memcache-test-result');
					$btn.prop('disabled', true);
					$result.text('<?php _e('Testing...', 'fastcache'); ?>').css('color', '#999');

					$.ajax({
						url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
						type: 'POST',
						data: {
							action: 'fastcache_test_memcache',
							nonce: '<?php echo wp_create_nonce('fastcache_test_memcache'); ?>'
						},
						success: function (response) {
							if (response.success) {
								$result.text('✓ ' + response.data).css('color', '#198754');
							} else {
								$result.text('✗ ' + response.data).css('color', '#dc3545');
							}
						},
						error: function (xhr) {
							$result.text('✗ Connection failed').css('color', '#dc3545');
						},
						complete: function () {
							$btn.prop('disabled', false);
						}
					});
				});
			});
		</script>
		<?php
	}
}
