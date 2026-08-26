<?php

namespace FastCache\Host;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link https://fastcache.host.it/
 * @since 1.0.0
 *       
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author host.it <info@host.it>
 */
class HAdmin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;
	private $purgeUrls = [];
	private $errorMessages = "";
	private $purgeable = true;
	private $prefix = "hostcache_";
	private $customFields = [];
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name
	 *        	The name of this plugin.
	 * @param string $version
	 *        	The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('init', array(
		&
			$this,
			'init'
		), 11);
		$this->activateAction();
	}
	public function init()
	{
		add_action('admin_menu', array(
			$this,
			'create_custom_fields'
		));
		add_action('save_post', array(
			$this,
			'save_custom_fields'
		), 1, 2);
		$this->customFields = array(
			array(
				'name' => 'inactive',
				'title' => 'No Cache',
				'description' => __('Disable the cache for this "post"'),
				'type' => 'checkbox',
				'scope' => array(
					'post',
					'page'
				),
				'capability' => 'manage_options'
			),
			array(
				'name' => 'ttl',
				'title' => 'TTL',
				'description' => __('Cache refresh time in seconds for this post. Entering "0" disables the cache. Default: ' . FASTCACHE_DEFAULTTTL),
				'type' => 'text',
				'scope' => array(
					'post',
					'page'
				),
				'capability' => 'manage_options'
			)
		);
	}
	public function save_custom_fields($post_id, $post)
	{
		if (!isset($_POST['hostcache-fields_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hostcache-fields_wpnonce'])), 'hostcache-fields')) {
			return;
		}
		if (!current_user_can('edit_post', $post_id))
			return;
		$pt = array(
			'page',
			'post'
		);
		if (!in_array(get_post_type($post_id), $pt)) {
			return;
		}
		foreach ($this->customFields as $customField) {
			if (current_user_can($customField['capability'], $post_id)) {
				if (isset($_POST[$this->prefix . $customField['name']]) && trim($_POST[$this->prefix . $customField['name']]) != '') {
					$field = $customField['name'];
					$sanitized_value = sanitize_text_field($_POST[$this->prefix . $field]);
					update_post_meta($post_id, $this->prefix . $field, $sanitized_value);
				} elseif(isset($field)) {
					delete_post_meta($post_id, $this->prefix . $field);
				}
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in HLoader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The HLoader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$pUrl = plugin_dir_url(__FILE__) . 'js/' . FASTCACHEHOST_HOST_PLUGINNAME . '-admin.js';
		$pPath = plugin_dir_path(__FILE__) . 'js/' . FASTCACHEHOST_HOST_PLUGINNAME . '-admin.js';
		wp_enqueue_script(FASTCACHEHOST_HOST_PLUGINNAME, $pUrl, array(
			'jquery'
		), filemtime($pPath), false);
		wp_localize_script(FASTCACHEHOST_HOST_PLUGINNAME, 'ajax_var', array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax-nonce')
		));
	}
	public function enqueue_scripts_public()
	{
		$pUrl = plugin_dir_url(__FILE__) . 'js/' . FASTCACHEHOST_HOST_PLUGINNAME . '-generic-admin.js';
		$pPath = plugin_dir_path(__FILE__) . 'js/' . FASTCACHEHOST_HOST_PLUGINNAME . '-generic-admin.js';
		wp_enqueue_script(FASTCACHEHOST_HOST_PLUGINNAME . "-generic", $pUrl, array(
			'jquery'
		), filemtime($pPath), false);
		wp_localize_script('fastcache-generic', 'ajax_var', array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax-nonce')
		));
		wp_localize_script('fastcache-generic', 'fastcache_confirm', array(
			'message_base' => __('Caches are automatically regenerated when the default TTL expires or at the TTL value set in the appropriate fields. For this reason, we suggest not performing the purge but waiting for the time indicated in TTL.', 'fastcache'),
			'message_question' => __('Do you want to proceed anyway with the deletion', 'fastcache'),
			'message_this' => __('of this page cache?', 'fastcache'),
			'message_all' => __('of all cache?', 'fastcache'),
			'message_homepage' => __('of the homepage cache?', 'fastcache'),
			'diagnostic_success' => __('No conflicts detected', 'fastcache'),
			'diagnostic_warning' => __('Cache conflict detected', 'fastcache')
		));
	}

	/**
	 * Sandbox our settings.
	 *
	 * @since 1.0.0
	 */
	public function record_setting($input)
	{
		$new_input = array();

		if (isset($input)) {
			// Loop trough each input and sanitize the value if the input id isn't post-types
			foreach ($input as $key => $value) {
				if ($key == 'ttl-post-types') {
					$new_input[$key] = $value;
				} else {
					$new_input[$key] = sanitize_text_field($value);
				}
			}
		}

		return $new_input;
	}
	/**
	 * Sandbox our settings.
	 *
	 * @since 1.0.0
	 */
	public function record_ttl($input)
	{
		$new_input = array();

		if (isset($input)) {
			// Loop trough each input and sanitize the value if the input id isn't post-types
			foreach ($input as $key => $value) {
				if ($key == 'post-types') {
					$new_input[$key] = $value;
				} else {
					$new_input[$key] = sanitize_text_field($value);
				}
			}
		}
		return $new_input;
	}

	/**
	 * Sandbox our section for the settings.
	 *
	 * @since 1.0.0
	 */
	public function sandbox_add_settings_section()
	{
		return;
	}
	/**
	 * Sandbox our section for the debug.
	 *
	 * @since 1.0.0
	 */
	public function sandbox_add_debug_section()
	{
		return;
	}
	/**
	 * Sandbox our section for the ttl.
	 *
	 * @since 1.0.0
	 */
	public function sandbox_add_ttl_section()
	{
		return;
	}

	/*
	 * azioni su cui fare il purge della cache
	 */
	private function get_register_events()
	{
		$actions = array(
			'publish_future_post',
			'save_post',
			'deleted_post',
			'trashed_post',
			'edit_post',
			'delete_attachment',
			'switch_theme',
			'publish_post',
			'transition_post_status'
		);
		return apply_filters('fastcache_host_UpdateCachingEvents', $actions);
	}

	/**
	 * interfaccia per gestire la pulizia della cache verso le azioni
	 *
	 * @return void
	 */
	private function activateAction()
	{
		// per tutti gli eventi identificati per essere "cambianti" dello stato di un post/elemento
		// collego la funzione di pulizia della cache
		foreach ($this->get_register_events() as $event) {
			add_action($event, array(
				$this,
				'purge_post'
			), 10, 2);
		}
		$this->canPurge();
	}
	public function msgErroreManageOption()
	{
		global $current_user;
		$html = "<div id=\"message\" class=\"fastcache-notice notice notice-error is-dismissible\">";
		$html .= "<p>";
		/* translators: %s: user display name */
		$html .= sprintf(
			__('WARNING! HOST Caching requires the user to have "manage_options" rights. Currently, the user: <b>%s</b> does not have the necessary rights. If content is updated, the cache will not be automatically updated.', 'fastcache'),
			$current_user->display_name
		);
		$html .= "</p>";
		$html .= "</div>";
		return $html;
	}
	public function msgErrorePermalink()
	{
		$html = "<div id=\"message\" class=\"fastcache-notice notice notice-error is-dismissible\">";
		$html .= "<p>";
		$html .= __('WARNING! HOST Caching requires the user to have "manage_options" rights.', 'fastcache');
		$html .= "</p>";
		$html .= "</div>";
		return $html;
	}
	public function displayError()
	{
		echo $this->errorMessages;
	}
	/**
	 *
	 * Entro in purge_post() quando un EVENTO lo richiede, ad esempio edit_post o new_post
	 *
	 * @param
	 *        	$postId
	 * @param
	 *        	$post
	 * @param
	 *        	$allow_background - Set to true for background processes (cron, etc)
	 * @param
	 *        	$is_scheduled_publish - Set to true when publishing scheduled posts (future→publish)
	 *
	 * @return void
	 */
	public function purge_post($postId, $post = null, $allow_background = false, $is_scheduled_publish = false)
	{

		// prima di tutto non devo intervenire sugli eventi relativi a post di tipo "nav_menu_item"
		// eliminata && $this->purgeOnMenuSave == false
		if (get_post_type($post) == 'nav_menu_item' ) {
			return;
		}
		if ($this->doOrNotToDo($postId) === false) {
			return;
		}

		// Check permissions - allow background processes to bypass user checks
		$this->canPurge($allow_background);

		$this->addCategoriesToPurge($postId);
		$this->addTagsToPurge($postId);
		$this->addArchiveAuthorsToPurge($postId);
		$this->addThisPostToPurge($postId);
		$this->addRssToPurge($postId);
		$this->addHomeRootToPurge();
		$this->addAmpToPurge($postId);
		$this->addSitemapToPurge();
		$this->purgeUrls = apply_filters('fastcache_host_v_purge_urls', $this->purgeUrls, $postId);
		if ($this->purgeable === true) {
			// Purge filesystem (htaccess) cache for affected URLs
			// For scheduled posts, purge ALL cache to ensure homepage/archives are updated
			$this->purgeFilesystemCache($is_scheduled_publish);
			// Purge CDN cache only if CDN is enabled
			$this->purgeCdnCache();

			// Flush Object Cache if enabled to avoid stale data issues
			if (function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}
		}
	}
	private function user_can_access_plugin(): bool {
		$caps = array(
				'manage_options',
				'edit_posts',
				'delete_posts',
				'publish_posts',
				'edit_others_posts',
				'delete_others_posts',
				'delete_published_posts',
		);
		
		foreach ( $caps as $cap ) {
			if ( current_user_can( $cap ) ) {
				return true;
			}
		}
		
		return false;
	}
	private function canPurge($allow_background = false)
	{
		// For background processes (cron), allow purge without permission checks
		if ($allow_background === true) {
			$this->purgeable = true;
			return;
		}

		// CDN purge incompatible with Multisite (single token configured per-site)
		if ( is_multisite() ) {
			$this->purgeable = false;
			return;
		}

		// Se WordPress non ha completato l'inizializzazione non possiamo verificare
		// i permessi utente in modo sicuro (AUTH_COOKIE potrebbe non essere ancora definita,
		// ad esempio durante il bootstrap di Multisite o in contesti REST/cron precoci)
		if ( ! did_action( 'init' ) || ! function_exists( 'wp_get_current_user' ) ) {
			$this->purgeable = false;
			return;
		}

		// Check user permissions only for non-background requests
		if (!$this->user_can_access_plugin()) {
			$this->errorMessages .= $this->msgErroreManageOption();
		}

		// To Test - $this->errorMessages .= $this->msgErroreManageOption();
		// if (get_option('permalink_structure') == '') {
		// $this->errorMessages.=$this->msgErrorePermalink();
		// }

		if ($this->errorMessages != "") {
			add_action('admin_notices', array(
				$this,
				'displayError'
			));
			$this->purgeable = false;
		} else {
			$this->purgeable = true;
		}
	}
	private function doOrNotToDo($postId)
	{
		/*
		 * eseguo la purge solo per gli stati publish e trash
		 * in caso di revisione non faccio nulla
		 */
		$validPostStatus = array(
			'publish',
			'trash'
		);
		$thisPostStatus = get_post_status($postId);
		$isNotAValidPostStatus = !in_array($thisPostStatus, $validPostStatus);
		$isRevision = get_permalink($postId);
		if ($isRevision === false) {
			return false;
		}
		if ($isNotAValidPostStatus === true) {
			return false;
		}
		return true;
	}
	private function addCategoriesToPurge($postId)
	{
		// in purgeUrls inserisco
		// tutte le categorie a cui appartiene il post
		$categories = get_the_category($postId);
		if ($categories) {
			foreach ($categories as $cat) {
				array_push($this->purgeUrls, get_category_link($cat->term_id));
			}
		}
	}
	private function addTagsToPurge($postId)
	{
		// in purgeUrls inserisco
		// tutti i tags assegnati il post
		$tags = get_the_tags($postId);
		if ($tags) {
			foreach ($tags as $tag) {
				array_push($this->purgeUrls, get_tag_link($tag->term_id));
			}
		}
	}
	private function addThisPostToPurge($postId)
	{
		// e anche il link stesso del post
		array_push($this->purgeUrls, get_permalink($postId));
	}
	private function addArchiveAuthorsToPurge($postId)
	{
		// inserisco anche la pagina dell'autore in quanto questa avrà nuovi/modificati url
		array_push($this->purgeUrls, get_author_posts_url(get_post_field('post_author', $postId)), get_author_feed_link(get_post_field('post_author', $postId)));

		// inserisco anche i link di "archivio"
		if (get_post_type_archive_link(get_post_type($postId)) == true) {
			array_push($this->purgeUrls, get_post_type_archive_link(get_post_type($postId)), get_post_type_archive_feed_link(get_post_type($postId)));
		}
	}
	private function addRssToPurge($postId)
	{
		// ci metto dentro anche tutti gli url degli rss
		array_push($this->purgeUrls, get_bloginfo_rss('rdf_url'), get_bloginfo_rss('rss_url'), get_bloginfo_rss('rss2_url'), get_bloginfo_rss('atom_url'), get_bloginfo_rss('comments_rss2_url'), get_post_comments_feed_link($postId));
	}
	private function addHomeRootToPurge()
	{
		// la home page sia come / che come url di homepage
		array_push($this->purgeUrls, home_url('/'));
		if (get_option('show_on_front') == 'page') {
			array_push($this->purgeUrls, get_permalink(get_option('page_for_posts')));
		}
	}
	private function addAmpToPurge($postId)
	{
		// anche tutti i link relativi alle pagine AMP
		if (function_exists('amp_get_permalink')) {
			array_push($this->purgeUrls, amp_get_permalink($postId));
		}
	}
	private function addSitemapToPurge()
	{
		// anche tutti i link relativi alle SITEMAP
		array_push($this->purgeUrls, get_home_url() . "/*map*xml*");
	}

	/**
	 * Purge filesystem (htaccess) cache for affected URLs.
	 * Deletes corresponding .html files from the page cache directory.
	 *
	 * @param bool $purge_all - If true, deletes ALL page cache (for scheduled posts)
	 * @return void
	 */
	private function purgeFilesystemCache($purge_all = false)
	{
		$logger = new \FastCache\Host\HCommon();

		// For scheduled posts, purge ALL page cache to ensure homepage/archives are regenerated
		if ($purge_all === true) {
			\FastCache\Platform\Cache::deleteCache('page');
			$logger->fr("FS-CACHE PURGE: Deleted ALL page cache (scheduled post published)");
			return;
		}

		// Otherwise, delete only specific URLs
		$purgeUrls = array_unique($this->purgeUrls);
		if (empty($purgeUrls)) {
			return;
		}
		$deleted = \FastCache\Platform\Cache::deleteCacheFilesByUrls($purgeUrls);
		if (!empty($deleted)) {
			$logger->fr("FS-CACHE PURGE: Deleted " . count($deleted) . " file(s): " . implode(', ', $deleted));
		}
	}

	/**
	 * Purge CDN cache only if CDN is enabled in settings.
	 *
	 * @return void
	 */
	private function purgeCdnCache()
	{
		$options = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);
		if (empty($options['fastcache-enable'])) {
			return; // CDN disabled, skip PURGE requests
		}
		$this->purgeCache();
	}

	/**
	 * purgeing
	 * TODO i seguenti metodi devono diventare una classe esterna comune tra public e admin
	 * purgeCache()
	 * executeThePurge()
	 * addStatusToPurgeUrls()
	 * addQueryStringToPurgeUrls()
	 *
	 * @return void
	 */
	private function purgeCache()
	{
		/*
		 * compressione e controllo
		 */
		$purgeUrls = array_unique($this->purgeUrls);
		if (empty($purgeUrls)) {
			return;
		}
		/*
		 * controllo ed eventuale return
		 */
		$purgeUrls2 = $this->addStatusToPurgeUrls($purgeUrls);
		if (empty($purgeUrls2)) {
			return;
		}
		/*
		 * aggiunta di querystring e cambio ENDOPINT verso CDN
		 */
		$purgeUrls3 = $this->addQueryStringToPurgeUrls($purgeUrls2);

		if (isset($_POST['hostcache-fields_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hostcache-fields_wpnonce']))) && isset($_POST['hostcache_ttl'])) {
			return;
		}

		/*
		 * esegui purge
		 */
		$this->executeThePurge($purgeUrls3);
	}

	/**
	 * Esecuzione effettiva della Purge in relazione agli Url trovati
	 *
	 * @param
	 *        	$purgeUrls3
	 * @return void
	 */
	private function executeThePurge($purgeUrls3)
	{
		$regexp = [];
		$exact = [];
		$token = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS)['text-token'];
		foreach ($purgeUrls3 as $k => $v) {
			if (str_contains($v, "*")) {
				$regexp[] = $v;
			} else {
				$exact[] = $v;
			}
		}
		$logger = new \FastCache\Host\HCommon();
		if (!empty($regexp)) {
			$method = "regexp";
			$purgeUrlsImploded = implode(",", $regexp);
			$response = wp_remote_request(FASTCACHEHOST_HOST_ENDPOINTCACHE, array(
				'method' => 'PURGE',
				'sslverify' => false,

				'headers' => array(
					'X-HST-CACHE-PurgeKey' => $token,
					'host' => FASTCACHEHOST_HOSTNAME,
					'X-HST-CACHE-Purge-Method' => $method,
					'XKey' => $purgeUrlsImploded
				)
			));
			if (is_wp_error($response)) {
				$logger->fr("PURGE: ERROR");
				$logger->fr("Host:" . FASTCACHEHOST_HOSTNAME);
				$logger->fr("Method:" . $method);
				$logger->fr("Token:" . $token);
				$errore = 1;
			} else {
				$retcode = isset($response['response']['code']) ? $response['response']['code'] : 'unknown';
				$logger->fr("PURGE: " . $purgeUrlsImploded . " Method: " . $method . "-adm - retcode:" . $retcode);
			}
		}

		if (!empty($exact)) {
			$method = "exact";
			$purgeUrlsImploded = implode(",", $exact);
			$response = wp_remote_request(FASTCACHEHOST_HOST_ENDPOINTCACHE, array(
				'method' => 'PURGE',
				'headers' => array(
					'X-HST-CACHE-PurgeKey' => $token,
					'host' => FASTCACHEHOST_HOSTNAME,
					'X-HST-CACHE-Purge-Method' => $method,
					'XKey' => $purgeUrlsImploded
				)
			));
			if (is_wp_error($response)) {
				$logger->fr("PURGE: ERROR");
				$logger->fr("Host:" . FASTCACHEHOST_HOSTNAME);
				$logger->fr("Method:" . $method);
				$logger->fr("Token:" . $token);
				$errore = 1;
			} else {
				$retcode = isset($response['response']['code']) ? $response['response']['code'] : 'unknown';
				$logger->fr("PURGE: " . $purgeUrlsImploded . " Method: " . $method . "-adm - retcode:" . $retcode);
			}
		}
		usleep(100000);
	}
	/**
	 * aggiungo ad ogni url la querystring
	 * sostituisco l'hostname originale con l'endpoint di CDN
	 *
	 * @param
	 *        	$purgeUrls2
	 *        	
	 * @return array
	 */
	private function addQueryStringToPurgeUrls($purgeUrls2)
	{
		$pathq = "";
		$purgeUrls3 = [];
		$executed = [];

		foreach ($purgeUrls2 as $url) {
			$executed[$url] = $url;
			$homeUrlSlash = get_home_url();
			$homeUrl = get_home_url();
			$lastChar = substr($homeUrl, -1);
			if ($lastChar != "/") {
				$homeUrlSlash = $homeUrl . "/";
			}
			if (str_contains($url, get_home_url()) && !str_contains($url, $homeUrlSlash)) {
				$url = str_replace(get_home_url(), $homeUrlSlash, $url);
			}
			if (isset($url) && trim($url) != "") {
				$pathq = "";
				$pu = wp_parse_url($url);
				$path = ".*";
				if (!empty($pu) && isset($pu['path']) && trim($pu['path']) != "") {
					$path = $pu['path'];
					if ($path == "/") {
						$path = "";
					}
				}
				if (!empty($pu) && isset($pu['query']) && trim($pu['query']) != "") {
					if (isset($pu['query']) && $pu['query'] != "") {
						$pathq = "?=" . $pu['query'];
					}
				}
			}
			if (substr($path, 0, 1) == "/") {
				$path = substr($path, 1);
			}
			$url2 = FASTCACHEHOST_HOST_ENDPOINTCACHE . $path . $pathq;
			$purgeUrls3[$url2] = $url2;
		}
		return $purgeUrls3;
	}
	/**
	 * set done =1 per ogni url per evitare la doppia elaborazione ad
	 * esempio in caso di post_edit e poi di post_save, evento che avviene quando si fa "update" di un post
	 *
	 * @param
	 *        	$purgeUrls
	 *        	
	 * @return array
	 */
	private function addStatusToPurgeUrls($purgeUrls)
	{
		global $host_v_purge_urls_func_var;
		$purgeUrls2 = [];
		if (isset($host_v_purge_urls_func_var) && !empty($host_v_purge_urls_func_var)) {
			foreach ($host_v_purge_urls_func_var as $k => $v) {
				if (in_array($v['url'], $purgeUrls) && $v['done'] == 0) {
					$host_v_purge_urls_func_var[$k]['done'] = 1;
					$purgeUrls2[$k] = $k;
				}
			}
		}
		return $purgeUrls2;
	}
	public function create_custom_fields()
	{
		$postTypes = array(
			'page',
			'post'
		);
		if (function_exists('add_meta_box')) {
			foreach ($postTypes as $postType) {
				add_meta_box(FASTCACHEHOST_HOST_PLUGINNAME, 'Host Caching', array(
					$this,
					'display_custom_fields'
				), $postType, 'side', 'high');
			}
		}
	}
	public function display_custom_fields()
	{
		global $post;
		wp_nonce_field('hostcache-fields', 'hostcache-fields_wpnonce', false, true);
		foreach ($this->customFields as $customField) {
			// Check scope
			$scope = $customField['scope'];
			$output = false;
			foreach ($scope as $scopeItem) {
				switch ($scopeItem) {
					default: {
						if (!isset($post->ID) || get_post_type($post->ID) === false) {
							return;
						}
						if (get_post_type($post->ID) == $scopeItem)
							$output = true;
						break;
					}
				}
				if ($output)
					break;
			}
			// Check capability
			if (!current_user_can($customField['capability'], $post->ID))
				$output = false;
			// Output if allowed
			if ($output) {
				switch ($customField['type']) {
					case "checkbox": {
						$html = <<<EOF
							                        <p><strong>--customfieldTitle--</strong></p>
							                        <label class="screen-reader-text" for="--for--">--customFieldTitle-- </label>
							                        <p><input type="checkbox" name="--prefix----customFieldName--" id="--prefix----customfieldName--" value="yes" --checked-- style="width: auto;" /></p>
							EOF;
						$html = str_replace("--customfieldTitle--", $customField['title'], $html);
						$html = str_replace("--customFieldName--", $customField['name'], $html);
						$html = str_replace("--prefix--", $this->prefix, $html);
						$html = str_replace("--for--", $this->prefix . $customField['name'], $html);
						if (get_post_meta($post->ID, $this->prefix . $customField['name'], true) == "yes") {
							$html = str_replace("--checked--", " checked=\"checked\" ", $html);
						} else {
							$html = str_replace("--checked--", "", $html);
						}
						echo wp_kses($html, FASTCACHEHOST_ALLOWEDHTML);
						break;
					}
					default: {
						$html = <<<EOF
															<p><strong>--cfTitle--</strong></p>
															<p>
															    <input type="text" name="--prefix----cfName--" id="--prefix----cfName--" value="--value--" />
															</p>
							EOF;
						$html = str_replace("--cfTitle--", $customField['title'], $html);
						$html = str_replace("--cfName--", $customField['name'], $html);
						$html = str_replace("--prefix--", $this->prefix, $html);
						$html = str_replace("--value--", get_post_meta($post->ID, $this->prefix . $customField['name'], true), $html);
						echo wp_kses($html, FASTCACHEHOST_ALLOWEDHTML);
						break;
					}
				}
			} else {
				$value = get_post_meta($post->ID, $this->prefix . $customField['name'], true);
				$html = '<p><strong>--cfTitle--</strong></p>';
				$html .= '<p><input type="text" name="--prefix----cfName--" id="--prefix----cfName--" value="--value--" disabled /></p>';
				$html = str_replace("--cfTitle--", $customField['title'], $html);
				$html = str_replace("--cfName--", $customField['name'], $html);
				$html = str_replace("--prefix--", $this->prefix, $html);
				$html = str_replace("--value--", $value, $html);
				echo wp_kses($html, FASTCACHEHOST_ALLOWEDHTML);
			}
			$default_ttl = get_option($this->prefix . 'ttl');
			if ($customField['description']) {
				echo '<p>' . esc_html(sprintf($customField['description'], $default_ttl)) . '</p>';
			}
		}
	}
}