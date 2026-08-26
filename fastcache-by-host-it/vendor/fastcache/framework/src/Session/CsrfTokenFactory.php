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
 *
 * A factory to create CSRF token objects.
 */
class CsrfTokenFactory
{
	/**
	 *
	 * Creates a CsrfToken object.
	 *
	 * @param Manager $manager The session manager.
	 *
	 * @return CsrfToken
	 *
	 */
	public function newInstance(Manager $manager)
	{
		$segment = $manager->newSegment('FastCacheFramework\Session\CsrfToken');

		return new CsrfToken($segment);
	}
}
