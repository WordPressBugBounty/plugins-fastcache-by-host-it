<?php

namespace FastCache\ObjectCache;

class ObjectCache
{

    private static $instance = null;
    private $adapter = null;
    private $stats = [
        'hits' => 0,
        'misses' => 0,
        'writes' => 0,
        'deletes' => 0
    ];
    private $nonPersistentGroups = [];
    private $memoryCache = [];
    private $defaultTTL = 3600;
    private $excludeLoggedIn = true;
    private $excludeAdmin = true;
    private $excludeFrontend = false;
    private $isInitializing = false;
    private $loggingEnabled = true;

    /**
     * Whether the current REQUEST is excluded from persistent caching.
     * Calculated once during init() and never changes during the request lifecycle.
     */
    private $requestExcluded = false;

    /**
     * Stored settings and backend name for lazy adapter initialization.
     * When a request is excluded, the adapter is NOT created during init()
     * to guarantee zero filesystem activity. It's created lazily only if
     * a delete() needs to propagate to invalidate stale data.
     */
    private $pendingSettings = [];
    private $pendingBackend = 'file';

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    public function init()
    {
        // Prevent infinite recursion if get_option calls back into the cache
        if ($this->isInitializing) {
            return;
        }
        $this->isInitializing = true;

        // Determine request-level exclusion FIRST (before adapter, using only $_SERVER/$_COOKIE)
        $this->requestExcluded = $this->determineRequestExclusion();

        $settings = get_option('fastcache_settings', []);
        $backend = BackendDetector::getDetectedBackendName($settings);

        $this->defaultTTL = (isset($settings['object_cache_default_ttl']) && $settings['object_cache_default_ttl'] !== '')
            ? (int) $settings['object_cache_default_ttl']
            : 0;

        if ($this->defaultTTL === 0 && $backend === 'file') {
            $this->defaultTTL = 3600;
        }

        $np_groups_raw = isset($settings['object_cache_non_persistent_groups']) ? $settings['object_cache_non_persistent_groups'] : "counts\nplugins\nthemes\nwc_session_data\nsession-tokens\noptions\nalloptions\nwc_cache\nproduct";
        $groups = array_map('trim', explode("\n", str_replace("\r", '', $np_groups_raw)));
        $this->nonPersistentGroups = array_filter($groups);

        $this->excludeLoggedIn = isset($settings['object_cache_exclude_logged_in']) ? (bool) $settings['object_cache_exclude_logged_in'] : true;
        $this->excludeAdmin = isset($settings['object_cache_exclude_admin']) ? (bool) $settings['object_cache_exclude_admin'] : true;
        $this->excludeFrontend = isset($settings['object_cache_exclude_frontend']) ? (bool) $settings['object_cache_exclude_frontend'] : false;
        $this->loggingEnabled = isset($settings['object_cache_logging_enable']) ? (bool) $settings['object_cache_logging_enable'] : true;

        // Re-evaluate with loaded settings (the initial determination used defaults)
        $this->requestExcluded = $this->determineRequestExclusion();

        if ($this->requestExcluded) {
            // EXCLUDED REQUEST: Do NOT create the adapter.
            // No adapter = zero filesystem/backend activity guaranteed.
            // Only in-memory cache will work for this request.
            // Store settings for lazy adapter creation if delete() needs to invalidate.
            $this->pendingSettings = $settings;
            $this->pendingBackend = $backend;
            $this->adapter = null;
        } else {
            // NORMAL REQUEST: Create and connect the adapter for persistent caching.
            $this->createAdapter($backend, $settings);
        }

        $this->isInitializing = false;
    }

    /**
     * Create and connect the backend adapter.
     */
    private function createAdapter($backend, $settings)
    {
        switch ($backend) {
            case 'redis':
                require_once __DIR__ . '/Adapter/AdapterInterface.php';
                require_once __DIR__ . '/Adapter/RedisAdapter.php';
                $this->adapter = new Adapter\RedisAdapter();
                break;
            case 'memcached':
                require_once __DIR__ . '/Adapter/AdapterInterface.php';
                require_once __DIR__ . '/Adapter/MemcachedAdapter.php';
                $this->adapter = new Adapter\MemcachedAdapter();
                break;
            case 'file':
            default:
                require_once __DIR__ . '/Adapter/AdapterInterface.php';
                require_once __DIR__ . '/Adapter/FileAdapter.php';
                $this->adapter = new Adapter\FileAdapter();
                break;
        }

        if ($this->adapter) {
            $this->adapter->connect($settings);
        }
    }

    /**
     * Ensure the adapter exists for operations that MUST reach the backend
     * (e.g., delete propagation on excluded requests).
     */
    private function ensureAdapter()
    {
        if ($this->adapter === null) {
            $this->createAdapter($this->pendingBackend, $this->pendingSettings);
        }
        return $this->adapter !== null;
    }

    /**
     * Determine if the CURRENT REQUEST should be excluded from persistent caching.
     * This uses ONLY raw PHP superglobals ($_SERVER, $_COOKIE) - NO WordPress functions.
     * This prevents any infinite recursion.
     */
    private function determineRequestExclusion()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';

        // 0) Always exclude static assets and favicon (they get redirected to index.php)
        if (preg_match('/\.(ico|png|jpg|jpeg|gif|css|js|woff|woff2|ttf|eot|svg|webp|map)$/i', $uri)) {
            return true;
        }

        // 1) Always exclude WP-Cron
        if (defined('DOING_CRON')) {
            return true;
        }
        if (strpos($scriptName, 'wp-cron.php') !== false) {
            return true;
        }

        // 1b) Always exclude FastCache's own backend requests
        if (strpos($uri, 'fastcacherunbackend') !== false) {
            return true;
        }

        // 2) Always exclude WP-CLI
        if (defined('WP_CLI') && WP_CLI) {
            return true;
        }

        // 3) Always exclude wp-login.php
        if (strpos($scriptName, 'wp-login.php') !== false || strpos($uri, '/wp-login.php') !== false) {
            return true;
        }

        // 3b) Always exclude FastCache's own settings page and its AJAX requests
        // This ensures configuration can always be modified even when admin caching is enabled
        if (strpos($uri, 'page=fastcache') !== false) {
            return true;
        }
        // Exclude FastCache internal AJAX actions (multiselect, pagespeed, object cache, purge, etc.)
        if (strpos($uri, 'admin-ajax.php') !== false && isset($_REQUEST['action'])) {
            $fcActions = ['multiselect', 'fastcache_hook_pagespeed', 'fastcache_object_cache_action', 'hstPurgeCache', 'testCache', 'attivazionefastcache'];
            if (in_array($_REQUEST['action'], $fcActions, true)) {
                return true;
            }
        }

        // 4) Exclude Admin area (includes admin-ajax.php!)
        if ($this->excludeAdmin) {
            if (defined('WP_ADMIN') && WP_ADMIN) {
                return true;
            }
            // DOING_AJAX is defined in admin-ajax.php BEFORE WordPress loads
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return true;
            }
            if (strpos($uri, '/wp-admin/') !== false) {
                return true;
            }
        }

        // 4b) Exclude frontend: all non-admin requests are excluded from persistent cache.
        //     This lets you test object cache exclusively in wp-admin.
        //     "Frontend" = any request that is NOT in /wp-admin/ and NOT WP_ADMIN.
        if ($this->excludeFrontend) {
            $isAdminUrl = strpos($uri, '/wp-admin/') !== false;
            $isWpAdmin  = defined('WP_ADMIN') && WP_ADMIN;
            if (!$isAdminUrl && !$isWpAdmin) {
                return true;
            }
        }

        // 5) Exclude logged-in users (cookie-based check - safe, no DB calls)
        if ($this->excludeLoggedIn) {
            foreach ($_COOKIE as $name => $value) {
                if (strpos($name, 'wordpress_logged_in_') === 0 || strpos($name, 'wordpress_sec_') === 0) {
                    return true;
                }
            }
        }

        // 6) Exclude frontend requests with an active WooCommerce session cookie.
        //    This covers anonymous users who have added items to the cart on the frontend.
        //    BUT: Do NOT exclude admin area, where WooCommerce cart is irrelevant.
        $isAdminUrl = strpos($uri, '/wp-admin/') !== false;
        $isWpAdmin  = defined('WP_ADMIN') && WP_ADMIN;
        if (!$isAdminUrl && !$isWpAdmin) {
            foreach ($_COOKIE as $name => $value) {
                if (strpos($name, 'wp_woocommerce_session_') === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Groups that are ALWAYS non-persistent regardless of user configuration.
     * These contain per-user data that must never be shared between requests/users.
     */
    private static $alwaysNonPersistentGroups = [
        'wc_session_data',   // WooCommerce cart/session data (per-user, CRITICAL)
        'session-tokens',    // WordPress login session tokens (CRITICAL)
        'user_meta',         // User-specific meta
        'usermeta',          // Alias used by some plugins
        'options',
    	'alloptions',
		'wc_cache',
		'product'
    ];

    /**
     * Check if a specific cache group is non-persistent, OR if the entire request is excluded.
     */
    private function shouldSkipPersistence($group)
    {
        // Request-level exclusion (calculated once in init)
        if ($this->requestExcluded) {
            return true;
        }

        // Hardcoded: user-specific groups that must NEVER be persisted to a shared backend.
        // This is a security guarantee — not user-configurable.
        if (in_array($group, self::$alwaysNonPersistentGroups, true)) {
            return true;
        }

        // User-configured non-persistent groups
        foreach ($this->nonPersistentGroups as $nonPersistentGroup) {
        	if (str_contains($group, $nonPersistentGroup)) {
        		return true;
        	}
        }

        return false;
    }

    public function get($key, $group = 'default', $force = false, &$found = null)
    {
        // FastCache's own settings must NEVER be fetched from any cache layer
        if ($group === 'options' && $key === 'fastcache_settings') {
            $found = false;
            return false;
        }

        // In-memory cache works for ALL requests (even excluded ones, even without adapter)
        if (!$force && isset($this->memoryCache[$group][$key])) {
            $found = true;
            $this->stats['hits']++;
            $this->log('HIT_MEMORY', $key, $group);
            return is_object($this->memoryCache[$group][$key]) ? clone $this->memoryCache[$group][$key] : $this->memoryCache[$group][$key];
        }

        // No adapter (excluded request) or excluded group → don't read from persistent backend
        if ($this->adapter === null || $this->shouldSkipPersistence($group)) {
            $found = false;
            $this->log('MISS_EXCLUDED', $key, $group);
            return false;
        }

        $cache_key = $this->buildKey($key, $group);
        $value = $this->adapter->get($cache_key, $found);
        if ($found) {
            $this->stats['hits']++;

            // Strip fastcache_settings from alloptions before caching in memory
            // WordPress caches ALL autoload options under the 'alloptions' key,
            // so fastcache_settings can leak into persistent cache indirectly
            if ($group === 'options' && $key === 'alloptions' && is_array($value)) {
                unset($value['fastcache_settings']);
            }

            $this->memoryCache[$group][$key] = $value;
            $this->log('HIT_PERSISTENT', $key, $group);
            return is_object($value) ? clone $value : $value;
        }

        $this->stats['misses']++;
        $this->log('MISS', $key, $group);
        return false;
    }

    public function set($key, $data, $group = 'default', $expire = 0)
    {
        if (is_object($data)) {
            $data = clone $data;
        }

        // FastCache's own settings must NEVER be persisted to cache (nor stored in memory)
        if ($group === 'options' && $key === 'fastcache_settings') {
            return true;
        }

        // Strip fastcache_settings from alloptions before storing anywhere
        if ($group === 'options' && $key === 'alloptions' && is_array($data)) {
            unset($data['fastcache_settings']);
        }

        // Always store in memory (within the same request) - works even without adapter
        $this->memoryCache[$group][$key] = $data;
        $this->log('SET_MEMORY', $key, $group);

        // No adapter (excluded request) or excluded group → don't write to persistent backend
        if ($this->adapter === null || $this->shouldSkipPersistence($group)) {
            $this->log('SET_EXCLUDED', $key, $group);
            return true;
        }

        if ($expire === 0) {
            $expire = $this->defaultTTL;
        }

        $cache_key = $this->buildKey($key, $group);
        $result = $this->adapter->set($cache_key, $data, $expire);
        if ($result) {
            $this->stats['writes']++;
            $this->log('SET_PERSISTENT', $key, $group, "TTL: {$expire}s");
        } else {
            $this->log('SET_FAILED', $key, $group);
        }
        return $result;
    }

    public function delete($key, $group = 'default')
    {
        if (!is_string($key) && !is_int($key)) {
            return false;
        }

        if (!is_string($group) && !is_int($group)) {
            return false;
        }

        if (isset($this->memoryCache[$group][$key])) {
            unset($this->memoryCache[$group][$key]);
        }

        // Skip persistent delete for non-persistent GROUPS only
        foreach ($this->nonPersistentGroups as $nonPersistentGroup) {
        	if (str_contains($group, $nonPersistentGroup)) {
        		return true;
        	}
        }

        // CRITICAL: delete() must ALWAYS propagate to the persistent backend,
        // even on excluded requests. A delete is an INVALIDATION operation.
        // If we block deletes on excluded requests (e.g., admin editing settings),
        // stale data remains in the backend and will be served to non-excluded
        // requests (e.g., frontend visitors) causing configuration to "stick".
        //
        // Use ensureAdapter() to lazy-create the adapter if needed (on excluded
        // requests the adapter is not created during init to prevent filesystem activity).
        if (!$this->ensureAdapter()) {
            return false;
        }

        $cache_key = $this->buildKey($key, $group);
        $result = $this->adapter->delete($cache_key);
        if ($result) {
            $this->stats['deletes']++;
            $this->log('DELETE_PERSISTENT', $key, $group);
        } else {
            $this->log('DELETE_FAILED', $key, $group);
        }
        return $result;
    }

    public function garbageCollection()
    {
        if (!$this->ensureAdapter()) {
            return 0;
        }
        if (method_exists($this->adapter, 'garbageCollection')) {
            $deletedCount = $this->adapter->garbageCollection();
            $this->log('GARBAGE_COLLECTION', 'all', 'all', "Deleted {$deletedCount} expired files");
            return $deletedCount;
        }
        return 0;
    }

    public function flush()
    {
        $this->memoryCache = [];
        $this->log('FLUSH_MEMORY', 'all', 'all');
        // Flush must always reach the backend (like delete, it's an invalidation)
        if (!$this->ensureAdapter()) {
            return false;
        }
        $result = $this->adapter->flush();
        if ($result) {
            $this->log('FLUSH_PERSISTENT', 'all', 'all');
        } else {
            $this->log('FLUSH_FAILED', 'all', 'all');
        }

        // Always delete the log file on flush, regardless of backend result.
        // The user explicitly requested a flush — the log must be cleared.
        if (defined('WP_CONTENT_DIR')) {
            $logFile = WP_CONTENT_DIR . '/cache/fastcache/object-cache.log';
            if (file_exists($logFile)) {
                @unlink($logFile);
            }
        }

        return $result;
    }

    public function getStats()
    {
        return $this->stats + ['connections' => 1];
    }

    /**
     * Get global statistics about the cache backend (files on disk, or server stats).
     */
    public function getGlobalStats()
    {
        $settings = get_option('fastcache_settings', []);
        $backend = BackendDetector::getDetectedBackendName($settings);

        if ($backend === 'file') {
            $cacheDir = WP_CONTENT_DIR . '/cache/fastcache/object/';
            if (!is_dir($cacheDir)) {
                return ['type' => 'file', 'count' => 0, 'size' => 0];
            }

            $count = 0;
            $size = 0;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($cacheDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $count++;
                    $size += $item->getSize();
                }
            }

            return [
                'type' => 'file',
                'count' => $count,
                'size' => $this->formatBytes($size)
            ];
        }

        // For Redis/Memcached, we could return server-level info here if needed.
        return ['type' => $backend, 'count' => 'N/A', 'size' => 'N/A'];
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function isExcludedRequest()
    {
        return $this->requestExcluded;
    }

    public function isInitialized()
    {
        return !$this->isInitializing && $this->adapter !== null;
    }

    private function buildKey($key, $group)
    {
        return $group . ':' . $key;
    }

    private function log($operation, $id, $group, $details = '')
    {
        if (!$this->loggingEnabled || !defined('WP_CONTENT_DIR')) {
            return;
        }

        $logDir = WP_CONTENT_DIR . '/cache/fastcache';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/object-cache.log';
        $timestamp = date('Y-m-d H:i:s');
        $message = "[{$timestamp}] {$operation} | Group: {$group} | ID: {$id}";
        if (!empty($details)) {
            $message .= " | {$details}";
        }
        $message .= "\n";

        @file_put_contents($logFile, $message, FILE_APPEND);
    }
}
