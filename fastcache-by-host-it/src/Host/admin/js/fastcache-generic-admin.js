jQuery(document).ready(function() {
    if (jQuery('#wp-admin-bar-fastcache-main-default .fastcache-confirm-purge .ab-item').length > 0) {
        eventiMenuBar();
    }
});

function eventiMenuBar() {
    jQuery('#wp-admin-bar-fastcache-main-default .fastcache-confirm-purge .ab-item').each(function() {
        jQuery(this).on('click', (function(e) {
            e.preventDefault();
            
            var param = '';
            var loc = '';
            var confirmMessage = fastcache_confirm.message_base + '\n\n' + fastcache_confirm.message_question + ' ';
            
            if(jQuery(this).parent().hasClass('this')) {
                param = 'this';
                loc = document.location.href;
                confirmMessage += fastcache_confirm.message_this;
            }
            if(jQuery(this).parent().hasClass('all')) {
                param = 'all';
                confirmMessage += fastcache_confirm.message_all;
            }
            if(jQuery(this).parent().hasClass('homepage')) {
                param = 'homepage';
                confirmMessage += fastcache_confirm.message_homepage;
            }
            
            // Mostra il popup di conferma
            if (!confirm(confirmMessage)) {
                // Se l'utente clicca "Annulla", esce dalla funzione
                return false;
            }
            
            // Se l'utente clicca "OK", procede con la chiamata AJAX
            jQuery.post(ajax_var.url, {
                action: 'hstPurgeCache',
                param: param,
                nonce: ajax_var.nonce,
                loc: loc
            }, function(response) {
                if(jQuery("#fastcacheMessageBox").length < 1) {
                    jQuery('body').append('<div id="fastcacheMessageBox" class="">');
                }
                setTimeout(function() {
                    jQuery("#fastcacheMessageBox").html(response);
                }, 50);
                setTimeout(function() {
                    jQuery("#fastcacheMessageBox").html('');
                }, 3000);
            });
        }));
    });
}