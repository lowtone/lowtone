<?php
namespace lowtone\db\records\schemata\properties\types;
use lowtone\db\records\schemata\properties\Property;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\schemata\properties\types
 */
class Enum extends Property {

	public function __construct($attributes = NULL) {
		parent::__construct(array_merge(array(
				self::ATTRIBUTE_TYPE => self::TYPE_ENUM,
				self::ATTRIBUTE_SERIALIZE => "serialize",
				self::ATTRIBUTE_UNSERIALIZE => "unserialize",
			), (array) $attributes));
	}

}