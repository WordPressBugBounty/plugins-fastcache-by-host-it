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

namespace FastCache\Core\Admin\Ajax;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

abstract class Ajax
{
	private function __construct()
	{
		ini_set('pcre.backtrack_limit', 1000000);
		ini_set('pcre.recursion_limit', 1000000);

		if (version_compare(PHP_VERSION, '7.0.0', '>='))
		{
			ini_set('pcre.jit', 0);
		}
	}

	public static function getInstance($sClass)
	{
		$sFullClass = 'FastCache\\Core\\Admin\\Ajax\\' . $sClass;

		return new $sFullClass();
	}

	abstract function run();
}