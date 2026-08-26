<?php

namespace FastCache\Host;

class Storefront {
	public function __construct() {
	}
	public function actions() {
		if (function_exists ( 'storefront_header_cart' )) {
			remove_action ( 'storefront_header', 'storefront_header_cart', 60 );
			add_action ( 'storefront_header', array (
					$this,
					'makeEsi'
			), 60 );
		}
	}
	public function makeEsi() {
		$html = "<esi:include src=\"/hstesi-storefront/?fragment=cart\" />";
		echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
	}
	public function buildContent() {
		if (is_cart ()) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		$html = "<ul id=\"site-header-cart\" class=\"site-header-cart menu\">";
		$html .= "<li class=\"" . esc_attr ( $class ) . "\">";
		$html .= $this->get_storefront_cart_link ();
		$html .= "</li>";
		$html .= "<li>";
		$html .= $this->get_widget_output ( 'WC_Widget_Cart', 'title=' );
		$html .= "</li>";
		$html .= "</ul>";
		echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
		exit ();
	}
	public function get_widget_output($widget, $instance = array (), $args = array ()) {
		ob_start ();
		the_widget ( $widget, $instance, $args );
		$widget_output = ob_get_clean ();
		return $widget_output;
	}
	public function get_storefront_cart_link() {
		ob_start ();
		storefront_cart_link ();
		$widget_output = ob_get_clean ();
		return $widget_output;
	}
}