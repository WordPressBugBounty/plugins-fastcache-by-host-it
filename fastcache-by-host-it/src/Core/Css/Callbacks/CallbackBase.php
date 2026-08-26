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

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );


abstract class CallbackBase
{
	public $oParams;

	protected $aUrl;

	public function __construct( $oParams, $aUrl )
	{
		$this->oParams = $oParams;
		$this->aUrl    = $aUrl;
	}

	abstract function processMatches( $aMatches, $sContext );
}
