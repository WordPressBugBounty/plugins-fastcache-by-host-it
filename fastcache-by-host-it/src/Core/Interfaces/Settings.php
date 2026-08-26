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

interface Settings
{
	public function __construct($params);

        public function get($param, $default=NULL);
        
        public function set($param, $value);
        
        public function getOptions();

	/**
	 * Delete a value from the settings object
	 *
	 * @param    mixed    $param    The parameter value to be deleted
	 *
	 * @return   null
	 */
	public function remove($param);

	public function toArray();
}
