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
use FastCache\Platform\Plugin;

class TabContent
{

	public static function start()
	{
		return <<<HTML
<div class="tab-content">
	<div style="display:none">
		<fieldset>
			<div>
HTML;

	}

	public static function addTab( $id, $active = false )
	{
		$active = $active ? ' active' : '';
		$params = Plugin::getPluginParams ();
		
		if($id == 'fastcache-cdn-tab' && $params->get('fastcache-enable', 0) == 0) {
			$active .= ' disabledhostcdn';
		}
		// Use hidden by default and block when active to mimic Bootstrap tab behavior with Tailwind
		return <<<HTML
			</div>
		</fieldset>
	</div>		
	<div class="tab-pane hidden [&.active]:block{$active}" id="{$id}">
		<fieldset style="display: none;">
			<div>
HTML;

	}

	public static function addSection( $header = '', $description = '', $class = '' )
	{
		if ( ! empty( $header ) )
		{
			$header = <<<HMTL
			<h3 class="fastcache-config-title text-xl! text-blue-400! font-semibold mb-4  pb-2 uppercase">{$header}</h3>
HMTL;
		}

		return <<<HTML
			</div>
		</fieldset>
		<fieldset class="fastcache-group mb-8">
			{$header}
			<div class="{$class} text-gray-600 mb-6"><p>{$description}</p></div>
			<div class="space-y-4">
HTML;
	}

	public static function end()
	{
		return <<<HTML
			</div>
		</fieldset>
	</div>
</div>
HTML;
	}
}