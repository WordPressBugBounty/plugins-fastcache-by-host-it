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

defined ( '_WP_EXEC' ) or die ( 'Restricted access' );

use FastCache\Core\Exception;
use FastCache\Platform\Uri;
use FastCache\Core\Interfaces\Cache as CacheInterface;

class Cache implements CacheInterface {
	protected static $wp_filesystem;

	/**
	 *
	 * @param string $id
	 * @param callable $function
	 * @param array $args
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function getCallbackCache($id, $function, $args) {
		$wp_filesystem = self::getWpFileSystem ();

		if ($wp_filesystem === false) {
			return false;
		}

		$file = self::_getFileName ( $id );

		if (! self::getCache ( $id, true )) {
			$contents = call_user_func_array ( $function, $args );

			return self::saveCache ( $contents, $id );
		}

		return self::_getCacheFile ( $file, $wp_filesystem, false );
	}

	/**
	 *
	 * @return \WP_Filesystem_Base|false
	 * @throws Exception
	 */
	public static function getWpFileSystem() {
		if (! isset ( self::$wp_filesystem )) {
			// Set the permission constants if not already set.
			if (! defined ( 'FS_CHMOD_DIR' )) {
				define ( 'FS_CHMOD_DIR', (fileperms ( ABSPATH ) & 0777 | 0755) );
			}
			if (! defined ( 'FS_CHMOD_FILE' )) {
				define ( 'FS_CHMOD_FILE', (fileperms ( ABSPATH . 'index.php' ) & 0777 | 0644) );
			}

			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

			self::$wp_filesystem = new \WP_Filesystem_Direct ( true );
		}

		return self::$wp_filesystem;
	}

	/**
	 *
	 * @param string $id
	 * @param bool $check_expire
	 * @param bool $page_cache
	 *
	 * @return string|false
	 * @throws Exception
	 */
	public static function getCache($id, $check_expire = false, $page_cache = false) {
		$wp_filesystem = self::getWpFileSystem ();

		if ($wp_filesystem === false) {
			return false;
		}

		$file = self::_getFileName ( $id, $page_cache );

		if (! $wp_filesystem->exists ( $file )) {
			return false;
		}

		if ($check_expire && time () > $wp_filesystem->mtime ( $file ) + self::getLifetime ( $page_cache )) {
			return false;
		}

		return self::_getCacheFile ( $file, $wp_filesystem, $page_cache );
	}

	/**
	 *
	 * @param string $content
	 * @param string $id
	 * @param bool $page_cache
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function saveCache($content, $id, $page_cache = false) {
		$params = Plugin::getPluginParams ();

		$wp_filesystem = self::getWpFileSystem ();
		// required for compatiblity with Hide My WP Ghost https://wordpress.org/support/topic/compatibility-with-hide-my-wp-ghost/
		$content = apply_filters ( 'fastcache_save_content', $content );

		if ($page_cache && $params->get ( 'htaccess_cache_enable', '1' )) {
			$file_contents = $content;
		} else {
			$file_contents = serialize ( $content );
		}

		if ($params->get ( 'base64encode', 0 )) {
			$file_contents = base64_encode ( $file_contents );
		}

		$file = self::_getFileName ( $id, $page_cache );

		self::initializeCache ();

		if ($wp_filesystem->put_contents ( $file, $file_contents, FS_CHMOD_FILE )) {
			return $content;
		} else {
			throw new Exception ( __ ( 'Error writing files to cache' ) );
		}
	}

	/**
	 *
	 * @throws Exception
	 */
	public static function initializeCache() {
		$wp_filesystem = self::getWpFileSystem ();

		if ($wp_filesystem !== false) {
			$index_contents = '<html lang=""><body></body></html>';

			if (! $wp_filesystem->exists ( FASTCACHE_CACHE_DIR )) {
				if (! $wp_filesystem->exists ( $wp_filesystem->wp_content_dir () . 'cache' )) {
					$wp_filesystem->mkdir ( $wp_filesystem->wp_content_dir () . 'cache', FS_CHMOD_DIR );
				}

				$wp_filesystem->mkdir ( FASTCACHE_CACHE_DIR, FS_CHMOD_DIR );
				$wp_filesystem->put_contents ( FASTCACHE_CACHE_DIR . 'index.html', $index_contents, FS_CHMOD_FILE );
			}

			// On multisite, isolate each blog under its own numeric subdirectory.
			$blog_prefix = is_multisite() ? get_current_blog_id() . '/' : '';

			if ( $blog_prefix && ! $wp_filesystem->exists ( FASTCACHE_CACHE_DIR . rtrim( $blog_prefix, '/' ) ) ) {
				$wp_filesystem->mkdir ( FASTCACHE_CACHE_DIR . rtrim( $blog_prefix, '/' ), FS_CHMOD_DIR );
				$wp_filesystem->put_contents ( FASTCACHE_CACHE_DIR . rtrim( $blog_prefix, '/' ) . '/index.html', $index_contents, FS_CHMOD_FILE );
			}

			$page_dir = FASTCACHE_CACHE_DIR . $blog_prefix . 'page';
			if (! $wp_filesystem->exists ( $page_dir )) {
				$wp_filesystem->mkdir ( $page_dir, FS_CHMOD_DIR );
				$wp_filesystem->put_contents ( $page_dir . '/index.html', $index_contents, FS_CHMOD_FILE );
			}
		}
	}

	/**
	 *
	 * @param
	 *        	$value
	 * @param
	 *        	$form_post
	 * @param
	 *        	$type
	 * @param
	 *        	$error
	 * @param
	 *        	$context
	 * @param
	 *        	$extra_fields
	 * @param
	 *        	$allow_relaxed_file_ownership
	 *        	
	 * @return bool|mixed|void
	 */
	public static function requestFilesystemCredentials($value, $form_post, $type, $error, $context, $extra_fields, $allow_relaxed_file_ownership) {
		$method = get_filesystem_method ( array (), $context, $allow_relaxed_file_ownership );

		if ($method == 'direct') {
			return true;
		}

		if (! function_exists ( 'wp_generate_password' )) {
			include_once Paths::rootPath () . '/wp-includes/pluggable.php';
		}

		$credentials = get_option ( 'ftp_credentials', array (
				'hostname' => '',
				'username' => ''
		) );

		$credentials ['hostname'] = defined ( 'FTP_HOST' ) ? FTP_HOST : $credentials ['hostname'];
		$credentials ['username'] = defined ( 'FTP_USER' ) ? FTP_USER : $credentials ['username'];
		$credentials ['password'] = defined ( 'FTP_PASS' ) ? FTP_PASS : '';

		if ($method == 'ssh2') {
			// Check to see if we are setting the public/private keys for ssh
			$credentials ['public_key'] = defined ( 'FTP_PUBKEY' ) ? FTP_PUBKEY : '';
			$credentials ['private_key'] = defined ( 'FTP_PRIKEY' ) ? FTP_PRIKEY : '';
		}

		if (in_array ( '', $credentials )) {
			return $value;
		}

		return $credentials;
	}

	/**
	 *
	 * @throws Exception
	 */
	public static function gc() {
		$wp_filesystem = self::getWpFileSystem ();

		if ($wp_filesystem === false) {
			return false;
		}

		$result = true;

		// Delete any page cache
		$result |= self::deleteCache ( 'page' );

		$files = FileSystem::lsFiles ( rtrim ( FASTCACHE_CACHE_DIR, '/\\' ), '.', true );
		$now = time ();

		// Check if preserve_cached_images option is enabled
		$params = Plugin::getPluginParams ();
		$preserveImages = ( bool ) $params->get ( 'preserve_cached_images', 0 );

		foreach ( $files as $file ) {
			$time = $wp_filesystem->mtime ( $file );

			// Skip deletion if file is under the "images" cache folder and preserve option is active
			if ($preserveImages && strpos ( $file, rtrim ( FASTCACHE_CACHE_DIR, '/\\' ) . '/images/' ) === 0) {
				continue;
			}

			if (($time + self::getLifetime ()) < $now || empty ( $time )) {
				$result |= $wp_filesystem->delete ( $file );
			}
		}

		return $result;
	}

	/**
	 *
	 * @param string $context
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function deleteCache($context = 'both') {
		// Flush WordPress Object Cache if available
		if (function_exists ( 'wp_cache_flush' )) {
			wp_cache_flush ();
		}

		// Purge LiteSpeed cache
		if ($context != 'plugin' && class_exists ( 'LiteSpeed_Cache_API' )) {
			\LiteSpeed_Cache_API::purge_all ();
		}

		$wp_filesystem = self::getWpFileSystem ();

		if ($wp_filesystem === false) {
			return false;
		}

		if (! $wp_filesystem->exists ( FASTCACHE_CACHE_DIR )) {
			// most likely already deleted so just return true as not to cause any alarm
			return true;
		}
		if ($context == 'plugin') {
			$result = true;
			$result |= ( bool ) $wp_filesystem->rmdir ( FASTCACHE_CACHE_DIR . 'js', true );
			$result |= ( bool ) $wp_filesystem->rmdir ( FASTCACHE_CACHE_DIR . 'css', true );

			return $result;
		}

		// 'blog': delete only the current blog's cache directory (page + wpc), leaving other blogs and shared caches (css/js/images) intact.
		if ($context == 'blog' && is_multisite()) {
			$blog_dir = FASTCACHE_CACHE_DIR . get_current_blog_id();
			if ($wp_filesystem->exists($blog_dir)) {
				return (bool) $wp_filesystem->rmdir($blog_dir, true);
			}
			return true;
		}

		$cache_dir = dirname ( FASTCACHE_CACHE_DIR );
		// Get list of all folders in the cache directory (.../wp-content/cache/)
		$cache_dir_list = $wp_filesystem->dirlist ( $cache_dir, false, false );

		// Check if preserve_cached_images option is enabled
		$params = Plugin::getPluginParams ();
		$preserveImages = ( bool ) $params->get ( 'preserve_cached_images', 0 );
		$deleteAllCacheFolders = ( bool ) $params->get ( 'delete_all_cache', 0 );

		foreach ( $cache_dir_list as $entry ) {
			// Skip the cache if we're only deleting page cache
			if ($context == 'page' && $entry ['name'] == 'fastcache') {
				// On multisite delete only the current blog's page directory; on single-site delete the shared one.
				$blog_prefix = is_multisite() ? get_current_blog_id() . '/' : '';
				$wp_filesystem->rmdir ( $cache_dir . '/fastcache/' . $blog_prefix . 'page', true );

				continue;
			}

			// Skip images folder if $preserveImages
			if ($preserveImages && $entry ['name'] == 'fastcache') {
				// Delete everything under fastcache/ except "images"
				$fastcache_list = $wp_filesystem->dirlist ( $cache_dir . DIRECTORY_SEPARATOR . $entry ['name'], false, false );

				foreach ( $fastcache_list as $sub ) {
					if ($sub ['name'] === 'images') {
						continue; // keep images cache
					}

					$target = $cache_dir . DIRECTORY_SEPARATOR . $entry ['name'] . DIRECTORY_SEPARATOR . $sub ['name'];

					if ($sub ['type'] === 'd') {
						$wp_filesystem->rmdir ( $target, true );
					} else {
						$wp_filesystem->delete ( $target );
					}
				}
			} else {
				// Delete each cache folder by parameter
				if ($entry ['name'] == 'fastcache' || ($entry ['name'] != 'fastcache' && $entry ['type'] == 'd' && $deleteAllCacheFolders)) {
					// Normal behaviour: delete the whole fastcache cache folder
					if (! $wp_filesystem->rmdir ( $cache_dir . DIRECTORY_SEPARATOR . $entry ['name'], true )) {
						return false;
					}
				}
			}
		}

		if (Plugin::getPluginParams ()->get ( 'clear_server_cache', 0 )) {
			self::purgeServerCache ( Uri::currentUrl () );
		}

		return true;
	}

	/**
	 * Selectively delete page cache files (.html) matching the given URLs.
	 * Only operates when htaccess_cache_enable is active.
	 *
	 * @param array $urls
	 *        	Full URLs to purge from filesystem cache
	 * @return array List of deleted file paths
	 * @throws Exception
	 */
	public static function deleteCacheFilesByUrls(array $urls) {
		$wp_filesystem = self::getWpFileSystem ();
		if ($wp_filesystem === false) {
			return [ ];
		}

		$params = Plugin::getPluginParams ();
		$deleted = [ ];

		// Only handle htaccess mode (.html files)
		if (! $params->get ( 'htaccess_cache_enable', '1' )) {
			return [ ];
		}

		$siteUrl = site_url ();
		$blog_prefix = is_multisite() ? get_current_blog_id() . '/' : '';
		$cacheDir = FASTCACHE_CACHE_DIR . $blog_prefix . 'page/';

		foreach ( $urls as $url ) {
			$slug = str_ireplace ( '/', '_', str_ireplace ( $siteUrl, '', $url ) );

			if (strpos ( $url, '*' ) !== false) {
				// Wildcard: use glob pattern
				$pattern = $cacheDir . $slug . '.html';
				$files = glob ( $pattern );
				if ($files) {
					foreach ( $files as $file ) {
						$wp_filesystem->delete ( $file );
						$deleted [] = $file;
					}
				}
			} else {
				// Exact URL
				$file = $cacheDir . $slug . '.html';
				if ($wp_filesystem->exists ( $file )) {
					$wp_filesystem->delete ( $file );
					$deleted [] = $file;
				}
			}
		}

		return $deleted;
	}
	public static function purgeServerCache($url) {
		// Parse the URL
		$parsed_url = parse_url ( $url );

		if ($parsed_url !== false && isset ( $parsed_url ['scheme'], $parsed_url ['host'] )) {
			// Reconstruct the URL with only the scheme and host (domain)
			$domain = $parsed_url ['scheme'] . '://' . $parsed_url ['host'];
		} else {
			return false;
		}

		$urlFormatted = self::getUrl ( $domain );
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'joomla_purgeCache' );
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, "PURGE" );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, array (
				'Host: ' . $urlFormatted ['hostname']
		) );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT, 200 );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 200 ); // timeout in seconds
		curl_setopt ( $curl, CURLOPT_URL, $urlFormatted ['url'] );
		$response = curl_exec ( $curl );
		if (is_resource ( $curl )) {
			curl_close ( $curl );
		}
		return true;
	}
	protected static function getUrl($url) {
		$parsedUrl = parse_url ( $url );
		$hostname = $parsedUrl ['host'];
		$address = gethostbyname ( $hostname );
		$url = $parsedUrl ['scheme'] . '://' . $address;
		if (isset ( $parsedUrl ['port'] ) && $parsedUrl ['port']) {
			$url .= ':' . $parsedUrl ['port'];
		}
		if (isset ( $parsedUrl ['path'] ) && $parsedUrl ['path']) {
			$url .= $parsedUrl ['path'];
		}
		if (isset ( $parsedUrl ['query'] ) && $parsedUrl ['query']) {
			$url .= '?' . $parsedUrl ['query'];
		}
		if (isset ( $parsedUrl ['fragment'] ) && $parsedUrl ['fragment']) {
			$url .= '#' . $parsedUrl ['fragment'];
		}
		return array (
				'url' => $url,
				'hostname' => $hostname
		);
	}
	protected static function getLifetime($page_cache = false) {
		static $lifetime, $page_cache_lifetime;

		if ($page_cache) {
			if (! $page_cache_lifetime) {
				$params = Plugin::getPluginParams ();
				$page_cache_lifetime = $params->get ( 'page_cache_lifetime', '86400' );
			}

			return ( int ) $page_cache_lifetime;
		}

		if (! $lifetime) {
			$params = Plugin::getPluginParams ();

			$lifetime = $params->get ( 'cache_lifetime', '86400' );
		}

		return ( int ) $lifetime;
	}

	/**
	 *
	 * @param string $id
	 * @param bool $page_cache
	 *
	 * @return string
	 */
	private static function _getFileName($id, $page_cache = false) {
		$params = Plugin::getPluginParams ();
		// On multisite each blog gets its own subdirectory so cache files never collide across sites.
		$blog_prefix = is_multisite() ? get_current_blog_id() . '/' : '';
		// Subdomain Multisite intentionally uses the SAME .html filename/content format as
		// regular htaccess mode -- Utility::htaccessCacheManagement() is the only place that
		// withholds it, by never writing the Apache rewrite rules for subdomain installs. So
		// these files are always found and served by PHP (PageCache::initialize() safety net),
		// never by a real Apache-level bypass, but their format matches getPageCacheId()'s
		// URL-slug ID exactly, and saveCache()/_getCacheFile() store/read them as raw HTML
		// (both key off this same setting) -- no hybrid hashed-name-with-raw-content file.
		$htaccess_enabled = $params->get ( 'htaccess_cache_enable', '1' );
		if ($page_cache && $htaccess_enabled) {
			return FASTCACHE_CACHE_DIR . $blog_prefix . 'page/' . $id . '.html';
		} elseif ($page_cache && ! $htaccess_enabled) {
			return FASTCACHE_CACHE_DIR . $blog_prefix . 'page/' . md5 ( NONCE_SALT . $id ) . '.wpc';
		} else {
			return FASTCACHE_CACHE_DIR . $blog_prefix . md5 ( NONCE_SALT . $id ) . '.wpc';
		}
	}

	/**
	 *
	 * @param string $file
	 * @param \WP_Filesystem_Base $wp_filesystem
	 *
	 * @return string
	 */
	private static function _getCacheFile($file, $wp_filesystem, $page_cache = false) {
		$params = Plugin::getPluginParams ();

		$content = $wp_filesystem->get_contents ( $file );

		if ($page_cache && $params->get ( 'htaccess_cache_enable', '1' )) {
			return $content;
		}

		if ($params->get ( 'base64encode', 0 )) {
			$content = base64_decode ( $content );
		}

		return unserialize ( $content );
	}
}
