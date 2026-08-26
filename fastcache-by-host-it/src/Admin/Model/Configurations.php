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
namespace FastCache\Admin\Model;

use FastCacheFramework\Mvc\Model;
use FastCache\Core\Admin\DashboardWidget;

class Configurations extends Model {
	public function getCacheSize($cache_path, &$size, &$no_files) {
		$stats    = DashboardWidget::getCacheStats();
		$size     = $stats['size_raw'];
		$no_files = $stats['files_raw'];
	}
}