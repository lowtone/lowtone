<?php
namespace lowtone\ui\forms\out;
use lowtone\ui\forms\base\out\FormElementDocument,
	lowtone\net\URL,
	lowtone\ui\forms\Input;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\out
 */
class InputDocument extends FormElementDocument {
	
	/**
	 * @var Input
	 */
	protected $itsInput;
	
	public function __construct(Input $input) {
		parent::__construct($input);
		
		$this->itsInput = $this->itsFormElement;

		$this->updateBuildOptions(array(
				self::OBJECT_ELEMENT_NAME => "input",
				self::PROPERTY_FILTERS => array(
						Input::PROPERTY_NAME => function($name) {
							if (!is_array($name))
								$name = URL::splitQueryParam($name);

							return URL::queryParam($name);
						},
						Input::PROPERTY_DISABLED => function($disabled, $input) {
							switch($input->{Input::PROPERTY_TYPE}) {
								case Input::TYPE_SELECT:
									if (is_array($disabled))
										return NULL;

									break;
							}

							return $disabled ? 1 : NULL;
						}
					),
				self::BUILD_ELEMENTS => array(
						Input::PROPERTY_NAME,
						Input::PROPERTY_LABEL,
						Input::PROPERTY_PLACEHOLDER,
						Input::PROPERTY_COMMENT
					),
				self::BUILD_ATTRIBUTES => array(
						Input::PROPERTY_TYPE,
						Input::PROPERTY_UNIQUE_ID,
						Input::PROPERTY_DISABLED,
						Input::PROPERTY_REQUIRED,
					),
				self::BUILD_CHILDREN => false
			));
		
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		$inputElement = $this->documentElement;
			
		if ($this->itsInput->{Input::PROPERTY_MULTIPLE})
			$inputElement->setAttribute(Input::PROPERTY_MULTIPLE, "1");
		
		if ($this->itsInput->{Input::PROPERTY_SELECTED})
			$inputElement->setAttribute(Input::PROPERTY_SELECTED, "1");
		
		switch ($this->itsInput->{Input::PROPERTY_TYPE}) {
			case Input::TYPE_SELECT:
				$altValues = (array) $this->itsInput->{Input::PROPERTY_ALT_VALUE};
				$selected = (array) $this->itsInput->{Input::PROPERTY_SELECTED};
				$disabled = is_array($disabled = $this->itsInput->{Input::PROPERTY_DISABLED}) ? $disabled : array();

				$createOption = function($parent, $value, $label) use ($selected, $disabled) {
					if (!$label)
						$label = $value;
					
					$optionElement = $parent->createAppendElement("option", array(
						Input::PROPERTY_VALUE => $value,
						Input::PROPERTY_LABEL => $label
					));
					
					if (@in_array($value, $selected))
						$optionElement->setAttribute(Input::PROPERTY_SELECTED, "1");
					
					if (@in_array($value, $disabled))
						$optionElement->setAttribute(Input::PROPERTY_DISABLED, "1");

					return $optionElement;
				};

				$createOptGroup = function($parent, $values, $altValues, $label) use ($createOption) {
					$optGroupElement = $parent->createAppendElement("optgroup");

					if ($label)
						$optGroupElement->setAttribute("label", $label);

					foreach ($values as $key => $value) 
						$createOption($optGroupElement, $value, $altValues[$key]);

					return $optGroupElement;
				};
				
				for ($values = (array) $this->itsInput->{Input::PROPERTY_VALUE}; list($key, $value) = each($values);) 
					$element = is_array($value) ? $createOptGroup($inputElement, $value, @$altValues[$key], $key) : $createOption($inputElement, $value, @$altValues[$key]);
				
				break;
			
			default:
				$inputElement->appendCreateElement(Input::PROPERTY_VALUE, (string) $this->itsInput->{Input::PROPERTY_VALUE});
		}
		
		return $this;
	}
	
}