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

interface Excludes
{
	/**
	 *
	 * @return string
	 */
	public static function extensions();

	/**
	 * @param   string  $type
	 * @param   string  $section
	 *
	 * @return array
	 */
	public static function head($type, $section = 'file');

	/**
	 * @param   string  $type
	 * @param   string  $section
	 *
	 * @return array
	 */
	public static function body($type, $section = 'file');

	/**
	 * @param   string  $url
	 *
	 * @return boolean
	 */
	public static function editors($url);
}
