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

namespace FastCache\Core\Interfaces;

defined('_FASTCACHE_EXEC') or die('Restricted access');

/**
 * Interface HttpInterface
 * @package FastCache\Core\Interfaces
 */
interface Http
{
	/**
	 *
	 * @param   string      $sPath
	 * @param   array       $aPost
	 * @param   array|null  $aHeaders
	 * @param   string      $sUserAgent
	 *
	 * @return array
	 */
	public function request($sPath, $aPost = null, $aHeaders = null, $sUserAgent = '');

	/**
	 * Returns an available http transport object
	 *
	 * @return mixed False if no http adapter found, Http object otherwise
	 */
	public function available();
}
