<?php

namespace FastCache\ObjectCache;

class BackendDetector
{
	public static function getAvailable()
	{
		return [
			'redis' => [
				'name' => 'Redis',
				'available' => class_exists('Redis'),
				'description' => function_exists('\__') ? \__('In-memory data structure store, used as a database, cache, and message broker.', 'fastcache') : 'In-memory data structure store, used as a database, cache, and message broker.'
			],
			'memcached' => [
				'name' => 'Memcached',
				'available' => class_exists('Memcached') || class_exists('Memcache'),
				'description' => function_exists('\__') ? \__('Free & open source, high-performance, distributed memory object caching system.', 'fastcache') : 'Free & open source, high-performance, distributed memory object caching system.'
			],
			'file' => [
				'name' => 'File System',
				'available' => true,
				'description' => function_exists('\__') ? \__('Caches objects to the local file system. Does not require additional server software.', 'fastcache') : 'Caches objects to the local file system. Does not require additional server software.'
			]
		];
	}

	public static function getDetectedBackendName($settings)
	{
		$selected = isset($settings['object_cache_backend']) ? $settings['object_cache_backend'] : 'auto';
		$available = self::getAvailable();

		if ($selected !== 'auto' && isset($available[$selected]) && $available[$selected]['available']) {
			return $selected;
		}

		if ($available['redis']['available'])
			return 'redis';
		if ($available['memcached']['available'])
			return 'memcached';
		return 'file';
	}
}
