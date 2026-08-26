<?php
/**
 * @package   FastCache
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU GPL version 3 or later
 */

/**
 * The Session package is based on the Session package in Aura for PHP. Please consult the LICENSE file in the
 * FastCacheFramework\Session package for copyright and license information.
 */

namespace FastCacheFramework\Session;

/**
 * A factory to create session segment objects.
 */
class SegmentFactory
{
	/**
	 *
	 * Creates a session segment object.
	 *
	 * @param Manager $manager
	 * @param string  $name
	 *
	 * @return Segment
	 */
	public function newInstance(Manager $manager, $name)
	{
		return new Segment($manager, $name);
	}
}
