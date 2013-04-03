<?php
namespace lowtone\db\records\schemata\properties\types;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\schemata\properties\types
 */
class Time extends DateTime {

	public function __construct($attributes = NULL) {
		parent::__construct(array_merge(array(
				self::ATTRIBUTE_SET => $this->createConvertToDateTime("H:i:s"),
				self::ATTRIBUTE_UNSERIALIZE => $this->createConvertToDateTime("H:i:s"),
				self::ATTRIBUTE_DEFAULT_VALUE => "00:00:00"
			), (array) $attributes));
	}

}