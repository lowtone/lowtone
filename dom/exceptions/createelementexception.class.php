<?php
namespace lowtone\dom\exceptions;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\dom\exceptions
 */
class CreateElementException extends Exception {

	private $messageBase;
	
	protected $elementName,
		$elementValue;

	public function __construct ($message = "", $code = 0, \Exception $previous = NULL) {
		parent::__construct($message, $code, $previous);

		$this->messageBase = $this->message;

	}

	// Getters
	
	public function getElementName() {
		return $this->elementName;
	}	

	public function getElementValue() {
		return $this->elementValue;
	}

	// Setters

	public function setElementName($elementName) {
		$this->elementName = $elementName;

		// Update the message

		$message = $this->messageBase;

		if (5 == $this->code) {

			// Identify the character type

			$type = gettype($this->elementName);

			if (preg_match("/\s/", (string) $this->elementName))
				$type = "whitespace";
			else if (is_numeric($this->elementName) || preg_match("/^\d/", (string) $this->elementName))
				$type = "numeric";

			$message = sprintf("%s (%s)", $this->messageBase, $type);

		}

		$this->message = $message;

		return $this;
	}

	public function setElementValue($elementValue) {
		$this->elementValue = $elementValue;

		return $this;
	}

}