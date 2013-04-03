<?php
namespace lowtone\dom;
use DOMNode,
	DOMXPath;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\dom
 */
class XPath extends DOMXPath {
	
	public function getRelPath($base, $path) {
		$baseParts = explode("/", static::__castToPath($base));
		$pathParts = explode("/", static::__castToPath($path));

		while ($baseParts && $pathParts && ($baseParts[0] == $pathParts[0])) {
			array_shift($baseParts);
			array_shift($pathParts);
		}

		return implode("/", $pathParts);
	}

	private function __castToPath($path) {
		if ($path instanceof DOMNode)
			$path = $path->getNodePath();

		return (string) $path;
	}
}