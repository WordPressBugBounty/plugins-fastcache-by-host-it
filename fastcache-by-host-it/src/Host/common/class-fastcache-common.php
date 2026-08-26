<?php

namespace FastCache\Host;

class HCommon {
	public function fr($vars, $exit = false, $force=false) {
		$skip=false;
		if($force===true) {
			$skip=true;
		}
		if (FASTCACHEHOST_LOGACTIVE == 0 ) {
			$skip=true;
		}
		if($skip===true) {
			return;
		}

		// Determina il file di log
		if (defined('FASTCACHEHOST_LOGPATH') && FASTCACHEHOST_LOGPATH !== '') {
			$log_file = FASTCACHEHOST_LOGPATH;
		} else {
			$upload_dir = wp_upload_dir();
			$log_dir    = $upload_dir['basedir'] . '/logs/';
			$log_file   = $log_dir . 'log.txt';
		}

		// Crea la directory se non esiste
		$log_dir = dirname($log_file);
		if (!file_exists($log_dir)) {
			wp_mkdir_p($log_dir);
		}

		// Formatta il messaggio
		if (is_array($vars) || is_object($vars)) {
			$out = current_time('mysql') . ' ' . print_r($vars, true);
		} else {
			$out = current_time('mysql') . ' ' . $vars;
		}

		// Scrive nel log — compatibile con frontend, admin e WP cron
		file_put_contents($log_file, $out . "\r\n", FILE_APPEND | LOCK_EX);

		if ($exit == true) {
			exit();
		}
	}
	/**
	 * Prints out all settings sections added to a particular settings page.
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages.
	 * @global array $wp_settings_fields Storage array of settings fields and info about their pages/sections.
	 * @since 2.7.0
	 *       
	 * @param string $page
	 *        	The slug name of the page whose settings sections you want to output.
	 */
	function override_do_settings_sections_host($page) {
		global $wp_settings_sections, $wp_settings_fields;

		if (! isset ( $wp_settings_sections [$page] )) {
			return;
		}

		foreach ( ( array ) $wp_settings_sections [$page] as $section ) {
			$visibility = "hidden";
			if ($section ['title'] == "Settings") {
				$visibility = "";
			}
			if ('' !== $section ['before_section']) {
				if ('' !== $section ['section_class']) {
					echo wp_kses_post ( sprintf ( $section ['before_section'], esc_attr ( $section ['section_class'] ), $visibility ) );
				} else {
					echo wp_kses_post ( $section ['before_section'] );
				}
			}

			if ($section ['title']) {
				$html = "<h2 class='w-2/3 flex p-4  font-normal text-2xl'>";
				$html .= esc_html ( $section ['title'] );
				$html .= "</h2>";
				echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
			}

			if ($section ['callback']) {
				call_user_func ( $section ['callback'], $section );
			}

			if (! isset ( $wp_settings_fields ) || ! isset ( $wp_settings_fields [$page] ) || ! isset ( $wp_settings_fields [$page] [$section ['id']] )) {
				continue;
			}

			$this->override_do_settings_fields_host ( $page, $section ['id'] );

			if ('' !== $section ['after_section']) {
				echo wp_kses ( $section ['after_section'], FASTCACHEHOST_ALLOWEDHTML );
			}
		}
	}
	function override_do_settings_fields_host($page, $section) {
		global $wp_settings_fields;

		if (! isset ( $wp_settings_fields [$page] [$section] )) {
			return;
		}

		foreach ( ( array ) $wp_settings_fields [$page] [$section] as $field ) {
			$class = '';

			if (! empty ( $field ['args'] ['class'] )) {
				$class = ' class="' . esc_attr ( $field ['args'] ['class'] ) . '"';
			}

			$html = "<ul " . $class . ">";
			if (! empty ( $field ['args'] ['notli'] ) && $field ['args'] ['notli'] !== true) {
				$html .= '<li>';
				if (! empty ( $field ['args'] ['label_for'] )) {
					$html .= '<li scope="row">xxxx<label for="' . esc_attr ( $field ['args'] ['label_for'] ) . '">' . esc_html ( $field ['title'] ) . '</label></li>';
				} else {
					$html .= '<li scope="row">' . esc_html ( $field ['title'] ) . '</li>';
				}
				$html .= '<li>';
			}
			echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );

			call_user_func ( $field ['callback'], $field ['args'] );
			$html = '</li>';
			$html .= '</ul>';
			echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
		}
	}
	public function createSection_host($sezione) {
		switch ($sezione) {
			case "help" :
				$html = $this->helpSection_host ();
				break;
			default :
				$html = "";
		}
		echo wp_kses ( $html, FASTCACHEHOST_ALLOWEDHTML );
	}
	private function helpSection_host() {
		$html = "<section id=\"help\" class=\"togglerTabs hidden\">";
		$html .= "<h2 class=\"w-2/3 flex p-4  font-normal text-2xl\">Frequently Asked Question</h2>";
		$html .= "<ul>--lis--</ul>";
		$html .= "</section>";

		$html = str_replace ( "--lis--", $this->faqs_fastcache_host (), $html );
		return $html;
	}
	private function faqs_fastcache_host() {
		require_once (plugin_dir_path ( __DIR__ ) . 'admin/partials/faq.php');
		$t = file_get_contents ( plugin_dir_path ( __DIR__ ) . 'admin/partials/faq.html' );
		$html = "";
		// $fqt=new \FASTCACHEHOST_HCommon\FASTCACHEHOST_faqText();

		$faqsArr = $this->faqs ();
		foreach ( $faqsArr as $k => $v ) {
			$t2 = str_replace ( "--name--", $v ['name'], $t );
			$t2 = str_replace ( "--question--", $v ['question'], $t2 );
			$t2 = str_replace ( "--answer--", $v ['answer'], $t2 );
			$html .= $t2;
		}
		return $html;
	}
	public function faqs() {
		$qa = array (
				array (
						"name" => "faqa",
						"question" => "Come posso ottenere un token di autenticazione per FastCache?",
						"answer" => "Puoi ottenere un token di autenticazione visitando https://host.it. Dopo aver ottenuto il token, potrai inserirlo nell'interfaccia del plugin FastCachesul tuo sito WordPress."
				),
				array (
						"name" => "faqb",
						"question" => "Cosa succede se un server della CDN va offline?",
						"answer" => "Se un server della CDN va offline, le richieste degli utenti verranno semplicemente reindirizzate a un altro server nella rete.
				Questo garantisce che il tuo sito rimanga sempre disponibile e veloce, anche in caso di problemi con un singolo server."
				),
				array (
						"name" => "faqc",
						"question" => "Come posso configurare FastCache sul mio sito?",
						"answer" => "Dopo aver installato e attivato il plugin, potrai accedere all'interfaccia del plugin sul tuo sito WordPress. Qui, potrai inserire il tuo token di autenticazione e configurare tutti i parametri di funzionamento della CDN"
				)
			// array(
			// "name" => "4faq",
			// "question" => "",
			// "answer" => "",
			// ),
			// array(
			// "name" => "5faq",
			// "question" => "",
			// "answer" => "",
			// ),
			// array(
			// "name" => "6faq",
			// "question" => "",
			// "answer" => "",
			// ),
		);
		return $qa;
	}
}