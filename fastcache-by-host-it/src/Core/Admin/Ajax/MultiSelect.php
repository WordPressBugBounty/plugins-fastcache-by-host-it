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

namespace FastCache\Core\Admin\Ajax;

use FastCache\Core\Admin\Json;
use FastCache\Core\Admin\MultiSelectItems;
use FastCache\Platform\Html;
use FastCache\Platform\Plugin;
use FastCache\Platform\Utility;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

class MultiSelect extends Ajax
{
	public function run()
	{
		$aData = Utility::get( 'data', array(), 'array' );

		$params = Plugin::getPluginParams();
		$oAdmin = new MultiSelectItems( $params );
		$oHtml  = new Html( $params );

		try
		{
			$sHtml = $oHtml->getHomePageHtml();
			$oAdmin->getAdminLinks( $sHtml );
		}
		catch ( \Exception $e )
		{
			$error = $e->getMessage();
		}

		$response = array();

		foreach ( $aData as $sData )
		{
			$options = $oAdmin->prepareFieldOptions( $sData['type'], $sData['param'], $sData['group'], false );

			$response[$sData['id']] = new Json( $options );
		}

		return new Json( $response );
	}
}