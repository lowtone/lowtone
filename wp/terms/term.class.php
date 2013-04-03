<?php
namespace lowtone\wp\terms;
use lowtone\db\records\Record;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\terms
 */
class Term extends Record {
	
	const PROPERTY_TERM_ID = "term_id",
		PROPERTY_NAME = "name",
		PROPERTY_SLUG = "slug",
		PROPERTY_TERM_GROUP = "term_group",
		PROPERTY_TERM_TAXONOMY_ID = "term_taxonomy_id",
		PROPERTY_TAXONOMY = "taxonomy",
		PROPERTY_DESCRIPTION = "description",
		PROPERTY_PARENT = "parent",
		PROPERTY_COUNT = "count",
		PROPERTY_OBJECT_ID = "object_id";

	public function getAncestors() {
		for ($term = $this, $ancestors = array(); $term = get_term($term->parent, $term->taxonomy);)
			$ancestors[] = new Term($term);

		return array_reverse($ancestors);
	}

	// Static

	public static function __getCollectionClass() {
		return "lowtone\\wp\\terms\\collections\\Collection";
	}

	public static function __getDocumentClass() {
		return "lowtone\\wp\\terms\\out\\TermDocument";
	}
	
}