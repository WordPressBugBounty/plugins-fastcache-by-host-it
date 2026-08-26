<?php
/**
 * @package   FastCache
 * @copyright Copyright (c) 2025-2026 FastCache
 * @license   GNU GPL version 3 or later
 */

namespace FastCacheFramework\Document;
use FastCacheFramework\Container\Container;
use FastCacheFramework\Application\Application;

/**
 * Class Raw
 *
 * Raw output of the document buffer
 *
 * @package FastCacheFramework\Document
 */
class Raw extends Document
{
	public function __construct(Container $container)
	{
		parent::__construct($container);

		$this->mimeType = 'text/plain';
	}


	/**
	 * It just echoes the output buffer to the browser
	 *
	 * @return  void
	 */
	public function render()
	{
		$this->addHTTPHeader('Content-Type', $this->getMimeType());

		$name = $this->getName();

		if (!empty($name))
		{
			$this->addHTTPHeader('Content-Disposition', 'attachment; filename="' . $name . '"', true);
		}

		$this->outputHTTPHeaders();

		echo $this->getBuffer();
	}
}
