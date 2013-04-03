<?php
namespace lowtone\wp\terms\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\terms\Term;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\users\out
 */
class TermDocument extends ObjectDocument {
	
	public function __construct(Term $object) {
		parent::__construct($object);
		
		$this->updateBuildOptions(array(
			self::BUILD_ATTRIBUTES => array(
				Term::PROPERTY_TERM_ID
			),
			self::BUILD_ELEMENTS => array(
				Term::PROPERTY_NAME,
				Term::PROPERTY_SLUG,
				Term::PROPERTY_TERM_GROUP,
				Term::PROPERTY_TERM_TAXONOMY_ID,
				Term::PROPERTY_TAXONOMY,
				Term::PROPERTY_DESCRIPTION,
				Term::PROPERTY_PARENT,
				Term::PROPERTY_COUNT,
				Term::PROPERTY_OBJECT_ID,
				"permalink"
			)
		));
	}

	protected function extractProperties() {
		$properties = parent::extractProperties();

		$properties["permalink"] = get_term_link($this->itsObject);

		return $properties;
	}
	
}