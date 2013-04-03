<?php 
namespace lowtone\wp\admin\menu;
use lowtone\util\properties\handlers\PropertyHandler;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\menu
 */
class Menu extends PropertyHandler {
	
	const KEY_MENUS = "menu";
	
	const PROPERTY_TITLE = 0,
		PROPERTY_CAPABILITY = 1,
		PROPERTY_SLUG = 2,
		PROPERTY_PAGE_TITLE = 3,
		PROPERTY_CLASS = 4,
		PROPERTY_HOOK_NAME = 5,
		PROPERTY_ICON_URL = 6;
		
	public function __construct($menu = NULL) {
		parent::__construct($menu);
	}
	
	// Getters
	
	public function getSubMenus() {
		return reset(SubMenu::getMenus(NULL, $this->getSlug()));
	}

	public function getTitle() {return $this->getProperty(self::PROPERTY_TITLE);}
	public function getCapability() {return $this->getProperty(self::PROPERTY_CAPABILITY);}
	public function getSlug() {return $this->getProperty(self::PROPERTY_SLUG);}
	public function getPageTitle() {return $this->getProperty(self::PROPERTY_PAGE_TITLE);}
	public function getClass() {return $this->getProperty(self::PROPERTY_CLASS);}
	public function getHookName() {return $this->getProperty(self::PROPERTY_HOOK_NAME);}
	public function getIconURL() {return $this->getProperty(self::PROPERTY_ICON_URL);}
		
	// Static
	
	protected static function toArrays(array $menus) {
		array_walk_recursive($menus, function($menu) {
			if ($menu instanceof Menu)
				$menu = $menu->getProperties();
				
			return $menu;
		});
		
		return $menus;
	}
	
	protected static function toObjects(array $menus) {
		/*if (is_array(reset(reset($menus))))
			return static::toObjects($menus);*/
			
		$class = get_called_class();
			
		return array_map(function($menu) use ($class) {
			return new $class($menu);
		}, $menus);
	}
	
	/**
	 * Filter the given menu set using the provided filter values.
	 * @param array $menus The subject menus.
	 * @param array $filter A list of array keys and values.
	 * @return array Returns the menus that match the values in the filter.
	 */
	protected static function filter(array $menus, array $filter) {
		return array_filter($menus, function($menu) use ($filter) {
			
			foreach ($filter as $key => $value) {
				
				if (@$menu[$key] != $value)
					return false;
				
			}
			
			return true;
			
		});
	}
	
	/**
	 * Merge multiple menus into one. Using array_merge on menus doesn't work 
	 * because of their numeric keys.
	 * @param array $menu,... One or more menus.
	 * @return array Returns the merged menu.
	 */
	public static function merge() {
		$new = array();
		
		foreach (func_get_args() as $menu) {
			
			foreach ($menu as $key => $val)
				$new[$key] = $val;
				
		}
		
		return $new;
	}
	
	/**
	 * Update a menu. The updated menus are returned but not updated! Use this 
	 * function with Menu::replace to actually replace the menus.
	 * @param array $filter The filter for the required menus.
	 * @param array $newValues The new values for the menus.
	 * @return array Returns a list of modified menus.
	 */
	public static function update(array $filter, array $newValues) {
		return array_map(function($menu) use ($newValues) {
			return Menu::merge($menu, $newValues);
		}, static::getMenus($filter));
	}
	
	/**
	 * Replace menus.
	 * @param array $replacements A list of menu replacements.
	 * @return array Returns the new menus.
	 */
	public static function replace(array $replacements) {
		return static::setMenus(static::merge(static::getMenus(), $replacements));
	}
	
	// Static getters
	
	/**
	 * Get the key for the global menu variable.
	 * @return string Returns the menu variable key.
	 */
	protected static function __getKey() {
		return static::KEY_MENUS;
	}
	
	/**
	 * Get a menu set.
	 * @param array|NULL $filter An optional menu filter.
	 * @return array Returns a set of menus matching the filter.
	 */
	public static function getMenus(array $filter = NULL) {
		$menus = (array) $GLOBALS[static::__getKey()];
		
		if (is_array($filter)) 
			$menus = static::filter($menus, $filter);
			
		return static::toObjects($menus);
	}
	
	// Static setters
	
	/**
	 * Set the menus.
	 * @param array $menus The new menus.
	 * @param array Returns the new menus.
	 */
	public static function setMenus(array $menus) {
		return ($GLOBALS[static::__getKey()] = $menus);
	}
	
}