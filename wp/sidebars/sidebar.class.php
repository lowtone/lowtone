<?php
namespace lowtone\wp\sidebars;
use lowtone\types\objects\Object;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\sidebars
 */
class Sidebar extends Object {
		
	const PROPERTY_NAME = "name",
		PROPERTY_ID = "id",
		PROPERTY_DESCRIPTION = "description",
		PROPERTY_CLASS = "class",
		PROPERTY_BEFORE_WIDGET = "before_widget",
		PROPERTY_AFTER_WIDGET = "after_widget",
		PROPERTY_BEFORE_TITLE = "before_title",
		PROPERTY_AFTER_TITLE = "after_title";
	
	// Getters
	
	public function getName() {return $this->__get(self::PROPERTY_NAME);}
	public function getId() {return $this->__get(self::PROPERTY_ID);}
	public function getDescription() {return $this->__get(self::PROPERTY_DESCRIPTION);}
	public function getClass() {return $this->__get(self::PROPERTY_CLASS);}
	public function getBeforeWidget() {return $this->__get(self::PROPERTY_BEFORE_WIDGET);}
	public function getAfterWdiget() {return $this->__get(self::PROPERTY_AFTER_WIDGET);}
	public function getBeforeTitle() {return $this->__get(self::PROPERTY_BEFORE_TITLE);}
	public function getAfterTitle() {return $this->__get(self::PROPERTY_AFTER_TITLE);}
	
}