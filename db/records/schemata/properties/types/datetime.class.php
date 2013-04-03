<?php
namespace lowtone\db\records\schemata\properties\types;
use lowtone\db\records\schemata\properties\Property,
	lowtone\types\datetime\DateTime as DT;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\schemata\properties\types
 */
class DateTime extends Property {

	public function __construct($attributes = NULL) {
		parent::__construct(array_merge(array(
				self::ATTRIBUTE_TYPE => self::TYPE_DATETIME,
				self::ATTRIBUTE_SET => $this->createConvertToDateTime("Y-m-d H:i:s"),
				self::ATTRIBUTE_UNSERIALIZE => $this->createConvertToDateTime("Y-m-d H:i:s"),
				self::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00 00:00:00"
			), (array) $attributes));
	}

	protected function createConvertToDateTime($defaultFormat) {
		return function($val) use ($defaultFormat) {
			$val = is_numeric($val) ? DT::fromUnix($val) : DT::fromString($val);
			return $val->setDefaultFormat($defaultFormat);
		};
	}

}