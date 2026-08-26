<?php

/**
 * FastCache - Nonce REST Endpoint
 * Provides fresh nonces to replace stale ones in cached pages
 *
 * @package   FastCache
 * @author    Host.it <info@host.it>
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 */

namespace FastCache\Core;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

class NonceEndpoint {

	/**
	 * Registra il REST endpoint
	 */
	public static function register() {
		add_action( 'rest_api_init', [ __CLASS__, 'registerRoute' ] );
	}

	/**
	 * Registra la rotta REST
	 */
	public static function registerRoute() {
		register_rest_route( 'fastcache/v1', '/nonces', [
			[
				'methods'             => 'GET',
				'callback'            => [ __CLASS__, 'handleNonceRequest' ],
				'permission_callback' => '__return_true', // Pubblico - i nonce sono comunque validi solo per action specifici
				'args'                => [
					'placeholders' => [
						'description' => 'Comma-separated list of nonce placeholders',
						'type'        => 'string',
						'required'    => true,
					],
				],
			],
		] );
	}

	/**
	 * Gestisce le richieste di nonce freschi
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public static function handleNonceRequest( $request ) {
		$placeholders_param = $request->get_param( 'placeholders' );
		$nonce_action = $request->get_param( 'nonce_action' );

		if ( empty( $placeholders_param ) ) {
			return new \WP_REST_Response(
				[
					'success' => false,
					'message' => 'Missing placeholders parameter',
				],
				400
			);
		}

		// Parse placeholders
		$placeholders = array_filter( array_map( 'trim', explode( ',', $placeholders_param ) ) );

		if ( empty( $placeholders ) ) {
			return new \WP_REST_Response(
				[
					'success' => false,
					'message' => 'No valid placeholders provided',
				],
				400
			);
		}

		// Genera nonce freschi per i placeholder (passa l'action se disponibile)
		$nonces = self::generateFreshNonces( $placeholders, $nonce_action );

		return new \WP_REST_Response(
			[
				'success' => true,
				'nonces'  => $nonces,
				'cached'  => false,
			],
			200
		);
	}

	/**
	 * Genera nonce freschi per i placeholder
	 *
	 * @param array $placeholders
	 * @param string $nonce_action Action specifica fornita dal data-nonce-action del form
	 * @return array Map placeholder => nonce fresco
	 */
	private static function generateFreshNonces( $placeholders, $nonce_action = null ) {
		$nonces = [];

		// Se c'è un nonce_action specifico dal client, usalo
		if ( ! empty( $nonce_action ) ) {
			$nonce_action = sanitize_text_field( $nonce_action );

			foreach ( $placeholders as $placeholder ) {
				// Valida il formato del placeholder
				if ( ! preg_match( '/^FASTCACHE_NONCE_/', $placeholder ) ) {
					continue;
				}

				// Genera il nonce con l'action specifico fornito dal client
				$fresh_nonce = wp_create_nonce( $nonce_action );
				if ( $fresh_nonce ) {
					$nonces[ $placeholder ] = $fresh_nonce;
				}
			}

			return $nonces;
		}

		// Fallback: usa lo standard WordPress '_wpnonce' se data-nonce-action non è fornito
		foreach ( $placeholders as $placeholder ) {
			// Valida il formato del placeholder
			if ( ! preg_match( '/^FASTCACHE_NONCE_/', $placeholder ) ) {
				continue;
			}

			// Genera il nonce con lo standard WordPress
			$fresh_nonce = wp_create_nonce( '_wpnonce' );
			if ( $fresh_nonce ) {
				$nonces[ $placeholder ] = $fresh_nonce;
			}
		}

		return $nonces;
	}
}
