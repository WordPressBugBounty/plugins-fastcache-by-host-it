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
namespace FastCache\Admin\Controller;

use FastCacheFramework\Mvc\Controller;
use FastCache\Platform\Plugin;
use FastCache\Platform\Utility;

class Configurations extends Controller {
	/**
	 * Default task.
	 * Assigns a model to the view and asks the view to render
	 * itself.
	 *
	 * @return void
	 */
	public function display() {
		$viewType = $this->input->getCmd ( 'format', 'html' );

		$view = $this->getView ();
		$view->setTask ( $this->task );
		$view->setDoTask ( $this->doTask );

		// Get/Create the model
		if ($model = $this->getModel ()) {
			// Push the model into the view (as default)
			$view->setDefaultModel ( $model );
		}

		// Set the layout
		if (! is_null ( $this->layout )) {
			$view->setLayout ( $this->layout );
		}
		
		// Manage settings for activation
		if(!Utility::isPluginEnabled()) {
			$view->setLayout ('token' );
		}

		// Display the view
		$view->display ();
	}
}