<?php
namespace lowtone\ui\hr;
use lowtone\dom\xhtml\XhtmlDocument;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\hr
 */
class HR extends XhtmlDocument {
	
	const CLASS_NAME = "class_name";
	
	public function build(array $options = NULL) {
		$this
			->createAppendElement("hr")
			->setAttribute("class", "lowtone");
		
		return $this;
	}

	public function out() {
		echo $this
			->build()
			->saveXHTML();
	}
}