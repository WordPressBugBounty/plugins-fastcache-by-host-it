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

// No direct access
defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

use FastCache\Core\Html\Processor as HtmlProcessor;
use FastCache\Core\Html\CacheManager;
use FastCache\Core\Html\LinkBuilder;
use FastCache\Core\LightImages;
use FastCache\Platform\Settings;
use FastCache\Platform\Profiler;
use FastCache\Platform\Utility;
use Joomla\Registry\Registry;

/**
 * Main plugin file
 *
 */
class Optimize
{

	/** @var object   Plugin params * */
	public $params = null;
	private $jit = 1;

	/**
	 * Constructor
	 *
	 * @param   Settings  $oParams  Plugin parameters
	 */
	private function __construct( $oParams )
	{
		ini_set( 'pcre.backtrack_limit', 1000000 );
		ini_set( 'pcre.recursion_limit', 1000000 );

		if ( version_compare( PHP_VERSION, '7.0.0', '>=' ) )
		{
			$this->jit = ini_get( 'pcre.jit' );
			ini_set( 'pcre.jit', "0" );
		}

		$this->params = $oParams;
	}

	/**
	 * Static method to initialize the plugin
	 *
	 * @param   Settings 	       $oParams
	 * @param   string             $sHtml
	 *
	 * @return string
	 * @throws Exception
	 */
	public static function optimize( Settings $oParams, $sHtml )
	{
		if ( version_compare( PHP_VERSION, '5.3.0', '<' ) )
		{
			throw new Exception( 'PHP Version less than 5.3.0. Exiting plugin...' );
		}

		$pcre_version = preg_replace( '#(^\d++\.\d++).++$#', '$1', PCRE_VERSION );

		if ( version_compare( $pcre_version, '7.2', '<' ) )
		{
			throw new Exception( 'PCRE Version less than 7.2. Exiting plugin...' );
		}

		$oOptimize = new Optimize( $oParams );

		return $oOptimize->process( $sHtml );
	}

	/**
	 * Optimize website by aggregating css and js
	 *
	 * @param   string  $sHtml
	 *
	 * @return string
	 * @throws Exception
	 */
	public function process( $sHtml )
	{
		FASTCACHE_DEBUG ? Profiler::start( 'Process', true ) : null;

		try
		{
			$oHtmlProcessor = new HtmlProcessor( $sHtml, $this->params );
			$oHtmlProcessor->processCombineJsCss();
			$oHtmlProcessor->processImageAttributes();

			$oCacheManager = new CacheManager( new LinkBuilder( $oHtmlProcessor ) );
			$oCacheManager->handleCombineJsCss();
			$oCacheManager->handleImgAttributes();

			$oHtmlProcessor->processCdn();
			$oHtmlProcessor->processLazyLoad();

			$toOptimizeHtml = $oHtmlProcessor->getHtml();
			
			// Light Images Optimizator
			$imagesOptimizer = new LightImages($this->params);
			$optimizedImagesHtml = $imagesOptimizer->optimize($toOptimizeHtml);
			if($optimizedImagesHtml) {
				$toOptimizeHtml = $optimizedImagesHtml;
			}
			
			$sOptimizedHtml = self::reduceDom( Helper::minifyHtml( $toOptimizeHtml, $this->params ) );

			$this->sendHeaders();
		}
		catch ( Exception $ex )
		{
			Logger::log( $ex->getMessage(), $this->params );

			$sOptimizedHtml = $sHtml;
		}

		FASTCACHE_DEBUG ? Profiler::stop( 'Process', true ) : null;

		FASTCACHE_DEBUG ? Profiler::attachProfiler( $sOptimizedHtml, $oHtmlProcessor->bAmpPage ) : null;

		if ( version_compare( PHP_VERSION, '7.0.0', '>=' ) )
		{
			ini_set( 'pcre.jit', $this->jit );
		}

		return $sOptimizedHtml;
	}

	protected function reduceDom( $sHtml )
	{
		$sHtml = ReduceDom::process( $this->params, $sHtml );

		return $sHtml;
	}

	protected function sendHeaders()
	{
		$oHttp2  = Http2::getInstance();
		$headers = array();

		if ( $oHttp2->bEnabled )
		{
			$aPreloads = $oHttp2->getPreloads();

			if ( ! empty( $aPreloads ) )
			{
				$headers['Link'] = implode( ',', $aPreloads );
			}
		}

		if ( ! empty( $headers ) )
		{
			Utility::sendHeaders( $headers );
		}
	}
}
