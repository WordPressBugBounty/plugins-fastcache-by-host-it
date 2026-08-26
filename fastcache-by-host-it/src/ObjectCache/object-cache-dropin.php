<?php
/*
Plugin Name: FastCache Object Cache Drop-In
Description: Object Cache drop-in provided by FastCache plugin. Enable/Disable this via FastCache settings.
Version: 1.0.0
Author: Host.it
*/

if (!defined('ABSPATH')) {
    exit;
}

if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	// Prefer the WP content constant if available
	if ( defined( 'WP_CONTENT_DIR' ) ) {
		$content_dir = rtrim( WP_CONTENT_DIR, DIRECTORY_SEPARATOR );
	} else {
		// Fallback to ABSPATH\wp-content
		$content_dir = rtrim( ABSPATH . 'wp-content', DIRECTORY_SEPARATOR );
	}
	
	define( 'WP_PLUGIN_DIR', $content_dir . DIRECTORY_SEPARATOR . 'plugins' );
}

if (!defined('FASTCACHE_FILE_PATH')) {
	// Discover plugin path
	$fastcacheRoot = trailingslashit(WP_PLUGIN_DIR) . 'fastcache-by-host-it/';
	$fastcacheFile = $fastcacheRoot . 'fastcache.php';
	
	if (file_exists($fastcacheFile)) {
		// Just ensure constants are loaded without re-initing the whole plugin hook tree
		if (!defined('FASTCACHE_OBJECTCACHE_DIR')) {
			define('FASTCACHE_OBJECTCACHE_DIR', $fastcacheRoot);
		}
	} else {
		return;
	}
}

// Ensure the Composer autoloader is available or the ObjectCache class exists
if (!class_exists('\FastCache\ObjectCache\ObjectCache')) {
	$autoloader = FASTCACHE_OBJECTCACHE_DIR . 'vendor/autoload.php';
	if (file_exists($autoloader)) {
		require_once $autoloader;
	} else {
		// Fallback to manual loading if no composer
		$base = FASTCACHE_OBJECTCACHE_DIR . 'src/ObjectCache/';
		if (file_exists($base . 'ObjectCache.php')) {
			require_once $base . 'BackendDetector.php';
			require_once $base . 'ObjectCache.php';
		} else {
			return; // Can't load
		}
	}
}

function wp_cache_add($key, $data, $group = '', $expire = 0)
{
    global $wp_object_cache;
    if (!$wp_object_cache)
    	return false;
    if (empty($group))
        $group = 'default';
    if (wp_cache_get($key, $group) !== false)
        return false;
    return wp_cache_set($key, $data, $group, $expire);
}

function wp_cache_add_multiple(array $data, $group = '', $expire = 0)
{
    $added = [];
    foreach ($data as $key => $value) {
        $added[$key] = wp_cache_add($key, $value, $group, $expire);
    }
    return $added;
}

function wp_cache_close()
{
    return true;
}

function wp_cache_decrease($key, $offset = 1, $group = '')
{
    global $wp_object_cache;
    if (!$wp_object_cache)
    	return false;
    if (empty($group))
        $group = 'default';
    $value = wp_cache_get($key, $group);
    if ($value === false)
        return false;
    $value -= $offset;
    wp_cache_set($key, $value, $group);
    return $value;
}

function wp_cache_decr($key, $offset = 1, $group = '')
{
    return wp_cache_decrease($key, $offset, $group);
}

function wp_cache_delete($key, $group = '')
{
    global $wp_object_cache;
    if (!$wp_object_cache)
    	return false;
    if (empty($group))
        $group = 'default';
    return $wp_object_cache->delete($key, $group);
}

function wp_cache_delete_multiple(array $keys, $group = '')
{
    $deleted = [];
    foreach ($keys as $key) {
        $deleted[$key] = wp_cache_delete($key, $group);
    }
    return $deleted;
}

function wp_cache_flush()
{
    global $wp_object_cache;
    if (!$wp_object_cache)
    	return false;
    return $wp_object_cache->flush();
}

function wp_cache_flush_group($group)
{
    return false;
}

function wp_cache_supports($feature)
{
    switch ($feature) {
        case 'add_multiple':
        case 'set_multiple':
        case 'get_multiple':
        case 'delete_multiple':
            return true;
        case 'flush_group':
        case 'flush_runtime':
        default:
            return false;
    }
}

function wp_cache_get($key, $group = '', $force = false, &$found = null)
{
	global $wp_object_cache;
	if (!$wp_object_cache) {
		$found = false;
		return false;
	}
    if (empty($group))
        $group = 'default';
    return $wp_object_cache->get($key, $group, $force, $found);
}

function wp_cache_get_multiple($keys, $group = '', $force = false)
{
    $values = [];
    foreach ($keys as $key) {
        $values[$key] = wp_cache_get($key, $group, $force);
    }
    return $values;
}

function wp_cache_increase($key, $offset = 1, $group = '')
{
    global $wp_object_cache;
    if (!$wp_object_cache)
    	return false;
    if (empty($group))
        $group = 'default';
    $value = wp_cache_get($key, $group);
    if ($value === false)
        return false;
    $value += $offset;
    wp_cache_set($key, $value, $group);
    return $value;
}

function wp_cache_incr($key, $offset = 1, $group = '')
{
    return wp_cache_increase($key, $offset, $group);
}

function wp_cache_init()
{
    global $wp_object_cache;
    $wp_object_cache = \FastCache\ObjectCache\ObjectCache::getInstance();
}

function wp_cache_replace($key, $data, $group = '', $expire = 0)
{
    global $wp_object_cache;
    if (!$wp_object_cache)
    	return false;
    if (empty($group))
        $group = 'default';
    if (wp_cache_get($key, $group) === false)
        return false;
    return wp_cache_set($key, $data, $group, $expire);
}

function wp_cache_set($key, $data, $group = '', $expire = 0)
{
    global $wp_object_cache;
    if (!$wp_object_cache)
    	return false;
    if (empty($group))
        $group = 'default';
    return $wp_object_cache->set($key, $data, $group, $expire);
}

function wp_cache_set_multiple(array $data, $group = '', $expire = 0)
{
    $set = [];
    foreach ($data as $key => $value) {
        $set[$key] = wp_cache_set($key, $value, $group, $expire);
    }
    return $set;
}

function wp_cache_switch_to_blog($blog_id)
{
    return true;
}

function wp_cache_add_global_groups($groups)
{
    // Handle via non-persistent groups if needed
}

function wp_cache_add_non_persistent_groups($groups)
{
    // Handled directly via settings configuration in FastCache
}
