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

use FastCache\Core\Html\Parser as HtmlParser;
use FastCache\Platform\Profiler;
use FastCache\Platform\Settings;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

class ReduceDom
{
	public static function process( Settings $oParams, $sHtml )
	{
		if ( ! $oParams->get( 'pro_reduce_dom', '0' ) )
		{
			return $sHtml;
		}

		FASTCACHE_DEBUG ? Profiler::start( 'ReduceDom', false ) : null;

		$aOptions = array(
			'num-elements'  => 0, //number of elements encountered
			'nesting-level' => 0,
			'in-comments'   => false, //Inside a section being commented out
			'processing'    => false, //Maximum number of elements reached and DOM is now being reduced
			'html-block'    => '' //Html block currently being processed
		);
		$sRegex   = '#(?:[^<]*+(?:' . HtmlParser::HTML_HEAD_ELEMENT() . '|' . HtmlParser::HTML_COMMENT() . '))?[^<]*+<(/)?(\w++)[^>]*+>#si';

		$sReducedHtml = preg_replace_callback( $sRegex, function ( $aMatches ) use ( &$aOptions, $oParams ) {
			//Initialize return string
			$sReturn       = '';
			$bEndComments  = false;
			$aHtmlSections = $oParams->get('pro_html_sections', [ 'section', 'header', 'footer', 'aside', 'nav' ] );

			switch ( true )
			{
				//Open tag
				case ! empty( $aMatches[2] ) && empty( $aMatches[1] ):
					//Increment count of elements
					$aOptions['num-elements']++;

					if ( $aOptions['processing'] && in_array( $aMatches[2], $aHtmlSections )
						&& $aOptions['nesting-level']++ == 0 )
					{
						$sReturn .= '<div class="fastcache-reduced-dom-container"><template class="fastcache-template">';

						$aOptions['in-comments'] = true;
						$aOptions['html-block']  = $aMatches[2];
					}

					//Start commenting out sections of HTML above 600 DOM elements
					if ( $aOptions['num-elements'] == 600 )
					{
						$aOptions['processing'] = true;
					}

					break;
				//Closing tag
				case ! empty( $aMatches[1] ):

					if ( $aOptions['in-comments'] && in_array( $aMatches[2], $aHtmlSections ) &&
						--$aOptions['nesting-level'] == 0 && $aMatches[2] == $aOptions['html-block'] )
					{
						$bEndComments = true;
					}

					break;
				default:
					break;
			}

			$sReturn .= $aMatches[0];

			if ( $bEndComments )
			{
				$sReturn .= '</template></div>';

				$aOptions['in-comments'] = false;
			}

			return $sReturn;
		}, $sHtml );

		FASTCACHE_DEBUG ? Profiler::stop( 'ReduceDom', true ) : null;

		return $sReducedHtml;

	}
}