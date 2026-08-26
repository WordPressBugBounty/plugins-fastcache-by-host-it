<?php

namespace FastCache\Host;

class HWorkarounds {
	private $version;
	private $plugin_name;
	public function __construct() {
		$this->version = FASTCACHE_VERSION;
		$this->plugin_name = FASTCACHEHOST_HOST_PLUGINNAME;
	}
	public function identify() {
		if (defined ( 'WOOCOMMERCE_VERSION' ) && defined ( 'YITH_WCWL' )) {
			require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'workarounds/Yithwcwl.class.php';
			$yith = new Yith_Wishlist ();
			$yith->sub_add_to_wishlist ();
			add_filter ( 'yith_wcwl_add_to_wishlisth_button_html', array (
					$yith,
					"filtro"
			), 999, 5 );
		}
		if (defined ( 'WOOCOMMERCE_VERSION' )) {
			$theme = wp_get_theme ();
			if ($theme->get ( 'Name' ) == 'Storefront' || $theme->get ( 'Name' ) == 'Storefront Child') {
				require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'workarounds/Storefront.class.php';
				$Storefront = new Storefront ();
				if (storefront_is_woocommerce_activated () && get_query_var ( 'esi_action' ) === 'storefront') {
					$Storefront->buildContent ();
				}
				$Storefront->actions ();
			}
		}
		if (defined ( 'WOOCOMMERCE_VERSION' )) {
			$theme = wp_get_theme ();
			if ($theme->get ( 'Name' ) == 'Blocksy' || $theme->get ( 'Name' ) == 'Blocksy Child') {
				require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'workarounds/EsiMiniCart.class.php';
				$esiMiniCart = new EsiMiniCart ();
				$esiMiniCart->actions ();
			}
		}
		if (defined ( 'WOOCOMMERCE_VERSION' )) {
			$theme = wp_get_theme ();
			if ($theme->get ( 'Name' ) == 'Rey' || $theme->get ( 'Name' ) == 'Rey Child') {
				require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'workarounds/Rey.class.php';
				$Storefront = new Rey ();
				$Storefront->actions ();
			}
		}
		if (function_exists ( 'get_plugins' )) {
			$plugins = get_plugins ();
			if (in_array ( 'handl-utm-grabber', $plugins )) {
				exit ();
			}
		}
		if (defined ( 'WOOCOMMERCE_VERSION' )) {
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				// $cookie_name = FASTCACHEHOST_WCFAST;
				// $cookie_value = '1';
				// $cookie_expiration = time() + (365 * 24 * 60 * 60);
				// if (!isset($_COOKIE[$cookie_name]) || $_COOKIE[$cookie_name]!=$cookie_value) {
				//      $dominio = $_SERVER['HTTP_HOST'];
				//      $dominioArr=explode(".",$dominio);
				//      if(count($dominioArr)>2) {
				//              $dominio = "." . $dominioArr[count($dominioArr)-2] . "." . $dominioArr[count($dominioArr)-1];
				//      }
				//      setcookie($cookie_name, $cookie_value, $cookie_expiration, "/", $dominio);
				//}
				header("X-HST-" . FASTCACHEHOST_WCFAST . ":1");
			}
		}
	}
	public function identify_after_get_header() {
	}
	public function identifyInInit() {
		$plugins = get_plugins ();
		if (in_array ( 'handl-utm-grabber/handl-utm-grabber.php', array_keys ( $plugins ) )) {
			require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'workarounds/handlUtmGrabber.class.php';
			remove_action ( 'init', "CaptureUTMs" );
			$overrider = new HandlUtmGrabber ();
			add_action ( "init", array (
					$overrider,
					"CaptureUTMs"
			) );
		}
	}
	public function register_esi_endpoint() {
		add_rewrite_rule ( '^hstesi-yith_wcwl/?$', 'index.php?esi_action=add_to_wishlist', 'top' );
		add_rewrite_rule ( '^hstesi-storefront/?$', 'index.php?esi_action=storefront', 'top' );
		add_rewrite_tag ( '%esi_action%', '([^&]+)' );

		add_filter ( 'wp_nonce_field', array (
				$this,
				'esi_nonce_handler'
		), 9999, 4 );
	}
	function esi_nonce_handler($field, $action, $name, $referer) {
		// Modifica il codice del nonce con un tag ESI
		$esi_nonce = "<esi:include src='/hstesi-wpnonce/?action=" . esc_attr ( $action ) . "' />";
		$esi_nonce = "host";

		// Restituisci il tag ESI al posto del campo nonce
		return '<input type="hidden" name="' . esc_attr ( $name ) . '" value="' . $esi_nonce . '" />';
	}
}