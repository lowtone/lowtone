<?php
namespace lowtone\db\records\schemata\properties\types;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\schemata\properties\types
 */
class Date extends DateTime {

	public function __construct($attributes = NULL) {
		parent::__construct(array_merge(array(
				self::ATTRIBUTE_SET => $this->createConvertToDateTime("Y-m-d"),
				self::ATTRIBUTE_UNSERIALIZE => $this->createConvertToDateTime("Y-m-d"),
				self::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00"
			), (array) $attributes));
	}

}