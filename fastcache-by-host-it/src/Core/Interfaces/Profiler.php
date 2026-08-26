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

interface Profiler
{
      public static function mark($text);  
      
      public static function attachProfiler(&$html, $bAmpPage=false);
      
      public static function start($text, $mark=FALSE);
      
      public static function stop($text, $mark=FALSE);
}
