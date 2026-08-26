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

use FastCache\Platform\Plugin;
use FastCache\Core\Admin\Icons;

$oParams            = Plugin::getPluginParams();
$hiddenContainsGF   = $oParams->get( 'hidden_containsgf', '' );

?>
<form action="options.php" method="post" id="fastcache_settings-form">
    <div class="fastcache-enable-screen clearfix-both mt-n3">
        <div class="col-8 m-auto">
            {{\FastCache\Admin\Settings\TabContent::start()}}

            {{settings_fields('fastcacheOptionsPage')}}
            {{do_settings_sections('fastcacheOptionsPage')}}

            {{\FastCache\Admin\Settings\TabContent::end()}}
            
			<div class="savebtn icon">{{submit_button(__('Attiva plugin','fastcache'), 'primary', 'fastcache_settings_submit')}}</div>
            <input type="hidden" id="fastcache_settings_hidden_containsgf"
                   name="fastcache_settings[hidden_containsgf]"
                   value="{{$hiddenContainsGF}}">
            <input type="hidden" id="fastcache_settings_hidden_api_secret"
                   name="fastcache_settings[hidden_api_secret]"
                   value="11e603aa">
        </div>
    </div>
</form>