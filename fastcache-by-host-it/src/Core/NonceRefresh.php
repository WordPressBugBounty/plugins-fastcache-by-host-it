<?php

/**
 * FastCache - Late Nonce Refresh
 * Handles nonce replacement and refresh to prevent stale nonces in cached pages
 *
 * @package   FastCache
 * @author    Host.it <info@host.it>
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 */

namespace FastCache\Core;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

class NonceRefresh {

	/**
	 * Sostituisce nonce nell'HTML con placeholder e inietta script refresh
	 *
	 * @param string $html
	 * @return string HTML modificato
	 */
	public static function processCacheContent( $html ) {
		// Verifica se il nonce refresh è abilitato
		if ( ! self::isNonceRefreshEnabled() ) {
			return $html;
		}

		// Skip per HTML non valido o troppo piccolo
		if ( empty( $html ) || strlen( $html ) < 200 ) {
			return $html;
		}

		// Estrai i nonce dall'HTML
		$nonces = self::extractNonces( $html );

		if ( empty( $nonces ) ) {
			return $html;
		}

		// Sostituisci i nonce con placeholder
		$html = self::replaceNoncesWithPlaceholders( $html, $nonces );

		// Inietta lo script che rigenera i nonce
		$html = self::injectNonceRefreshScript( $html, array_keys( $nonces ) );

		return $html;
	}

	/**
	 * Estrae i nonce dall'HTML
	 *
	 * @param string $html
	 * @return array Array di nonce trovati
	 */
	private static function extractNonces( $html ) {
		$nonces = [];

		// Cerca nonce negli input nascosti
		if ( preg_match_all(
			'#<input[^>]*name=["\']_wpnonce["\'][^>]*value=["\']([a-f0-9]+)["\'][^>]*>#i',
			$html,
			$matches
		) ) {
			foreach ( $matches[1] as $nonce ) {
				$nonces[ $nonce ] = '_wpnonce';
			}
		}

		// Cerca nonce in data attributes
		if ( preg_match_all(
			'#data-[\w-]*nonce=["\']([a-f0-9]+)["\']#i',
			$html,
			$matches
		) ) {
			foreach ( $matches[1] as $nonce ) {
				// Valida che sia effettivamente un nonce (10 char hex)
				if ( preg_match( '#^[a-f0-9]{10}$#i', $nonce ) ) {
					$nonces[ $nonce ] = 'data-nonce';
				}
			}
		}

		return $nonces;
	}

	/**
	 * Sostituisce i nonce con placeholder univoci
	 *
	 * @param string $html
	 * @param array $nonces
	 * @return string
	 */
	private static function replaceNoncesWithPlaceholders( $html, $nonces ) {
		foreach ( $nonces as $nonce => $type ) {
			$placeholder = 'FASTCACHE_NONCE_' . strtoupper( substr( $nonce, 0, 8 ) ) . '_' . substr( $nonce, -4 );

			// Sostituisci in input
			$html = str_ireplace(
				'value="' . $nonce . '"',
				'value="' . $placeholder . '"',
				$html
			);

			// Sostituisci in data attributes
			$html = preg_replace(
				'/(data-[\w-]*nonce)=["\']' . $nonce . '["\']/',
				'$1="' . $placeholder . '"',
				$html
			);
		}

		return $html;
	}

	/**
	 * Inietta lo script che rigenera i nonce fresh dal server
	 *
	 * @param string $html
	 * @param array $nonce_actions
	 * @return string
	 */
	private static function injectNonceRefreshScript( $html, $nonce_actions ) {
		// Ottieni l'URL del REST endpoint al momento della cache
		$rest_endpoint = rest_url( 'fastcache/v1/nonces' );

		$script = <<<JAVASCRIPT
<script>
(function() {
	if (typeof window.FastCacheNonceRefresh !== 'undefined') return;
	window.FastCacheNonceRefresh = true;

	// URL del REST endpoint iniettato al momento della cache
	var restUrl = '$rest_endpoint';

	// Estrai tutti i placeholder di nonce dal DOM
	var placeholders = new Set();
	var walker = document.createTreeWalker(
		document.body,
		NodeFilter.SHOW_TEXT | NodeFilter.SHOW_COMMENT,
		null,
		false
	);

	var node;
	while (node = walker.nextNode()) {
		if (node.nodeValue && node.nodeValue.indexOf('FASTCACHE_NONCE_') !== -1) {
			var matches = node.nodeValue.match(/FASTCACHE_NONCE_[A-F0-9_]+/g);
			if (matches) {
				matches.forEach(function(m) { placeholders.add(m); });
			}
		}
	}

	// Se non trova placeholder, cerca negli attributi
	if (placeholders.size === 0) {
		document.querySelectorAll('[data-nonce], input[name="_wpnonce"]').forEach(function(el) {
			var val = el.getAttribute('data-nonce') || el.value;
			if (val && val.indexOf('FASTCACHE_NONCE_') !== -1) {
				placeholders.add(val);
			}
		});
	}

	if (placeholders.size === 0) return; // Nessun placeholder trovato

	// Richiedi nonce freschi dal REST endpoint
	var nonceList = Array.from(placeholders).join(',');

	if (!restUrl) {
		console.warn('FastCache: REST API URL not configured');
		return;
	}

	// Leggi l'action dal data-nonce-action se presente
	var nonceAction = null;
	var nonceInput = document.querySelector('input[name="_wpnonce"]');
	if (nonceInput && nonceInput.getAttribute('data-nonce-action')) {
		nonceAction = nonceInput.getAttribute('data-nonce-action');
	}

	var nonceEndpoint = restUrl + '?placeholders=' + encodeURIComponent(nonceList);
	if (nonceAction) {
		nonceEndpoint += '&nonce_action=' + encodeURIComponent(nonceAction);
	}

	fetch(nonceEndpoint, {
		method: 'GET',
		credentials: 'same-origin',
		headers: {
			'Accept': 'application/json'
		}
	})
	.then(function(response) {
		if (!response.ok) throw new Error('HTTP ' + response.status);
		return response.json();
	})
	.then(function(data) {
		if (!data.success || !data.nonces) return;

		// Sostituisci placeholder con nonce freschi nel DOM
		replacePlaceholdersInDom(data.nonces);
	})
	.catch(function(error) {
		console.error('FastCache nonce refresh failed:', error);
	});

	function replacePlaceholdersInDom(nonceMap) {
		// Sostituisci negli input nascosti
		document.querySelectorAll('input[name="_wpnonce"]').forEach(function(el) {
			var placeholder = el.value;
			if (nonceMap[placeholder]) {
				el.value = nonceMap[placeholder];
			}
		});

		// Sostituisci nei data attributes
		document.querySelectorAll('[data-nonce]').forEach(function(el) {
			var placeholder = el.getAttribute('data-nonce');
			if (nonceMap[placeholder]) {
				el.setAttribute('data-nonce', nonceMap[placeholder]);
			}
		});

		// Sostituisci in altri data-*-nonce attributes
		document.querySelectorAll('[data-nonce]').forEach(function(el) {
			Array.from(el.attributes).forEach(function(attr) {
				if (attr.name.indexOf('nonce') !== -1) {
					var placeholder = attr.value;
					if (nonceMap[placeholder]) {
						el.setAttribute(attr.name, nonceMap[placeholder]);
					}
				}
			});
		});
	}
})();
</script>
JAVASCRIPT;

		// Inietta lo script prima di </body>
		if ( strpos( $html, '</body>' ) !== false ) {
			$html = str_replace( '</body>', $script . '</body>', $html );
		} else {
			// Se non c'è </body>, inietta alla fine
			$html .= $script;
		}

		return $html;
	}

	/**
	 * Verifica se il nonce refresh è abilitato nelle opzioni di FastCache
	 *
	 * @return bool
	 */
	private static function isNonceRefreshEnabled() {
		static $enabled = null;

		if ( $enabled === null ) {
			if ( class_exists( '\FastCache\Platform\Plugin' ) ) {
				$params  = \FastCache\Platform\Plugin::getPluginParams();
				$enabled = (bool) $params->get( 'enable_nonce_refresh', 1 );
			} else {
				// Fallback: leggi direttamente le opzioni WordPress quando
				// la classe Plugin non è ancora caricata (es. mu-plugin context)
				$options = get_option( 'fastcache_settings', [] );
				$enabled = isset( $options['enable_nonce_refresh'] ) ? (bool) $options['enable_nonce_refresh'] : true;
			}
		}

		return $enabled;
	}
}
