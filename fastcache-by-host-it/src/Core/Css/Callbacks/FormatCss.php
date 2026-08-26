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

namespace FastCache\Core\Css\Callbacks;

use FastCache\Core\Css\Parser;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );


class FormatCss extends CallbackBase
{
	public $sValidCssRules;

	function processMatches( $aMatches, $sContext )
	{
		if ( isset ( $aMatches[7] ) && !preg_match( '#' . $this->sValidCssRules . '#i', $aMatches[7] ) )
		{
			return '';
		}

		return $aMatches[0];
	}
}
