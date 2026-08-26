/**
 * Auto configuration class
 * 
 * @package JSPEED::plugins::system
 * @author FastCache
 * @Copyright (c) 2025-2026
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
//'use strict';
(function ($) {
	var AutoConfiguration = function () {
		/**
		 * Register user events for interface controls
		 * 
		 * @access private
		 * @param Boolean initialize
		 * @return Void
		 */
		var addListeners = function (initialize) {
			var optimizationLevelSettings = {
				1: {
					'fastcache_settings_html_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_html_minify_checkbox' },
					'fastcache_settings_html_minify_level': '0',
					'fastcache_settings_combine_files_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_combine_files_enable_checkbox' },
					'fastcache_settings_css_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_checkbox' },
					'fastcache_settings_javascript_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_javascript_checkbox' },
					'fastcache_settings_font_display_swap_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_font_display_swap_checkbox' },
					'fastcache_settings_defer_combined_styles_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_defer_combined_styles_checkbox' },
					'fastcache_settings_css_minify_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_css_minify_checkbox' },
					'fastcache_settings_js_minify_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_js_minify_checkbox' },
					'fastcache_settings_bottom_js_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_bottom_js_checkbox' },
					'fastcache_settings_lazyload_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_lazyload_enable_checkbox' },
					'fastcache_settings_lightimgs_status_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_lightimgs_status_checkbox' },
					'fastcache_settings_img_attributes_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_img_attributes_enable_checkbox' },
					'fastcache_settings_convert_all_images_to_webp_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_convert_all_images_to_webp_checkbox' },
					'fastcache_settings_convert_all_images_to_avif_checkbox': { 'action': 'uncheckuncheck', 'target': 'fastcache_settings_convert_all_images_to_avif_checkbox' },
					'fastcache_settings_cache_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_cache_enable_checkbox' }
				},
				2: {
					'fastcache_settings_html_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_html_minify_checkbox' },
					'fastcache_settings_html_minify_level': '1',
					'fastcache_settings_combine_files_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_combine_files_enable_checkbox' },
					'fastcache_settings_css_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_checkbox' },
					'fastcache_settings_javascript_checkbox': { 'action': 'check', 'target': 'fastcache_settings_javascript_checkbox' },
					'fastcache_settings_font_display_swap_checkbox': { 'action': 'check', 'target': 'fastcache_settings_font_display_swap_checkbox' },
					'fastcache_settings_defer_combined_styles_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_defer_combined_styles_checkbox' },
					'fastcache_settings_css_minify_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_css_minify_checkbox' },
					'fastcache_settings_js_minify_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_js_minify_checkbox' },
					'fastcache_settings_bottom_js_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_bottom_js_checkbox' },
					'fastcache_settings_lazyload_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_lazyload_enable_checkbox' },
					'fastcache_settings_lightimgs_status_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_lightimgs_status_checkbox' },
					'fastcache_settings_img_attributes_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_img_attributes_enable_checkbox' },
					'fastcache_settings_convert_all_images_to_webp_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_convert_all_images_to_webp_checkbox' },
					'fastcache_settings_convert_all_images_to_avif_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_convert_all_images_to_avif_checkbox' },
					'fastcache_settings_cache_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_cache_enable_checkbox' }
				},
				3: {
					'fastcache_settings_html_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_html_minify_checkbox' },
					'fastcache_settings_html_minify_level': '1',
					'fastcache_settings_combine_files_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_combine_files_enable_checkbox' },
					'fastcache_settings_css_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_checkbox' },
					'fastcache_settings_javascript_checkbox': { 'action': 'check', 'target': 'fastcache_settings_javascript_checkbox' },
					'fastcache_settings_font_display_swap_checkbox': { 'action': 'check', 'target': 'fastcache_settings_font_display_swap_checkbox' },
					'fastcache_settings_defer_combined_styles_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_defer_combined_styles_checkbox' },
					'fastcache_settings_css_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_minify_checkbox' },
					'fastcache_settings_js_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_js_minify_checkbox' },
					'fastcache_settings_bottom_js_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_bottom_js_checkbox' },
					'fastcache_settings_lazyload_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_lazyload_enable_checkbox' },
					'fastcache_settings_lightimgs_status_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_lightimgs_status_checkbox' },
					'fastcache_settings_img_attributes_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_img_attributes_enable_checkbox' },
					'fastcache_settings_convert_all_images_to_webp_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_convert_all_images_to_webp_checkbox' },
					'fastcache_settings_convert_all_images_to_avif_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_convert_all_images_to_avif_checkbox' },
					'fastcache_settings_cache_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_cache_enable_checkbox' }
				},
				4: {
					'fastcache_settings_html_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_html_minify_checkbox' },
					'fastcache_settings_html_minify_level': '1',
					'fastcache_settings_combine_files_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_combine_files_enable_checkbox' },
					'fastcache_settings_css_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_checkbox' },
					'fastcache_settings_javascript_checkbox': { 'action': 'check', 'target': 'fastcache_settings_javascript_checkbox' },
					'fastcache_settings_font_display_swap_checkbox': { 'action': 'check', 'target': 'fastcache_settings_font_display_swap_checkbox' },
					'fastcache_settings_defer_combined_styles_checkbox': { 'action': 'check', 'target': 'fastcache_settings_defer_combined_styles_checkbox' },
					'fastcache_settings_css_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_minify_checkbox' },
					'fastcache_settings_js_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_js_minify_checkbox' },
					'fastcache_settings_bottom_js_checkbox': { 'action': 'check', 'target': 'fastcache_settings_bottom_js_checkbox' },
					'fastcache_settings_lazyload_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_lazyload_enable_checkbox' },
					'fastcache_settings_lightimgs_status_checkbox': { 'action': 'check', 'target': 'fastcache_settings_lightimgs_status_checkbox' },
					'fastcache_settings_img_attributes_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_img_attributes_enable_checkbox' },
					'fastcache_settings_convert_all_images_to_webp_checkbox': { 'action': 'check', 'target': 'fastcache_settings_convert_all_images_to_webp_checkbox' },
					'fastcache_settings_convert_all_images_to_avif_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_convert_all_images_to_avif_checkbox' },
					'fastcache_settings_cache_enable_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_cache_enable_checkbox' }
				},
				5: {
					'fastcache_settings_html_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_html_minify_checkbox' },
					'fastcache_settings_html_minify_level': '2',
					'fastcache_settings_combine_files_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_combine_files_enable_checkbox' },
					'fastcache_settings_css_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_checkbox' },
					'fastcache_settings_javascript_checkbox': { 'action': 'check', 'target': 'fastcache_settings_javascript_checkbox' },
					'fastcache_settings_font_display_swap_checkbox': { 'action': 'check', 'target': 'fastcache_settings_font_display_swap_checkbox' },
					'fastcache_settings_defer_combined_styles_checkbox': { 'action': 'check', 'target': 'fastcache_settings_defer_combined_styles_checkbox' },
					'fastcache_settings_css_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_css_minify_checkbox' },
					'fastcache_settings_js_minify_checkbox': { 'action': 'check', 'target': 'fastcache_settings_js_minify_checkbox' },
					'fastcache_settings_bottom_js_checkbox': { 'action': 'check', 'target': 'fastcache_settings_bottom_js_checkbox' },
					'fastcache_settings_lazyload_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_lazyload_enable_checkbox' },
					'fastcache_settings_lightimgs_status_checkbox': { 'action': 'check', 'target': 'fastcache_settings_lightimgs_status_checkbox' },
					'fastcache_settings_img_attributes_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_img_attributes_enable_checkbox' },
					'fastcache_settings_convert_all_images_to_webp_checkbox': { 'action': 'check', 'target': 'fastcache_settings_convert_all_images_to_webp_checkbox' },
					'fastcache_settings_convert_all_images_to_avif_checkbox': { 'action': 'uncheck', 'target': 'fastcache_settings_convert_all_images_to_avif_checkbox' },
					'fastcache_settings_cache_enable_checkbox': { 'action': 'check', 'target': 'fastcache_settings_cache_enable_checkbox' }
				}
			};
			var optimizationIconsMapping = {
				'fastcache_settings_html_minify_checkbox': 'fas fa-compress',
				'fastcache_settings_html_minify_level': 'fas fa-compress',
				'fastcache_settings_combine_files_enable_checkbox': 'fas fa-object-group',
				'fastcache_settings_css_checkbox': 'fas fa-object-group',
				'fastcache_settings_javascript_checkbox': 'fas fa-object-group',
				'fastcache_settings_font_display_swap_checkbox': 'fas fa-tachometer-alt',
				'fastcache_settings_defer_combined_styles_checkbox': 'fas fa-tachometer-alt',
				'fastcache_settings_css_minify_checkbox': 'fas fa-compress',
				'fastcache_settings_js_minify_checkbox': 'fas fa-compress',
				'fastcache_settings_bottom_js_checkbox': 'fas fa-download',
				'fastcache_settings_lazyload_enable_checkbox': 'fas fa-images',
				'fastcache_settings_lightimgs_status_checkbox': 'fas fa-images',
				'fastcache_settings_img_attributes_enable_checkbox': 'fas fa-ruler-combined',
				'fastcache_settings_convert_all_images_to_webp_checkbox': 'fas fa-images',
				'fastcache_settings_convert_all_images_to_avif_checkbox': 'fas fa-images',
				'fastcache_settings_cache_enable_checkbox': 'fas fa-database',
			};
			var optimizationLevel = parseInt($('#fastcache_settings_rangeSlider').val());
			$('#fastcache_settings_rangeSlider').on('change', function (jqEvent, doNotRefreshSettings) {
				optimizationLevel = $(this).val();
				$('span.innerlabel').removeClass('activeselected');
				$('span.innerlabel[data-label=' + optimizationLevel + ']').addClass('activeselected');
				$('#optimizationslist').empty();
				$.each(optimizationLevelSettings[optimizationLevel], function (control, controlValue) {
					if (typeof (controlValue) !== "object") {
						var textOptimizationContext = $('#' + control).parents('tr');
						if (!doNotRefreshSettings) {
							$('#' + control).val(controlValue);
						}
					} else {
						var textOptimizationContext = $('label[for=' + controlValue.target + ']').parents('tr');
						if (!doNotRefreshSettings) {
							if (controlValue.action == 'check') {
								$('#' + controlValue.target).prop('checked', true);
							} else {
								$('#' + controlValue.target).prop('checked', false);
							}
						}
					}

					var targetControl = $('*[id="' + control + '"]');
					var labelClass = 'bg-primary';
					var labelIcon = '<span class="fas fa-info-circle"></span>';
					if (targetControl.get(0).nodeName.toLowerCase() == 'select') {
						var optionValue = targetControl.val();
						var controlCalculatedValue = $('option[value=' + optionValue + ']', targetControl).text();
					} else {
						var controlCalculatedValue = targetControl.prop('checked') ? 'Enabled' : 'Disabled';
						var controlCalculatedValueInteger = targetControl.prop('checked') ? 1 : 0;
						labelClass = targetControl.prop('checked') ? 'bg-success' : 'bg-secondary';
						labelIcon = targetControl.prop('checked') ? '<span class="fas fa-check"></span>' : '<span class="fas fa-times-circle"></span>';
						var previousiHiddenInput = targetControl.prev('input[type=hidden]');
						previousiHiddenInput.val(controlCalculatedValueInteger);
					}
					var textOptimizationLabel = $('th div.title', textOptimizationContext).clone()
						.children()
						.remove()
						.end()
						.text();
					$('#optimizationslist').append('<div class="row pt-2 pb-2 jspeed-setting-row border-bottom">' +
						'<div class="col col-lg-10 col-md-10 col-sm-8"><span class="' + optimizationIconsMapping[control] + '" aria-hidden="true"></span>' + textOptimizationLabel + '</div>' +
						'<div class="col col-lg-2 col-md-2 col-sm-4"><span class="badge ' + labelClass + '">' + labelIcon + controlCalculatedValue + '</span></div>' +
						'</div>');
				});

				if (optimizationLevel == 0) {
					$('#optimizationslist').removeClass('populated');
				} else {
					$('#optimizationslist').addClass('populated');
				}
			});
			if (optimizationLevel > 0) {
				$('#fastcache_settings_rangeSlider').trigger('change', [true]);
				$('#optimizationslist').addClass('populated');
			} else {
			}

			// Auto configuration slider management
			const slider = document.getElementById("fastcache_settings_rangeSlider");
			function updateProgress() {
				const val = slider.value * 20;
				document.documentElement.style.setProperty("--range-progress", val + "%");
			}
			updateProgress();
			slider.addEventListener("input", updateProgress);


			// ---- Allineamento label attiva sotto al thumb ----
			function updateActiveLabelPosition() {
				const sliderRect = slider.getBoundingClientRect();
				const min = parseInt(slider.min || 0);
				const max = parseInt(slider.max || 5);
				const val = parseInt(slider.value);
				const percent = (val - min) / (max - min);

				// posizione del thumb
				const thumbX = sliderRect.left + sliderRect.width * percent;

				// prendi la label attiva
				const activeLabel = document.querySelector('#rangelabels .innerlabel.active');
				if (!activeLabel) return;

				// imposta la posizione
				activeLabel.style.left = thumbX + "px";
			}

			slider.addEventListener("input", updateActiveLabelPosition);
			updateActiveLabelPosition();

		};

		/**
		 * Function dummy constructor
		 * 
		 * @access private
		 * @param String
		 *            contextSelector
		 * @method <<IIFE>>
		 * @return Void
		 */
		(function __construct() {
			// Add UI events
			addListeners.call(this, true);

			$('#fastcache_settings_rangeSlider').removeClass('form-range');

		}).call(this);
	}

	//On DOM Ready
	$(function () {
		window.JSpeedAutoConfiguration = new AutoConfiguration();
	});
})(jQuery);