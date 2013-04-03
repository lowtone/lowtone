<?php 
namespace lowtone\wp\admin\menu;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\menu
 */
class SubMenu extends Menu {
	
	const KEY_SUB_MENUS = "submenu";
	
	const PROPERTY_TITLE = 0,
		PROPERTY_CAPABILITY = 1,
		PROPERTY_SLUG = 2,
		PROPERTY_PAGE_TITLE = 3;
	
	// Static setters
	
	protected static function __getKey() {
		return static::KEY_SUB_MENUS;
	}
	
	public static function getMenus(array $filter = NULL, $parents = NULL) {
		$subMenus = (array) $GLOBALS[static::__getKey()];
		
		if (!is_null($parents)) 
			$subMenus = array_intersect_key($subMenus, array_flip((array) $parents));
			
		if (is_array($filter)) {
			
			$subMenus = array_map(function($menu) use ($filter) {
				return SubMenu::filter($menu, $filter);
			}, $subMenus);
			
		
		}
			
		return static::toObjects($subMenus);
	}
	
}