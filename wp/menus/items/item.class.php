<?php
namespace lowtone\wp\menus\items;
use lowtone\types\objects\Object,
	lowtone\wp\posts\Post;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\menus\items
 */
class Item extends Object {
	
	/**
	 * @var Post
	 */
	protected $itsSubject;
	
	/**
	 * @var array
	 */
	protected $itsChildren;
	
	const PROPERTY_DB_ID = "db_id",
		PROPERTY_MENU_ITEM_PARENT = "menu_item_parent",
		PROPERTY_OBJECT_ID = "object_id",
		PROPERTY_OBJECT = "object",
		PROPERTY_TYPE = "type",
		PROPERTY_LABEL = "type_label",
		PROPERTY_URL = "url",
		PROPERTY_TITLE = "title",
		PROPERTY_TARGET = "target",
		PROPERTY_ATTR_TITLE = "attr_title",
		PROPERTY_DESCRIPTION = "description",
		PROPERTY_CLASSES = "classes",
		PROPERTY_XFN = "xfn";
	
	public function __construct($item = NULL) {
		parent::__construct($item);
	}
	
	public function addChild(Item $item) {$this->itsChildren[] = $item; return $this;}
	
	// Getters
	
	public function getDbId() {return $this->__get(self::PROPERTY_DB_ID);}
	public function getMenuItemParent() {return $this->__get(self::PROPERTY_MENU_ITEM_PARENT);}
	public function getObjectId() {return $this->__get(self::PROPERTY_OBJECT_ID);}
	public function getObject() {return $this->__get(self::PROPERTY_OBJECT);}
	public function getType() {return $this->__get(self::PROPERTY_TYPE);}
	public function getLabel() {return $this->__get(self::PROPERTY_LABEL);}
	public function getURL() {return $this->__get(self::PROPERTY_URL);}
	public function getTitle() {return $this->__get(self::PROPERTY_TITLE);}
	public function getTarget() {return $this->__get(self::PROPERTY_TARGET);}
	public function getAttrTitle() {return $this->__get(self::PROPERTY_ATTR_TITLE);}
	public function getDescription() {return $this->__get(self::PROPERTY_DESCRIPTION);}
	public function getClasses() {return $this->__get(self::PROPERTY_CLASSES);}
	public function getXfn() {return $this->__get(self::PROPERTY_XFN);}
	
	public function getSubject() {return $this->itsSubject;}
	
	public function getChildren() {return $this->itsChildren;}
	
	// Setters
	
	public function setDbId($dbId) {return $this->__set(self::PROPERTY_DB_ID, $dbId);}
	public function setMenuItemParent($menuItemParent) {return $this->__set(self::PROPERTY_MENU_ITEM_PARENT, $menuItemParent);}
	public function setObjectId($objectId) {return $this->__set(self::PROPERTY_OBJECT_ID, $objectId);}
	public function setObject($object) {return $this->__set(self::PROPERTY_OBJECT, $object);}
	public function setType($type) {return $this->__set(self::PROPERTY_TYPE, $type);}
	public function setLabel($label) {return $this->__set(self::PROPERTY_LABEL, $label);}
	public function setURL($url) {return $this->__set(self::PROPERTY_URL, $url);}
	public function setTitle($title) {return $this->__set(self::PROPERTY_TITLE, $title);}
	public function setTarset($target) {return $this->__set(self::PROPERTY_TARGET, $target);}
	public function setAttrTitle($attrTitle) {return $this->__set(self::PROPERTY_ATTR_TITLE, $attrTitle);}
	public function setDescription($description) {return $this->__set(self::PROPERTY_DESCRIPTION, $description);}
	public function setClasses($classes) {return $this->__set(self::PROPERTY_CLASSES, $classes);}
	public function setXfn($xfn) {return $this->__set(self::PROPERTY_XFN, $xfn);}
	
	public function setSubject($subject) {$this->itsSubject = $subject; return $this;}
	
	public function setChildren($children) {$this->itsChildren = $children;}
	
}