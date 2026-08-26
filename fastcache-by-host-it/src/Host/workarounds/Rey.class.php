<?php

namespace FastCache\Host;

class Rey {
	public function __construct() {
	}
	public function actions() {
		remove_action ( 'template_redirect', 'wc_track_product_view', 20 );
		remove_action ( 'template_redirect', 'reycore_wc__track_product_view', 20 );
		remove_action ( 'reycore/woocommerce/quickview/before_render', 'reycore_wc__track_product_view', 20 );
	}
}