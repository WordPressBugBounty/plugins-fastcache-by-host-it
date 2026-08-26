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

namespace FastCache\Core\Html\Callbacks;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

use FastCache\Core\Helper;
use FastCache\Core\Html\ElementObject;
use FastCache\Core\Html\Parser;
use FastCache\Core\Html\Processor;

class LazyLoad extends CallbackBase
{
	/** @var bool $bExcluded Used to indicate when the child of a parent element is excluded so the whole element can be excluded */
	public $bExcluded = false;
	protected $aExcludes;
	protected $aArgs;

	public function __construct( Processor $oProcessor, $aArgs )
	{
		parent::__construct( $oProcessor );

		$this->aArgs = $aArgs;

		$this->getLazyLoadExcludes();
	}

	protected function getLazyLoadExcludes()
	{
		$aExcludesFiles   = Helper::getArray( $this->oParams->get( 'excludeLazyLoad', array() ) );
		$aExcludesFolders = Helper::getArray( $this->oParams->get( 'pro_excludeLazyLoadFolders', array() ) );
		$aExcludesUrl     = array_merge( array( 'data:image' ), $aExcludesFiles, $aExcludesFolders );

		$aExcludeClass = Helper::getArray( $this->oParams->get( 'pro_excludeLazyLoadClass', array() ) );

		$this->aExcludes = array( 'url' => $aExcludesUrl, 'class' => $aExcludeClass );
	}

	function processMatches( $aMatches )
	{
		if ( empty( $aMatches[0] ) )
		{
			return $aMatches[0];
		}

		if ( $this->oParams->get( 'pro_next_gen_images', '1' ) && $this->aArgs['parent'] != 'picture' )
		{
			$aMatches = \FastCache\Core\Webp::convert( $this, $aMatches );
		}
		$sFullMatch			= false;
		$sElementName		= false;
		$sClassAttribute	= false;
		$sClassDelimiter	= false;
		$sClassValue		= false;
		$sSrcAttribute		= false;
		$sPosterAttribute	= false;
		$sInnerContent		= false;
		$sStyleAttribute	= false;
		$sSrcDelimiter		= false;
		$sPosterDelimiter	= false;
		$sStyleDelimiter	= false;
		$sSrcValue			= false;
		$sPosterValue		= false;
		$sBgDeclaration		= false;
		$sSrcsetAttribute	= false;
		$sPreloadAttribute	= false;
		$sCssUrl			= false;
		$sSrcsetDelimiter   = false;
		$sPreloadDelimiter 	= false;
		$sCssUrlValue 		= false;
		$sSrcsetValue		= false;
		$sPreloadValue		= false;
		$sAutoLoadAttribute = false;
		$sWidthAttribute 	= false;
		$sWidthDelimiter	= false;
		$sWidthValue		= 1;
		$sHeightAttribute 	= false;
		$sHeightDelimiter	= false;
		$sHeightValue		= 1;
		$bLazyLoaded 		= false;
		// $sSrcsetAttribute   = $sPreloadAttribute = $sCssUrl = @$aMatches[8] ?: false;
		// $sSrcsetDelimiter   = $sPreloadDelimiter = $sCssUrlValue = @$aMatches[9] ?: false;
		// $sSrcsetValue       = $sPreloadValue = @$aMatches[10] ?: false;
		// $sAutoLoadAttribute = $sWidthAttribute = @$aMatches[11] ?: false;
		// $sWidthDelimiter    = @$aMatches[12] ?: false;
		// $sHeightAttribute   = @$aMatches[14] ?: false;
		// $sHeightDelimiter   = @$aMatches[15] ?: false;
		// $sHeightValue       = @$aMatches[16] ?: 1;
		// $sFullMatch         = @$aMatches[0] ?: false;
		// $sElementName       = @$aMatches[1] ?: false;
		// $sClassAttribute    = @$aMatches[2] ?: false;
		// $sClassDelimiter    = @$aMatches[3] ?: false;
		// $sClassValue        = @$aMatches[4] ?: false;
		// $sSrcAttribute      = $sPosterAttribute = $sInnerContent = $sStyleAttribute = @$aMatches[5] ?: false;
		// $sSrcDelimiter      = $sPosterDelimiter = $sStyleDelimiter = @$aMatches[6] ?: false;
		// $sSrcValue          = $sPosterValue = $sBgDeclaration = @$aMatches[7] ?: false;
		// $sWidthValue        = @$aMatches[13] ?: 1;
		if(isset($aMatches[0])) { $sFullMatch = $aMatches[0]; }
		if(isset($aMatches[1])) { $sElementName = $aMatches[1]; }
		if(isset($aMatches[2])) { $sClassAttribute = $aMatches[2]; }
		if(isset($aMatches[3])) { $sClassDelimiter = $aMatches[3]; }
		if(isset($aMatches[4])) { $sClassValue = $aMatches[4]; }
		if(isset($aMatches[5])) {
			$sSrcAttribute = $aMatches[5];
			$sPosterAttribute = $aMatches[5];
			$sInnerContent = $aMatches[5];
			$sStyleAttribute = $aMatches[5];
		}
		if(isset($aMatches[6])) {
			$sSrcDelimiter = $aMatches[6];
			$sPosterDelimiter = $aMatches[6];
			$sStyleDelimiter= $aMatches[6];
		}
		if(isset($aMatches[7])) {
			$sSrcValue = $aMatches[7];
			$sPosterValue = $aMatches[7];
			$sBgDeclaration = $aMatches[7];
		}
		if(isset($aMatches[8])) {
			$sPreloadAttribute = $aMatches[8];
			$sCssUrl = $aMatches[8];
			$sSrcsetAttribute= $aMatches[8];
		}
		if(isset($aMatches[9])) {
			$sSrcsetDelimiter   = $aMatches[9];
			$sPreloadDelimiter = $aMatches[9];
			$sCssUrlValue = $aMatches[9];
		}
		if(isset($aMatches[10])) {
			$sSrcsetValue = $aMatches[10];
			$sPreloadValue = $aMatches[10];
		}
		if(isset($aMatches[11])) {
			$sAutoLoadAttribute = $aMatches[11];
			$sWidthAttribute = $aMatches[11];
		}
		if(isset($aMatches[12])) {
			$sWidthDelimiter = $aMatches[12];
		}
		if(isset($aMatches[13])) {
			$sWidthValue = $aMatches[13];
		}
		if(isset($aMatches[14])) {
			$sHeightAttribute = $aMatches[14];
		}
		if(isset($aMatches[15])) {
			$sHeightDelimiter = $aMatches[15];
		}
		if(isset($aMatches[16])) {
			$sHeightValue = $aMatches[16];
		}
		//Return match if it isn't an HTML element
		if ( $sElementName === false )
		{
			return $sFullMatch;
		}

		switch ( $sElementName )
		{
			case 'img':
			case 'input':
			case 'picture':
			case 'iframe':
			case 'source':

				$sImgType = 'embed';
				break;
			case 'video':
			case 'audio':

				$sImgType = 'audiovideo';
				break;
			default:
				$sImgType = 'background';
				break;
		}

		if ( $this->aArgs['lazyload'] )
		{
			if ( $sElementName == 'img' || $sElementName == 'input' )
			{
				Helper::addHttp2Push( $sSrcValue, 'image', true );
			}

			//Start modifying the element to return
			$sReturn = $sFullMatch;

			if ( $sElementName != 'picture' )
			{
				//If a src attribute is found
				if ( $sSrcAttribute !== false )
				{
					$sImgName = $sImgType == 'embed' ? $sSrcValue : $sCssUrlValue;
					//Abort if this file is excluded
					if (
						Helper::findExcludes( $this->aExcludes['url'], $sImgName )
						|| ( $sElementName && Helper::findExcludes( $this->aExcludes['class'], $sClassValue ) )
					)
					{
						//If element child of a parent element set excluded flag
						if ( $this->aArgs['parent'] != '' )
						{
							$this->bExcluded = true;
						}

						return $sFullMatch;
					}

					//If no srcset attribute was found, modify the src attribute and add a data-src attribute
					if ( $sSrcsetAttribute === false && $sImgType == 'embed' )
					{
						$sSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $sWidthValue . '" height="' . $sHeightValue . '"></svg>';

						$sNewSrcValue     = $sElementName == 'iframe' ? 'about:blank' : 'data:image/svg+xml;base64,' . base64_encode( $sSvg );
						$sNewSrcAttribute = 'data-fastcache-lazyload="1" src=' . $sSrcDelimiter . $sNewSrcValue . $sSrcDelimiter . ' data-' . $sSrcAttribute;

						$sReturn = str_replace( $sSrcAttribute, $sNewSrcAttribute, $sReturn );

						$bLazyLoaded = true;
					}

					if ( $sImgType == 'audiovideo' )
					{
						$sReturn     = \FastCache\Core\LazyLoadExtended::lazyLoadAudioVideo( $aMatches, $sReturn );
						$bLazyLoaded = true;
					}
				}

				//Modern browsers will lazy-load without loading the src attribute
				if ( $sSrcsetAttribute !== false && $sImgType == 'embed' )
				{
					$sSvgSrcset          = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $sWidthValue . '" height="' . $sHeightValue . '"></svg>';
					$sNewSrcsetAttribute = 'data-fastcache-lazyload="1" srcset=' . $sSrcsetDelimiter . 'data:image/svg+xml;base64,' . base64_encode( $sSvgSrcset ) . $sSrcsetDelimiter . ' data-' . $sSrcsetAttribute;

					$sReturn     = str_replace( $sSrcsetAttribute, $sNewSrcsetAttribute, $sReturn );
					$bLazyLoaded = true;
				}

				if ( $sImgType == 'audiovideo' )
				{
					$sReturn     = \FastCache\Core\LazyLoadExtended::negateAudioVideoPreload( $aMatches, $sReturn );
					$bLazyLoaded = true;
				}
			}
			//Process and add content of element if not self closing
			if ( $sElementName == 'picture' && $sInnerContent !== false )
			{
				$sInnerContentLazyLoaded = $this->lazyLoadInnerContent( $sInnerContent );
				//If any child element were lazyloaded this function will return false
				if ( $sInnerContentLazyLoaded === false )
				{
					return $sFullMatch;
				}

				return str_replace( $sInnerContent, $sInnerContentLazyLoaded, $sFullMatch );
			}

			if ( $sImgType == 'background' && $this->oParams->get( 'pro_lazyload_bgimages', '0' ) )
			{
				$sReturn     = \FastCache\Core\LazyLoadExtended::lazyLoadBgImages( $aMatches, $sReturn );
				$bLazyLoaded = true;
			}

			if ( $bLazyLoaded )
			{
				//If class attribute not on the appropriate element add it
				if ( $sElementName != 'source' && $sClassAttribute === false )
				{
					$sReturn = str_replace( '<' . $sElementName, '<' . $sElementName . ' class="fastcache-lazyload"', $sReturn );
				}

				//If class already on element add the lazy-load class
				if ( $sElementName != 'source' && $sClassAttribute !== false )
				{
					$sNewClassAttribute = 'class=' . $sClassDelimiter . $sClassValue . ' fastcache-lazyload' . $sClassDelimiter;
					$sReturn            = str_replace( $sClassAttribute, $sNewClassAttribute, $sReturn );
				}
			}

			if ( $this->aArgs['parent'] != 'picture' && $bLazyLoaded )
			{
				//Wrap and add img elements in noscript
				if ( $sElementName == 'img' || $sElementName == 'iframe' )
				{
					$sReturn .= '<noscript>' . $sFullMatch . '</noscript>';
				}
			}

			return $sReturn;

		}
		else
		{
			if ( $sSrcAttribute !== false && ( $sElementName == 'img' || $sElementName == 'input' ) )
			{
				Helper::addHttp2Push( $sSrcValue, 'image', $this->aArgs['deferred'] );
			}

			if ( $sImgType == 'background' && $sStyleAttribute !== false )
			{
				Helper::addHttp2Push( $sCssUrlValue, 'image', $this->aArgs['deferred'] );
			}


			return $sFullMatch;
		}
	}

	protected function lazyLoadInnerContent( $sInnerContent )
	{
		$oParser = new Parser();

		$oImgElement               = new ElementObject();
		$oImgElement->bSelfClosing = true;
		$oImgElement->setNamesArray( array( 'img', 'source' ) );
		//language=RegExp
		$oImgElement->addNegAttrCriteriaRegex( '(?:data-(?:src|original))' );
		$oImgElement->setCaptureAttributesArray( array(
			'class',
			'src',
			'srcset',
			'(?:data-)?width',
			'(?:data-)?height'
		) );
		$oParser->addElementObject( $oImgElement );

		$aArgs = array(
			'lazyload' => true,
			'deferred' => true,
			'parent'   => 'picture'
		);

		$oLazyLoadCallback = new LazyLoad( $this->oProcessor, $aArgs );

		$sResult = $oParser->processMatchesWithCallback( $sInnerContent, $oLazyLoadCallback );

		//if any child element were excluded return false
		if ( $oLazyLoadCallback->bExcluded )
		{
			return false;
		}

		return $sResult;
	}
}
