<?php
namespace FastCache\Host;

class EsiMiniCart
{
	public function __construct()
	{
	}
	
	public function actions()
	{
		// Qui sostituiamo il template originale con la chiamata ESI
		add_filter('woocommerce_locate_template', array($this, 'overrideMiniCart'), 10, 3);
	}
	
	/**
	 * Sostituisce il file mini-cart.php con un placeholder ESI
	 */
	public function overrideMiniCart($template, $template_name, $template_path)
	{
		if ($template_name === 'cart/mini-cart.php') {
			return __DIR__ . '/templates/esi-mini-cart.php';
		}
		return $template;
	}
	
	/**
	 * Stampa l’ESI tag
	 */
	public function makeEsi()
	{
		$html = '<esi:include src="/hstesi-mini-cart/?fragment=cart" />';
		echo wp_kses($html, FASTCACHEHOST_ALLOWEDHTML);
	}
	
	/**
	 * Costruisce il contenuto del mini-cart vero e proprio
	 */
	public function buildContent()
	{
		// Headers per disabilitare cache lato varnish
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		header('X-HST-CACHE-Enabled: NO:ESI MiniCart', true);
		
		wc_get_template('cart/mini-cart.php');
		exit();
	}
}