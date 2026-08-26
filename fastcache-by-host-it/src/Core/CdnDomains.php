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

use FastCache\Platform\Settings;
use FastCache\Platform\Utility;

defined( '_FASTCACHE_EXEC' ) or die( 'Restricted access' );

class CdnDomains
{
	public static function addCdnDomains( Cdn $oCdn, array &$aDomain )
	{
		if ( trim( $oCdn->oParams->get( 'pro_cookielessdomain_2', '' ) ) != '' )
		{
			$domain2       = $oCdn->oParams->get( 'pro_cookielessdomain_2' );
			$sStaticFiles2 = implode( '|', $oCdn->oParams->get( 'pro_staticfiles_2', Cdn::getStaticFiles() ) );

			$aDomain[ $oCdn->scheme . $oCdn->prepareDomain( $domain2 ) ] = $sStaticFiles2;
		}

		if ( trim( $oCdn->oParams->get( 'pro_cookielessdomain_3', '' ) ) != '' )
		{
			$domain3       = $oCdn->oParams->get( 'pro_cookielessdomain_3' );
			$sStaticFiles3 = implode( '|', $oCdn->oParams->get( 'pro_staticfiles_3', Cdn::getStaticFiles() ) );

			$aDomain[ $oCdn->scheme . $oCdn->prepareDomain( $domain3 ) ] = $sStaticFiles3;
		}
	}

	public static function preconnect( Settings $oParams )
	{
		if ( $oParams->get( 'cookielessdomain_enable', '0' ) && $oParams->get( 'pro_cdn_preconnect', '1' ) )
		{
			$oCdn     = Cdn::getInstance( $oParams );
			$aDomains = $oCdn->getCdnDomains();

			$sCdnPreConnect = '';

			foreach ( $aDomains as $sDomain => $sStaticFiles )
			{
				$sCdnPreConnect .= Utility::tab() . '<link rel="preconnect" href="' . $sDomain . '" crossorigin />'
					. Utility::lnEnd();
			}

			return $sCdnPreConnect;
		}
	}
}