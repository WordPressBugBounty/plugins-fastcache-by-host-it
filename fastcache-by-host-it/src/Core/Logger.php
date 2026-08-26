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

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

use FastCache\Platform\Settings;
use FastCache\Platform\Utility;

/**
 *
 *
 */
class Logger
{
	static protected $ip = '';

	/**
	 *
	 * @param   string    $sMessage
	 * @param   Settings  $params
	 */
	public static function log( $sMessage, Settings $params )
	{
		FASTCACHE_DEBUG ? Utility::log( $sMessage, 'ERROR', 'fastcache-log-errors.php' ) : null;
	}

	/**
	 *
	 * @param   string  $variable
	 * @param   string  $name
	 */
	public static function debug( $variable, $name = '' )
	{
		$sMessage = $name != '' ? "$name = '" . $variable . "'" : $variable;

		Utility::log( $sMessage, 'DEBUG', 'fastcache-log-debug.php' );
	}

	/**
	 *
	 * @param   string  $sMessage
	 */
	public static function logInfo( $sMessage )
	{
		Utility::log( $sMessage, 'INFO', 'fastcache-log-info.php' );
	}

}
