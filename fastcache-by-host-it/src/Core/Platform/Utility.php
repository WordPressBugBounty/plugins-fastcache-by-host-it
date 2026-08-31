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

use FastCache\Core\Interfaces\Settings;
use FastCache\Core\Interfaces\Utility as UtilityInterface;
use FastCache\Platform\Plugin;

defined ( '_WP_EXEC' ) or die ( 'Restricted access' );
class Utility implements UtilityInterface {
	/**
	 * Holds the list of bots IP addresses
	 *
	 * @var array
	 */
	public static $botsIP = array (
			'52.162.212.163' => true,
			'13.78.216.56' => true,
			'65.52.113.236' => true,
			'52.229.122.240' => true,
			'172.255.48.147' => true,
			'172.255.48.146' => true,
			'172.255.48.145' => true,
			'172.255.48.144' => true,
			'172.255.48.143' => true,
			'172.255.48.142' => true,
			'24.109.190.162' => true,
			'172.255.48.141' => true,
			'172.255.48.140' => true,
			'172.255.48.139' => true,
			'172.255.48.138' => true,
			'172.255.48.137' => true,
			'172.255.48.136' => true,
			'172.255.48.135' => true,
			'172.255.48.134' => true,
			'172.255.48.133' => true,
			'172.255.48.132' => true,
			'172.255.48.131' => true,
			'172.255.48.130' => true,
			'104.214.48.247' => true,
			'40.74.243.176' => true,
			'40.74.243.13' => true,
			'40.74.242.253' => true,
			'13.85.82.26' => true,
			'13.85.24.90' => true,
			'13.85.24.83' => true,
			'13.66.7.11' => true,
			'104.214.72.101' => true,
			'191.235.99.221' => true,
			'191.235.98.164' => true,
			'104.41.2.19' => true,
			'104.211.165.53' => true,
			'104.211.143.8' => true,
			'172.255.61.40' => true,
			'172.255.61.39' => true,
			'172.255.61.38' => true,
			'172.255.61.37' => true,
			'172.255.61.36' => true,
			'172.255.61.35' => true,
			'172.255.61.34' => true,
			'13.91.230.174' => true,
			'20.52.146.77' => true,
			'65.52.36.250' => true,
			'70.37.83.240' => true,
			'104.214.110.135' => true,
			'157.55.189.189' => true,
			'191.232.194.51' => true,
			'52.175.57.81' => true,
			'52.237.236.145' => true,
			'52.237.250.73' => true,
			'52.237.235.185' => true,
			'40.83.89.214' => true,
			'40.123.218.94' => true,
			'102.133.169.66' => true,
			'52.172.14.87' => true,
			'52.231.199.170' => true,
			'52.246.165.153' => true,
			'13.76.97.224' => true,
			'13.53.162.7' => true,
			'20.52.36.49' => true,
			'20.188.63.151' => true,
			'51.144.102.233' => true,
			'23.96.34.105' => true
	);

	/**
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function translate($text) {
		return __ ( $text, 'fastcache' );
	}

	/**
	 *
	 * @return integer
	 */
	public static function unixCurrentDate() {
		return current_time ( 'timestamp', true );
	}

	/*
	 *
	 */
	public static function getEditorName() {
		return '';
	}

	/**
	 *
	 * @param string $message
	 * @param string $priority
	 * @param string $filename
	 */
	public static function log($message, $priority, $filename) {
		$file = Utility::getLogsPath () . '/fastcache.log';

		error_log ( $message . "\n", 3, $file );
	}

	/**
	 */
	public static function getLogsPath() {
		return FASTCACHE_DIR . 'logs';
	}

	/**
	 *
	 * @return string
	 */
	public static function lnEnd() {
		return "\n";
	}

	/**
	 *
	 * @return string
	 */
	public static function tab() {
		return "\t";
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function decrypt($value) {
		return self::encrypt_decrypt ( $value, 'decrypt' );
	}
	public static function filter_string_polyfill(string $string): string {
		$str = preg_replace ( '/\x00|<[^>]*>?/', '', $string );
		return str_replace ( [ 
				"'",
				'"'
		], [ 
				'&#39;',
				'&#34;'
		], $str );
	}

	/**
	 *
	 * @param string $value
	 * @param string $action
	 *
	 * @return string
	 */
	private static function encrypt_decrypt($value, $action) {
		$output = false;

		$encrypt_method = "AES-256-CBC";
		$secret_key = AUTH_KEY;
		$secret_iv = AUTH_SALT;

		// hash
		$key = hash ( 'sha256', $secret_key );

		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr ( hash ( 'sha256', $secret_iv ), 0, 16 );

		if ($action == 'encrypt') {
			if (version_compare ( PHP_VERSION, '5.3.3', '<' )) {
				$output = @openssl_encrypt ( $value, $encrypt_method, $key, 0 );
			} else {
				$output = openssl_encrypt ( $value, $encrypt_method, $key, 0, $iv );
			}
			$output = base64_encode ( $output );
		} else if ($action == 'decrypt') {
			if (version_compare ( PHP_VERSION, '5.3.3', '<' )) {
				$output = @openssl_decrypt ( base64_decode ( $value ), $encrypt_method, $key, 0 );
			} else {
				$output = openssl_decrypt ( base64_decode ( $value ), $encrypt_method, $key, 0, $iv );
			}
		}

		return $output;
	}

	/**
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function encrypt($value) {
		return self::encrypt_decrypt ( $value, 'encrypt' );
	}

	/**
	 *
	 * @param string $value
	 * @param string $default
	 * @param string $filter
	 * @param string $method
	 *
	 * @return mixed
	 */
	public static function get($value, $default = '', $filter = 'cmd', $method = 'request') {
		$request = '_' . strtoupper ( $method );
		$callback = '';

		if (! isset ( $GLOBALS [$request] [$value] )) {
			$GLOBALS [$request] [$value] = $default;
		}

		switch ($filter) {
			case 'int' :
				$filter = FILTER_SANITIZE_NUMBER_INT;

				break;

			case 'array' :
			case 'json' :
				return ( array ) $GLOBALS [$request] [$value];
			case 'string' :
			case 'cmd' :
			default :
				$filter = FILTER_CALLBACK;
				$callback = array (
						'options' => array (
								__CLASS__,
								'filter_string_polyfill'
						)
				);

				break;
		}

		switch ($method) {
			case 'get' :
				$type = INPUT_GET;

				break;

			case 'post' :
				$type = INPUT_POST;

				break;

			default :

				return filter_var ( $_REQUEST [$value], $filter, $callback );
		}

		$input = filter_input ( $type, $value, $filter );

		return is_null ( $input ) ? $default : $input;
	}

	/**
	 *
	 * @param string $url
	 */
	public static function loadAsync($url) {
	}

	/**
	 */
	public static function menuId() {
	}

	/**
	 * Checks if user is not logged in
	 */
	public static function isGuest() {
		return ! is_user_logged_in ();
	}
	public static function sendHeaders($headers) {
		if (! empty ( $headers )) {
			foreach ( $headers as $header => $value ) {
				header ( $header . ': ' . $value, false );
			}
		}
	}
	public static function userAgent($userAgent) {
		global $is_chrome, $is_IE, $is_edge, $is_safari, $is_opera, $is_gecko, $is_winIE, $is_macIE, $is_iphone;

		$oUA = new \stdClass ();
		$oUA->browser = 'Unknown';
		$oUA->browserVersion = 'Unknown';
		$oUA->os = 'Unknown';

		if ($is_chrome) {
			$oUA->browser = 'Chrome';
		} elseif ($is_gecko) {
			$oUA->browser = 'Firefox';
		} elseif ($is_safari) {
			$oUA->browser = 'Safari';
		} elseif ($is_edge) {
			$oUA->browser = 'Edge';
		} elseif ($is_IE) {
			$oUA->browser = 'Internet Explorer';
		} elseif ($is_opera) {
			$oUA->browser = 'Opera';
		}

		if ($oUA->browser != 'Unknown') {

			// Build the REGEX pattern to match the browser version string within the user agent string.
			$pattern = '#(?<browser>Version|' . $oUA->browser . ')[/ :]+(?<version>[0-9.|a-zA-Z.]*)#';

			// Attempt to find version strings in the user agent string.
			$matches = array ();

			if (preg_match_all ( $pattern, $userAgent, $matches )) {
				// Do we have both a Version and browser match?
				if (\count ( $matches ['browser'] ) == 2) {
					// See whether Version or browser came first, and use the number accordingly.
					if (strripos ( $userAgent, 'Version' ) < strripos ( $userAgent, $oUA->browser )) {
						$oUA->browserVersion = $matches ['version'] [0];
					} else {
						$oUA->browserVersion = $matches ['version'] [1];
					}
				} elseif (\count ( $matches ['browser'] ) > 2) {
					$key = array_search ( 'Version', $matches ['browser'] );

					if ($key) {
						$oUA->browserVersion = $matches ['version'] [$key];
					}
				} else {
					// We only have a Version or a browser so use what we have.
					$oUA->browserVersion = $matches ['version'] [0];
				}
			}
		}

		if ($is_winIE) {
			$oUA->os = 'Windows';
		} elseif ($is_macIE) {
			$oUA->os = 'Mac';
		} elseif ($is_iphone) {
			$oUA->os = 'iOS';
		}

		return $oUA;
	}
	
	public static function htaccessCacheManagement($postedSettings, $enable = null, $currentSettingsOverride = null) {
		// Subdomain multisite: Apache cannot distinguish sites by REQUEST_URI alone (every
		// site's home is '/'), so we never write the rewrite rules for it. The page cache
		// still works: files are generated in the same .html format as regular htaccess mode
		// (see Cache::_getFileName()) and are found and served by the PHP safety net in
		// PageCache::initialize() on every request, correctly isolated per domain via blog_id
		// -- just never via a real Apache-level bypass.
		if ( is_multisite() && defined( 'SUBDOMAIN_INSTALL' ) && SUBDOMAIN_INSTALL ) {
			return;
		}

		// $currentSettingsOverride lets a caller that already has the authoritative "before"
		// value (e.g. the update_option_{...} hook, which WordPress hands the old value to
		// directly) skip Plugin::getPluginParams() entirely for this comparison -- avoiding
		// any dependency on whether that cache happens to still hold pre-save data.
		$currentSettings = is_array( $currentSettingsOverride ) ? $currentSettingsOverride : Plugin::getPluginParams()->toArray();
		$currentHtaccessCacheEnable = isset($currentSettings['htaccess_cache_enable']) ? (int)$currentSettings['htaccess_cache_enable'] : 0;
		$postedHtaccessCacheEnable = $postedSettings['htaccess_cache_enable'];

		// Also rewrite when "Cache specifica della piattaforma" changes: the mobile/desktop
		// UA-detection rules depend on this setting too (Bug#34278). Without this check, toggling
		// pro_cache_platform while htaccess_cache_enable stays unchanged (e.g. already 1) would
		// leave stale rules in .htaccess that never look for the _mobile variant files, because
		// the block below would never re-run buildHtaccessBlock().
		$currentPlatformCache = isset($currentSettings['pro_cache_platform']) ? (int)$currentSettings['pro_cache_platform'] : 0;
		$postedPlatformCache  = isset($postedSettings['pro_cache_platform']) ? (int)$postedSettings['pro_cache_platform'] : 0;
		$platformCacheChanged = $currentPlatformCache !== $postedPlatformCache;

		if ( (int)$currentHtaccessCacheEnable != (int)$postedHtaccessCacheEnable || $postedHtaccessCacheEnable === 'auto' || $platformCacheChanged ) {
			$htaccess = Paths::rootPath() . '/.htaccess';

			if ( file_exists( $htaccess ) ) {
				$contents = file_get_contents( $htaccess );

				// update_option() for this settings array has already run by the time this
				// hook fires, but Plugin::getPluginParams() may have cached the pre-save
				// values earlier in this same request. Force a fresh read so the rules
				// buildHtaccessBlock() generates reflect what was just saved, not what was
				// saved on the previous request (Bug#34278 follow-up).
				Plugin::resetPluginParamsCache();

				$htaccessPageCache = self::buildHtaccessBlock();

				$regex = '~^[ \t]*## BEGIN HTACCESS PAGE CACHING - FASTCACHE[^\r\n]*\R.*?^[ \t]*## END HTACCESS PAGE CACHING - FASTCACHE[^\r\n]*\R?~sm';

				if ( $postedHtaccessCacheEnable == 1 || $postedHtaccessCacheEnable == -1 || $enable === true ) {
					$clean = preg_replace( $regex, '', $contents, -1, $count );
					if ( $count > 0 ) {
						return file_put_contents( $htaccess, $htaccessPageCache . $clean );
					}
					return file_put_contents( $htaccess, $htaccessPageCache . $contents );

				} elseif ( $postedHtaccessCacheEnable == 0 || $postedHtaccessCacheEnable == -1 || $enable === false ) {
					$clean = preg_replace( $regex, '', $contents, -1, $count );
					if ( $count > 0 ) {
						return file_put_contents( $htaccess, $clean );
					}
					return true;
				}

			} else {
				return 'FILEDOESNTEXIST';
			}
		}
	}

	/**
	 * Build the full htaccess cache block for single-site or multisite subdirectory.
	 *
	 * For multisite subdirectory, generates one Level 0-9 block per registered site.
	 * Subsite blocks are written before the main site block so more-specific patterns
	 * take precedence (e.g. /site2/ is matched before the root-level Level-1 rule).
	 */
	private static function buildHtaccessBlock() {
		if ( is_multisite() ) {
			return self::buildMultisiteHtaccessBlock();
		}
		$site_prefix = rtrim( wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		$cache_base  = rtrim( wp_parse_url( content_url( 'cache/fastcache/page' ), PHP_URL_PATH ), '/' );
		// Relative path for the CACHEFILE RewriteRule pattern (Apache strips the site prefix in .htaccess).
		$cache_rel   = ltrim( substr( $cache_base, strlen( $site_prefix ) ), '/' );

		$out  = "## BEGIN HTACCESS PAGE CACHING - FASTCACHE ##\n";
		$out .= "RewriteEngine On\n";
		$out .= "RewriteRule ^{$cache_rel}/ - [E=FASTCACHE_LEVEL:CACHEFILE]\n";
		$out .= "RewriteCond %{REQUEST_URI} !^{$cache_base}/ [NC]\n";
		$out .= "RewriteRule ^ - [E=FASTCACHE_LEVEL:MISS]\n";
		$out .= self::buildSiteHtaccessRules( $cache_base, $site_prefix, [] );
		$out .= "\n<IfModule mod_headers.c>\n\tHeader always set X-HST-FASTCACHE-FS \"%{FASTCACHE_LEVEL}e\"\n</IfModule>\n";
		$out .= "## END HTACCESS PAGE CACHING - FASTCACHE ##\n\n";
		return $out;
	}

	private static function buildMultisiteHtaccessBlock() {
		$sites = get_sites( [ 'number' => 200 ] );
		$main_id = (int) get_main_site_id();

		// Collect subsite paths so the main-site block can exclude them.
		$subsite_paths = [];
		foreach ( $sites as $site ) {
			if ( (int) $site->blog_id !== $main_id ) {
				$subsite_paths[] = rtrim( $site->path, '/' ); // e.g. '/site2'
			}
		}

		$subsite_blocks = '';
		$main_block     = '';

		foreach ( $sites as $site ) {
			$blog_id    = (int) $site->blog_id;
			$cache_base = '/wp-content/cache/fastcache/' . $blog_id . '/page';
			// site_prefix: the path segment that prefixes each URI for this blog.
			// Main site at '/' -> no prefix; site2 at '/site2/' -> prefix '/site2'
			$site_prefix = rtrim( $site->path, '/' ); // '' for main, '/site2' for site2

			if ( $blog_id === $main_id ) {
				// Main site rules exclude all subsite paths to prevent false matches
				// (e.g. Level-1 rule ^/([^/]+)/$ would otherwise match /site2/).
				$main_block = self::buildSiteHtaccessRules( $cache_base, $site_prefix, $subsite_paths );
			} else {
				$subsite_blocks .= self::buildSiteHtaccessRules( $cache_base, $site_prefix, [] );
			}
		}

		$out  = "## BEGIN HTACCESS PAGE CACHING - FASTCACHE ##\n";
		$out .= "RewriteEngine On\n";
		$out .= "RewriteRule ^wp-content/cache/fastcache/ - [E=FASTCACHE_LEVEL:CACHEFILE]\n";
		$out .= "RewriteCond %{REQUEST_URI} !^/wp-content/cache/fastcache/ [NC]\n";
		$out .= "RewriteRule ^ - [E=FASTCACHE_LEVEL:MISS]\n";
		$out .= $subsite_blocks;
		$out .= $main_block;
		$out .= "\n<IfModule mod_headers.c>\n\tHeader always set X-HST-FASTCACHE-FS \"%{FASTCACHE_LEVEL}e\"\n</IfModule>\n";
		$out .= "## END HTACCESS PAGE CACHING - FASTCACHE ##\n\n";
		return $out;
	}

	/**
	 * Generate Level 0-9 rewrite rules for one site.
	 *
	 * @param string $cache_base   Web-root-relative path to the site's page cache dir,
	 *                             e.g. '/wp-content/cache/fastcache/2/page'
	 * @param string $site_prefix  URI prefix for this site, e.g. '' or '/site2'
	 * @param array  $exclude_paths  URI prefixes to exclude (used for main site to skip subsite paths)
	 */
	private static function buildSiteHtaccessRules( $cache_base, $site_prefix, array $exclude_paths ) {
		$cookie_pattern = 'wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_';

		// Append admin-configured cookie exclusions (e.g. cmplz_ for Complianz/GDPR plugins).
		$params        = Plugin::getPluginParams();
		$extra_cookies = $params->get( 'cache_cookie_exclude', [ 'cmplz_' ] );
		foreach ( (array) $extra_cookies as $cookie ) {
			$cookie = trim( $cookie );
			if ( $cookie !== '' ) {
				$cookie_pattern .= '|' . $cookie;
			}
		}

		// When "Cache specifica della piattaforma" is on, generate separate mobile/desktop
		// cache-file pairs so mobile visitors never receive a desktop-generated page and
		// vice-versa (Bug#34278 / Ticket#71450934).
		$platform_cache = (bool) $params->get( 'pro_cache_platform', '0' );

		$p = $site_prefix; // e.g. '' or '/site2'

		// Common conditions for every level (built once, prepended to each block)
		$exclude_conds = '';
		foreach ( $exclude_paths as $excl ) {
			$exclude_conds .= 'RewriteCond %{REQUEST_URI} !^' . preg_quote( $excl, null ) . '/ [NC]' . "\n";
		}

		$common = "RewriteCond %{REQUEST_METHOD} ^GET$ [NC]\n"
			. "RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]\n"
			. "RewriteCond %{QUERY_STRING} ^$\n"
			. "RewriteCond %{HTTP_COOKIE} !({$cookie_pattern}) [NC]\n"
			. "RewriteCond %{REQUEST_URI} !^{$p}/(wp-admin|wp-login|wp-json) [NC]\n"
			. $exclude_conds;

		// Level labels for single-site have no prefix marker; multisite adds the blog path.
		$label = $p ? " [{$p}]" : '';

		$out = '';

		// UA detection: stamp FASTCACHE_MOBILE env var for all subsequent rules in this block.
		// The rule fires only when the UA matches a mobile pattern and is a no-op rewrite (^ → -).
		if ( $platform_cache ) {
			$out .= "\n# Mobile UA detection for platform-specific cache{$label}\n"
				. 'RewriteCond %{HTTP_USER_AGENT} "Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini" [NC]' . "\n"
				. "RewriteRule ^ - [E=FASTCACHE_MOBILE:1]\n";
		}

		// Helper closure: emit one cache-hit rule block (mobile or desktop variant, or unified).
		// $uri_cond  : RewriteCond line(s) matching the URI pattern
		// $file_id   : filename stem (without leading cache_base or trailing .html)
		// $hit_label : E=FASTCACHE_LEVEL value
		// $mobile_cond : 'mobile' | 'desktop' | '' (unified when platform cache is off)
		$make_rule = function( $uri_cond, $file_stem, $hit_label, $variant ) use ( $common, $cache_base ) {
			$out = $common;
			if ( $variant === 'mobile' ) {
				$out .= "RewriteCond %{ENV:FASTCACHE_MOBILE} =1\n";
			} elseif ( $variant === 'desktop' ) {
				$out .= "RewriteCond %{ENV:FASTCACHE_MOBILE} !1\n";
			}
			$out .= $uri_cond
				. "RewriteCond %{DOCUMENT_ROOT}{$cache_base}/{$file_stem}.html -f\n"
				. "RewriteRule ^ {$cache_base}/{$file_stem}.html [L,E=FASTCACHE_LEVEL:{$hit_label}]\n";
			return $out;
		};

		// HOME
		$home_uri_cond = "RewriteCond %{REQUEST_URI} " . ( $p === '' ? '^/$' : "^{$p}/$" ) . "\n";

		if ( $platform_cache ) {
			$out .= "\n# ===== HOME{$label} (mobile) =====\n"
				. $make_rule( $home_uri_cond, '__mobile', 'HITHOME', 'mobile' );
			$out .= "\n# ===== HOME{$label} (desktop) =====\n"
				. $make_rule( $home_uri_cond, '_', 'HITHOME', 'desktop' );
		} else {
			$out .= "\n# ===== HOME{$label} =====\n"
				. $make_rule( $home_uri_cond, '_', 'HITHOME', '' );
		}

		// Levels 1-9: each level captures one more URI segment
		$capture_levels = [
			1 => [ 'pattern' => $p . '/([^/]+)/',         'vars' => '%1' ],
			2 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/', 'vars' => '%1_%2' ],
			3 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/([^/]+)/', 'vars' => '%1_%2_%3' ],
			4 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/', 'vars' => '%1_%2_%3_%4' ],
			5 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/', 'vars' => '%1_%2_%3_%4_%5' ],
			6 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/', 'vars' => '%1_%2_%3_%4_%5_%6' ],
			7 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/', 'vars' => '%1_%2_%3_%4_%5_%6_%7' ],
			8 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/', 'vars' => '%1_%2_%3_%4_%5_%6_%7_%8' ],
			9 => [ 'pattern' => $p . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/', 'vars' => '%1_%2_%3_%4_%5_%6_%7_%8_%9' ],
		];

		foreach ( $capture_levels as $level => $cfg ) {
			$uri_cond = "RewriteCond %{REQUEST_URI} ^{$cfg['pattern']}$\n";
			if ( $platform_cache ) {
				$out .= "\n# ===== LEVEL {$level}{$label} (mobile) =====\n"
					. $make_rule( $uri_cond, "_{$cfg['vars']}__mobile", "HITL{$level}", 'mobile' );
				$out .= "\n# ===== LEVEL {$level}{$label} (desktop) =====\n"
					. $make_rule( $uri_cond, "_{$cfg['vars']}_", "HITL{$level}", 'desktop' );
			} else {
				$out .= "\n# ===== LEVEL {$level}{$label} =====\n"
					. $make_rule( $uri_cond, "_{$cfg['vars']}_", "HITL{$level}", '' );
			}
		}

		return $out;
	}
	
	public static function bsTooltipContentAttribute() {
		return 'data-bs-content';
	}
	public static function isPageCacheEnabled(Settings $oParams) {
		return ( bool ) $oParams->get ( 'cache_enable', '1' );
	}
	public static function isPluginEnabled() {
		return true;
		
		static $licenseValidation;
		
		if(!is_null($licenseValidation)) {
			return $licenseValidation;
		}

		$settings = Plugin::getPluginParams();
		$licensecode = $settings->get('licensecode', '');
		$currentLiveSite = site_url();
		$parsed_url = parse_url($currentLiveSite);
		$current_domain = $parsed_url['host'];
		
		// API call to test if the license code is valid and active, API example: https://api.host.it/test/v1/licence/check/{licenceCode}/{nomeDominio}
		$get_response = wp_remote_get('https://api.host.it/public/v1/licence/check/' . $licensecode . '/' . $current_domain);
		if (is_wp_error($get_response)) {
			return false;
		}
		$get_body = wp_remote_retrieve_body($get_response);
		$get_data = json_decode($get_body, true);
		
		if(isset($get_data['status'])) {
			if($get_data['status'] == 'OK') {
				$licenseValidation = true;
			} else {
				$licenseValidation = false;
			}
		}
		
		return $licenseValidation;
	}
}