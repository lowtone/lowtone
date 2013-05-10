<?php
namespace lowtone\ui\forms;
use ReflectionClass,
	lowtone\Util,
	lowtone\types\arrays\Map;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms
 */
class Form extends base\FormElement {

	const PROPERTY_ACCEPT = "accept",
		PROPERTY_ACCEPT_CHARSET = "accept-charset",
		PROPERTY_ACTION = "action",
		PROPERTY_METHOD = "method",
		PROPERTY_AUTOCOMPLETE = "autocomplete",
		PROPERTY_ENCTYPE = "enctype",
		PROPERTY_TARGET = "target";

	const METHOD_POST = "post",
		METHOD_GET = "get";

	protected $itsOptionGroup;
	
	/**
	 * Constructor for the form.
	 * @param array|NULL $properties Optional properties to set on the form.
	 * @param array|NULL $options Options to set on the form.
	 */
	public function __construct($properties = NULL, array $options = NULL) {
		$properties = array_merge(array(
				self::PROPERTY_METHOD => self::METHOD_POST
			), (array) $properties);

		parent::__construct($this, $properties, $options);
		
	}
	
	// Form elements
	
	/**
	 * Create a form element instance.
	 * @param string $class The class name for the required form element 
	 * instance.
	 * @param array|NULL $properties Optional properties to set on the element.
	 * @param array|NULL $options Options to set on the element.
	 * @throws ErrorException Throws an error exception if the required class 
	 * isn't a instance of the FormElement class.
	 * @return FormElement Returns the newly created form element.
	 */
	public function createElement($class, array $properties = NULL, array $options = NULL) {
		$reflector = new ReflectionClass($class);

		$formElementClass = __NAMESPACE__ . "\\base\\FormElement";

		if (!$reflector->isSubClassOf($formElementClass))
			throw new \ErrorException(sprintf("%s can only create instances of subclasses of %s", __METHOD__, $formElementClass));

		return $reflector->newInstanceArgs(array(
				$this,
				$properties,
				$options
			));
	}

	/**
	 * Create a new field set instance.
	 * @param array|NULL $properties Optional properties to set on the field 
	 * set.
	 * @return FieldSet Returns the newly created field set.
	 */
	public function createFieldSet(array $properties = NULL) {
		return FieldSet::create($this, $properties);
	}
	
	/**
	 * Create a new input instance.
	 * @param string $type The type for the input.
	 * @param array|NULL $properties Optional properties to set on the input. 
	 * @return Input Returns the newly created input.
	 */
	public function createInput($type, array $properties = NULL) {
		return Input::create($this, $type, $properties);
	}

	/**
	 * Create a new Html instance.
	 * @param array|NULL $properties Optional properties to set on the html 
	 * section. 
	 * @return Html Returns the newly create Html section.
	 */
	public function createHtml(array $properties = NULL) {
		return Html::create($this, $properties);
	}

	/**
	 * Prefix the names for all inputs of the form.
	 * @param array|string $prefix The prefix for the inputs.
	 * @return Form Returns the form for method chaining.
	 */
	public function prefixNames($prefix) {
		$prefix = Util::mergeArgs(func_get_args());

		return $this->walkChildren(function($element) use ($prefix) {
			if (!($element instanceof Input))
				return;

			$element->{Input::PROPERTY_NAME}(array_merge($prefix, (array) $element->{Input::PROPERTY_NAME}));
		});
	}

	// Nonce
	
	/**
	 * Add a nonce field to the form.
	 * @param integer $action An action definition for the nonce field. Should 
	 * give the context to what is taking place.
	 * @param string $name The name for the nonce field.
	 * @param boolean $referer Whether also the referer hidden form field should 
	 * be created.
	 * @return Form Returns the form for method chaining.
	 */
	public function nonceField($action = -1, $name = "_wpnonce", $referer = true) {
		$this
			->appendChild(
					$this->createInput(Input::TYPE_HIDDEN, array(
							Input::PROPERTY_UNIQUE_ID => $name,
							Input::PROPERTY_NAME => $name,
							Input::PROPERTY_VALUE => wp_create_nonce($action)
						))
				);

		if ($referer)
			$this->refererField();

		return $this;
	}

	// Referer

	/**
	 * Add a referer field to the form.
	 * @return Form Returns the form for method chaining.
	 */
	public function refererField() {
		return $this
			->appendChild(
					$this->createInput(Input::TYPE_HIDDEN, array(
							Input::PROPERTY_NAME => "_wp_http_referer",
							Input::PROPERTY_VALUE => $_SERVER["REQUEST_URI"]
						))
				);
	}

	// Settings API
	
	/**
	 * Add nonce, action and option_page to the form. This is required for forms 
	 * used on settings pages.
	 * @param string $optionGroup The name for the settings group.
	 * @return Form Returns the form for method chaining.
	 */
	public function settingsFields($optionGroup) {
		$this->itsOptionGroup = $optionGroup;

		return $this
			->appendChild(
					$this->createInput(Input::TYPE_HIDDEN, array(
							Input::PROPERTY_NAME => "option_page",
							Input::PROPERTY_VALUE => $this->itsOptionGroup
						))
				)
			->appendChild(
					$this->createInput(Input::TYPE_HIDDEN, array(
							Input::PROPERTY_NAME => "action",
							Input::PROPERTY_VALUE => "update"
						))
				)
			->nonceField($optionGroup . "-options");
	}

	// Getters
	
	/**
	 * Get the name for the option group (only for forms on settings pages). 
	 * @return string Returns the name for the option group.
	 */
	public function getOptionGroup() {
		return $this->itsOptionGroup;
	}

	// Setters
	
	/**
	 * Set values on all inputs in the form.
	 * @param array An array of input names and their values.
	 * @return Form Returns the form for method chaining.
	 */
	public function setValues($values) {
		$values = new Map($values);

		return $this->walkChildren(function($element) use ($values) {
			if (!($element instanceof Input))
				return;

			if (is_null($value = $values->path($element->{Input::PROPERTY_NAME})))
				return;

			switch ($element->{Input::PROPERTY_TYPE}) {
				case Input::TYPE_CHECKBOX:
				case Input::TYPE_RADIO:
					$element->{Input::PROPERTY_SELECTED}($element->{Input::PROPERTY_VALUE}() == $value);
					break;

				case Input::TYPE_SELECT:
					$element->{Input::PROPERTY_SELECTED}($value);
					break;

				default:
					$element->{Input::PROPERTY_VALUE}($value);

			}
		});
	}

	// Static
	
	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\FormDocument";
	}
	
	public static function create(array $properties = NULL, array $options = NULL) {
		return new static($properties, $options);
	}
	
}