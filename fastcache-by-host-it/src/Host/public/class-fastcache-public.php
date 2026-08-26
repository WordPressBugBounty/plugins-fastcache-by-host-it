<?php

namespace FastCache\Host;

/**
 * The public-facing functionality of the plugin.
 *
 * @link https://fastcache.host.it/
 * @since 1.0.0
 *       
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @author host.it <info@host.it>
 */
class HPublic {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name
	 *        	The name of the plugin.
	 * @param string $version
	 *        	The version of this plugin.
	 */
	private $iconOk; // svg dell'icona OK
	private $iconKO; // svg dell'icona KO
	private $iconOkBig; // svg dell'icona OK
	private $iconKOBig; // svg dell'icona KO
	private $log;
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action ( "wp_ajax_hstPurgeCache", array (
				$this,
				"purgeCache"
		) );
		add_action ( "wp_ajax_nopriv_hstPurgeCache", array (
				$this,
				"purgeCache"
		) );
		add_action ( "wp_ajax_testCache", array (
				$this,
				"testCache"
		) );
		add_action ( "wp_ajax_attivazionefastcache", array (
				$this,
				"attivazionefastcache"
		) );
		add_action ( 'transition_post_status', array (
				$this,
				'purgePostTransitionPostStatus'
		), 10, 3 );
		$this->log =  new \FastCache\Host\HCommon();	
		$this->setIcons ();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

	/**
	 * This function is provided for demonstration purposes only.
	 *
	 * An instance of this class should be passed to the run() function
	 * defined in Loader as all of the hooks are defined
	 * in that particular class.
	 *
	 * The Loader will then create the relationship
	 * between the defined hooks and the functions defined in this
	 * class.
	 */

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/' . FASTCACHEHOST_HOST_PLUGINNAME . '-public.css', [], $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$p = plugin_dir_path ( __FILE__ ) . 'js/' . FASTCACHEHOST_HOST_PLUGINNAME . '-public.js';
		$u = plugin_dir_url ( __FILE__ ) . 'js/' . FASTCACHEHOST_HOST_PLUGINNAME . '-public.js';
		wp_enqueue_script ( $this->plugin_name, $u, array (
				'jquery'
		), filemtime ( $p ), false );
		wp_localize_script ( 'fastcache', 'ajax_var', array (
				'url' => admin_url ( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce ( 'ajax-nonce' )
		) );
		
		wp_localize_script('fastcache', 'fastcache_confirm', array(
				'message_base' => __('Caches are automatically regenerated when the default TTL expires or at the TTL value set in the appropriate fields. For this reason, we suggest not performing the purge but waiting for the time indicated in TTL.', 'fastcache'),
				'message_question' => __('Do you want to proceed anyway with the deletion', 'fastcache'),
				'message_this' => __('of this page cache?', 'fastcache'),
				'message_all' => __('of all cache?', 'fastcache'),
				'message_homepage' => __('of the homepage cache?', 'fastcache')
		));
	}
	/**
	 * Append the button to the end of the content.
	 *
	 * @since 1.0.0
	 */
	public function append_the_button($content) {

		// Get our item ID
		$item_id = get_queried_object_id ();

		// Get current item post type
		$current_post_type = get_post_type ( $item_id );

		// Get our saved page ID, so we can make sure that this button isn't being shown there
		$saved_page_id = get_option ( 'toptal_save_saved_page_id' );

		// Set default values for options that we are going to call below
		$post_types = array ();
		$override = 0;

		// Get our options
		$options = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
		if (! empty ( $options ['post-types'] )) {
			$post_types = $options ['post-types'];
		}
		if (! empty ( $options ['toggle-content-override'] )) {
			$override = $options ['toggle-content-override'];
		}

		// Let's check if all conditions are ok
		if ($override == 1 && ! empty ( $post_types ) && ! is_page ( $saved_page_id ) && in_array ( $current_post_type, $post_types )) {

			// Append the button
			$custom_content = '';
			ob_start ();
			// echo $this->show_save_button();
			$custom_content .= ob_get_contents ();
			ob_end_clean ();
			$content = $content . $custom_content;
		}

		return $content;
	}
	public function send_headers() {
		// CDN headers incompatible with Multisite (single token per site)
		if ( is_multisite() ) {
			return;
		}

		global $post;
		$enable = 0;
		$pt = 'post';
		$ttl = FASTCACHE_DEFAULTTTL;
		if ( isset ( $post->post_type )) {
			$pt = $post->post_type;
			
			$settings = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
			$enable = 0;
			$token = "";
			if (isset ( $settings ['fastcache-enable'] )) {
				$enable = $settings ['fastcache-enable'];
			}
			if (isset ( $settings ['text-token'] )) {
				$token = $settings ['text-token'];
			}
			/*
			 * se ho un ttl impostato per un certo posttype e se è superiore a "0"
			 * allora lo uso come valore di ttl
			 */
			if (isset ( $settings ['ttl-post-types'] [$pt] ) && trim ( $settings ['ttl-post-types'] [$pt] ) != "" && $settings ['ttl-post-types'] [$pt] > 0) {
				// echo "trovato post type ".$pt . " set ttl: " . $settings['ttl-post-types'][$pt] . "<br>" ;
				$ttl = $settings ['ttl-post-types'] [$pt];
			} else if (
			/*
			 * ALTRIMENTI SE
			 * se ho un impostato il flag tale per cui in caso di NON ttl imposto il default
			 * allora uso come ttl il default_ttl generico per l'applicazione
			 */
				isset ( $settings ['default-ttl'] )
				&& trim ( $settings ['default-ttl'] ) != ""
				&& $settings ['default-ttl'] > 0
				&& isset ( $settings ['fastcache_cdn_default_ttl_notfound_set'] )
				&& $settings ['fastcache_cdn_default_ttl_notfound_set'] == 1
			) {
				// echo "trovato default ttl: " . $settings['default-ttl'] . "<br>" ;
				$ttl = $settings ['default-ttl'];
			} else if (isset ( $settings ['fastcache_cdn_default_ttl_notfound_set'] ) && $settings ['fastcache_cdn_default_ttl_notfound_set'] == 1 ) {
				/*
				 * altrimenti SE
			 	* se ho un impostato il flag tale per cui in caso di NON ttl imposto il default
				 * FASTCACHE_DEFAULTTTL
				 */
				
					$ttl = $settings ['default-ttl'] ?: FASTCACHE_DEFAULTTTL;
			}
			
		}
		if (is_feed ()) {
			$ttl = 60;
		}
		$excluded = (int) $this->ttlExcludedUrls (); // ensure integer even if ttlExcludedUrls returns null
		
		// Considera anche l'esclusione per tipo di post
		if ($this->excludePostType($pt)) {
			$excluded ++;
		}
		if ($excluded > 0) {
			Header ( 'X-HST-CACHE-Enabled: NO:Url Exclusion Matched', true );
			Header('X-HST-CACHE-ttl: 0', true);
		} else if ($enable == 1 && trim ( $token ) != "") {
			// Controlla se il ttl specifico da plugin è disabilitato
			//	$ttl = FASTCACHE_DEFAULTTTL;
			
			if (is_user_logged_in ()) {
				Header ( 'X-HST-CACHE-Enabled: false:User is logged in', true );
				$ttl = 0;
			} else {
				Header ( 'X-HST-CACHE-Enabled: true', true );
			}
		
			// if(!$settings ['enable-ttl']) {
				Header('X-HST-CACHE-ttl: ' . $ttl, true);
			// }
			// if ($this->debug) {
			// Header('X-HST-CACHE-Debug: true', true);
			// }
		} else {
			Header ( 'X-HST-CACHE-Enabled: false', true );
		}
	}
	private function ttlExcludedUrls() {
		$options = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
		
		if (! isset ( $options ['url_exclusion'] ) || empty ( $options ['url_exclusion'] )) {
			return;
		}

		$urls = $options ['url_exclusion'];
		if (! is_array ( $urls )) {
			return;
		}
		
		// URL attuale assoluto
		$utils = new HCommon ();
		$current_url = home_url ( add_query_arg ( null, null ) );
		$current_url = strtolower ( untrailingslashit ( $current_url ) );
		$excluded = 0;
		foreach ( $urls as $url ) {
			$url = strtolower ( untrailingslashit ( trim ( $url ) ) );
			if ($url !== '' && strpos ( $current_url, $url ) !== false) {
				$excluded ++;
			}
		}
		return $excluded;
	}
	/**
	 * Verifica se il tipo di post corrente è nella lista di esclusione
	 * impostata in $settings['posttype_exclusion'].
	 * Può essere una stringa ('post' o 'page'), una lista separata da virgole o un array.
	 * Restituisce 1 se escluso, 0 altrimenti.
	 */
	private function excludePostType($current_post_type) {
		$settings = get_option(FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);
		if (!isset($settings['posttype_exclusion'])) {
			return 0;
		}

		$exclusion = $settings['posttype_exclusion'];

		if (!$current_post_type) {
			return 0;
		}

		return in_array($current_post_type, $exclusion) ? 1 : 0;
	}
	public function testCache() {
		if (! wp_verify_nonce ( $_POST ['nonce'], 'ajax-nonce' )) {
			die ( __ ( 'Some security checks failed, we recommend contacting plugin support!' ) );
		}
		$token = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS ) ['text-token'];
		$method = "exact";
		$url = FASTCACHEHOST_HOST_ENDPOINTCACHE . "testcache";
		$params = array (
				'X-HST-CACHE-PurgeKey' => $token,
				'host' => FASTCACHEHOST_HOSTNAME,
				'X-HST-CACHE-Purge-Method' => $method
		);
		$response = wp_remote_request ( $url, array (
				'method' => 'PURGE',
				'headers' => $params,
				'sslverify' => false
		) );

		if (is_wp_error ( $response )) {
			$htmlReport = "Errore di comunicazione";
		} else {
			$retArr = $this->createReportTest ( $response );
			$htmlReport = $this->createHtmlReportTest ( $retArr );
		}
		echo wp_kses ( $htmlReport, FASTCACHEHOST_ALLOWEDHTML );
		exit ();
	}
	private function createReportTest($response) {
		// print_r($response);
		$messaggiBodyArr = json_decode ( $response ['body'], true );
		$report = [ ];
		$report ['cdn'] = "ko";
		$report ['comandi'] = "ko";
		$report ['cache-ita'] ['servercount'] = 0;
		$report ['cache-ita'] ['ko'] = 0;
		$report ['cache-ita'] ['ok'] = 0;
		$report ['cache-ita'] ['unauth'] = 0;
		$report ['cache-int'] ['servercount'] = 0;
		$report ['cache-int'] ['ko'] = 0;
		$report ['cache-int'] ['ok'] = 0;
		$report ['cache-int'] ['unauth'] = 0;
		$errore = 0;
		if (isset ( $response ['response'] ['code'] ) && $response ['response'] ['code'] == 200) {
			$report ['cdn'] = "ok";
		} else {
			$errore ++;
		}

		if (isset ( $messaggiBodyArr ['done'] ) && $messaggiBodyArr ['done'] == 1) {
			// file_put_contents(dirname(__FILE__) . "/done.log",$messaggiBodyArr['done']);
			$report ['comandi'] = "ok";
		} else {
			$errore ++;
		}
		if (isset ( $messaggiBodyArr ['nodes'] )) {
			foreach ( $messaggiBodyArr ['nodes'] as $server => $messaggio ) {
				if (str_contains ( strtolower ( $server ), "cache-ita" )) {
					$report ['cache-ita'] ['servercount'] ++;
					if ($messaggio == "404") {
						$report ['cache-ita'] ['ko'] ++;
						$errore ++;
					} else if ($messaggio == "405") {
						$report ['cache-ita'] ['unauth'] ++;
						$errore ++;
					} else {
						$report ['cache-ita'] ['ok'] ++;
					}
				}
				if (str_contains ( strtolower ( $server ), "cache-int" )) {
					$report ['cache-int'] ['servercount'] ++;

					if ($messaggio == "404") {
						$report ['cache-int'] ['ko'] ++;
						$errore ++;
					} else if ($messaggio == "405") {
						$report ['cache-int'] ['unauth'] ++;
						$errore ++;
					} else {
						$report ['cache-int'] ['ok'] ++;
					}
				}
				if ($messaggio != "200" && $messaggio != "404") {
					$errore = 1;
				}
			}
		}
		return array (
				"report" => $report,
				"errore" => $errore
		);
	}
	private function createHtmlReportTest($arr) {
		$report = $arr ['report'];
		$errore = $arr ['errore'];
		$html = $this->htmlBlockGeneralStatus ( $errore );
		$html .= $this->htmlBlockFunzionamentoGlobale ( $report );
		$html .= $this->htmlBlockCacheSections ( $report );
		return $html;
	}
	private function htmlBlockCacheSections($report) {
		$html = "<div class=\"peer-has-[span.clicked]:grid hidden  grid-cols-4 gapx-4 w-full\">";
		$sezioni = array (
				"cache-ita" => [ 
						'label' => 'CDN Ita'
				],
				"cache-int" => [ 
						'label' => 'CDN Int'
				]
		);
		$servizi = [ 
				"servercount" => "Server",
				"ko" => "KO",
				"ok" => "OK",
				"unauth" => "UnAuth"
		];

		foreach ( $sezioni as $sezione => $datas ) {
			if ($report [$sezione] ['servercount'] == 0) {
				continue;
			}
			$label = $datas ['label'];
			$html .= "<div class='peer-has-[span.clicked]:grid hidden col-span-4 p-2 bg-gray-200  mt-4 '>" . $label . "</div>";
			foreach ( $servizi as $k => $v ) {
				$html .= "<div class='h-8 justify-around content-around bg-gray-100 text-gray-800  my-4'>" . $v . "</div>";
			}
			if (isset ( $report [$sezione] ['servercount'] ) && $report [$sezione] ['servercount'] > 0) {
				$html .= $this->createIcon ( "ok", $report [$sezione] ['servercount'] );
			} else {
				$html .= $this->createIcon ( "ko", $report [$sezione] ['servercount'] );
			}
			if (isset ( $report [$sezione] ['ko'] ) && $report [$sezione] ['ko'] > 0) {
				$html .= $this->createIcon ( "ko", $report [$sezione] ['ko'] );
			} else {
				$html .= $this->createIcon ( "ok", $report [$sezione] ['ko'] );
			}
			if (isset ( $report [$sezione] ['ok'] ) && $report [$sezione] ['ok'] > 0) {
				$html .= $this->createIcon ( "ok", $report [$sezione] ['ok'] );
			} else {
				$html .= $this->createIcon ( "ko", $report [$sezione] ['ok'] );
			}
			if (isset ( $report [$sezione] ['unauth'] ) && $report [$sezione] ['unauth'] > 0) {
				$html .= $this->createIcon ( "ko", $report [$sezione] ['unauth'] );
			} else {
				$html .= $this->createIcon ( "ok", $report [$sezione] ['unauth'] );
			}
		}
		$html .= "</div>";
		return $html;
	}
	private function htmlBlockFunzionamentoGlobale($report) {
		$html = "<div class='peer-has-[span.clicked]:flex hidden  w-full p-2 bg-gray-200 mt-4'>" . __ ( 'Global features', 'fastcache' ) . "</div>";

		$html .= "<div class=\"peer-has-[span.clicked]:grid hidden grid grid-cols-2 gapx-4 w-full my-8\"> ";
		if (isset ( $report ['cdn'] ) && $report ['cdn'] == "ok") {
			$html .= $this->createIcon ( "ok", "CDN" );
		} else {
			$html .= $this->createIcon ( "ko", "CDN" );
		}
		if (isset ( $report ['comandi'] ) && $report ['comandi'] == "ok") {
			$html .= $this->createIcon ( "ok", __ ( 'Commands', 'fastcache' ) );
		} else {
			$html .= $this->createIcon ( "ko", __ ( 'Commands', 'fastcache' ) );
		}
		$html .= "</div>";

		return $html;
	}
	private function htmlBlockGeneralStatus($errore) {
		$sn = sanitize_text_field ( $_SERVER ['SERVER_NAME'] );
		$html = "<div class='peer  w-full p-2 bg-gray-200 mt-4'>" . __ ( 'Check IP adddresses', 'fastcache' ) . "</div>";

		$html .= "<div class=\"w-full p-2\">Hostname <span class='font-bold'>" . $sn . "</span></div>";
		$html .= "<div class=\"w-full p-2\">Ip Server <span class='font-bold'>" . $this->idServer () . "</span></div>";
		$html .= "<div class=\"w-full p-2\">Ip da dns <span class='font-bold'>" . $this->idDns ( $sn ) . "</span></div>";
		$html .= "<div class='peer  w-full p-2 bg-gray-200 mt-4'>CDN Check<span id='opendettaglio'  class='text-[10px] text-blue-500 underline float-right'>" . __ ( 'Details', 'fastcache' ) . "</span></div>";
		$msgOk = __ ( 'The CDN is correctly configured', 'fastcache' );
		$msgErrore = __ ( 'There are problems with the CDN configuration, check all the parameters and if you find no errors, contact the support staff.', 'fastcache' );
		if ($errore == 0) {
			$html .= $this->createIcon ( "ok", $msgOk, "w-full text-center" );
		} else {
			$html .= $this->createIcon ( "ko", $msgErrore, "w-full text-center" );
		}
		return $html;
	}
	private function idServer() {
		$ip = '';

		if (function_exists ( 'filter_var' ) && filter_var ( $_SERVER ['SERVER_ADDR'], FILTER_VALIDATE_IP, array (
				'flags' => FILTER_FLAG_IPV4,
				FILTER_FLAG_NO_PRIV_RANGE,
				FILTER_FLAG_NO_RES_RANGE
		) )) {
			$ip = filter_var ( $_SERVER ['SERVER_ADDR'], FILTER_VALIDATE_IP, array (
					'flags' => FILTER_FLAG_IPV4,
					FILTER_FLAG_NO_PRIV_RANGE,
					FILTER_FLAG_NO_RES_RANGE
			) );
		}

		return $ip;
	}
	private function idDns($sn) {
		$ip = gethostbyname ( $sn );
		return $ip;
	}
	private function createIcon($status, $label, $class = "") {
		if ($class != "") {
			$class = " class=\"" . $class . "\"";
		}
		$html = "<div " . $class . ">";

		if ($class != "") {
			if ($status == "ok") {
				$html .= $this->iconOkBig;
			} else {
				$html .= $this->iconKOBig;
			}
		} else {
			if ($status == "ok") {
				$html .= $this->iconOk;
			} else {
				$html .= $this->iconKO;
			}
		}
		$html .= "<span class='label'>$label</span>";
		$html .= "</div>";
		return $html;
	}
	public function purgePostTransitionPostStatus($new_status, $old_status, $post) {
		$log =  new \FastCache\Host\HCommon();
		if ($old_status === 'future' && $new_status === 'publish') {
			$hadmin = new \FastCache\Host\HAdmin($this->plugin_name, $this->version);
			// Pass allow_background=true since scheduled posts are published via WordPress cron
			// This allows cache purge to work even when there's no current user context
			// Pass is_scheduled_publish=true to purge ALL page cache (ensures homepage/archives are updated)
			$hadmin->purge_post($post->ID, $post, true, true);
			return;
			/*
			 * old rel in funziona in caso di SOLO CDN sostituita con funzione più estesa
			 * 
			// Ottieni l'URL della pagina
			$post_url = get_permalink ( $post->ID );
			$pu = wp_parse_url ( sanitize_text_field ( $post_url ) );
			$path = $pu ['path'];
			$pathq = "";
			$settings = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
			if (! isset ( $settings ['text-token'] ) || trim ( $settings ['text-token'] ) == "") {
				echo "KO";
				exit ();
			}
			$token = $settings ['text-token'];
			$url = FASTCACHEHOST_HOST_ENDPOINTCACHE . ltrim ( $path, '/' ) . $pathq;
			$method = "exact";
			
			$this->execute_purge ( $token, $url, $method );
			*/
		}
	}
	private function setIcons() {
		$this->iconOk = <<<EOF
		                        <span class="inline-flex items-center justify-center w-6 h-6 me-2 text-sm font-semibold text-white bg-green-500 rounded-full okicon">
		                            <svg class="w-4 h-4"  fill="none" viewBox="0 0 16 12" >
		                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
		                            </svg>
		                        </span>
		EOF;
		$this->iconKO = <<<EOF
		                        <span class="inline-flex items-center justify-center w-6 h-6 me-2 text-sm font-semibold text-white bg-red-500 rounded-full koicon">
		                           <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
		                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
		                            </svg>
		                        </span>
		EOF;
		$this->iconOkBig = <<<EOF
		                        <span class="inline-flex justify-self-center items-center justify-center w-8 h-8 me-2 mt-2  text-sm font-semibold text-white bg-green-500 rounded-full">
		                            <svg class="w-8 h-8"  fill="none" viewBox="0 0 16 12" >
		                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5"/>
		                            </svg>
		                        </span>
		EOF;
		$this->iconKOBig = <<<EOF
		                        <span class="inline-flex justify-self-center items-center justify-center w-8 h-8 me-2 mt-2 text-sm font-semibold text-white bg-red-500 rounded-full">
		                           <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" >
		                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
		                            </svg>
		                        </span>
		EOF;
	}
	public function purgeCache() {
		$logger = new \FastCache\Host\HCommon ();
		if (! isset ( $_POST ['nonce'] ) || ! wp_verify_nonce ( $_POST ['nonce'], 'ajax-nonce' )) {
			die ( __ ( 'Some security checks failed, we recommend contacting plugin support!' ) );
		}
		$settings = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );
		if (! isset ( $settings ['text-token'] ) || trim ( $settings ['text-token'] ) == "") {
			echo "KO";
			exit ();
		}
		$token = $settings ['text-token'];
		if (isset ( $_POST ) && isset ( $_POST ['param'] ) && trim ( $_POST ['param'] ) != "") {
			$param = sanitize_text_field ( $_POST ['param'] );
		} else {
			exit ();
		}
		$method = "regexp";
		$pathq = "";
		switch ($param) {
			case "this" :
				if (isset ( $_POST ) && isset ( $_POST ['loc'] ) && trim ( $_POST ['loc'] ) != "") {
					$pu = wp_parse_url ( $_POST ['loc'] );
					$path = ".*";
					if (! empty ( $pu ) && isset ( $pu ['path'] ) && trim ( $pu ['path'] ) != "") {
						$path = $pu ['path'];
						if ($path == "/") {
							$path = "";
						}
					}
					if (! empty ( $pu ) && isset ( $pu ['query'] ) && trim ( $pu ['query'] ) != "") {
						if (isset ( $pu ['query'] ) && $pu ['query'] != "") {
							$pathq = "?=" . $pu ['query'];
						}
					}
				}
				$url = FASTCACHEHOST_HOST_ENDPOINTCACHE . $path . $pathq;
				$method = "exact";
				break;
			case "all" :
				$url = FASTCACHEHOST_HOST_ENDPOINTCACHE . ".*";
				$method = "regexp";
				break;
			case "homepage" :
				$url = FASTCACHEHOST_HOST_ENDPOINTCACHE;
				$method = "exact";
				break;
			default :
				$url = FASTCACHEHOST_HOST_ENDPOINTCACHE . ".*";
				$method = "regexp";
				break;
		}
		$this->execute_purge ( $token, $url, $method );
	}
	private function execute_purge($token, $url, $method) {
		$logger = new \FastCache\Host\HCommon ();

		$params = array (
				'X-HST-CACHE-PurgeKey' => $token,
				'host' => FASTCACHEHOST_HOSTNAME,
				'X-HST-CACHE-Purge-Method' => $method
		);
		$response = wp_remote_request ( $url, array (
				'method' => 'PURGE',
				'headers' => $params,
				'sslverify' => false
		) );
		$errore = 0;
		if (is_wp_error ( $response )) {
			$logger->fr ( "PURGE: ERROR" );
			$logger->fr ( "Host:" . FASTCACHEHOST_HOSTNAME );
			$logger->fr ( "Method:" . $method );
			$logger->fr ( "Token:" . $token );

			$errore = 1;
		} else {
			$logger->fr ( "PURGE: " . $url . " Method: " . $method . " noadm - retcode:" . $response ['response'] ['code'] );
			usleep ( 100000 );
			$messaggiBodyArr = json_decode ( $response ['body'], true );
			if (isset ( $messaggiBodyArr ['nodes'] )) {
				foreach ( $messaggiBodyArr ['nodes'] as $messaggio ) {
					if ($messaggio != "200" && $messaggio != "404") {
						$errore = 1;
					}
				}
			}
		}
		/**
		 * classi in "inline style" e non in tailwind perchè in frontend non abbiamo tailwind
		 */
		if ($errore != 0) {
			$html = "<div style=\"padding:1rem; background-color:#FFDCDCFF; border-color: #E5E7EBFF; border-width:1px; z-index:5000; right:3.5rem; position:fixed; top:7rem; box-shadow: 0 0 #0000, 0 0 #0000, 0 0 #0000, 0 0 #0000, 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);\" class=\"\">";
			$html .= "<div style=\"padding:1rem; font-size: 0.875rem; line-height:1.25rem; color:#AD3333FF; border-radius: 0.5rem; display: flex; flex-direction: column;\" class=\" \" role=\"alert\">";
			$html .= "<span style=\"font-weight: 500; width:100%; margin-bottom:0.5rem;\" >Attenzione!</span> è stato rilevato un problema. In caso l'errore persistesse contattate l'assistenza di HOST.it";
			$html .= "</div>";
			$html .= "</div>";

			echo __ ( "Attention, a problem has been detected. If the error persists, please contact HOST.it support." );
		} else {
			$html = "<div style=\"padding:1rem; background-color:#DCFFDCFF; border-color: #E5E7EBFF; border-width:1px; z-index:5000; right:3.5rem; position:fixed; top:7rem;box-shadow: 0 0 #0000, 0 0 #0000, 0 0 #0000, 0 0 #0000, 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);\" class=\"  \">";
			$html .= "<div style=\"padding:1rem;  font-size: 0.875rem; line-height:1.25rem;  color:#33AD33FF; border-radius: 0.5rem; display: flex; flex-direction: column; \" class=\"\" role=\"alert\">";
			$html .= "<span style=\"font-weight: 500; width:100%; margin-bottom:0.5rem;\" >Success!</span> Pulizia delle cache effettuata";
			$html .= "</div>";
			$html .= "</div>";
		}
		echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
		exit ();
	}
}
