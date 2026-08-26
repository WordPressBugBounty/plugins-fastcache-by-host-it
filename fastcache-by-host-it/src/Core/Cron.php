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

namespace FastCache\Core;

defined('_FASTCACHE_EXEC') or die('Restricted access');

use FastCache\Platform\Profiler;
use FastCache\Platform\Cache;
use FastCache\Platform\Settings;
use FastCache\Platform\Utility;

class Cron
{
        public $params;
        
        /**
         * 
         * @param Settings $params
         */
        public function __construct($params)
        {
                $this->params = $params;
        }

	/**
	 *
	 *
	 * @return string
	 */
        public function runCronTasks()
        {
                //$this->getAdminObject($oParser);
                $this->garbageCron();
                
                return 'CRON';
        }

        /**
         * 
         */
        public function garbageCron()
        {
                FASTCACHE_DEBUG ? Profiler::start('GarbageCron') : null;
                
               // $url = Paths::ajaxUrl('garbagecron');
               // Helper::postAsync($url, $this->params, array('async' => '1'));
		Cache::gc();

                FASTCACHE_DEBUG ? Profiler::stop('GarbageCron', true) : null;
        }
}
