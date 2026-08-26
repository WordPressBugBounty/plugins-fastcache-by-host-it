/**
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * JavaScript behavior to allow selected tab to be remained after save or page reload
 * keeping state in localstorage
 */

jQuery(function($) {
    var loadTabs = function() {
        var storageKey = 'active-tabs-fc';
        var $tabs = $('a[data-tab-toggle="tab"],a[data-bs-toggle="tab"]');
        var $panes = $('.tab-pane');

        function activateTab(href) {
            // Deactivate all tabs
            $tabs.removeClass('active');
            
            // Hide all panes
            $panes.removeClass('active').addClass('hidden');

            // Activate target tab
            var $targetTab = $('a[data-tab-toggle="tab"][href="' + href + '"],a[data-bs-toggle="tab"][href="' + href + '"]');
            if ($targetTab.length) {
                $targetTab.addClass('active');
            }

            // Show target pane
            try {
                // Ensure href is a valid selector (e.g. #id)
                if (href && href.startsWith('#')) {
                    var $targetPanel = $(href);
                    if ($targetPanel.length) {
                        $targetPanel.removeClass('hidden').addClass('active');
                    }
                }
            } catch(e) {
                console.log('FastCache Tab Error: ' + e);
            }

            // Save state
            if (href) {
                localStorage.setItem(storageKey, href);
            }
        }

        $tabs.on('click', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            activateTab(href);
        });

        // Initialize
        var saved = localStorage.getItem(storageKey);
        
        // Validation for saved state - ensure it starts with #
        if (saved && saved.startsWith('#') && $('a[href="' + saved + '"]').length) {
            activateTab(saved);
        } else {
            // Default to first tab if nothing saved or invalid
            var firstHref = $tabs.first().attr('href');
            if (firstHref) {
                activateTab(firstHref);
            }
        }
    };
    loadTabs();
});
