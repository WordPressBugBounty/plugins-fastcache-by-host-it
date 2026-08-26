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
namespace FastCache\Core;

defined ( '_FASTCACHE_EXEC' ) or die ( 'Restricted access' );

use FastCache\Platform\Uri;
use FastCache\Platform\Cache;
use FastCache\Platform\Plugin;
use FastCache\Platform\Utility;

class PageCache {

	/**
	 */
	public static function initialize() {
		if (self::isCachingEnabled ()) {
			if ($_SERVER ['REQUEST_METHOD'] === 'POST') {
				Cache::deleteCache ( 'page' );

				return;
			}

			$html = Cache::getCache ( self::getPageCacheId (), true, true );

			if ($html != false) {
				while ( @ob_end_clean () )
					;
				echo $html;

				exit ();
			}
		}
	}
	protected static function getPageCacheId() {
		static $sCacheId;

		if (! $sCacheId) {
			$params = Plugin::getPluginParams ();
			if ($params->get ( 'htaccess_cache_enable', '1' )) {
				$siteUrl = site_url ();

				$uri = str_ireplace ( $siteUrl, '', Uri::getInstance ()->toString () );
				// Normalizza: rimuovi index.php dall'inizio della URI per evitare che
				// la home page venga cachata come _index.php_.html invece di _.html.
				// Senza questa normalizzazione le regole htaccess Level 1-7 catturano
				// "index.php" come primo segmento e servono la home page per URL interne.
				$uri = preg_replace( '#^/?index\.php/?#i', '/', $uri );
				$sCacheId = str_ireplace ( '/', '_', $uri );
			} else {
				$parts = array ();

				$parts [] = Browser::getInstance ()->getFontHash ();
				$parts [] = Uri::getInstance ()->toString ();

				// Add a value to the array that will be used to determine the page cache id
				$parts = apply_filters ( 'fastcache_get_page_cache_id', $parts );

				$sCacheId = md5 ( serialize ( $parts ) );
			}
		}

		return $sCacheId;
	}
	public static function store($sHtml) {
		if (self::isCachingEnabled ()) {
			// Apply late nonce refresh to prevent stale nonces in cached pages (v1.6.9)
			if ( file_exists( dirname(__FILE__) . '/NonceRefresh.php' ) ) {
				require_once dirname(__FILE__) . '/NonceRefresh.php';
				$sHtml = NonceRefresh::processCacheContent( $sHtml );
			}

			if (FASTCACHE_DEBUG) {
				$now = date ( 'l, F d, Y h:i:s A' );
				$tag = '<!-- Cached by FastCache on ' . $now . ' GMT --> </body>';
				$sHtml = str_replace ( '</body>', $tag, $sHtml );
			}

			Cache::saveCache ( $sHtml, self::getPageCacheId (), true );
		}
	}
	public static function isExcluded($params) {
		// Lista di URL WooCommerce standard (inglese + italiano)
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
		
		$cache_exclude = $params->get ( 'cache_exclude', $default_cache_exclude);

		if (Helper::findExcludes ( $cache_exclude, Uri::getInstance ()->toString () )) {
			return true;
		}

		return false;
	}
	public static function isCachingEnabled() {
		// Page cache incompatible with Multisite (htaccess rules conflict per-domain)
		if ( is_multisite() ) {
			return false;
		}

		// Divi Visual Builder sessions must never be cached
		if ( isset( $_GET['et_fb'] ) || isset( $_GET['et_pb_preview'] ) ) {
			return false;
		}

		// just return false with this filter if you don't want the page to be cached
		$enabled = apply_filters ( 'fastcache_page_cache_set_caching', true );

		if (! $enabled) {
			return false;
		}

		$params = Plugin::getPluginParams ();

		// Se il caching via htaccess è attivo, aggiungi i controlli metodo e XHR
		if ($params->get('htaccess_cache_enable', '1')) {
			
			// Escludi richieste con metodo diverso da GET o HEAD
			$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : '';
			if (!in_array($method, ['GET', 'HEAD'])) {
				return false;
			}
			
			// Escludi chiamate AJAX/XHR o fetch programmatiche
			$xhrHeader = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
			$jsonFetch = (
					(isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
					(isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
					);
			$crossOrigin = (
					isset($_SERVER['HTTP_ORIGIN']) &&
					parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST) !== parse_url(site_url(), PHP_URL_HOST)
					);
			
			if ($xhrHeader || $jsonFetch || $crossOrigin) {
				return false;
			}
		}

		if ($params->get ( 'cache_enable', '1' ) && Utility::isGuest () && ! self::isExcluded ( $params )) {
			return true;
		}

		return false;
	}
}

