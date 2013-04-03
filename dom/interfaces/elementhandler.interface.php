<?php
namespace lowtone\dom\interfaces;
use DOMNode,
	lowtone\dom\handlers\ElementHandler as EH;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\dom\interfaces
 */
interface ElementHandler {
	
	/**
	 * @see EH::createElement()
	 */
	//public function createElement($name, $value = NULL);
	
	/**
	 * @see EH::appendChild()
	 */
	public function appendChild(DOMNode $child);
	
	/**
	 * @see EH::appendCreateElement()
	 */
	public function appendCreateElement($name, $value = NULL);
	
	/**
	 * @see EH::createAppendElement()
	 */
	public function createAppendElement($name, $value = NULL);
	
	/**
	 * @see EH::createElements()
	 */
	//public function createElements(array $values);
	
	/**
	 * @see EH::appendChildren()
	 */
	public function appendChildren(array $children);
	
	/**
	 * @see EH::appendCreateElements()
	 */
	public function appendCreateElements(array $values);
	
	/**
	 * @see EH::createAppendElements()
	 */
	public function createAppendElements(array $values);
	
}