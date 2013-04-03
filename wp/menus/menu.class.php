<?php
namespace lowtone\wp\menus;
use lowtone\wp\terms\Term,
	lowtone\wp\posts\Post,
	lowtone\wp\pages\Page,
	lowtone\wp\categories\Category;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\menus
 */
class Menu extends Term {
	
	/**
	 * @var array
	 */
	protected $itsItems;
	
	const PROPERTY_ITEMS = "items",
		PROPERTY_LOCATION = "location";
	
	// Getters
	
	public function getItems() {return $this->__get(self::PROPERTY_ITEMS);}
	public function getLocation() {return $this->__get(self::PROPERTY_LOCATION);}
	
	// Setters
	
	public function setItems(array $items) {return $this->__set(self::PROPERTY_ITEMS, $items);}
	public function setLocation($location) {return $this->__set(self::PROPERTY_LOCATION, $location);}
	
	// Static
	
	public static function loadMenus() {
		$menus = array();
		
		foreach(get_nav_menu_locations() as $location => $id) {
			if (($menu = wp_get_nav_menu_object($id)) === false)
				continue;
			
			$menu = new Menu($menu);
			
			$menu->setLocation($location);
			
			if (($items = wp_get_nav_menu_items($id)) !== false) {
				
				$itemObjs = array();
				
				foreach ($items as $item) {
					$itemObj = new items\Item($item);
					
					switch ($itemObj->getObject()) {
						case "page":
							$itemObj->setSubject(new Page($item));
							break;
			
						case "category":
							$itemObj->setSubject(new Category($item));
							break;
			
						default:
							$itemObj->setSubject(new Post($item));
							
					}
					
					if (!($parent = $itemObj->getMenuItemParent())) 
						$itemObjs[$itemObj->getDbId()] = $itemObj;
					
					else if ($parentObj = $itemObjs[$parent])
						$parentObj->addChild($itemObj);
					
				}
				
				$menu->setItems($itemObjs);
				
			}
			
			$menus[$id] = $menu;
		}
		
		return $menus;
	}
	
}