<?php

namespace FastCache\Host;

class HAdminbar {
	private $plugin_name;
	private $version;
	private $getParam = 'purge-varnish-cache';
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	public function purge_cache_publicbar($admin_bar) {
		$params = \FastCache\Platform\Plugin::getPluginParams();
		if ($params->get('fastcache-enable', 0) == 0) {
			return;
		}

		$admin_bar->add_menu ( array (
				'parent' => 'fastcache-main',
				'title' => 'CDN Purge All',
				'id' => 'purge-all',
				'href' => '#',
				'meta' => array (
						'class' => "all",
						'data-scope' => 'all'
				)
		) );
		$admin_bar->add_menu ( array (
				'parent' => 'fastcache-main',
				'title' => 'CDN Purge this page',
				'id' => 'purge-this',
				'href' => '#',
				'meta' => array (
						'class' => "this",
						'data-scope' => 'this'
				)
		) );
		$admin_bar->add_menu ( array (
				'parent' => 'fastcache-main',
				'title' => 'CDN Purge Homepage',
				'id' => 'purge-homepage',
				'href' => '#',
				'meta' => array (
						'class' => "homepage",
						'data-scope' => 'homepage'
				)
		) );
	}
	public function purge_cache_adminbar($admin_bar) {
		$params = \FastCache\Platform\Plugin::getPluginParams();
		if ($params->get('fastcache-enable', 0) == 0) {
			return;
		}

		$admin_bar->add_menu ( array (
				'parent' => 'fastcache-main',
				'title' => 'CDN Purge All',
				'id' => 'purge-all',
				'href' => '#',
				'meta' => array (
						'class' => "all fastcache-confirm-purge",
						'data-scope' => 'all',
						'data-confirm-message' => __('Are you sure you want to purge all cache?', 'fastcache')
				)
		) );
		
		$admin_bar->add_menu ( array (
				'parent' => 'fastcache-main',
				'title' => 'CDN Purge Homepage',
				'id' => 'purge-homepage',
				'href' => '#',
				'meta' => array (
						'class' => "homepage fastcache-confirm-purge",
						'data-scope' => 'homepage',
						'data-confirm-message' => __('Are you sure you want to purge homepage cache?', 'fastcache')
				)
		) );
	}
}