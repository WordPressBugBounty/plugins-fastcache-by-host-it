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

var adminUtilities = (function ($) {
	// Manage the linked controls
	$('#fastcache_settings_htaccess_cache_enable_checkbox').on('change', function () {
		var htaccessCacheValue = parseInt($(this).prev('input[type=hidden]').val());
		var phpPageCacheCtrlValue = $('#fastcache_settings_cache_enable_value');
		var phpPageCacheValue = phpPageCacheCtrlValue.val();
		var phpPageCacheCtrl = $('#fastcache_settings_cache_enable_checkbox');
		if (htaccessCacheValue == 1 && phpPageCacheValue == 0) {
			phpPageCacheCtrl.trigger('click');
		}
	});

	let fastcache_ajax_url_optimizeimages = ajaxurl + '?action=optimizeimages';
	let fastcache_ajax_url_multiselect = ajaxurl + '?action=multiselect';


	let configure_url = "options-general.php?page=fastcache&view=configure";

	var submitForm = function () {
		document.getElementById('fastcache_settings-form').submit();
	}

	/**
	 * Toggle fields visibility based on SimpleHtmlDom switcher
	 */
	var toggleSimpleHtmlDomFields = function () {
		var useSimpleHtmlDom = $('input[name="fastcache_settings[use_simplehtmldom]"]').val();

		if (useSimpleHtmlDom === "1") {
			// Show Decode text entities
			$('#fastcache_settings_img_processing_simplehtmldom_entity_decode_wrapper').closest('tr').show();

			// Hide standard decode/purify fields
			$('#fastcache_settings_img_processing_entity_decode_wrapper').closest('tr').hide();
			$('#fastcache_settings_img_processing_utf8_entity_decode_wrapper').closest('tr').hide();
			$('#fastcache_settings_purify_string_wrapper').closest('tr').hide();
			$('#fastcache_settings_purify_string_replacement_wrapper').closest('tr').hide();
		} else {
			// Hide Decode text entities
			$('#fastcache_settings_img_processing_simplehtmldom_entity_decode_wrapper').closest('tr').hide();

			// Show standard decode/purify fields
			$('#fastcache_settings_img_processing_entity_decode_wrapper').closest('tr').show();
			$('#fastcache_settings_img_processing_utf8_entity_decode_wrapper').closest('tr').show();
			$('#fastcache_settings_purify_string_wrapper').closest('tr').show();
			$('#fastcache_settings_purify_string_replacement_wrapper').closest('tr').show();
		}
	};

	/**
	 * Toggle TTL fields (default TTL + all post-type TTLs)
	 */
	var toggleTTLFields = function () {

		var enableTTL = $('input[name="fastcache_settings[enable-ttl]"]').val();

		// wrapper "default-notfound-ttl"
		var defaultNotFoundTTLRow = $('#fastcache_settings_fastcache_cdn_default_ttl_notfound_set_checkbox').closest('tr');

		// wrapper "default-ttl"
		var defaultTTLRow = $('#fastcache_settings_default-ttl_wrapper').closest('tr');

		// tutti gli ID che iniziano con fastcache_settings_ttl-post-types
		var postTypeTTLRows = $('[id^="fastcache_settings_ttl-post-types"]').closest('tr');

		if (enableTTL === "1") {
			defaultTTLRow.show();
			postTypeTTLRows.show();
			defaultNotFoundTTLRow.show();
		} else {
			defaultTTLRow.hide();
			postTypeTTLRows.hide();
			defaultNotFoundTTLRow.hide();
		}
	};

	// Init on page load
	$(document).ready(function () {
		toggleSimpleHtmlDomFields();
		toggleTTLFields();

		// Listen for changes
		$(document).on('change', '#fastcache_settings_use_simplehtmldom_checkbox', function () {
			toggleSimpleHtmlDomFields();
		});

		// Listen for TTL toggle
		$(document).on('change', '#fastcache_settings_enable-ttl_checkbox', function () {
			$('#fastcache_settings_enable-ttl_checkbox').val(this.checked ? "1" : "0");
			toggleTTLFields();
		});

		// Object Cache backend selection auto-TTL
		$(document).on('change', 'select[name="fastcache_settings[object_cache_backend]"]', function () {
			var backend = $(this).val();
			var $ttlInput = $('input[name="fastcache_settings[object_cache_default_ttl]"]');
			var currentTtl = parseInt($ttlInput.val()) || 0;

			if (backend === 'file') {
				if (currentTtl === 0) {
					$ttlInput.val('3600');
				}
			} else if (backend === 'redis' || backend === 'memcached') {
				if (currentTtl > 0) {
					$ttlInput.val('0');
				}
			}
		});
	});

	document.addEventListener('DOMContentLoaded', function () {
		// Trova tutte le descrizioni che contengono un <p>
		const descriptions = document.querySelectorAll('.description p');

		descriptions.forEach(p => {
			// Trova il contenitore più vicino che include il blocco (tipicamente <tr> o <th>)
			const container = p.closest('tr');

			if (container) {
				// Trova la label associata dentro quel blocco
				const label = container.querySelector('label');

				if (label) {
					// Clona il paragrafo per non rimuoverlo dal posto originale
					const clone = p.cloneNode(true);

					// Aggiungi una classe opzionale per styling, se vuoi distinguere la copia
					clone.classList.add('injected-description');

					// Aggiunge uno spazio e poi inserisce la descrizione dentro la label, prima della chiusura
					if (label.querySelector('.notice-fastcache') === null) {
						label.appendChild(clone);
					} else {
						label.querySelector('.notice-fastcache').before(clone);
					}

					// Remove the original
					p.closest('.description').remove();
				} else {
					// Always remove the original
					p.closest('.description').remove();
				}
			}
		});

		const labels = document.querySelectorAll('.badge.jspeed-checker');

		labels.forEach(l => {
			// Trova il contenitore più vicino che include il blocco (tipicamente <tr> o <th>)
			const container = l.closest('tr');
			const labelContainer = container.querySelector('div.group > label');

			if (container) {
				// Clona il paragrafo per non rimuoverlo dal posto originale
				const clone = l.cloneNode(true);

				// Aggiunge uno spazio e poi inserisce la descrizione dentro la label, prima della chiusura
				labelContainer.appendChild(clone);

				// Remove the original
				l.remove();
			}
		});

		// Aggiungi icona di stato dinamica al tab Diagnostic
		const diagnosticTab = document.querySelector('a[href="#diagnostic-tab"] div.tab-item');
		const diagnosticPanel = document.querySelector('#diagnostic-tab .fastcache-diagnostic-message');

		if (!diagnosticTab || !diagnosticPanel) return;

		const status = diagnosticPanel.getAttribute('data-status');

		diagnosticTab.querySelectorAll('.diag-status').forEach(el => el.remove());

		const icon = document.createElement('span');
		icon.classList.add('diag-status');
		icon.style.marginLeft = '6px';
		icon.style.fontSize = '15px';
		icon.style.float = 'right';

		if (status === 'warning') {
			icon.textContent = '⚠️';
			icon.title = fastcache_confirm.diagnostic_warning;
		} else if (status === 'ok') {
			icon.textContent = '✔️';
			icon.title = fastcache_confirm.diagnostic_success;
			icon.style.filter = 'hue-rotate(236deg) brightness(1.5)';
		}

		diagnosticTab.appendChild(icon);
	});

	return {
		//properties
		fastcache_ajax_url_optimizeimages: fastcache_ajax_url_optimizeimages,
		fastcache_ajax_url_multiselect: fastcache_ajax_url_multiselect,
		//methods
		submitForm: submitForm
	}

})(jQuery);