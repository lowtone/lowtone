<?php
namespace lowtone\wp\taxonomies\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\taxonomies\Taxonomy;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\queries\out
 */
class TaxonomyDocument extends ObjectDocument {
	
	public function __construct(Taxonomy $object) {
		parent::__construct($object);

		$this->updateBuildOptions(array(
				self::BUILD_ELEMENTS => array(
						Taxonomy::PROPERTY_HIERARCHICAL,
						// Taxonomy::PROPERTY_UPDATE_COUNT_CALLBACK,
						// Taxonomy::PROPERTY_REWRITE,
						Taxonomy::PROPERTY_QUERY_VAR,
						Taxonomy::PROPERTY_PUBLIC,
						Taxonomy::PROPERTY_SHOW_UI,
						Taxonomy::PROPERTY_SHOW_TAGCLOUD,
						// Taxonomy::PROPERTY__BUILTIN,
						Taxonomy::PROPERTY_LABELS,
						Taxonomy::PROPERTY_SHOW_IN_NAV_MENUS,
						Taxonomy::PROPERTY_CAP,
						Taxonomy::PROPERTY_NAME,
						Taxonomy::PROPERTY_OBJECT_TYPE,
						Taxonomy::PROPERTY_LABEL,
					)
			));

		$this->setElementNameFilter(function($name, $document) {
			if (!is_numeric($name))
				return $name;

			$element = $document->createElement("type");
			
			$element->setAttribute("key", $name);

			return $element;
		});
		
	}

}