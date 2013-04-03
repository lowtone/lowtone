<?php
namespace lowtone\wp\taxonomies;
use lowtone\db\records\Record,
	lowtone\db\records\schemata\properties\Property,
	lowtone\db\records\schemata\properties\types\String;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\taxonomies
 */
class Taxonomy extends Record {

	const PROPERTY_HIERARCHICAL = "hierarchical",
		PROPERTY_UPDATE_COUNT_CALLBACK = "update_count_callback",
		PROPERTY_REWRITE = "rewrite",
		PROPERTY_QUERY_VAR = "query_var",
		PROPERTY_PUBLIC = "public",
		PROPERTY_SHOW_UI = "show_ui",
		PROPERTY_SHOW_TAGCLOUD = "show_tagcloud",
		PROPERTY__BUILTIN = "_builtin",
		PROPERTY_LABELS = "labels",
		PROPERTY_SHOW_IN_NAV_MENUS = "show_in_nav_menus",
		PROPERTY_CAP = "cap",
		PROPERTY_NAME = "name",
		PROPERTY_OBJECT_TYPE = "object_type",
		PROPERTY_LABEL = "label";

	const REWRITE_SLUG = "slug",
		REWRITE_WITH_FRONT = "with_front",
		REWRITE_HIERARCHICAL = "hierarchical";

	const LABEL_NAME = "name",
		LABEL_SINGULAR_NAME = "singular_name",
		LABEL_SEARCH_ITEMS = "search_items",
		LABEL_POPULAR_ITEMS = "popular_items",
		LABEL_ALL_ITEMS = "all_items",
		LABEL_PARENT_ITEM = "parent_item",
		LABEL_PARENT_ITEM_COLON = "parent_item_colon",
		LABEL_EDIT_ITEM = "edit_item",
		LABEL_VIEW_ITEM = "view_item",
		LABEL_UPDATE_ITEM = "update_item",
		LABEL_ADD_NEW_ITEM = "add_new_item",
		LABEL_NEW_ITEM_NAME = "new_item_name",
		LABEL_SEPARATE_ITEMS_WITH_COMMAS = "separate_items_with_commas",
		LABEL_ADD_OR_REMOVE_ITEMS = "add_or_remove_items",
		LABEL_CHOOSE_FROM_MOST_USED = "choose_from_most_used",
		LABEL_MENU_NAME = "menu_name",
		LABEL_NAME_ADMIN_BAR = "name_admin_bar";

	const CAP_MANAGE_TERMS = "manage_terms",
		CAP_EDIT_TERMS = "edit_terms",
		CAP_DELETE_TERMS = "delete_terms",
		CAP_ASSIGN_TERMS = "assign_terms";

	// Getters
	
	public function getName() {
		return $this->__get(self::PROPERTY_NAME);
	}

	public function getObjectType() {
		return $this->__get(self::PROPERTY_OBJECT_TYPE);
	}

	public function getLabels($find = NULL) {
		$labels = (array) $this->__get(self::PROPERTY_LABELS);

		if (is_array($find))
			$labels = array_intersect_key($labels, array_flip($find));
		else if (is_string($find))
			$labels = @$labels[$find];

		return $labels;
	}

	// Static
	
	public static function __getDocumentClass() {
		return "lowtone\\wp\\taxonomies\\out\\TaxonomyDocument";
	}

	public static function __createSchema($defaults = NULL) {
		$schema = parent::__createSchema($defaults);

		$schema[self::PROPERTY_UPDATE_COUNT_CALLBACK] = (array) new String(array(
				Property::ATTRIBUTE_LENGTH => 65535
			));

		return $schema;
	}

}