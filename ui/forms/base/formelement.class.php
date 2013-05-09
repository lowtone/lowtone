<?php
namespace lowtone\ui\forms\base;
use lowtone\Util,
	lowtone\db\records\Record,
	lowtone\db\records\schemata\properties\Property,
	lowtone\types\arrays\XArray,
	lowtone\ui\forms\Form;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\base
 */
abstract class FormElement extends Record implements interfaces\FormElement {
	
	/**
	 * @var Form
	 */
	protected $itsForm;
	
	/**
	 * @var array
	 */
	protected $itsChildren = array();
	
	const PROPERTY_UNIQUE_ID = "uniqid",
		PROPERTY_DISABLED = "disabled",
		PROPERTY_CLASS = "class",
		PROPERTY_ATTRIBUTES = "attributes",
		PROPERTY_ORDER = "order";
	
	public function __construct(Form $form = NULL, $properties = NULL, array $options = NULL) {
		$properties = array_merge(array(
				self::PROPERTY_UNIQUE_ID => sha1(uniqid(md5(serialize($this->itsForm)), true)),
				self::PROPERTY_CLASS => array("lowtone")
			), (array) $properties);
		
		$this->itsForm = $form;

		parent::__construct($properties, $options);
		
	}
	
	public function appendChild(FormElement $child) {
		$this->itsChildren[] = $child;

		return $this;
	}

	public function offsetSet($offset, $value) {
		if (isset($offset))
			return parent::offsetSet($offset, $value);

		return $this->appendChild($value);
	}
	
	public function __clone() {
		$this->itsChildren = array_map(function($child) {
			return clone $child;
		}, (array) $this->itsChildren);
	}

	// Children
	
	public function walkChildren($callback, FormElement $element = NULL) {
		if (!is_callable($callback))
			throw new \Exception(sprintf("%s requires a valid callback", __FUNCTION__));

		if (is_null($element))
			$element = $this;

		if (!($element instanceof FormElement))
			throw new \Exception(sprintf("%s requires a valid FormElement", __FUNCTION__));

		foreach ($element->getChildren() as $child) {
			call_user_func($callback, $child);

			if ($child instanceof FormElement)
				$child->walkChildren($callback);
			
		}

		return $element;
	}

	// Output
	
	public function out(array $options = NULL) {
		$document = $this
			->createDocument()
			->build($options);

		if ($template = @$options["template"])
			$document->setTemplate($template);

		echo $document
			->transform()
			->saveHTML();
	}

	public function __toString() {
		return $this
			->createDocument()
			->build()
			->transform()
			->saveHTML();
	}
	
	// Getters
	
	public function getForm() {
		return $this->itsForm;
	}
	
	public function getChildren() {
		return (array) $this->itsChildren;
	}
	
	// Setters
	
	public function setChildren(array $children) {
		$this->itsChildren = $children; 

		return $this;
	}

	public function setAttribute($name, $value = NULL) {
		$atts = is_array($name) ? Util::mergeArgs(func_get_args()) : XArray::remix(func_get_args());


		return $this->{self::PROPERTY_ATTRIBUTES}(array_merge($this->{self::PROPERTY_ATTRIBUTES}, (array) $atts));
	}

	public function addClass($class) {
		return $this->{self::PROPERTY_CLASS}(array_unique(Util::mergeArgs(array_merge($this->{self::PROPERTY_CLASS}, func_get_args()))));
	}
	
	// Static
	
	public static function create(Form $form, array $properties = NULL, array $options = NULL) {
		return new static($form, $properties, $options);
	}

	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\FormElementDocument";
	}

	public static function __createSchema($defaults = NULL) {
		$arrayProperty = array(
				Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
				Property::ATTRIBUTE_SERIALIZE => "serialize",
				Property::ATTRIBUTE_UNSERIALIZE => "unserialize",
				Property::ATTRIBUTE_SET => function($val) {
					return (array) $val;
				},
				Property::ATTRIBUTE_GET => function($val) {
					return (array) $val;
				},
			);

		return parent::__createSchema(array_merge(array(
				self::PROPERTY_CLASS => $arrayProperty,
				self::PROPERTY_ATTRIBUTES => $arrayProperty,
			), (array) $defaults));
	}

	// Deprecated
	
	/*public function getUniqueId() {return $this->__get(self::PROPERTY_UNIQUE_ID);}
	public function getDisabled() {return $this->__get(self::PROPERTY_DISABLED);}
	public function getClass() {return (array) $this->__get(self::PROPERTY_CLASS);}
	public function getAttributes() {return (array) $this->__get(self::PROPERTY_ATTRIBUTES);}*/
	/*public function setUniqueId($uniqueId) {return $this->__set(self::PROPERTY_UNIQUE_ID, $uniqueId);}
	public function setDisabled($disabled) {return $this->__set(self::PROPERTY_DISABLED, $disabled);}
	public function setClass(array $class) {return $this->__set(self::PROPERTY_CLASS, $class);}
	public function setAttributes(array $attributes) {return (array) $this->__set(self::PROPERTY_ATTRIBUTES, $attributes);}*/
	
}