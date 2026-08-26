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

interface Cache
{
	/**
	 *
	 * @param   string    $id
	 * @param   callable  $function
	 * @param   array     $args
	 */
	public static function getCallbackCache($id, $function, $args);

	/**
	 *
	 * @param   string  $id
	 * @param   bool    $checkexpire
	 */
	public static function getCache($id, $checkexpire = false);

	/**
	 *
	 *
	 */
	public static function gc();

	/**
	 *
	 * @param   string  $content
	 * @param   string  $id
	 */
	public static function saveCache($content, $id);

	/**
	 *
	 * @param   string  $context
	 */
	public static function deleteCache($context = 'both');
}
