<?php
namespace lowtone\ui\forms;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms
 */
class FieldSet extends base\FormElement {

	const PROPERTY_LEGEND = "legend";

	// Static
	
	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\FieldSetDocument";
	}
	
}