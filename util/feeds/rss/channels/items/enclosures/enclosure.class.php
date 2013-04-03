<?php
namespace lowtone\util\feeds\rss\channels\items\enclosures;
use lowtone\db\records\Record,
	lowtone\types\objects\out\ObjectDocument;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\feeds\rss\channels\items\enclosures
 */
class Enclosure extends Record {

	const PROPERTY_URL = "url",
		PROPERTY_TYPE = "type",
		PROPERTY_LENGTH = "length";

	public function __toDocument() {
		$document = parent::__toDocument();

		$document->updateBuildOptions(array(
				ObjectDocument::BUILD_ATTRIBUTES => array(
						self::PROPERTY_URL,
						self::PROPERTY_TYPE,
						self::PROPERTY_LENGTH
					),
				ObjectDocument::BUILD_ELEMENTS => false
			));

		return $document;
	}

}