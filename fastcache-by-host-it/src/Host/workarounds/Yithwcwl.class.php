<?php

namespace FastCache\Host;

class Yith_Wishlist {
	public function __construct() {
	}
	public function sub_add_to_wishlist() {
		if (get_query_var ( 'esi_action' ) === 'add_to_wishlist') {
			if (isset ( $_GET ['productid'] ) && wp_verify_nonce ( sanitize_text_field ( wp_unslash ( $_POST ['nonce'] ) ), 'controlloesi' )) {
				$pid = sanitize_text_field ( $_GET ['productid'] );
				if (! intval ( $pid )) {
					exit ();
				}
			}
			if ($pid > 0) {
				$html = \YITH_WCWL_Shortcode::add_to_wishlist ( array (
						'product_id' => $pid
				) );
				echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
			}
			exit ();
		}
	}
	public function filtro($html, $due, $tre, $quattro, $cinque) {
		$pid = $cinque ['product_id'];
		$nonce = wp_create_nonce ( 'controlloesi' );

		$html = "<esi:include src=\"/hstesi-yith_wcwl/?productid=" . $pid . "&nonce=" . $nonce . " />";
		return wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
	}
}