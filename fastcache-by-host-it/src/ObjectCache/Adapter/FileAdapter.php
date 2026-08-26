<?php

namespace FastCache\ObjectCache\Adapter;

class FileAdapter implements AdapterInterface
{
    private $cacheDir = '';
    private $prefix = '';

    public function connect($settings)
    {
        $this->prefix = isset($settings['object_cache_key_prefix']) ? $settings['object_cache_key_prefix'] : 'fc_';
        $this->cacheDir = WP_CONTENT_DIR . '/cache/fastcache/object/';
        // Directory creation is deferred to getFilePathForWrite() (called only by set())
        // This ensures zero filesystem activity for excluded requests or read-only operations.
    }

    /**
     * Build the file path for a cache key WITHOUT creating the directory.
     * Used by get() and delete() which should never create directories.
     */
    private function getFilePath($key)
    {
    	// if is present ":" inside the $key, estract the part before ":" and use it as group name and the other side as $key
    	if (strpos($key, ':') !== false) {
    		$vars = explode(":", $key);
    		$group = $vars[0];
    		// pop the first elelment from $vars
    		array_shift($vars);
    		$key = implode(":", $vars);
    	}

    	// Use readable key names sanitized for filesystem safety
    	$filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $this->prefix . $key) . '.php';
    	$dir = $this->cacheDir . $group . DIRECTORY_SEPARATOR;
    	return $dir . $filename;
    }

    /**
     * Build the file path for a cache key AND ensure all directories exist.
     * Used only by set() which needs to write the file.
     * The recursive flag in mkdir() creates both the base dir and subdirectory.
     */
    private function getFilePathForWrite($key)
    {
    	// if is present ":" inside the $key, estract the part before ":" and use it as group name and the other side as $key
    	if (strpos($key, ':') !== false) {
    		$vars = explode(":", $key);
    		$group = $vars[0];
    		// pop the first elelment from $vars
    		array_shift($vars);
    		$key = implode(":", $vars);
    	}

    	// Use readable key names sanitized for filesystem safety
    	$filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $this->prefix . $key) . '.php';
    	$dir = $this->cacheDir . $group . DIRECTORY_SEPARATOR;
    	if (!is_dir($dir)) {
    		@mkdir($dir, 0755, true); // recursive: creates object/ and object/xx/ in one call
    	}
    	return $dir . $filename;
    }

    public function get($key, &$found)
    {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            $found = false;
            return null;
        }

        $data = @include $file;
        if ($data === false || !is_array($data) || !isset($data['expiry']) || !isset($data['data'])) {
            $found = false;
            return null;
        }

        if ($data['expiry'] !== 0 && $data['expiry'] < time()) {
            @unlink($file);
            $found = false;
            return null;
        }

        $found = true;
        return unserialize($data['data']);
    }

    public function set($key, $data, $expire)
    {
        $file = $this->getFilePathForWrite($key);

        if ($expire > 0) {
            // WordPress logic: if expire is > 30 days, it's a timestamp.
            if ($expire > 2592000) {
                $expiry = $expire;
            } else {
                $expiry = time() + $expire;
            }
        } else {
            $expiry = 0;
        }

        $cacheData = [
            'expiry' => $expiry,
            'data' => serialize($data)
        ];

        $content = "<?php\nreturn " . var_export($cacheData, true) . ";\n";

        $tmpFile = $file . '.tmp.' . uniqid();
        if (@file_put_contents($tmpFile, $content) !== false) {
            if (@rename($tmpFile, $file)) {
                return true;
            }
            @unlink($tmpFile);
        }
        return false;
    }

    public function delete($key)
    {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            return @unlink($file);
        }
        return true;
    }

    public function flush()
    {
        return $this->deleteDirectory($this->cacheDir);
    }

    public function getGlobalStats()
    {
        $count = 0;
        $size = 0;
        if (!is_dir($this->cacheDir)) {
            return ['count' => $count, 'size' => '0 B'];
        }
        $iterator = new \RecursiveDirectoryIterator($this->cacheDir, \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator);
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $count++;
                $size += $file->getSize();
            }
        }
        return ['count' => $count, 'size' => $this->formatBytes($size)];
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function garbageCollection($timeMarginSeconds = 5)
    {
        $now = time();
        $deletedCount = 0;

        if (!is_dir($this->cacheDir)) {
            return $deletedCount;
        }

        // --- Time-budget setup ---
        // max_execution_time = 0 means no limit (e.g. WP-CLI) → skip time check.
        $maxExecTime = (int) ini_get('max_execution_time');
        $hasTimeLimit = $maxExecTime > 0;

        // PHP sets REQUEST_TIME_FLOAT at the very start of the request (most accurate).
        // Fall back to microtime() if not available (e.g. CLI with no request context).
        $requestStart = isset($_SERVER['REQUEST_TIME_FLOAT'])
            ? (float) $_SERVER['REQUEST_TIME_FLOAT']
            : microtime(true);

        $iterator = new \RecursiveDirectoryIterator($this->cacheDir, \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            // Time-budget check: bail out if we are too close to the PHP time limit.
            if ($hasTimeLimit) {
                $elapsed   = microtime(true) - $requestStart;
                $remaining = $maxExecTime - $elapsed;
                if ($remaining < $timeMarginSeconds) {
                    break;
                }
            }

            if ($file->isFile() && $file->getExtension() === 'php') {
                $data = @include $file->getRealPath();
                if (is_array($data) && isset($data['expiry'])) {
                    if ($data['expiry'] !== 0 && $data['expiry'] < $now) {
                        @unlink($file->getRealPath());
                        $deletedCount++;
                    }
                } else {
                    @unlink($file->getRealPath());
                    $deletedCount++;
                }
            }
        }

        $this->cleanupEmptyDirs($this->cacheDir);
        return $deletedCount;
    }

    private function cleanupEmptyDirs($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        try {
            $iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator(
                $iterator,
                \RecursiveIteratorIterator::CHILD_FIRST,
                \RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    $realPath = $file->getRealPath();
                    if ($realPath && @is_dir($realPath) && @count(@scandir($realPath)) === 2) {
                        @rmdir($realPath);
                    }
                }
            }
        } catch (\UnexpectedValueException $e) {
            // Directory disappeared while iterating (concurrent cleanup): safe to ignore.
        }
    }

    private function deleteDirectory($dir)
    {
        // Nothing to delete = success (consistent with delete() on a missing file)
        if (!is_dir($dir))
            return true;

        try {
            $iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator(
                $iterator,
                \RecursiveIteratorIterator::CHILD_FIRST,
                \RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            foreach ($files as $file) {
                $realPath = $file->getRealPath();
                if (!$realPath) {
                    continue;
                }
                if ($file->isDir()) {
                    @rmdir($realPath);
                } else {
                    @unlink($realPath);
                }
            }
        } catch (\UnexpectedValueException $e) {
            // Directory/file disappeared while iterating: treat as already removed.
        }
        return true;
    }
}
