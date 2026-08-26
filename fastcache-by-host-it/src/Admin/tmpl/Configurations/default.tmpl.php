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
	<div>
		<div class="savebtn icon">{{submit_button(__('Save Settings','fastcache'), 'primary', 'fastcache_settings_submit')}}</div>
		<div class="icons-container">
			<div class="left-icon-container">
				{{Icons::printIconsHTML(Icons::compileUtilityIcons(Icons::getUtilityArray(['browsercaching', 'restorehtaccess', 'orderplugins', 'keycache'])))}}
				{{Icons::printIconsHTML(Icons::compileUtilityIcons(Icons::getUtilityArray(['cleancache'])))}}
			</div>
			<div class="right-icon-container">
				<span class="badge bg-transparent">
				    <span class="fas fa-chart-area"></span>
				    {{ sprintf(
				        __('Cache files: <span class="chart-details">%s</span>', 'fastcache'),
				        $this->no_files
				    ) }}
				</span>
				
				<span class="badge bg-transparent">
				    <span class="fas fa-chart-bar"></span>
				    {{ sprintf(
				        __('Cache size: <span class="chart-details">%s</span>', 'fastcache'),
				        $this->size
				    ) }}
				</span>
			</div>
		</div>
	</div>
	
    <div class="grid grid-cols-[16_84] gap-6 py-2" style="gap:1.5rem!important; ">
        <div class="rounded-lg shadow-sm border ">
            <ul class="grid grid-cols-[1fr] overflow-x-auto md:flex-col gap-1 bg-slate-50 rounded-lg">
				<li class="w-full px-4 pt-4">
                    <a class=" page-cache-tab block px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#page-cache-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Page Cache', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
				<li class="w-full px-4">
                    <a class=" fastcache-cdn-tab block px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#fastcache-cdn-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('FastCache CDN', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                
				<li class="w-full px-4">
                    <a class=" fastcache-objectcache-tab block px-4 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#fastcache-objectcache-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Object cache', 'fastcache')}} <span class="fc-beta-pill">BETA</span></div>
                        </div>
                    </a>
                </li>
                
                <li class="w-full px-4">
                    <a class=" autoconfiguration-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#autoconfiguration-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Auto configuration', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" general-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#general-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Optimizations', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" assets-inclusions block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#assets-inclusions" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Assets Inclusions', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" assets-exclusions block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#assets-exclusions" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Assets Exclusions', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
				<li class="w-full px-4">
                    <a class=" lazy-load-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#lazy-load-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Lazy Load', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" optimize-image-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#optimize-image-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Optimize Images', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" remove-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#remove-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Assets Management', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" http2-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#http2-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Http/2', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" media-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#media-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Combine Images', 'fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" miscellaneous-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#miscellaneous-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Advanced','fastcache')}}</div>
                        </div>
                    </a>
                </li>
				<li class="w-full px-4">
                    <a class=" speedtest-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#speedtest-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Speed Test','fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" diagnostic-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#diagnostic-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Diagnostic','fastcache')}}</div>
                        </div>
                    </a>
                </li>
                <li class="w-full px-4">
                    <a class=" transfer-config-tab block px-3 py-2 rounded-md text-sm font-medium text-slate-600 hover:bg-white hover:text-blue-400! transition-colors [&.active]:bg-blue-400/30 [&.active]:text-blue-400! [&.active]:shadow-sm text-decoration-none" href="#transfer-config-tab" data-tab-toggle="tab">
                        <div>
                            <div class="tab-item">{{__('Transfer Configuration','fastcache')}}</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-1 w-full min-w-0">
            {{\FastCache\Admin\Settings\TabContent::start()}}

            {{settings_fields('fastcacheOptionsPage')}}
            {{do_settings_sections('fastcacheOptionsPage')}}

            {{\FastCache\Admin\Settings\TabContent::end()}}
            
			<div class="savebtn icon">{{submit_button(__('Save Settings','fastcache'), 'primary', 'fastcache_settings_submit')}}</div>
            <input type="hidden" id="fastcache_settings_hidden_containsgf"
                   name="fastcache_settings[hidden_containsgf]"
                   value="{{$hiddenContainsGF}}">
            <input type="hidden" id="fastcache_settings_hidden_api_secret"
                   name="fastcache_settings[hidden_api_secret]"
                   value="11e603aa">
        </div>
    </div>
</form>
