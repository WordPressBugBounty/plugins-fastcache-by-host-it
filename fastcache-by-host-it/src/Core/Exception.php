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

class Exception extends \Exception
{

        public function __construct($message = null, $code = 0)
        {
                if (!$message)
                {
			$this->message = 'Unknown Exception';
                }

		$error_message = get_class($this) . " '{$message}' in {$this->getFile()}({$this->getLine()})\n"
		       . "{$this->getTraceAsString()}";	

                parent::__construct($error_message, $code);
        }
}

