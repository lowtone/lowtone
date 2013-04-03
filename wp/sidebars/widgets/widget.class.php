<?php
namespace lowtone\wp\sidebars\widgets;
use lowtone\types\objects\Object;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\sidebars\widgets
 */
class Widget extends Object {
		
	const PROPERTY_NAME = "name",
		PROPERTY_ID = "id",
		PROPERTY_CALLBACK = "callback",
		PROPERTY_PARAMS = "params",
		PROPERTY_CLASSNAME = "classname",
		PROPERTY_DESCRIPTION = "description";
	
	// Getters
	
	public function getName() {return $this->__get(self::PROPERTY_NAME);}
	public function getId() {return $this->__get(self::PROPERTY_ID);}
	public function getCallback() {return $this->__get(self::PROPERTY_CALLBACK);}
	public function getParams() {return $this->__get(self::PROPERTY_PARAMS);}
	public function getClassname() {return $this->__get(self::PROPERTY_CLASSNAME);}
	public function getDescription() {return $this->__get(self::PROPERTY_DESCRIPTION);}
	
}