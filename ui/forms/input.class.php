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
	
	// Getters
	
	public function getType() {return $this->__get(self::PROPERTY_TYPE);}
	public function getLabel() {return $this->__get(self::PROPERTY_LABEL);}
	public function getName() {return $this->__get(self::PROPERTY_NAME);}
	public function getValue() {return $this->__get(self::PROPERTY_VALUE);}
	public function getAltValue() {return $this->__get(self::PROPERTY_ALT_VALUE);}
	public function getMultiple() {return (bool) $this->__get(self::PROPERTY_MULTIPLE);}
	public function getSelected() {return $this->__get(self::PROPERTY_SELECTED);}
	
	// Setters
	
	public function setType($type) {return $this->__set(self::PROPERTY_TYPE, $type);}
	public function setLabel($label) {return $this->__set(self::PROPERTY_LABEL, $label);}
	public function setName($name) {return $this->__set(self::PROPERTY_NAME, $name);}
	public function setValue($value) {return $this->__set(self::PROPERTY_VALUE, $value);}
	public function setAltValue($altValue) {return $this->__set(self::PROPERTY_ALT_VALUE, $altValue);}
	public function setMultiple($multiple) {return $this->__set(self::PROPERTY_MULTIPLE, (bool) $multiple);}
	public function setSelected($selected) {return $this->__set(self::PROPERTY_SELECTED, $selected);}
	
	// Static
	
	public static function create(Form $form, $type, array $properties = NULL, array $options = NULL) {
		if (is_array($type))
			list($form, $type, $properties, $options) = array($form, @$type[self::PROPERTY_TYPE], $type, $properties);

		$input = new Input($form, $properties, $options);
		
		return $input
			->addClass($type)
			->setType($type);
	}

	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\InputDocument";
	}
	
}