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

namespace FastCache\Core\Admin;

use FastCache\Platform\Paths;
use FastCache\Platform\Plugin;
use FastCache\Platform\Utility;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

class Icons
{

	public static function printIconsHTML( $aButtons )
	{
		$sIconsHTML = '';

		foreach ( $aButtons as $aButton )
		{
			$sContentAttr = Utility::bsTooltipContentAttribute();
			$sTooltip     = @$aButton['tooltip'] ? " class=\"hasPopover fig-caption\" title=\"{$aButton['name']}\" {$sContentAttr}=\"{$aButton['tooltip']}\" " : ' class="fig-caption"';
			$sIconSrc     = Paths::iconsUrl() . '/' . $aButton['icon'];
			$sToggle      = '<i class="toggle fa"></i>';

			$sIconsHTML .= <<<HTML
<div id="{$aButton['id']}" class="icon {$aButton['class']}">
	<a href="{$aButton['link']}" class="btn btn-primary" {$aButton['script']}>
		<span class="{$aButton['icon']}"></span>
		<span{$sTooltip}>{$aButton['name']}</span>
		{$sToggle}
	</a>
</div>

HTML;
		}

		return $sIconsHTML;
	}

	public static function getAutoSettingsArray()
	{
		return [
			[
				'name'    => 'Minimum',
				'icon'    => 'minimum.png',
				'setting' => 1,

			],
			[
				'name'    => 'Intermediate',
				'icon'    => 'intermediate.png',
				'setting' => 2
			],
			[
				'name'    => 'Average',
				'icon'    => 'average.png',
				'setting' => 3
			],
			[
				'name'    => 'Deluxe',
				'icon'    => 'deluxe.png',
				'setting' => 4
			],
			[
				'name'    => 'Premium',
				'icon'    => 'premium.png',
				'setting' => 5
			],
			[
				'name'    => 'Optimum',
				'icon'    => 'optimum.png',
				'setting' => 6
			]
		];
	}

	public static function getUtilityArray( $aActions = array() ) 
	{
		$aUtilities = [
			( $action = 'browsercaching' )  => [
				'action'  => $action,
				'icon'    => 'fas fa-tachometer-alt',
				'name'    => __('Optimize .htaccess', 'fastcache'),
				'tooltip' => __( 'Click this button to optimize your htaccess file adding special directives to leverage browser caching and gzip compression. NOTICE: the Apache modules mod_expires, mod_headers and mod_deflate must be enabled on the server.', 'fastcache' )
			],
			( $action = 'restorehtaccess' )  => [
				'action'  => $action,
				'icon'    => 'fas fa-sync-alt',
				'name'    => __('Restore .htaccess', 'fastcache'),
				'tooltip' => __( 'Click this button to remove the optimizations code added to the .htaccess by FastCache and restore the original file.', 'fastcache' )
			],
			( $action = 'cleancache' )      => [
				'action'  => $action,
				'icon'    => 'fas fa-trash-alt',
				'name'    => __('Clear Cache', 'fastcache'),
				'tooltip' => __( "Click this button to clear the plugin cache and page cache.", 'fastcache' )
			],
			( $action = 'orderplugins' )    => [
				'action'  => $action,
				'icon'    => 'fas fa-sort-amount-down',
				'name'    => __('Reorder Plugins', 'fastcache'),
				'tooltip' => __( 'The execution order of plugins could matter to include all assets within the optimization, by clicking this button FastCache attempts to order plugins correctly.', 'fastcache' )
			],
			( $action = 'keycache' )        => [
				'action'  => $action,
				'icon'    => 'fas fa-sync',
				'name'    => __('Refresh cache', 'fastcache'),
				'tooltip' => __( "If you've made any changes to your files you can generate a new cache key to refresh it for browsers and get new contents.", 'fastcache' )
			]
		];

		if ( empty( $aActions ) )
		{
			return $aUtilities;
		}
		else
		{
			return array_intersect_key( $aUtilities, array_flip( $aActions ) );
		}
	}

	public static function compileUtilityIcons( $aUtilities )
	{
		$aIcons = [];
		$i      = 0;

		foreach ( $aUtilities as $aUtility )
		{
			$aIcons[ $i ]['link']    = Paths::adminController( $aUtility['action'] );
			$aIcons[ $i ]['icon']    = $aUtility['icon'];
			$aIcons[ $i ]['name']    = Utility::translate( $aUtility['name'] );
			$aIcons[ $i ]['id']      = strtolower( str_replace( ' ', '-', trim( $aUtility['name'] ) ) );
			$aIcons[ $i ]['tooltip'] = @$aUtility['tooltip'] ?: false;
			$aIcons[ $i ]['script']  = '';
			$aIcons[ $i ]['class']   = '';

			$i++;
		}

		return $aIcons;
	}

	public static function getCombineFilesEnableSetting()
	{
		$oParams = Plugin::getPluginParams();

		return [
			[
				'name'    => 'Combine Files Enable',
				'setting' => ( $setting = 'combine_files_enable' ),
				'icon'    => 'combine_files_enable.png',
				'enabled' => $oParams->get( $setting, '1' )
			]
		];
	}
}