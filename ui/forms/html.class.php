<?php
namespace lowtone\ui\forms;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms
 */
class Html extends base\FormElement {

	const PROPERTY_CONTENT = "content";

	// Static

	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\HtmlDocument";
	}

	// Deprecated

	/*public function getContent() {return $this->__get(self::PROPERTY_CONTENT);}
	public function setContent($content) {return $this->__set(self::PROPERTY_CONTENT, $content);}*/

}