<?php
/**
 * @package   FastCache
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU GPL version 3 or later
 */

namespace FastCacheFramework\Mvc\Engine;

use FastCacheFramework\Mvc\View;

/**
 * View engine for compiling PHP template files.
 */
class BladeEngine extends CompilingEngine implements EngineInterface
{
	public function __construct(View $view)
	{
		parent::__construct($view);

		// Assign the Blade compiler to this engine
		$this->compiler = $view->getContainer()->blade;
	}
}
