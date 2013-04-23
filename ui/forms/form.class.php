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
	
	public function __construct($properties = NULL, array $options = NULL) {
		$properties = array_merge(array(
				self::PROPERTY_METHOD => self::METHOD_POST
			), (array) $properties);

		parent::__construct($this, $properties, $options);
		
	}
	
	// Form elements
	
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

	public function createFieldSet(array $properties = NULL) {
		return FieldSet::create($this, $properties);
	}
	
	public function createInput($type, array $properties = NULL) {
		return Input::create($this, $type, $properties);
	}

	public function createHtml(array $properties = NULL) {
		return Html::create($this, $properties);
	}

	public function prefixNames($prefix) {
		$prefix = Util::mergeArgs(func_get_args());

		return $this->walkChildren(function($element) use ($prefix) {
			if (!($element instanceof Input))
				return;

			$element->setName(array_merge($prefix, (array) $element->getName()));
		});
	}

	// Nonce
	
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
	
	public function getOptionGroup() {
		return $this->itsOptionGroup;
	}

	// Setters
	
	public function setValues($values) {
		$values = new Map($values);

		return $this->walkChildren(function($element) use ($values) {
			if (!($element instanceof Input))
				return;

			if (is_null($value = $values->path($element->getName())))
				return;

			switch ($element->getType()) {
				case Input::TYPE_CHECKBOX:
				case Input::TYPE_RADIO:
					$element->setSelected($element->getValue() == $value);
					break;

				case Input::TYPE_SELECT:
					$element->setSelected($value);
					break;

				default:
					$element->setValue($value);

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