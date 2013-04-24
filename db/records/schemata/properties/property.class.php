<?php
namespace lowtone\db\records\schemata\properties;
use lowtone\types\objects\Object;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\schemata\properties
 */
class Property extends Object {

	const ATTRIBUTE_NAME = "name",
		ATTRIBUTE_TYPE = "type",
		ATTRIBUTE_LENGTH = "length",
		ATTRIBUTE_NULL = "null",
		ATTRIBUTE_AUTO_INCREMENT = "auto_increment",
		ATTRIBUTE_DEFAULT_VALUE = "default_value",
		ATTRIBUTE_ON_UPDATE = "on_update",
		ATTRIBUTE_INDEXES = "indexes",
		ATTRIBUTE_GET = "get",
		ATTRIBUTE_SET = "set",
		ATTRIBUTE_SERIALIZE = "serialize",
		ATTRIBUTE_UNSERIALIZE = "unserialize";

	const ATTRIBUTE_COLUMN_DEFINITION = "column_definition";

	const TYPE_INT = "int",
		TYPE_STRING = "string",
		TYPE_DATETIME = "datetime",
		TYPE_DATE = "date",
		TYPE_TIME = "time";

	const INDEX_PRIMARY_KEY = "primary_key";

	public function primaryKey($isPrimaryKey = NULL) {
		$indexes = &$this[self::ATTRIBUTE_INDEXES];

		$indexes = (array) $indexes;

		if (is_null($isPrimaryKey))
			return in_array(self::INDEX_PRIMARY_KEY, $indexes);

		$indexes = $isPrimaryKey ? array_merge($indexes, array(self::INDEX_PRIMARY_KEY)) : array_diff($indexes, array(self::INDEX_PRIMARY_KEY));

		return $this;
	}

	// Static
	
	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\PropertyDocument";
	}

}