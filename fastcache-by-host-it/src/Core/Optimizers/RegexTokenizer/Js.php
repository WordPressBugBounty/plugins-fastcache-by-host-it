<?php

/**
 * @package   FastCache/regextokenizer
 * @copyright Copyright (c) 2025-2026 FastCache
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace FastCache\RegexTokenizer;

trait Js
{
        use Base;

        public static function JS_HTML_COMMENT()
        {
                return '(?:(?:<!--|(?<=[\s/^])-->)[^\r\n]*+)';
        }
}