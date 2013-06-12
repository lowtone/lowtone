<?php
namespace lowtone\dom\handlers;
use DOMNode,
	DOMDocument,
	lowtone\util\buildables\interfaces\Buildable,
	lowtone\util\documentable\interfaces\Documentable;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\dom\handlers
 */
class ElementHandler {
	
	/**
	 * @var DOMNode
	 */
	protected $itsContext;
	
	/**
	 * @var DOMDocument
	 */
	protected $itsDocument;
	
	public function __construct(DOMNode &$context) {
		$this->itsContext = $context;
		
		$this->itsDocument = $this->itsContext instanceof DOMDocument ? $this->itsContext : $this->itsContext->ownerDocument;
		
	}
	
	/**
	 * Override DOMDocument::createElement(). Add automatic value 
	 * type handling.
	 * @param string $name The name for the new element.
	 * @param string|array|NULL An optional value for the new element.
	 * If an array is provided child nodes are created and appended
	 * from the array, otherwise if a non-NULL value is provided it's
	 * appended as a DOMTextNode.
	 * @return Element Returns the new Element instance.
	 */
	public function createElement($name, $value = NULL) {
		return $this->itsDocument->createElement($name, $value);
	}
	
	public function appendChild(DOMNode $child) {
		return $this->itsContext->appendChild($child);
	}
	
	/**
	 * Shorthand to create and append a new element.
	 * @param string $name The name for the new element.
	 * @param string|array|NULL the value for the new element.
	 * @return Document Returns the document for chaining.
	 */
	public function appendCreateElement($name, $value = NULL) {
		$this->appendChild($this->createElement($name, $value));

		return $this;
	}

	/**
	 * Shorthand to create and append a new element.
	 * @param string $name The name for the new element.
	 * @param string|array|NULL the value for the new element.
	 * @return Element Returns the new Element instance.
	 */
	public function createAppendElement($name, $value = NULL) {
		return $this->appendChild($this->createElement($name, $value));
	}
	
	// Multiple elements

	/**
	 * Create multiple elements from an array.
	 * @param array $values The subject values. The array keys are 
	 * used for element names and array values for element values.
	 * @return array Returns an array of new Element instances (similar
	 * to Document::createElement() which returns the new Element 
	 * instance).
	 */
	public function createElements(array $values) {
		for ($elements = array(); list($name, $value) = each($values);) {
			if ($value instanceof Documentable) {
				$value = $value->createDocument();

				if ($value instanceof Buildable) 
					$value->build();
				
			}

			if ($value instanceof DOMDocument)
				$value = $this->itsDocument->importDocument($value);

			if (!($value instanceof DOMNode))
				$value = $this->createElement($name, $value);
			
			if ($value)
				$elements[] = $value;
			
		}
		
		return $elements;
	}

	/**
	 * Append multiple elements.
	 * @param array An array of multiple Element instances.
	 * @return array Returns the appended Elements (similar to 
	 * Document::appendChild() which returns the appended element).
	 */
	public function appendChildren(array $children) {
		foreach ($children as $child)
			$this->appendChild($child);
		
		return $children;
	}

	/**
	 * Shorthand to create and append multiple elements.
	 * @param array $values The subject values.
	 * @return Document Returns the document for chaining.
	 */
	public function appendCreateElements(array $values) {
		$this->appendChildren($this->createElements($values));
		
		return $this;
	}

	/**
	 * Shorthand to create and append multiple elements.
	 * @param array $values The subject values.
	 * @return array Returns the appended elements (who needs this?).
	 */
	public function createAppendElements(array $values) {
		return $this->appendChildren($this->createElements($values));
	}
	
	// Static
	
	public static function create(DOMNode $context) {
		return new self($context);
	}
	
}