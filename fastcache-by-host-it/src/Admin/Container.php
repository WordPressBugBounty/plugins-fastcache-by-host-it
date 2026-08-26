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

namespace FastCache\Admin;

use FastCacheFramework\Container\Container as FastCacheContainer;

class aContainer extends FastCacheContainer
{
	public function __construct( array $values = array() )
	{
		

		parent::__construct( $values );
	}
}