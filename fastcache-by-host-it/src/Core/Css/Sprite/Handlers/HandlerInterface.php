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

namespace FastCache\Core\Css\Sprite\Handlers;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );


interface HandlerInterface
{

	public function getSupportedFormats();

	public function createSprite($iSpriteWidth, $iSpriteHeight, $sBgColour, $sOutputFormat);

	public function createBlankImage($aFileInfo);

	public function resizeImage($oSprite, $oCurrentImage, $aFileInfo);

	public function copyImageToSprite($oSprite, $oCurrentImage, $aFileInfo, $bResize);

	public function destroy($oImage);

	public function createImage($aFileInfo);

	public function writeImage($oImage, $sExtension, $sFilename);
}

