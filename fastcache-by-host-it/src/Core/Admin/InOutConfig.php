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

namespace FastCache\Core\Admin;

// No direct access
defined('_FASTCACHE_EXEC') or die('Restricted access');

/**
 * Class InOutConfig
 * Handles the export and import of plugin configurations.
 */
class InOutConfig
{
    /**
     * Export the current settings as a JSON file.
     *
     * @return void
     */
    public static function export()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Not authorized'));
        }

        $settings = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);

        if ($settings === false) {
            $settings = [];
        }

        $filename = 'fastcache-config-' . date('Y-m-d-H-i-s') . '.json';
        $json = json_encode($settings, JSON_PRETTY_PRINT);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json));

        echo $json;
        exit;
    }

    /**
     * Import settings from a JSON string.
     *
     * @param string $jsonContent The JSON content to import.
     * @return bool True on success, false on failure.
     */
    public static function import($jsonContent)
    {
        if (!current_user_can('manage_options')) {
            return false;
        }

        $settings = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($settings)) {
            return false;
        }

        if (!self::validate($settings)) {
            return false;
        }

        return update_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, $settings);
    }

    /**
     * Validate the settings array to ensure all required primary keys are present.
     *
     * @param array $settings The settings array to validate.
     * @return bool True if valid, false otherwise.
     */
    private static function validate($settings)
    {
        $requiredKeys = [
            'combine_files_enable', 'css', 'javascript', 'css_minify', 'js_minify', 'html_minify',
            'html_minify_level', 'cache_lifetime', 'bottom_js', 'loadAsynchronous', 'font_display_swap',
            'defer_combined_styles', 'add_custom_js_code', 'custom_js_code', 'add_custom_css_code',
            'custom_css_code', 'cache_enable', 'htaccess_cache_enable', 'page_cache_lifetime',
            'pro_cache_platform', 'delete_all_cache', 'enable_instant_page', 'instant_page_delay',
            'lazyload_enable', 'lazyload_autosize', 'pro_lazyload_effects', 'pro_lazyload_iframe',
            'pro_lazyload_bgimages', 'pro_lazyload_audiovideo', 'disableCoreLazyload',
            'lazyload_html_enable', 'lazyload_html_css_selector', 'lazyload_method',
            'lazyload_html_delay', 'lazyload_html_use_important_override', 'csg_enable',
            'csg_drection', 'csg_wrap_images', 'http2_push_enable', 'pro_http2_exclude_deferred',
            'pro_http2_push_cdn', 'lightimgs_status', 'img_processing_minwidth', 'img_quality',
            'img_resizing_switcher', 'img_resizing', 'img_resizing_minwidth',
            'convert_all_images_to_webp', 'convert_all_images_to_avif', 'exclude_light_images_safari',
            'optimize_css_background_images', 'optimize_html_background_images', 'img_support_gif',
            'webservice_processing', 'hash_images_algo', 'preserve_cached_images',
            'img_processing_datasrc', 'img_processing_datacustom', 'img_attributes_enable',
            'use_simplehtmldom', 'img_processing_entity_decode', 'img_processing_utf8_entity_decode',
            'purify_string', 'purify_string_replacement', 'img_processing_simplehtmldom_entity_decode',
            'img_processing_srcset', 'img_processing_srcset_starting_quality',
            'img_processing_srcset_quality_decrease_step', 'img_processing_srcset_starting_resize',
            'img_processing_srcset_resize_decrease_step', 'img_processing_srcset_original_image',
            'img_processing_srcset_datasrc', 'optimizeCssDelivery_enable', 'optimizeCssDelivery',
            'pro_remove_unused_css', 'optimizeCssDeliveryExcludeFontface', 'pro_reduce_dom',
            'clear_server_cache', 'base64encode', 'debug', 'order_plugin', 'try_catch',
            'includeAllExtensions', 'replaceImports', 'phpAndExternal', 'inlineStyle',
            'inlineScripts', 'excludeAllStyles', 'excludeAllScripts', 'disable_logged_in_users',
            'text-token', 'enable-ttl', 'fastcache_cdn_default_ttl_notfound_set', 'default-ttl',
            'ttl-post-types', 'rangeSlider', 'pagespeedtest_domain_url', 'google_pagespeed_api_key',
            'hidden_containsgf', 'hidden_api_secret'
        ];

        foreach ($requiredKeys as $key) {
            if (!isset($settings[$key])) {
                return false;
            }
        }

        return true;
    }
}
