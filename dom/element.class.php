<?php
namespace lowtone\dom;
use DOMElement,
	DOMNode;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\dom
 */
class Element extends DOMElement implements interfaces\ElementHandler {
	
	/*public function appendChild(DOMNode $child, $parent = false) {
		if ($parent)
			return parent::appendChild($child);
			
		return handlers\ElementHandler::create($this)->appendChild($child);
	}*/
	
	/**
	 * Create and append a new element.
	 * @param string $name The name for the element.
	 * @param string|NULL $value The value for the element.
	 * @return Element Returns the element object for chaining.
	 */
	public function appendCreateElement($name, $value = NULL) {
		return handlers\ElementHandler::create($this)->appendCreateElement($name, $value);
	}
	
	/**
	 * Create and append a new element.
	 * @param string $name The name for the element.
	 * @param string|NULL $value The value for the element.
	 * @return Element Returns the newly appended element.
	 */
	public function createAppendElement($name, $value = NULL) {
		return handlers\ElementHandler::create($this)->createAppendElement($name, $value);
	}
	
	/**
	 * Add multiple child elements from the given array.
	 * @param array $children The elements to add as children.
	 * @param Element Returns the parent element.
	 */
	public function appendChildren(array $children) {
		return handlers\ElementHandler::create($this)->appendChildren($children);
	}
	
	public function appendCreateElements(array $values) {
		return handlers\ElementHandler::create($this)->appendCreateElements($values);
	}
	
	public function createAppendElements(array $values) {
		return handlers\ElementHandler::create($this)->createAppendElements($values);
	}
	
	/**
	 * Check the element's childnodes for DOMElements.
	 * @return bool Returns TRUE if a DOMElement is found or FALSE if not.
	 */
	public function hasChildElements() {
		foreach ($this->childNodes as $child) {
			
			if ($child instanceof DOMElement)
				return true;
				
		}
		
		return false;
	}

	// Getters
	
	public function getAttributes() {
		$attributes = array();

		foreach ($this->attributes as $attr)
			$attributes[$attr->name] = $attr->value;

		return $attributes;
	}
	
	// Setters
		
	/**
	 * Override the parent setAttribute method. This method removes the 
	 * attribute if value NULL is supplied.
	 * @see DOMElement::setAttribute()
	 */
	public function setAttribute($name, $value) {
		if (is_null($value))
			return $this->removeAttribute($name);
			
		return parent::setAttribute($name, (string) $value);
	}
	
	/**
	 * Set multiple attributes at once from the given array where the array's
	 * keys will be used for the attribute's names.
	 * @param array $attributes A list of attributes.
	 */
	public function setAttributes(array $attributes) {
		foreach ($attributes as $name => $value) 
			$this->setAttribute(strtolower($name), $value);
		
		return $this;
	}

	// Static
	
	public static function validateName($name) {
		return $name && !preg_match("/^([[:punct:][:digit:]]|xml)|[[:space:]]/i", $name);
	}

	public static function normalizeName($name, $template = "elm_%s") {
		return sprintf($template, md5($name));
	}
	
}