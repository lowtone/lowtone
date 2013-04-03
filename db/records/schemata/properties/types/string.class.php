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
class String extends Property {

	const LENGTH_DEFAULT = 65535,
		LENGTH_SHORT = 0,
		LENGTH_LONG = 4294967295;

	public function __construct($attributes = NULL) {
		parent::__construct(array_merge(array(
				self::ATTRIBUTE_TYPE => self::TYPE_STRING
			), (array) $attributes));
	}

}