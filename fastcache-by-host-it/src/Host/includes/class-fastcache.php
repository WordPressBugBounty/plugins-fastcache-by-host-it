<?php

namespace FastCache\Host;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link https://fastcache.host.it/
 * @since 1.0.0
 *       
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since 1.0.0
 * @author host.it <info@host.it>
 */
class HFastCache {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var HLoader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if (defined ( 'FASTCACHE_VERSION' )) {
			$this->version = FASTCACHE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = FASTCACHEHOST_HOST_PLUGINNAME;

		$this->load_dependencies ();
		$this->define_admin_hooks ();
		$this->define_public_hooks ();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - HLoader. Orchestrates the hooks of the plugin.
	 * - Hi18n. Defines internationalization functionality.
	 * - HAdmin. Defines all hooks for the admin area.
	 * - HPublic. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'includes/class-' . FASTCACHEHOST_HOST_PLUGINNAME . '-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'admin/class-' . FASTCACHEHOST_HOST_PLUGINNAME . '-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'public/class-' . FASTCACHEHOST_HOST_PLUGINNAME . '-public.php';
		require_once plugin_dir_path ( dirname ( __FILE__ ) ) . 'public/workarounds.class.php';

		$this->loader = new HLoader ();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new HAdmin ( $this->get_plugin_name (), $this->get_version () );

		require_once plugin_dir_path ( __FILE__ ) . 'class-' . FASTCACHEHOST_HOST_PLUGINNAME . '-adminbar.php';
		$adminbar_admin = new HAdminbar ( $this->get_plugin_name (), $this->get_version () );
		if (! is_admin ()) {
			$this->loader->add_action ( 'admin_bar_menu', $adminbar_admin, 'purge_cache_publicbar', 500 );
		} else {
			$this->loader->add_action ( 'admin_bar_menu', $adminbar_admin, 'purge_cache_adminbar', 500 );
		}

		if (isset ( $_SERVER ['REQUEST_URI'] ) && str_contains ( $_SERVER ['REQUEST_URI'], "page=" . FASTCACHEHOST_HOST_PLUGINNAME )) {
			$this->loader->add_action ( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 499 );
		}
		$this->loader->add_action ( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts_public', 500 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_public_hooks() {
		$plugin_public = new HPublic ( $this->get_plugin_name (), $this->get_version () );
		$workarounds = new HWorkarounds ();

		// $this->loader->add_action ( 'the_content', $plugin_public, 'append_the_button', 45 );
		$this->loader->add_action ( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action ( 'send_headers', $plugin_public, 'send_headers' );
		$this->loader->add_action ( 'template_redirect', $workarounds, 'identify' );
		// $this->loader->add_action('get_header', $workarounds, 'identify_after_get_header');
		$this->loader->add_action ( 'init', $workarounds, 'register_esi_endpoint' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run ();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 1.0.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 1.0.0
	 * @return HLoader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 1.0.0
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
