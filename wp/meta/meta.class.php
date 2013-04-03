<?php
namespace lowtone\wp\meta;
use lowtone\db\records\Record,
	lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\meta
 */
class Meta extends Record implements interfaces\Meta {
		
	const PROPERTY_META_ID = "meta_id",
		PROPERTY_META_KEY = "meta_key",
		PROPERTY_META_VALUE = "meta_value";

	public function isPublic() {
		return !preg_match("/^_/", $this->getMetaKey());
	}

	public function __toString() {
		return $this->getMetaValue();
	}
	
	// Getters
	
	public function getMetaId() {return $this->__get(self::PROPERTY_META_ID);}
	public function getMetaKey() {return $this->__get(self::PROPERTY_META_KEY);}
	public function getMetaValue() {return $this->__get(self::PROPERTY_META_VALUE);}
	
	// Setters
	
	public function setMetaId($metaId) {return $this->__set(self::PROPERTY_META_ID, $metaId);}
	public function setMetaKey($key) {return $this->__set(self::PROPERTY_META_KEY, $key);}
	public function setMetaValue($value) {return $this->__set(self::PROPERTY_META_VALUE, $value);}

	// Static

	public static function __createSchema($defaults = NULL) {
		return Schema::mergeSchemata(array(
				self::PROPERTY_META_ID => array(
						Property::ATTRIBUTE_INDEXES => array(Property::INDEX_PRIMARY_KEY),
						Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
						Property::ATTRIBUTE_LENGTH => 20
					),
				self::PROPERTY_META_KEY => array(
						Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
						Property::ATTRIBUTE_LENGTH => 255
					),
				self::PROPERTY_META_VALUE => array(
						Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
						Property::ATTRIBUTE_LENGTH => 4294967295,
						Property::ATTRIBUTE_SERIALIZE => "maybe_serialize",
						Property::ATTRIBUTE_UNSERIALIZE => "maybe_unserialize"
					)
			), $defaults);
	}

	public static function __cast($properties) {
		if ($properties instanceof static)
			return $properties;

		return new static((array) $properties);
	}

	public static function __getCollectionClass() {
		return "lowtone\\wp\\meta\\collections\\Collection";
	}

	public static function __getObjectIdProperty() {
		return NULL;
	}

	// These functions loosely replicate WordPress's meta functionality.

	/**
	 * Add a new Meta value. Replicates add_metadata().
	 * @param [type]  $objectId [description]
	 * @param [type]  $key      [description]
	 * @param [type]  $value    [description]
	 * @param boolean $unique   [description]
	 */
	public static function addData($objectId, $key, $value, $unique = false) {
		$properties = array(
				self::PROPERTY_META_KEY => $key,
				self::PROPERTY_META_VALUE => $value
			);

		if ($objectIdProperty = static::__getObjectIdProperty())
			$properties[$objectIdProperty] = $objectId;

		if ($unique && static::find(array_diff_key($properties, array_flip(array(self::PROPERTY_META_VALUE))))->count() > 0)
			return false;

		static::save($properties);
	}

	/**
	 * A replacement for udpate_metadata().
	 * @param  [type] $objectId [description]
	 * @param  [type] $newVal   [description]
	 * @param  [type] $oldVal   [description]
	 * @return [type]           [description]
	 */
	public static function updateData($objectId, $key, $newVal, $oldVal = NULL) {
		$find = array(
				self::PROPERTY_META_KEY => $key,
			);

		if ($objectIdProperty = static::__getObjectIdProperty())
			$find[$objectIdProperty] = $objectId;

		if (!is_null($oldVal))
			$find[self::PROPERTY_META_VALUE] = $oldVal;

		$old = static::find($find);

		if ($old->count() < 1)
			$old[] = new static($find);

		return $old->save(
				array(
					self::PROPERTY_META_VALUE => $newVal
				),
				array(
					self::OPTION_OVERWRITE_WITH_DEFAULTS => true
				)
			);
	}

	public static function deleteData($objectId, $key, $value = NULL, $deleteAll = false) {
		// This function's logic is ridonkulous!!
	}
	
}