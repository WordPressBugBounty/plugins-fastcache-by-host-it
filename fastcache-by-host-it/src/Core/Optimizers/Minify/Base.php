<?php

/**
 * @package   FastCache/minify
 * @copyright Copyright (c) 2025-2026 FastCache
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace FastCache\Minify;

abstract class Base
{
        use \FastCache\RegexTokenizer\Base;

	protected function __construct($options)
	{
		foreach ($options as $key => $value)
		{
			$this->{'_' . $key} = $value;
		}

		if (!defined('FASTCACHE_MINIFY_CONFIGURED'))
		{
			ini_set('pcre.backtrack_limit', 1000000);
			ini_set('pcre.recursion_limit', 1000000);
			ini_set('pcre.jit', 0);

			define('FASTCACHE_MINIFY_CONFIGURED', 1);
		}
	}

	/**
	 *
	 * @staticvar bool $tm
	 *
	 * @param   string    $regex
	 * @param   string    $replacement
	 * @param   string    $code
	 * @param   mixed     $regex_num
	 * @param   callable  $callback
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function _replace($regex, $replacement, $code, $regex_num, $callback = null)
	{
		static $tm = false;

		if ($tm === false)
		{
			$this->_debug('', '');
			$tm = true;
		}

		if (empty($callback))
		{
			$op_code = preg_replace($regex, $replacement, $code);
		}
		else
		{
			$op_code = preg_replace_callback($regex, $callback, $code);
		}

		$this->_debug($regex, $code, $regex_num);

		self::throwExceptionOnPregError();

		return $op_code;
	}

}
