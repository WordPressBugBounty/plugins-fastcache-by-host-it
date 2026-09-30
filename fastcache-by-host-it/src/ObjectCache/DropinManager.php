<?php

namespace FastCache\ObjectCache;

class DropinManager
{

    public static function getDropinPath()
    {
        return WP_CONTENT_DIR . '/object-cache.php';
    }

    public static function getSourceDropinPath()
    {
        return FASTCACHE_DIR . '/src/ObjectCache/object-cache-dropin.php';
    }

    /**
     * Parse Version: header from drop-in file contents (first 1KB is enough).
     */
    private static function parseVersion($contents)
    {
        if (preg_match('/Version:\s*([0-9.]+)/', (string) $contents, $matches)) {
            return $matches[1];
        }
        return '0';
    }

    public static function getSourceVersion()
    {
        $source = self::getSourceDropinPath();
        if (!file_exists($source)) {
            return '0';
        }
        $contents = @file_get_contents($source, false, null, 0, 1024);
        return self::parseVersion($contents);
    }

    public static function getStatus()
    {
        $target = self::getDropinPath();
        $exists = file_exists($target);

        $isOurs = false;
        $isForeign = false;
        $foreignInfo = [];
        $installedVersion = '0';

        if ($exists) {
            $contents = file_get_contents($target, false, null, 0, 1024); // Read first 1KB
            if (strpos($contents, 'FastCache Object Cache Drop-In') !== false) {
                $isOurs = true;
                $installedVersion = self::parseVersion($contents);
            } else {
                $isForeign = true;
                if (preg_match('/Plugin Name:\s*([^\r\n]+)/', $contents, $matches)) {
                    $foreignInfo['name'] = trim($matches[1]);
                } else {
                    $foreignInfo['name'] = 'Unknown Object Cache Plugin';
                }
            }
        }

        $sourceVersion = self::getSourceVersion();

        return [
            'dropin_exists' => $exists,
            'is_ours' => $isOurs,
            'is_foreign' => $isForeign,
            'foreign_info' => $foreignInfo,
            'installed_version' => $installedVersion,
            'source_version' => $sourceVersion,
            'needs_update' => $isOurs && version_compare($installedVersion, $sourceVersion, '<'),
            'wp_content_writable' => is_writable(WP_CONTENT_DIR)
        ];
    }

    /**
     * Refresh installed drop-in when our source is newer (e.g. after plugin update).
     */
    public static function ensureCurrent()
    {
        $status = self::getStatus();
        if (!empty($status['needs_update'])) {
            return self::update();
        }
        return true;
    }

    public static function install()
    {
        $status = self::getStatus();

        if ($status['is_ours']) {
            if (!empty($status['needs_update'])) {
                return self::update();
            }
            return true;
        }

        if ($status['is_foreign']) {
            return __('A foreign object-cache.php drop-in is active. Please deactivate other object caching plugins first.', 'fastcache');
        }

        if (!$status['wp_content_writable']) {
            return __('The wp-content directory is not writable. Automatic drop-in installation failed.', 'fastcache');
        }

        $source = self::getSourceDropinPath();

        // Validate source exists and is not empty
        if (!file_exists($source) || filesize($source) === 0) {
            return __('The source drop-in file is missing or empty.', 'fastcache');
        }

        // Read source content
        $sourceContent = @file_get_contents($source);
        if ($sourceContent === false || strlen($sourceContent) === 0) {
            return __('Failed to read source drop-in file.', 'fastcache');
        }

        // Check available disk space (need at least 50KB free)
        $availableSpace = @disk_free_space(WP_CONTENT_DIR);
        if ($availableSpace !== false && $availableSpace < 51200) {
            return __('Not enough disk space to install drop-in.', 'fastcache');
        }

        $target = self::getDropinPath();
        $tempTarget = $target . '.tmp';

        // Atomic write: write to temp file first, then rename
        if (@file_put_contents($tempTarget, $sourceContent, LOCK_EX) === false) {
            return __('Failed to write drop-in file to temporary location.', 'fastcache');
        }

        // Verify temp file was written completely
        if (filesize($tempTarget) !== strlen($sourceContent)) {
            @unlink($tempTarget);
            return __('Drop-in file write verification failed. Disk space or permissions issue.', 'fastcache');
        }

        // Atomic rename (atomic on most filesystems)
        if (!@rename($tempTarget, $target)) {
            @unlink($tempTarget);
            return __('Failed to finalize drop-in file.', 'fastcache');
        }

        // Final verification
        if (filesize($target) === 0) {
            @unlink($target);
            return __('Drop-in file created but is empty. Possible disk space or corruption issue.', 'fastcache');
        }

        return true;
    }

    public static function uninstall()
    {
        $status = self::getStatus();

        if (!$status['dropin_exists']) {
            return true;
        }

        if ($status['is_foreign']) {
            return __('The active object-cache.php drop-in belongs to another plugin. FastCache will not remove it.', 'fastcache');
        }

        if (unlink(self::getDropinPath())) {
            return true;
        }

        return __('Failed to delete object-cache.php drop-in. Please remove it manually.', 'fastcache');
    }

    public static function update()
    {
        $status = self::getStatus();
        if ($status['is_ours']) {
            self::uninstall();
            return self::install();
        }
        return true;
    }
}
