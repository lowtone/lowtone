<?php
namespace lowtone\ui\forms\base;
use lowtone\Util,
	lowtone\db\records\Record,
	lowtone\db\records\schemata\properties\Property,
	lowtone\types\arrays\XArray,
	lowtone\ui\forms\Form;

/**
 * The form element represents a base for each node in a form.
 * 
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
	
	/**
	 * Constructor for the form element.
	 * @param Form $form The form instance to which the element belongs.
	 * @param array|NULL $properties Optional properties to set on the element.
	 * @param array|NULL $options Options to set on the element.
	 */
	public function __construct(Form $form = NULL, $properties = NULL, array $options = NULL) {
		$properties = array_merge(array(
				self::PROPERTY_UNIQUE_ID => sha1(uniqid(md5(serialize($this->itsForm)), true)),
				self::PROPERTY_CLASS => array("lowtone")
			), (array) $properties);
		
		$this->itsForm = $form;

		parent::__construct($properties, $options);
		
	}
	
	/**
	 * Add a child form element.
	 * @param FormElement $child The form element to add as a child.
	 * @return FormElement Returns the form element for method chaining.
	 */
	public function appendChild(FormElement $child) {
		$child->itsForm = $this->itsForm;

		$this->itsChildren[] = $child;

		return $this;
	}

	/**
	 * Overwrite parent method to add a child if no offset is defined. When an 
	 * offset is defined the variable is set as a property on the form element 
	 * object (confusing?).
	 * @param string|int|NULL $offset An optional offset for the property.
	 * @param mixed $value The property value.
	 * @return FormElement Returns the form element for method chaining.
	 */
	public function offsetSet($offset, $value) {
		if (isset($offset))
			return parent::offsetSet($offset, $value);

		return $this->appendChild($value);
	}
	
	/**
	 * Clone children.
	 */
	public function __clone() {
		$this->itsChildren = array_map(function($child) {
			return clone $child;
		}, (array) $this->itsChildren);
	}

	// Children
	
	/**
	 * Walk through all child form elements and their children of the called or 
	 * a given element.
	 * @param callback $callback A function to execute on the elements.
	 * @param FormElement|null $element Discard to execute on the called object 
	 * or provide a subject form element.
	 * @return FormElement Returns the subject form element.
	 */
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
	
	/**
	 * Create HTML output for the form element and send it to the client.
	 * @param array|NULL $options Options for building the document.
	 */
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

	/**
	 * Create a Html string for the form.
	 * @return string A Html string representation for the form element.
	 */
	public function __toString() {
		return $this
			->createDocument()
			->build()
			->transform()
			->saveHTML();
	}
	
	// Getters
	
	/**
	 * Get the form instance to which the form element belongs.
	 * @return Form Returns the form instance to which the form element belongs.
	 */
	public function getForm() {
		return $this->itsForm;
	}
	
	/**
	 * Get all children of the form element.
	 * @return array Returns an array of form elements.
	 */
	public function getChildren() {
		return (array) $this->itsChildren;
	}
	
	// Setters
	
	/**
	 * Set the children for the form element.
	 * @param array $children The new children for the form element.
	 * @return FormElement Returns the form element for method chaining.
	 */
	public function setChildren(array $children) {
		$this->itsChildren = $children; 

		return $this;
	}

	/**
	 * Set an attribute on the form element. If the attribute exists it will be 
	 * overwritten.
	 * @param string|array $name Either a name for the attribute to set or an 
	 * array of attributes. When a name is provided every even argument is 
	 * assumed to be an attribute name (with the first argument starting in 
	 * position 0) and every odd argument is assumed to be the value bound to 
	 * the preceding name. When an array is supplied all arguments are combined 
	 * in a single array and set as attributes.
	 * @param string|array|NULL $value Either a value if the $name argument is a 
	 * string or an optional extra attribute array if the $name argument is an
	 * array.
	 * @return FormElement Returns the form element for method chaining.
	 */
	public function setAttribute($name, $value = NULL) {
		$atts = is_array($name) ? Util::mergeArgs(func_get_args()) : XArray::remix(func_get_args());


		return $this->{self::PROPERTY_ATTRIBUTES}(array_merge($this->{self::PROPERTY_ATTRIBUTES}, (array) $atts));
	}

	/**
	 * Add a class definition to the form element. Every class definition will 
	 * only occur once.
	 * @param string $class The class definition to add to the form element. 
	 * Double class definitions are removed.
	 * @return FormElement Returns the form element for method chaining.
	 */
	public function addClass($class) {
		return $this->{self::PROPERTY_CLASS}(array_unique(Util::mergeArgs(array_merge($this->{self::PROPERTY_CLASS}, func_get_args()))));
	}
	
	// Static
	
	/**
	 * Create a new form element instance.
	 * @param Form $form The form instance to which the element belongs.
	 * @param array|NULL $properties Optional properties to set on the element.
	 * @param array|NULL $options Options to set on the element.
	 * @return FormElement Returns the newly created form element instance.
	 */
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