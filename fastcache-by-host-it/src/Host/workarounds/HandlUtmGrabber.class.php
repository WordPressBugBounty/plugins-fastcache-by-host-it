<?php

namespace FastCache\Host;

class HandlUtmGrabber {
	public function __construct() {
	}
	public function CaptureUTMs() {
		/*
		 * if ( is_admin() || $GLOBALS['pagenow'] === 'wp-login.php' || defined( 'DOING_CRON' ) ) {
		 * return "";
		 * }
		 *
		 * if (!isset($_COOKIE['handl_original_ref']))
		 * $_COOKIE['handl_original_ref'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		 *
		 * if (!isset($_COOKIE['handl_landing_page']) && isset($_SERVER["SERVER_NAME"]) && isset($_SERVER["REQUEST_URI"]))
		 * $_COOKIE['handl_landing_page'] = ( isset($_SERVER["HTTPS"]) ? 'https://' : 'http://' ) . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		 *
		 * if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] != "")
		 * $_COOKIE['handl_ip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
		 * else
		 * $_COOKIE['handl_ip'] = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';
		 *
		 * $_COOKIE['handl_ref'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		 *
		 * if (isset($_SERVER["SERVER_NAME"]) && isset($_SERVER["REQUEST_URI"]))
		 * $_COOKIE['handl_url'] = ( isset($_SERVER["HTTPS"]) ? 'https://' : 'http://' ) . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		 *
		 * $fields = array('utm_source','utm_medium','utm_term', 'utm_content', 'utm_campaign', 'gclid', 'handl_original_ref', 'handl_landing_page', 'handl_ip', 'handl_ref', 'handl_url', 'email', 'username');
		 *
		 * $cookie_field = '';
		 * foreach ($fields as $id=>$field){
		 *
		 * if (isset($_GET[$field]) && $_GET[$field] != '') {
		 * $cookie_field = htmlspecialchars($_GET[$field],ENT_QUOTES, 'UTF-8');
		 * $doset=1;
		 * } elseif(isset($_COOKIE[$field]) && $_COOKIE[$field] != ''){
		 * $cookie_field = $_COOKIE[$field];
		 * $doset=0;
		 * }else{
		 * $cookie_field = '';
		 * $doset=0;
		 * }
		 *
		 * $domain = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : '';
		 * if ( strtolower( substr($domain, 0, 4) ) == 'www.' ) $domain = substr($domain, 4);
		 * if ( substr($domain, 0, 1) != '.' && $domain != "localhost" && $domain != "handl-sandbox" ) $domain = '.'.$domain;
		 * if($doset==1) {
		 * setcookie($field, $cookie_field , time()+61*60*24*30, '/', $domain );
		 * $_COOKIE[$field] = $cookie_field;
		 * }
		 * add_shortcode($field, function() use ($field) {return urldecode($_COOKIE[$field]);});
		 * add_shortcode($field."_i", function($atts,$content) use ($field) {return sprintf($content,urldecode($_COOKIE[preg_replace("/_i$/",
		 * "",$field)]));});
		 *
		 * //This is for Gravity Forms
		 * add_filter( 'gform_field_value_'.$field, function() use ($field) {return urldecode($_COOKIE[$field]); } );
		 * }
		 */
	}
}