<?php
namespace lowtone\util\documentable\interfaces;
use lowtone\dom\Document;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\documentable\interfaces
 */
interface Documentable {
	
	/**
	 * Create a Document instance from the object.
	 * @return Document Returns a Document object to represent the Documentable
	 * object instance.
	 */
	public function __toDocument();

}