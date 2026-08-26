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

use FastCache\Platform\Cache;

class GarbageCron extends Ajax
{

	/**
	 *
	 */
	public function run()
	{
		Cache::gc();
	}

}