<?php

/**
 * Discovers WordPress pages that list posts (Divi Blog/TB, Query Loop, etc.)
 * so related purge can invalidate them automatically.
 *
 * @package FastCache
 */

namespace FastCache\Core;

/**
 * Maintains an index of listing page IDs.
 *
 * Primary source: runtime observation when a page is stored in page cache
 * (works for Divi Theme Builder pages that have no listing shortcode in post_content).
 * Secondary source: lightweight DB scan for known content markers (bootstrap).
 */
class ListingPagesIndex
{
	const OPTION_KEY = 'fastcache_listing_page_ids';
	const OPTION_SEEDED = 'fastcache_listing_pages_seeded';

	/**
	 * Whether auto listing purge is enabled in settings (default: on).
	 *
	 * @return bool
	 */
	public static function isEnabled()
	{
		$options = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS, array());
		if (!is_array($options) || !array_key_exists('related_purge_auto_listing', $options)) {
			return true;
		}
		return (string) $options['related_purge_auto_listing'] === '1';
	}

	/**
	 * Heuristic: HTML of a page looks like a multi-post listing.
	 *
	 * @param string $html
	 * @return bool
	 */
	public static function htmlLooksLikePostListing($html)
	{
		if (!is_string($html) || strlen($html) < 200) {
			return false;
		}

		// Divi Blog / Theme Builder post modules (rossiof.com/necrologi/)
		if (substr_count($html, 'et_pb_post') >= 1) {
			return true;
		}
		// Gutenberg Query Loop / post template
		if (substr_count($html, 'wp-block-post') >= 2) {
			return true;
		}
		// Elementor posts widget
		if (substr_count($html, 'elementor-post') >= 2) {
			return true;
		}
		// Generic WP loop markers (require several to avoid false positives)
		if (substr_count($html, 'type-post') >= 3) {
			return true;
		}

		return false;
	}

	/**
	 * Register the current front-end page if it looks like a post listing.
	 *
	 * @param string $html Cached page HTML.
	 * @return void
	 */
	public static function maybeRegisterFromHtml($html)
	{
		if (!self::isEnabled()) {
			return;
		}
		if (!function_exists('is_page') || !is_page()) {
			return;
		}
		// Skip posts page / front page — already in related purge via addHomeRootToPurge.
		if (function_exists('is_front_page') && is_front_page()) {
			return;
		}
		if (function_exists('is_home') && is_home()) {
			return;
		}
		if (!self::htmlLooksLikePostListing($html)) {
			return;
		}

		$pageId = (int) get_queried_object_id();
		if ($pageId <= 0) {
			return;
		}

		self::addPageId($pageId);
	}

	/**
	 * @param int $pageId
	 * @return void
	 */
	public static function addPageId($pageId)
	{
		$pageId = (int) $pageId;
		if ($pageId <= 0) {
			return;
		}
		$ids = self::getPageIds();
		if (in_array($pageId, $ids, true)) {
			return;
		}
		$ids[] = $pageId;
		$ids = array_values(array_unique(array_map('intval', $ids)));
		update_option(self::OPTION_KEY, $ids, false);
	}

	/**
	 * @param int $pageId
	 * @return void
	 */
	public static function removePageId($pageId)
	{
		$pageId = (int) $pageId;
		$ids = array_values(array_filter(self::getPageIds(), function ($id) use ($pageId) {
			return (int) $id !== $pageId;
		}));
		update_option(self::OPTION_KEY, $ids, false);
	}

	/**
	 * @return int[]
	 */
	public static function getPageIds()
	{
		$ids = get_option(self::OPTION_KEY, array());
		if (!is_array($ids)) {
			return array();
		}
		return array_values(array_unique(array_map('intval', $ids)));
	}

	/**
	 * Absolute permalinks for indexed listing pages (published only).
	 *
	 * @return string[]
	 */
	public static function getListingUrls()
	{
		if (!self::isEnabled()) {
			return array();
		}

		self::ensureSeededFromDatabase();

		$urls = array();
		foreach (self::getPageIds() as $pageId) {
			if (get_post_status($pageId) !== 'publish') {
				continue;
			}
			if (get_post_type($pageId) !== 'page') {
				continue;
			}
			$link = get_permalink($pageId);
			if (is_string($link) && $link !== '') {
				$urls[] = $link;
			}
		}

		/**
		 * Filter automatically discovered listing page URLs.
		 *
		 * @param string[] $urls
		 */
		$urls = apply_filters('fastcache_auto_listing_urls', $urls);
		return is_array($urls) ? $urls : array();
	}

	/**
	 * One-time / lazy DB seed for pages whose content contains known listing markers.
	 * Does not cover Theme Builder-only layouts (those are learned at cache-store time).
	 *
	 * @return void
	 */
	public static function ensureSeededFromDatabase()
	{
		if (get_option(self::OPTION_SEEDED)) {
			return;
		}

		global $wpdb;
		if (!isset($wpdb->posts)) {
			return;
		}

		$patterns = array(
			'%et_pb_blog%',
			'%et_pb_fullwidth_post_slider%',
			'%et_pb_post_slider%',
			'%<!-- wp:query%',
			'%wp:latest-posts%',
			'%wp:query%',
		);

		$ids = self::getPageIds();
		foreach ($patterns as $like) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$rows = $wpdb->get_col($wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish' AND post_content LIKE %s LIMIT 100",
				$like
			));
			if (is_array($rows)) {
				foreach ($rows as $id) {
					$ids[] = (int) $id;
				}
			}
		}

		// Elementor posts widget stored in post meta
		if (!empty($wpdb->postmeta)) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$elRows = $wpdb->get_col(
				"SELECT DISTINCT post_id FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE p.post_type = 'page' AND p.post_status = 'publish'
				AND pm.meta_key = '_elementor_data'
				AND pm.meta_value LIKE '%\"widgetType\":\"posts\"%'
				LIMIT 100"
			);
			if (is_array($elRows)) {
				foreach ($elRows as $id) {
					$ids[] = (int) $id;
				}
			}
		}

		$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
		update_option(self::OPTION_KEY, $ids, false);
		update_option(self::OPTION_SEEDED, 1, false);
	}

	/**
	 * Drop a page from the index when it is deleted/trashed or no longer a listing.
	 *
	 * @param int $postId
	 * @return void
	 */
	public static function onPageStatusChange($postId)
	{
		if (get_post_type($postId) !== 'page') {
			return;
		}
		$status = get_post_status($postId);
		if ($status !== 'publish') {
			self::removePageId($postId);
		}
	}
}
