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

namespace FastCache\Core\Html\Callbacks;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

use FastCache\Core\Html\Processor;
use FastCache\Platform\Settings;

abstract class CallbackBase
{
	/** @var Settings        Plugin parameters */
	public $oParams;
	/** @var $sRegex        Regex used to process HTML */
	public $sRegex;
	/** @var array          Array of excludes parameters */
	protected $aExcludes;
	/** @var Processor      Processor object */
	protected $oProcessor;

	public function __construct( $oProcessor )
	{
		$this->oProcessor = $oProcessor;
		$this->oParams    = $oProcessor->oParams;
	}

	abstract function processMatches( $aMatches );

	protected function getMValue( $sValue )
	{
		return ! empty( $sValue ) ? $sValue : false;
	}
}
