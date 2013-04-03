<?php
namespace lowtone\wp\posts\types;
use lowtone\db\records\Record;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\posts\types
 */
class Type extends Record {

	const PROPERTY_LABELS = "labels",
		PROPERTY_DESCRIPTION = "description",
		PROPERTY_PUBLICLY_QUERYABLE = "publicly_queryable",
		PROPERTY_EXCLUDE_FROM_SEARCH = "exclude_from_search",
		PROPERTY_CAPABILITY_TYPE = "capability_type",
		PROPERTY_MAP_META_CAP = "map_meta_cap",
		PROPERTY__BUILTIN = "_builtin",
		PROPERTY__EDIT_LINK = "_edit_link",
		PROPERTY_HIERARCHICAL = "hierarchical",
		PROPERTY_PUBLIC = "public",
		PROPERTY_REWRITE = "rewrite",
		PROPERTY_HAS_ARCHIVE = "has_archive",
		PROPERTY_QUERY_VAR = "query_var",
		PROPERTY_SUPPORTS = "supports",
		PROPERTY_REGISTER_META_BOX_CB = "register_meta_box_cb",
		PROPERTY_TAXONOMIES = "taxonomies",
		PROPERTY_SHOW_UI = "show_ui",
		PROPERTY_MENU_POSITION = "menu_position",
		PROPERTY_MENU_ICON = "menu_icon",
		PROPERTY_CAN_EXPORT = "can_export",
		PROPERTY_SHOW_IN_NAV_MENUS = "show_in_nav_menus",
		PROPERTY_SHOW_IN_MENU = "show_in_menu",
		PROPERTY_SHOW_IN_ADMIN_BAR = "show_in_admin_bar",
		PROPERTY_DELETE_WITH_USER = "delete_with_user",
		PROPERTY_NAME = "name",
		PROPERTY_CAP = "cap",
		PROPERTY_LABEL = "label";

	const LABEL_NAME = "name",
		LABEL_SINGULAR_NAME = "singular_name",
		LABEL_ADD_NEW = "add_new",
		LABEL_ADD_NEW_ITEM = "add_new_item",
		LABEL_EDIT_ITEM = "edit_item",
		LABEL_NEW_ITEM = "new_item",
		LABEL_VIEW_ITEM = "view_item",
		LABEL_SEARCH_ITEMS = "search_items",
		LABEL_NOT_FOUND = "not_found",
		LABEL_NOT_FOUND_IN_TRASH = "not_found_in_trash",
		LABEL_PARENT_ITEM_COLON = "parent_item_colon",
		LABEL_ALL_ITEMS = "all_items",
		LABEL_MENU_NAME = "menu_name",
		LABEL_NAME_ADMIN_BAR = "name_admin_bar";
	
	public function getName() {
		return $this->__get(self::PROPERTY_NAME);
	}

	public function getLabels($find = NULL) {
		$labels = (array) $this->__get(self::PROPERTY_LABELS);

		if (is_array($find))
			$labels = array_intersect_key($labels, array_flip($find));
		else if (is_string($find))
			$labels = @$labels[$find];

		return $labels;
	}

}