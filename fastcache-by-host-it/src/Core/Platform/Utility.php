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
	
	public static function htaccessCacheManagement($postedSettings, $enable = null) {
		// Htaccess page cache rules incompatible with Multisite (shared .htaccess)
		if ( is_multisite() ) {
			return;
		}

		$currentSettings = Plugin::getPluginParams()->toArray();
		$currentHtaccessCacheEnable = isset($currentSettings['htaccess_cache_enable']) ? (int)$currentSettings['htaccess_cache_enable'] : 0;
		$postedHtaccessCacheEnable = $postedSettings['htaccess_cache_enable'];
		
		if((int)$currentHtaccessCacheEnable != (int)$postedHtaccessCacheEnable || $postedHtaccessCacheEnable === 'auto') {
			$htaccess = Paths::rootPath() . '/.htaccess';
			
			if ( file_exists( $htaccess ) ) {
				$contents = file_get_contents( $htaccess );
				
				$apacheVersion = '';
				if (function_exists('apache_get_version')) {
					$apacheVersion = apache_get_version();
				}
				
				// fallback per hosting con FastCGI/FPM
				if (empty($apacheVersion) && isset($_SERVER['SERVER_SOFTWARE'])) {
					$apacheVersion = $_SERVER['SERVER_SOFTWARE'];
				}
				
				$isModernApache = (
						strpos($apacheVersion, 'Apache/2.4') !== false ||
						strpos($apacheVersion, 'Apache/2.5') !== false
				);
				
				// scegli il blocco in base alla versione
				if ($isModernApache) {
					// Versione moderna per Apache 2.4+
					$htaccessPageCache = <<<APACHECONFIG
## BEGIN HTACCESS PAGE CACHING - FASTCACHE 2.4 ##
# Serve cached pages only for GET requests
RewriteCond %{REQUEST_METHOD} ^(GET)$
# Exclude AJAX/XHR requests
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
# Exclude URLs containing query strings (?param=)
RewriteCond %{QUERY_STRING} ^$
# Exclude logged-in users and comment authors
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_) [NC]
# Exclude WordPress backend and REST API requests
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
# Check if the cached file exists (replace '/' with '_')
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_\${REQUEST_URI//\//_}_.html -f
# Serve the static cached file directly
RewriteRule ^(.*)$ /wp-content/cache/fastcache/page/_\${REQUEST_URI//\//_}_.html [L]
## END HTACCESS PAGE CACHING - FASTCACHE ##


APACHECONFIG;
				} else {
					// Versione legacy per Apache 2.2 (compatibile universale)
					$htaccessPageCache = <<<APACHECONFIG
## BEGIN HTACCESS PAGE CACHING - FASTCACHE ##
## 3.0 
RewriteEngine On
RewriteRule ^wp-content/cache/fastcache/page/ - [E=FASTCACHE_LEVEL:CACHEFILE]
RewriteCond %{REQUEST_URI} !^/wp-content/cache/fastcache/page/ [NC]
RewriteRule ^ - [E=FASTCACHE_LEVEL:MISS]
# ===== HOME =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_.html -f
RewriteRule ^$ /wp-content/cache/fastcache/page/_.html [L,E=FASTCACHE_LEVEL:HITHOME]

# ===== LEVEL 1 =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/([^/]+)/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_%1_.html -f
RewriteRule ^ /wp-content/cache/fastcache/page/_%1_.html [L,E=FASTCACHE_LEVEL:HITL1]

# ===== LEVEL 2 =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/([^/]+)/([^/]+)/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_%1_%2_.html -f
RewriteRule ^ /wp-content/cache/fastcache/page/_%1_%2_.html [L,E=FASTCACHE_LEVEL:HITL2]

# ===== LEVEL 3 =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/([^/]+)/([^/]+)/([^/]+)/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_%1_%2_%3_.html -f
RewriteRule ^ /wp-content/cache/fastcache/page/_%1_%2_%3_.html [L,E=FASTCACHE_LEVEL:HITL3]

# ===== LEVEL 4 =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/([^/]+)/([^/]+)/([^/]+)/([^/]+)/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_%1_%2_%3_%4_.html -f
RewriteRule ^ /wp-content/cache/fastcache/page/_%1_%2_%3_%4_.html [L,E=FASTCACHE_LEVEL:HITL4]

# ===== LEVEL 5 =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_%1_%2_%3_%4_%5_.html -f
RewriteRule ^ /wp-content/cache/fastcache/page/_%1_%2_%3_%4_%5_.html [L,E=FASTCACHE_LEVEL:HITL5]

# ===== LEVEL 6 =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_%1_%2_%3_%4_%5_%6_.html -f
RewriteRule ^ /wp-content/cache/fastcache/page/_%1_%2_%3_%4_%5_%6_.html [L,E=FASTCACHE_LEVEL:HITL6]

# ===== LEVEL 7 =====
RewriteCond %{REQUEST_METHOD} ^GET$ [NC]
RewriteCond %{HTTP_X_REQUESTED_WITH} !^XMLHttpRequest$ [NC]
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(wordpress_logged_in|comment_author_|woocommerce_items_in_cart|woocommerce_cart_hash|wp_woocommerce_session_) [NC]
RewriteCond %{REQUEST_URI} !^/(wp-admin|wp-login|wp-json) [NC]
RewriteCond %{REQUEST_URI} ^/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/([^/]+)/$
RewriteCond %{DOCUMENT_ROOT}/wp-content/cache/fastcache/page/_%1_%2_%3_%4_%5_%6_%7_.html -f
RewriteRule ^ /wp-content/cache/fastcache/page/_%1_%2_%3_%4_%5_%6_%7_.html [L,E=FASTCACHE_LEVEL:HITL7]

# ===== DEBUG HEADER (set only when FASTCACHE_LEVEL is present) =====
Header always set X-HST-FASTCACHE-FS "%{FASTCACHE_LEVEL}e"

## END HTACCESS PAGE CACHING - FASTCACHE ##


APACHECONFIG;
				}
				
				$regex = '~^[ \t]*## BEGIN HTACCESS PAGE CACHING - FASTCACHE[^\r\n]*\R.*?^[ \t]*## END HTACCESS PAGE CACHING - FASTCACHE[^\r\n]*\R?~sm';

				if ($postedHtaccessCacheEnable == 1 || $postedHtaccessCacheEnable == -1 || $enable === true) {

					// 1) Rimuovi TUTTE le occorrenze (anche duplicate, anche legacy)
					$clean = preg_replace($regex, '', $contents, -1, $count);

					// 2) Se non c’era nulla oppure c’erano duplicati, riscrivi inserendo UN SOLO blocco canonico
					if ($count > 0) {
						return file_put_contents($htaccess, $htaccessPageCache . $clean);
					}

					// 3) Se non è stato rimosso nulla, il blocco non c’era: aggiungilo
					// (qui mantieni la tua logica originale: prepend)
					return file_put_contents($htaccess, $htaccessPageCache . $contents);

				} elseif ($postedHtaccessCacheEnable == 0 || $postedHtaccessCacheEnable == -1 || $enable === false) {

					// Disable: rimuovi TUTTI i blocchi
					$clean = preg_replace($regex, '', $contents, -1, $count);

					if ($count > 0) {
						return file_put_contents($htaccess, $clean);
					}

					return true;
				}

			} else {
				return 'FILEDOESNTEXIST';
			}
		}
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