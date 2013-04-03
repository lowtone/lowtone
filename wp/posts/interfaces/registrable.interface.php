<?php
namespace lowtone\wp\posts\interfaces;
use lowtone\wp\interfaces\Registrable as Base;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\interfaces
 */
interface Registrable extends Base {
	
	const OPTION_POST_TYPE = "post_type",
		OPTION_LABEL = "label",
		OPTION_LABELS = "labels",
		OPTION_DESCRIPTION = "description",
		OPTION_PUBLIC = "public",
		OPTION_EXCLUDE_FROM_SEARCH = "exclude_from_search",
		OPTION_PUBLICLY_QUERYABLE = "publicly_queryable",
		OPTION_SHOW_UI = "show_ui",
		OPTION_SHOW_IN_NAV_MENUS = "show_in_nav_menus",
		OPTION_SHOW_IN_MENU = "show_in_menu",
		OPTION_SHOW_IN_ADMIN_BAR = "show_in_admin_bar",
		OPTION_MENU_POSITION = "menu_position",
		OPTION_MENU_ICON = "menu_icon",
		OPTION_CAPABILITY_TYPE = "capability_type",
		OPTION_CAPABILITIES = "capabilities",
		OPTION_MAP_META_CAP = "map_meta_cap",
		OPTION_HIERARCHICAL = "hierarchical",
		OPTION_SUPPORTS = "supports",
		OPTION_REGISTER_META_BOX_CB = "register_meta_box_cb",
		OPTION_TAXONOMIES = "taxonomies",
		OPTION_HAS_ARCHIVE = "has_archive",
		OPTION_PERMALINK_EPMASK = "permalink_epmask",
		OPTION_REWRITE = "rewrite",
		OPTION_QUERY_VAR = "query_var",
		OPTION_CAN_EXPORT = "can_export",
		OPTION_BUILTIN = "_builtin",
		OPTION_EDIT_LINK = "_edit_link";

	const OPTION_POST_CLASS = "post_class";

	const LABEL_NAME = "name",
		LABEL_SINGULAR_NAME = "singular_name",
		LABEL_ADD_NEW = "add_new",
		LABEL_ALL_ITEMS = "all_items",
		LABEL_ADD_NEW_ITEM = "add_new_item",
		LABEL_EDIT_ITEM = "edit_item",
		LABEL_NEW_ITEM = "new_item",
		LABEL_VIEW_ITEM = "view_item",
		LABEL_SEARCH_ITEMS = "search_items",
		LABEL_NOT_FOUND = "not_found",
		LABEL_NOT_FOUND_IN_TRASH = "not_found_in_trash",
		LABEL_PARENT_ITEM_COLON = "parent_item_colon",
		LABEL_MENU_NAME = "menu_name";
	
}