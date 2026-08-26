var g_currentAJAXCallTimeout = null;
	jQuery(document).ready(function() {
		if(jQuery('#attivazionefastcache').length>0) {
			jQuery('#attivazionefastcache').click(function (e) {
				jQuery('#fastcache_settings_submit').trigger('click');
			});
		}
		if(jQuery('#attivazionefastcachelogs').length>0) {
			jQuery('#attivazionefastcachelogs').click(function (e) {
				jQuery('#fastcache_settings_submit').trigger('click');
			});
		}


		if(jQuery('#button-test').length>0) {
			jQuery('#button-test').click(function (e) {
				e.preventDefault();
				jQuery.post(ajax_var.url, {
					nonce: ajax_var.nonce,
					action: 'testCache'
				}, function (response) {
					jQuery('#outputreporthtml').html(response);
					fastcache_host_checkDettaglio();
				});
			});
		}
	});
function fastcache_host_checkDettaglio() {
	if(jQuery('#opendettaglio').length>0) {
		jQuery('#opendettaglio').click(function (e) {
			e.preventDefault();
			jQuery(this).addClass('clicked').addClass('hidden');
		});
	}
}