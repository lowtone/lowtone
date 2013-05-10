<?php
namespace lowtone\ui\forms;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms
 */
class Input extends base\FormElement {
	
	const PROPERTY_TYPE = "type",
		PROPERTY_LABEL = "label",
		PROPERTY_NAME = "name",
		PROPERTY_VALUE = "value",
		PROPERTY_ALT_VALUE = "alt_value",
		PROPERTY_MULTIPLE = "multiple",
		PROPERTY_SELECTED = "selected",
		PROPERTY_COMMENT = "comment";
		
	const TYPE_TEXT = "text",
		TYPE_PASSWORD = "password",
		TYPE_CHECKBOX = "checkbox",
		TYPE_RADIO = "radio",
		TYPE_SELECT = "select",
		TYPE_BUTTON = "button",
		TYPE_SUBMIT = "submit",
		TYPE_HIDDEN = "hidden",
		TYPE_FILE = "file";
	
	// Static
	
	public static function create(Form $form, $type, array $properties = NULL, array $options = NULL) {
		if (is_array($type))
			list($form, $type, $properties, $options) = array($form, (isset($type[self::PROPERTY_TYPE]) ? $type[self::PROPERTY_TYPE] : NULL), $type, $properties);

		$input = new Input($form, $properties, $options);
		
		return $input
			->addClass($type)
			->{self::PROPERTY_TYPE}($type);
	}

	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\InputDocument";
	}
	
}