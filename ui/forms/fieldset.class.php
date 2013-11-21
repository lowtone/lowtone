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

	const PROPERTY_LEGEND = "legend",
		PROPERTY_ELEMENT_NAME = "element_name";

	public function __construct(Form $form = NULL, $properties = NULL, array $options = NULL) {
		$properties = array_merge(array(
				self::PROPERTY_ELEMENT_NAME => "fieldset",
			), (array) $properties);

		parent::__construct($form, $properties, $options);
	}

	// Static
	
	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\FieldSetDocument";
	}
	
}