<?php
// Questo file sostituisce il mini-cart.php di WooCommerce
$html = '<esi:include src="/hstesi-mini-cart/?fragment=cart" />';
echo wp_kses($html, FASTCACHEHOST_ALLOWEDHTML);
