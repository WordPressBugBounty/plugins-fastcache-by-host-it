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

namespace FastCache\Core;

use FastCache\Core\Css\Callbacks\ExtractCriticalCss;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

class DynamicSelectors
{
	public static function getDynamicSelectors( ExtractCriticalCss $oExtractCriticalCss, $aMatches )
	{
		//Add all CSS containing any specified dynamic CSS to the critical CSS
		$aDynamicSelectors = Helper::getArray( $oExtractCriticalCss->oParams->get( 'pro_dynamic_selectors', [] ) );
		$aDynamicSelectors = array_unique( array_merge( $aDynamicSelectors, [ 'offcanvas', 'off-canvas', 'mobilemenu', 'mobile-menu' ] ) );

		if ( ! empty( $aDynamicSelectors ) )
		{
			foreach ( $aDynamicSelectors as $sDynamicSelector )
			{
				if ( strpos( $aMatches[2], $sDynamicSelector ) !== false )
				{
					$oExtractCriticalCss->appendToCriticalCss( $aMatches[0] );

					$oExtractCriticalCss->_debug( '', '', 'afterAddDynamicCss' );

					return true;
				}
			}
		}

		$oExtractCriticalCss->_debug( '', '', 'afterSearchDynamicCss' );

		return false;
	}
}