<?php
namespace lowtone\io\logging\entries;
use lowtone\db\records\Record,
	lowtone\db\records\schemata\properties\Property;

class Entry extends Record {

	const TABLE = "lowtone_log";
	
	const PROPERTY_LOG_ID = "log_id",
		PROPERTY_USER_ID = "user_id",
		PROPERTY_TIMESTAMP = "timestamp",
		PROPERTY_DOMAIN = "domain",
		PROPERTY_CODE = "code",
		PROPERTY_MESSAGE = "message";

	const VERSION = "1.0";

	public function __toString() {
		return vsprintf(
			"%s (%s) %s", 
			array(
				$this->__get(self::PROPERTY_TIMESTAMP), 
				$this->__get(self::PROPERTY_DOMAIN), 
				$this->__get(self::PROPERTY_MESSAGE)
			)
		) . PHP_EOL;
	}

	// Static
	
	public static function __createSchema($defaults = NULL) {
		return parent::__createSchema(array(
				self::PROPERTY_TIMESTAMP => array(
						Property::ATTRIBUTE_DEFAULT_VALUE => ""
					)
			));
	}
}