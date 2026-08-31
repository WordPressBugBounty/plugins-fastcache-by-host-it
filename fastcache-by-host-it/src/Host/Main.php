<?php
// If this file is called directly, abort.
if (! defined ( 'WPINC' )) {
	die ();
}

add_filter ( 'fastcache_host_v_purge_urls', "host_v_purge_urls_func", 99, 1 );
function host_v_purge_urls_func($urls) {
	global $host_v_purge_urls_func_var;
	if (is_array ( $urls ) && ! empty ( $urls )) {
		foreach ( $urls as $k => $v ) {
			$host_v_purge_urls_func_var [$v] ['url'] = $v;
			if (! isset ( $host_v_purge_urls_func_var [$v] ['done'] )) {
				$host_v_purge_urls_func_var [$v] ['done'] = 0;
			}
		}
	}
	return $urls;
}
;
$perm = wp_kses_allowed_html ( 'post' );
$perm ['input'] = [ 
		"type" => 1,
		"name" => 1,
		"id" => 1,
		"value" => 1,
		"data-posttype" => 1,
		"class" => 1,
		"checked" => 1,
		"disabled" => 1
];
$perm ["svg"] = [ 
		"width" => true,
		"height" => true,
		"class" => true,
		"fill" => true,
		"aria-hidden" => true,
		"viewbox" => true,
		"stroke" => true,
		"stroke-width" => true,
		"style" => true
];
$perm ["path"] = [ 
		"stroke-linecap" => true,
		"stroke-linejoin" => true,
		"stroke-width" => true,
		"stroke" => true,
		"d" => true,
		"style" => true,
		"fill" => true
];
$perm ["g"] = [ ];
add_filter ( 'safe_style_css', function ($styles) {
	$styles [] = 'background-color';
	$styles [] = 'border-color';
	$styles [] = 'border-width';
	$styles [] = 'border-radius';
	$styles [] = 'clip-rule';
	$styles [] = 'color';
	$styles [] = 'fill-rule';
	$styles [] = 'flex';
	$styles [] = 'flex-direction';
	$styles [] = 'font-size';
	$styles [] = 'font-weight';
	$styles [] = 'image-rendering';
	$styles [] = 'line-height';
	$styles [] = 'margin-bottom';
	$styles [] = 'opacity';
	$styles [] = 'padding';
	$styles [] = 'position';
	$styles [] = 'right';
	$styles [] = 'top';
	$styles [] = 'shape-rendering';
	$styles [] = 'text-rendering';
	$styles [] = 'width';
	$styles [] = 'z-index';

	return $styles;
} );
define ( 'FASTCACHEHOST_ALLOWEDHTML', $perm );

define ( 'FASTCACHEHOST_HOST_PLUGINNAME_PATH', 'fastcache' );
if (getenv ( 'FASTCACHEHOST_HOST_ENDPOINTCACHE' )) {
	define ( 'FASTCACHEHOST_HOST_ENDPOINTCACHE', getenv ( 'FASTCACHEHOST_HOST_ENDPOINTCACHE' ) );
} else {
	define ( 'FASTCACHEHOST_HOST_ENDPOINTCACHE', 'https://purge.hostcdn.it:8443/' );
}
define ( 'FASTCACHEHOST_HOSTNAME', wp_parse_url ( get_site_url (), PHP_URL_HOST ) );
define ( "FASTCACHEHOST_LOGPATH", HOST_PLUGIN_DIR . "logs/log.txt" );
define ( "FASTCACHEHOST_WCFAST", "wcfast" );
$options = get_option (FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
if (isset ( $options ['button-checkbox-activate-log'] )) {
	define ( "FASTCACHEHOST_LOGACTIVE", $options ['button-checkbox-activate-log'] );
} else {
	define ( "FASTCACHEHOST_LOGACTIVE", 0 );
}
// pluggable.php is already included by WordPress core in wp-settings.php;
// re-requiring it here on Multisite can trigger fatal errors on admin pages
// when cookie constants (AUTH_COOKIE, SECURE_AUTH_COOKIE) are not yet defined.
if ( ! function_exists( 'is_user_logged_in' ) ) {
	require_once( ABSPATH . 'wp-includes/pluggable.php' );
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path ( __FILE__ ) . 'includes/class-' . FASTCACHEHOST_HOST_PLUGINNAME_PATH . '.php';
/**
 * The core plugin class that is used to common environment.
 */
require plugin_dir_path ( __FILE__ ) . 'common/class-' . FASTCACHEHOST_HOST_PLUGINNAME_PATH . '-common.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function fastcache_host_run_fastcache_host() {
	$plugin = new FastCache\Host\HFastCache ();
	$plugin->run ();
}
fastcache_host_run_fastcache_host ();
