<?php

/**
 * FastCache - Performs several front-end optimizations for fast downloads
 *
 * @package   FastCache
 * @author    Host.it <info@host.it>
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace FastCache\Admin\Settings;

use FastCache\Core\Admin\MultiSelectItems;

class Html
{
	/**
	 *
	 * @param
	 *        	$key
	 * @param
	 *        	$settingName
	 * @param
	 *        	$defaultValue
	 * @param
	 *        	...$aArgs
	 *        	
	 * @return false|mixed|string
	 */
	public static function _($key, $settingName, $defaultValue, ...$aArgs) {
		list ( $function, $proOnly ) = static::extract ( $key );

		$aSavedSettings = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS );

		if (stripos ( $settingName, '][' ) !== false) {
			list ( $firstNamePart, $secondNamePart ) = explode ( '][', $settingName );
			$activeValue = $aSavedSettings [$firstNamePart] [$secondNamePart];
		} elseif (! isset ( $aSavedSettings [$settingName] ) && $key != 'multiselect') {
			$activeValue = $defaultValue;
		} elseif (! isset ( $aSavedSettings [$settingName] ) && $key == 'multiselect') {
			if (! $aSavedSettings && $aArgs [0] !== '') {
				$activeValue = $defaultValue;
			} else {
				if ($aArgs [0] === '') {
					$activeValue = [ ];
				} else {
					$activeValue = $defaultValue;
				}
			}
		} else {
			$activeValue = $aSavedSettings [$settingName];
		}

		$callable = [ 
				__CLASS__,
				$function
		];

		// prepend $settingName, $activeValue to arguments
		array_unshift ( $aArgs, $settingName, $activeValue );

		if ($key == 'multiselect') {
			array_push ( $aArgs, $defaultValue );
		}

		return call_user_func_array ( $callable, $aArgs );
	}

	/**
	 * @param $key
	 *
	 * @return array
	 */
	protected static function extract( $key )
	{
		$parts = explode( '.', $key );

		$function = $parts[0];
		$proOnly  = isset( $parts[1] ) && $parts[1] === 'pro';

		return [ $function, $proOnly ];
	}

	/**
	 * @param          $title
	 * @param          $description
	 * @param   false  $new
	 *
	 * @return string
	 */
	public static function description( $title, $description, $new = false )
	{
		$text = '<div class="title">' . $title;
		
		if ( $description )
		{
			$text .= '<div class="description"><div><p>' . $description . '</p></div></div>';
		}
		
		if ( $new )
		{
			$text .= ' <span class="badge badge-danger">New!</span>';
		}
		
		$text .= '</div>';
		
		return $text;
		
	}
	
	/**
	 * @param $settingName
	 * @param $activeValue
	 * @param string $class
	 *
	 * @return string
	 */
	public static function radio( $settingName, $activeValue, $class = '' )
	{
		$checked = ( $activeValue == '1' ) ? 'checked="checked"' : '';
		
		$noText = __( 'No', 'fastcache' );
		$yesText = __( 'Yes', 'fastcache' );
		
		$radioHtml = <<<HTML
		<div class="group cursor-pointer {$class}" id="fastcache_settings_{$settingName}_wrapper">
		    <input type="hidden" name="fastcache_settings[{$settingName}]" id="fastcache_settings_{$settingName}_value" value="{$activeValue}">
		    <input type="checkbox" class="!hidden peer" id="fastcache_settings_{$settingName}_checkbox" {$checked}>
		    
		    <label for="fastcache_settings_{$settingName}_checkbox" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600! hover:text-gray-600 peer-checked:text-gray-600 hover:bg-gray-50 [&.active]:border-blue-600!">
		        <div class="w-full flex items-center gap-2">
		            <div>
		                <div class="
		                    relative
		                    w-11
		                    h-6
		                    bg-gray-200
		                    rounded-full
		                    after:content-['']
		                    after:absolute
		                    after:top-[2px]
		                    after:start-[2px]
		                    after:bg-white
		                    after:border-gray-300
		                    after:border
		                    after:rounded-full
		                    after:w-5
		                    after:h-5
		                    after:transition-all
		                    group-has-[:checked]:bg-blue-600
		                    group-has-[:checked]:after:left-[22px]
		                ">
		                </div>
		            </div>
		 			<div class="text-sm">
		                <span id="fastcache_settings_{$settingName}_no_text">{$noText}</span>
		                <span id="fastcache_settings_{$settingName}_yes_text" class="hidden">{$yesText}</span>
		            </div>
		        </div>
		    </label>
		</div>
		
		<script>
		(function() {
		    const checkbox = document.getElementById('fastcache_settings_{$settingName}_checkbox');
		    const hiddenInput = document.getElementById('fastcache_settings_{$settingName}_value');
		    const noText = document.getElementById('fastcache_settings_{$settingName}_no_text');
		    const yesText = document.getElementById('fastcache_settings_{$settingName}_yes_text');
		    
		    function updateLabels() {
		        if (checkbox.checked) {
		            noText.classList.add('hidden');
		            yesText.classList.remove('hidden');
		        } else {
		            noText.classList.remove('hidden');
		            yesText.classList.add('hidden');
		        }
		    }
		    
		    // Inizializza lo stato corretto
		    updateLabels();
		    
		    if (checkbox && hiddenInput) {
		        checkbox.addEventListener('change', function() {
		            hiddenInput.value = this.checked ? '1' : '0';
		            updateLabels();
		        });
		    }
		})();
		</script>
		
		HTML;
		
		return $radioHtml;
	}
	
	/**
	 * @param $settingName
	 * @param $activeValue
	 * @param $aOptions
	 * @param string $class
	 *
	 * @return string
	 */
	public static function select( $settingName, $activeValue, $aOptions, $class = '', $multiple = false )
	{
		$optionsHtml = '';
		$multipleAttribute = $multiple ? 'multiple' : '';
		$multipleArray = $multiple ? '[]' : '';
		
		foreach ( $aOptions as $key => $value )
		{
			if(is_array($activeValue)) {
				$selected = in_array($key, $activeValue) ? ' selected="selected"' : '';
			} else {
				$selected = $activeValue == $key ? ' selected="selected"' : '';
			}
			
			$optionsHtml .= <<<HTML
			<option value="{$key}"{$selected}>{$value}</option>
			HTML;
		}
		
		$selectHtml = <<<HTML
		<div class="group cursor-pointer {$class}" id="fastcache_settings_{$settingName}_wrapper">
		    <label for="fastcache_settings_{$settingName}" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-600 hover:text-gray-600 hover:bg-gray-50">
		        <div class="w-full">
		            <select id="fastcache_settings_{$settingName}" name="fastcache_settings[{$settingName}]{$multipleArray}" class="{$class}" {$multipleAttribute}>
		                {$optionsHtml}
		            </select>
		        </div>
		    </label>
		</div>
		HTML;
                
                return $selectHtml;
	}
	
	/**
	 * @param $settingName
	 * @param $aActiveValues
	 * @param $type
	 * @param $group
	 * @param string $defaultValues
	 *
	 * @return string
	 */
	public static function multiselect( $settingName, $aActiveValues, $type, $group, $defaultValues = array() )
	{
		$optionsHtml = '';
		
		foreach ( $aActiveValues as $value )
		{
			$option = MultiSelectItems::{'prepare' . ucfirst( $group ) . 'Values'}( $value );
			
			$optionsHtml .= <<<HTML
			<option value="{$value}" selected>{$option}</option>
			HTML;
		}
		
		// Add all other default options if any as non selected
		if(count($defaultValues)) {
			foreach ($defaultValues as $default) {
				if(in_array($default, $aActiveValues)) {
					continue;
				}
				
				$defaultOption = MultiSelectItems::{'prepare' . ucfirst( $group ) . 'Values'}( $default );
				
				$optionsHtml .= <<<HTML
				<option value="{$default}">{$defaultOption}</option>
				HTML;
			}
		}
		
		$imgSrc = FASTCACHE_URL . 'media/core/images/loading.gif';
		
		$multiSelectHtml = <<<HTML
		<div class="group cursor-pointer" id="fastcache_settings_{$settingName}_wrapper">
		    <label for="fastcache_settings_{$settingName}" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-600 hover:text-gray-600 hover:bg-gray-50">
		        <div class="w-full">
		            <select id="fastcache_settings_{$settingName}" name="fastcache_settings[{$settingName}][]" class="fastcache-multiselect select2-ctrl w-full border-0 bg-transparent text-sm focus:ring-0 focus:outline-none" multiple="multiple" size="5" data-fastcache_type="{$type}" data-fastcache_group="{$group}" data-fastcache_param="{$settingName}">
		                {$optionsHtml}
		            </select>
		        </div>
		    </label>
		    <img id="img-{$settingName}" class="fastcache-multiselect-loading-image mt-2" src="{$imgSrc}" />
		</div>
		HTML;
                
                return $multiSelectHtml;
	}
	
	/**
	 * @param $settingName
	 * @param $activeValue
	 * @param string $size
	 * @param string $class
	 *
	 * @return string
	 */
	public static function text( $settingName, $activeValue, $size = '30', $class = '' )
	{
		$textInputHtml = <<<HTML
		<div class="group cursor-pointer {$class}" id="fastcache_settings_{$settingName}_wrapper">
		    <label for="fastcache_settings_{$settingName}" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-600 hover:text-gray-600 hover:bg-gray-50">
		        <div class="w-full">
		            <input type="text" id="fastcache_settings_{$settingName}" name="fastcache_settings[{$settingName}]" value="{$activeValue}" class="regular-text bg-transparent text-sm focus:ring-0 focus:outline-none" placeholder="">
		        </div>
		    </label>
		</div>
		HTML;
		
		return $textInputHtml;
	}
	
	/**
	 * @param $settingName
	 * @param $activeValue
	 * @param string $rows
	 * @param string $cols
	 * @param string $class
	 *
	 * @return string
	 */
	public static function textarea( $settingName, $activeValue, $rows = '10', $cols = '50', $class = '' )
	{
		$textInputHtml = <<<HTML
		<div class="group cursor-pointer {$class}" id="fastcache_settings_{$settingName}_wrapper">
		    <label for="fastcache_settings_{$settingName}" class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-600 hover:text-gray-600 hover:bg-gray-50">
		        <div class="w-full">
		            <textarea id="fastcache_settings_{$settingName}" name="fastcache_settings[{$settingName}]" rows="{$rows}" class="w-full bg-transparent text-sm focus:ring-0 focus:outline-none resize-none" placeholder="">{$activeValue}</textarea>
		        </div>
		    </label>
		</div>
		HTML;
		
		return $textInputHtml;
	}
	
	/**
	 * @param           $settingName
	 * @param           $activeValue
	 * @param   string  $class
	 *
	 * @return string
	 */
	public static function checkbox( $settingName, $activeValue, $class = '' )
	{
		$checked = $activeValue == '1' ? 'checked="checked"' : '';
		$offText = __( 'No', 'fastcache' );
		$onText  = __( 'Yes', 'fastcache' );
		
		$checkBoxHtml = <<<HTML
				<input type="checkbox" id="fastcache_settings_{$settingName}" class="{$class}" name="fastcache_settings[$settingName]" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="{$onText}" data-off="{$offText}" value="1" {$checked}>
		HTML;

		return $checkBoxHtml;
	}

	/**
	 * @param           $settingName
	 * @param           $aActiveValues
	 * @param           $aOptions
	 * @param   string  $class
	 *
	 * @return string
	 */
	public static function checkboxes( $settingName, $aActiveValues, $aOptions, $class = '' )
	{
		$optionsHtml = '';
		$i           = '0';
		
		foreach ( $aOptions as $key => $value )
		{
			$checked = '';
			
			if ( is_array( $aActiveValues ) && in_array( $key, $aActiveValues ) )
			{
				$checked = 'checked';
			}
			
			$optionsHtml .= <<<HTML
						<li>
							<input type="checkbox" id="fastcache_settings_{$settingName}{$i}" name="fastcache_settings[$settingName][]" value="{$key}" $checked>
							<label for="fastcache_settings_{$settingName}{$i}">{$value}</label>
						</li>
			HTML;
			$i ++;
		}
		
		$checkboxesHtml = <<<HTML
				<fieldset id="fastcache_settings_{$settingName}" class="{$class}">
					<ul>
						{$optionsHtml}
					</ul>
				</fieldset>
		HTML;
				
				return $checkboxesHtml;
	}

	/**
	 * Render a group of radio buttons (single-choice).
	 *
	 * @param string $settingName  Nome dell'opzione nel database
	 * @param string $activeValue  Valore attualmente selezionato
	 * @param array  $aOptions     Array associativo [ value => label ]
	 * @param string $class        Classe CSS aggiuntiva per il fieldset
	 *
	 * @return string
	 */
	public static function radioGroup( $settingName, $activeValue, $aOptions, $class = '' )
	{
		$optionsHtml = '';
		$i           = '0';

		foreach ( $aOptions as $key => $value )
		{
			$checked = ( (string) $activeValue === (string) $key ) ? 'checked' : '';

			$optionsHtml .= <<<HTML
						<li>
							<input type="radio" id="fastcache_settings_{$settingName}{$i}" name="fastcache_settings[{$settingName}]" value="{$key}" {$checked}>
							<label for="fastcache_settings_{$settingName}{$i}">{$value}</label>
						</li>
			HTML;
			$i++;
		}

		$radioGroupHtml = <<<HTML
				<fieldset id="fastcache_settings_{$settingName}" class="{$class}">
					<ul>
						{$optionsHtml}
					</ul>
				</fieldset>
		HTML;

		return $radioGroupHtml;
	}

	
	/**
	 * Render a dynamic list of TTL inputs for all public post types.
	 *
	 * @param string $settingName  Es: 'ttl-post-types'
	 * @param array  $activeValues Valori attivi dal database
	 */
	public static function fieldPostTypes( $settingName ) {
	
	    // Recupera tutti i post type pubblici
	    $post_types = get_post_types( ['public' => true], 'objects' );
	
	    // Wrapper principale (stile coerente con i tuoi altri campi)
	    $html  = '<div class="group cursor-pointer">';
	
	    $options = get_option ( FASTCACHEHOST_HOST_PLUGINNAME_SETTINGS);
	    $activeValues = isset($options[$settingName]) ? $options[$settingName] : [];
	    
	    foreach ( $post_types as $post_type ) {
	
	        // escludi attachment
	        if ( $post_type->name === 'attachment' ) {
	            continue;
	        }
	
	        $pt = $post_type->name;
	
	        // name HTML / id HTML
	        $field_name = "fastcache_settings[{$settingName}][{$pt}]";
	        $field_id   = "fastcache_settings_{$settingName}_{$pt}";
	
	        // Valore salvato o fallback 300
	        $value = isset( $activeValues[$pt] ) ? $activeValues[$pt] : '300';
	
	        // Render del singolo campo
	        $html .= '
	        <div id="' . esc_attr( $field_id ) . '_wrapper" class="pb-2">
	            <label for="' . esc_attr( $field_id ) . '"
	                   class="inline-block items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer hover:border-blue-600 hover:text-gray-600 hover:bg-gray-50">
	
	                <div class="w-full flex flex-col gap-2">
	                    <span class="text-sm font-medium text-gray-700">'
	                        . esc_html( $post_type->label ) .
	                    '</span>
	
	                    <input  type="text"
	                            id="' . esc_attr( $field_id ) . '"
	                            name="' . esc_attr( $field_name ) . '"
	                            value="' . esc_attr( $value ) . '"
	                            class="regular-text bg-transparent text-sm border-gray-300 rounded focus:ring-0 focus:outline-none" />
	                </div>
	
	                <p class="injected-description">'
	                    . esc_html__( 'TTL in seconds for this post type. You can set a specific TTL (in seconds) for each post type', 'fastcache' ) .
	                '</p>
	
	            </label>
	        </div>';
	    }
	
	    $html .= '</div>';
	
	    echo wp_kses( $html, FASTCACHEHOST_ALLOWEDHTML );
	}
	
	/**
	 * @param           $settingName
	 * @param           $aActiveValues
	 *
	 * @return string
	 */
	public static function rangeSlider( $settingName, $aActiveValues )
	{
		wp_register_script ( 'fastcache-autoconfiguration-js', FASTCACHE_URL . 'media/js/autoconfiguration.js', [
				'jquery'
		], FASTCACHE_VERSION, true );
		wp_enqueue_script ( 'fastcache-autoconfiguration-js' );
		
		wp_register_style( 'fastcache-autoconfiguration-css', FASTCACHE_URL . 'media/css/autoconfiguration.css', [], FASTCACHE_VERSION );
		wp_enqueue_style( 'fastcache-autoconfiguration-css' );
		
		$rangeSlider = '<div id="rangecontainer">';
		$rangeSlider .= '<input type="range" name="fastcache_settings[' . $settingName . ']" id="fastcache_settings_' . $settingName . '" value="' . $aActiveValues . '" class="valid form-control-success"  max="5" step="1" aria-invalid="false">';
		$rangeSlider .= '<div id="rangelabels">' .
				'<span data-container-label="0"><span class="innerlabel" data-label="0">' . __ ( 'Personalizzata', 'fastcache' ) . '</span></span>' .
				'<span data-container-label="1"><span class="innerlabel" data-label="1">' . __ ( 'Minima', 'fastcache' ) . '</span></span>' .
				'<span data-container-label="2"><span class="innerlabel" data-label="2">' . __ ( 'Standard', 'fastcache' ) . '</span></span>' .
				'<span data-container-label="3"><span class="innerlabel" data-label="3">' . __ ( 'Media', 'fastcache' ) . '</span></span>' .
				'<span data-container-label="4"><span class="innerlabel" data-label="4">' . __ ( 'Ottimale', 'fastcache' ) . '</span></span>' .
				'<span data-container-label="5"><span class="innerlabel" data-label="5">' . __ ( 'Massima', 'fastcache' ) . '</span></span>' .
				'</div>';
		$rangeSlider .= '</div>';
		$rangeSlider .= '<div id="optimizationslist" class="mt-2"></div>';
		
		return $rangeSlider;
	}
	
	/**
	 * @param           $settingName
	 * @param           $aActiveValues
	 *
	 * @return string
	 */
	public static function pageSpeed ( $settingName ) {
		
		wp_register_style( 'fastcache-pagespeed-css', FASTCACHE_URL . 'media/css/pagespeed.css', [], FASTCACHE_VERSION );
		wp_enqueue_style( 'fastcache-pagespeed-css' );
		
		wp_register_script ( 'fastcache-progressring-js', FASTCACHE_URL . 'media/js/progress-ring.js' );
		wp_enqueue_script ( 'fastcache-progressring-js' );
		
		wp_register_script ( 'fastcache-pagespeed-js', FASTCACHE_URL .'media/js/pagespeed.js' );
		wp_enqueue_script ( 'fastcache-pagespeed-js' );
		
		wp_localize_script ('fastcache-pagespeed-js', 'my_ajax_object', array(
				'ajax_url'=>admin_url('admin-ajax.php'),
				'nonce'=>wp_create_nonce('fastcache')
		));
		
		wp_localize_script('fastcache-pagespeed-js', 'ajax_var', array(
				'url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('ajax-nonce')
		));
		
		$htmlControl = <<<HTMLCONTROL
							<a class="btn btn-sm btn-primary" id="pagespeed-test"><span class="fas fa-tachometer-alt"></span> Test Google PageSpeed Insights</a>
							<div class="pagespeed-test-url badge bg-light"></div>
							<div class="timeline-container">
								<div class="timeline-wrapper" id="timeline-mobile">
								    <div class="timeline-item">
								        <div class="static-background">
								            <div class="background-masker header-top"></div>
								            <div class="background-masker header-left"></div>
								            <div class="background-masker header-right"></div>
								            <div class="background-masker subheader-left"></div>
								            <div class="background-masker subheader-bottom"></div>
								            <div class="background-masker content-top"></div>
								            <div class="background-masker content-first-end"></div>
								            <div class="background-masker content-second-end"></div>
								            <div class="background-masker content-third-end"></div>
											<div class="background-masker content-forth-line"></div>
											<div class="background-masker content-fifth-line"></div>
								        </div>
								    </div>
								</div>
								<div class="timeline-wrapper" id="timeline-desktop">
								    <div class="timeline-item">
								        <div class="static-background">
								            <div class="background-masker header-top"></div>
								            <div class="background-masker header-left"></div>
								            <div class="background-masker header-right"></div>
								            <div class="background-masker subheader-left"></div>
								            <div class="background-masker subheader-bottom"></div>
								            <div class="background-masker content-top"></div>
								            <div class="background-masker content-first-end"></div>
								            <div class="background-masker content-second-end"></div>
								            <div class="background-masker content-third-end"></div>
											<div class="background-masker content-forth-line"></div>
											<div class="background-masker content-fifth-line"></div>
								        </div>
								    </div>
								</div>
							</div>
		HTMLCONTROL;
		
		return $htmlControl;
	}
	
	/**
	 * @param           $settingName
	 * @param           $aActiveValues
	 *
	 * @return string
	 */
	public static function pluginsDiagnosis( $settingName, $aActiveValues ) {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		
		$known_cache_plugins = [
				// --- CACHE GENERALI ---
				'w3-total-cache/w3-total-cache.php'                 => 'W3 Total Cache',
				'wp-super-cache/wp-cache.php'                       => 'WP Super Cache',
				'litespeed-cache/litespeed-cache.php'               => 'LiteSpeed Cache',
				'wp-rocket/wp-rocket.php'                           => 'WP Rocket',
				'comet-cache/comet-cache.php'                       => 'Comet Cache',
				'cache-enabler/cache-enabler.php'                   => 'Cache Enabler',
				'sg-cachepress/sg-cachepress.php'                   => 'SiteGround Optimizer',
				'swift-performance-lite/performance.php'            => 'Swift Performance Lite',
				'swift-performance/performance.php'                 => 'Swift Performance Pro',
				'breeze/breeze.php'                                 => 'Breeze (Cloudways Cache)',
				'nitropack/main.php'                                => 'NitroPack',
				'hyper-cache/plugin.php'                            => 'Hyper Cache',
				'cachify/cachify.php'                               => 'Cachify',
				'wp-fastest-cache/wpFastestCache.php'               => 'WP Fastest Cache',
				'simple-cache/simple-cache.php'                     => 'Simple Cache',
				'advanced-cache/advanced-cache.php'                 => 'Advanced Cache',
				'flyingpress/flyingpress.php'                       => 'FlyingPress',
				'flying-pages/flying-pages.php'                     => 'Flying Pages',
				'flying-images/flying-images.php'                   => 'Flying Images',
				'surge/surge.php'                                   => 'Surge Performance',
				'tenweb-speed-optimizer/tenweb_speed_optimizer.php' => '10Web Booster',
				'powered-cache/powered-cache.php'                   => 'Powered Cache',
				
				// --- OTTIMIZZAZIONI / MINIFY / PERFORMANCE ---
				'autoptimize/autoptimize.php'                       => 'Autoptimize',
				'hummingbird-performance/wp-hummingbird.php'        => 'Hummingbird',
				'asset-cleanup/asset-cleanup.php'                   => 'Asset CleanUp',
				'perfmatters/perfmatters.php'                       => 'Perfmatters',
				'pagespeed-ninja/pagespeedninja.php'                => 'PageSpeed Ninja',
				'wp-performance-score-booster/wp-performance-score-booster.php' => 'WP Performance Score Booster',
				'jch-optimize/jch-optimize.php'                     => 'JCH Optimize',
				'wp-optimize/wp-optimize.php'                       => 'WP-Optimize',
				'speed-booster-pack/speed-booster-pack.php'         => 'Speed Booster Pack',
				'fast-velocity-minify/fvm.php'                      => 'Fast Velocity Minify',
				'merge-minify-refresh/merge-minify-refresh.php'     => 'Merge + Minify + Refresh',
				'clearfy/clearfy.php'                               => 'Clearfy',
				'optimization-io/optimization-io.php'               => 'Optimization.io',
				'rapidload/rapidload.php'                           => 'RapidLoad by Ezoic',
				
				// --- OBJECT CACHE / DATABASE / TRANSIENTS ---
				'redis-cache/redis-cache.php'                       => 'Redis Object Cache',
				'memcached-redux/memcached-redux.php'               => 'Memcached Redux',
				'wpsc-object-cache/wpsc-object-cache.php'           => 'WPSC Object Cache',
				'query-monitor/query-monitor.php'                   => 'Query Monitor (profiling)',
				'wp-cloudflare-page-cache/wp-cloudflare-super-page-cache.php' => 'WP Cloudflare Super Page Cache',
				
				// --- CDN / IMAGE OPTIMIZATION ---
				'jetpack/jetpack.php'                               => 'Jetpack Boost / CDN',
				'photon/photon.php'                                 => 'Photon (Jetpack CDN)',
				'smushit/wp-smush.php'                              => 'Smush (image optimization)',
				'imagify/imagify.php'                               => 'Imagify',
				'shortpixel-image-optimiser/wp-shortpixel.php'      => 'ShortPixel',
				'ewww-image-optimizer/ewww-image-optimizer.php'     => 'EWWW Image Optimizer',
				'optimole-wp/optimole-wp.php'                       => 'Optimole',
				'webp-express/webp-express.php'                     => 'WebP Express',
				
				// --- HOSTING / PLATFORM CACHES ---
				'kinsta-mu-plugins/kinsta-mu-plugin.php'            => 'Kinsta MU Plugin',
				'wpengine-common/plugin.php'                        => 'WP Engine System Plugin',
				'bluehost-wordpress-plugin/bluehost-wordpress-plugin.php' => 'Bluehost Performance Tools',
				'rocketcdn/rocketcdn.php'                           => 'RocketCDN',
				'pantheon-advanced-page-cache/pantheon-advanced-page-cache.php' => 'Pantheon Advanced Page Cache',
				
				// --- ALTRO / MISC ---
				'wp-compress-image-optimizer/wp-compress.php'       => 'WP Compress',
				'resmushit-image-optimizer/resmushit.php'           => 'reSmush.it Image Optimizer',
				'wphb/wp-hummingbird.php'                           => 'Hummingbird (alt slug)',
				'lazy-load/lazy-load.php'                           => 'Lazy Load by WP Rocket',
				'aio-optimizations/aio-optimizations.php'           => 'AIO Optimizations',
				'wpspeed/wpspeed.php'                               => 'WPSpeed',
				'fastest-cache/fastest-cache.php'                   => 'Fastest Cache (clone)',
				'super-page-cache-for-cloudflare/wp-cloudflare-super-page-cache.php' => 'Super Page Cache for Cloudflare',
		];
		
		$active_cache_plugins = [];
		
		foreach ($known_cache_plugins as $plugin_path => $plugin_name) {
			if (is_plugin_active($plugin_path)) {
				$active_cache_plugins[] = $plugin_name;
			}
		}
		
		// Messaggio dinamico da mostrare nel tab Diagnostic
		$diagnostic_message = '';
		
		if (!empty($active_cache_plugins)) {
			$diagnostic_message  = '<div class="fastcache-diagnostic-message" data-status="warning" style="padding:10px 15px;background:#fff3cd;border:1px solid #ffeeba;border-radius:6px;">';
			$diagnostic_message .= '<strong style="color:#856404;"><span>⚠️</span>'
					. __('Other cache plugins detected:', 'fastcache')
					. '</strong><br/><br/>';
					$diagnostic_message .= '<ul style="margin-left:20px;list-style:disc;">';
					
					foreach ($active_cache_plugins as $p) {
						$diagnostic_message .= '<li><b>' . esc_html($p) . '</b></li>';
					}
					
					$diagnostic_message .= '</ul>';
					$diagnostic_message .= '<p style="margin-top:10px;">'
							. __('These plugins may conflict with FastCache. It is recommended to deactivate them to ensure proper functionality.', 'fastcache')
							. '</p>';
							$diagnostic_message .= '</div>';
		} else {
			$diagnostic_message  = '<div class="fastcache-diagnostic-message" data-status="ok" style="padding:10px 15px;background:#d4edda;border:1px solid #c3e6cb;border-radius:6px;">';
			$diagnostic_message .= '<strong style="color:#155724;"><span style="filter: hue-rotate(236deg) brightness(1.5)">✔️</span> '
					. __('No other caching plugins detected.', 'fastcache')
					. '</strong>';
					$diagnostic_message .= '<p style="margin-top:5px;">'
							. __('FastCache can safely manage your site cache.', 'fastcache')
							. '</p>';
							$diagnostic_message .= '</div>';
		}
		
		echo $diagnostic_message;
	}
}