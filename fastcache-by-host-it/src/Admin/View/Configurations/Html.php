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
namespace FastCache\Admin\View\Configurations;

use FastCacheFramework\Mvc\View;

class Html extends View {
	protected $size = 0;
	protected $no_files = 0;
	protected function onBeforeMain() {
		wp_register_script ( 'fastcache-tabstate-js', FASTCACHE_URL . 'media/js/tabs-state.js', [ 
				'jquery',
				'fastcache-bootstrap-js'
		], FASTCACHE_VERSION, true );

		wp_enqueue_script ( 'fastcache-tabstate-js' );

		$this->getCacheSize ();

		return true;
	}
	private function getCacheSize() {
		/** @var Main $oModel */
		$oModel = $this->getModel ();

		$oModel->getCacheSize ( FASTCACHE_CACHE_DIR, $this->size, $this->no_files );

		$decimals = 2;
		$sz = 'BKMGTP';
		$factor = ( int ) floor ( (strlen ( $this->size ) - 1) / 3 );

		$this->size = sprintf ( "%.{$decimals}f", $this->size / pow ( 1024, $factor ) ) . $sz [$factor];
		$this->no_files = number_format ( $this->no_files );
	}
}