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

namespace FastCache\Platform;

defined( '_WP_EXEC' ) or die( 'Restricted access' );

use FastCache\Core\FileRetriever;
use FastCache\Core\Logger;
use FastCache\Core\Exception;
use FastCache\Core\Interfaces\Html as HtmlInterface;
use FastCache\Core\Url;

class Html implements HtmlInterface
{
	protected $params;

	public function __construct( $params )
	{
		$this->params = $params;
	}

	public function getHomePageHtml()
	{
		FASTCACHE_DEBUG ? Profiler::mark( 'beforeGetHtml' ) : null;

		$url = home_url() . '/?fastcacherunbackend=1';

		try
		{
			$oFileRetriever = FileRetriever::getInstance();

			$response = $oFileRetriever->getFileContents( $url );

			if ( $oFileRetriever->response_code != 200 )
			{
				throw new Exception(
					Utility::translate( 'Failed fetching front end HTML with response code ' . $oFileRetriever->response_code )
				);
			}

			FASTCACHE_DEBUG ? Profiler::mark( 'afterGetHtml' ) : null;

			return $response;
		}
		catch ( Exception $e )
		{
			Logger::log( $url . ': ' . $e->getMessage(), $this->params );

			FASTCACHE_DEBUG ? Profiler::mark( 'afterGetHtml)' ) : null;

			throw new \RunTimeException( _( 'Load or refresh the front-end site first then refresh this page '
			                                . 'to populate the multi select exclude lists.' ) );
		}
	}

	public function getMainMenuItemsHtmls( $iLimit = 5, $bIncludeUrls = false )
	{
		$aMenuUrls = $this->getMenuUrls();
		$aMenuUrls = array_slice( $aMenuUrls, 0, $iLimit );

		$aHtmls = [];

		//Limit the time spent on this
		$iTimer = microtime( true );

		foreach ( $aMenuUrls as $menuUrl )
		{
			try
			{
				if ( $bIncludeUrls )
				{
					$aHtmls[] = [
						'url'  => $menuUrl,
						'html' => $this->getHtml( $menuUrl )
					];
				}
				else
				{
					$aHtmls[] = $this->getHtml( $menuUrl );
				}
			}
			catch ( Exception $e )
			{
				Logger::log( $e->getMessage(), $this->params );
			}

			if ( microtime( true ) > $iTimer + 10.0 )
			{
				break;
			}
		}

		return $aHtmls;
	}

	protected function getMenuUrls()
	{
		$homeUrl = rtrim( home_url(), '/\\' );
		//Start array of oMenu urls with home page
		$aMenuUrls = [ $homeUrl ];

		$aMenus = wp_get_nav_menus();
		//If nothing just work with the home url
		if ( ! $aMenus )
		{
			return $aMenuUrls;
		}

		$locations      = get_registered_nav_menus();
		$aMenuLocations = get_nav_menu_locations();

		//Iterate through menus to find primary
		foreach ( $aMenus as $oMenu )
		{
			if ( $oMenu->term_id == $aMenuLocations['menu-primary'] )
			{
				break;
			}
		}

		$aMenuItems = wp_get_nav_menu_items( $oMenu );

		foreach ( $aMenuItems as $oMenuItem )
		{
			if ( rtrim( $oMenuItem->url, '/\\' ) == $homeUrl )
			{
				continue;
			}

			if ( ! $cleanUrl = $this->cleanUrl( $oMenuItem->url ) )
			{
				continue;
			}

			if ( Url::isInternal( $cleanUrl ) )
			{
				$aMenuUrls[] = $cleanUrl;
			}
		}

		return $aMenuUrls;
	}

	private function cleanUrl( string $oMenuItem )
	{
		$oUri = Uri::getInstance( $oMenuItem );

		return $oUri->toString( [ 'scheme', 'host', 'path', 'query' ] );
	}

	protected function getHtml( $sUrl )
	{
		$oUri   = Uri::getInstance( $sUrl );
		$sQuery = $oUri->getQuery();
		parse_str( $sQuery, $aQuery );
		$aNewQuery = array_merge( $aQuery, array( 'fastcacherunbackend' => '1' ) );
		$oUri->setQuery( $aNewQuery );

		$oFileRetriever = FileRetriever::getInstance();
		$sHtml          = $oFileRetriever->getFileContents( $oUri->toString() );

		if ( $oFileRetriever->response_code != 200 )
		{
			throw new Exception( 'Failed fetching HTML: ' . $sUrl . ' - Response code: ' . $oFileRetriever->response_code );
		}

		return $sHtml;
	}

}

