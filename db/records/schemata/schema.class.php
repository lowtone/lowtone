<?php
namespace lowtone\db\records\schemata;
use ReflectionClass,
	lowtone\types\arrays\XArray,
	lowtone\types\datetime\DateTime,
	lowtone\types\objects\Object,
	lowtone\db\records\schemata\properties\Property;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\schemata
 */
class Schema extends Object {

	protected $__itsRecordClass;

	public function hasProperty($name) {
		return isset($this[$name]);
	}

	// Getters
	
	public function getPrimaryKeys() {
		return array_keys(array_filter((array) $this, function($property) {
			$indexes = isset($property[Property::ATTRIBUTE_INDEXES]) ? (array) $property[Property::ATTRIBUTE_INDEXES] : array();

			return in_array(Property::INDEX_PRIMARY_KEY, $indexes);
		}));
	}

	public function getGetters($property = NULL) {
		return $this->getAttribute(Property::ATTRIBUTE_GET, $property);
	}

	public function getSetters($property = NULL) {
		return $this->getAttribute(Property::ATTRIBUTE_SET, $property);
	}

	public function getSerializers($property = NULL) {
		return $this->getAttribute(Property::ATTRIBUTE_SERIALIZE, $property);
	}

	public function getUnserializers($property = NULL) {
		return $this->getAttribute(Property::ATTRIBUTE_UNSERIALIZE, $property);
	}

	public function getDefaults($property = NULL) {
		return $this->getAttribute(Property::ATTRIBUTE_DEFAULT_VALUE, $property, function($val) {
			if (is_callable($val))
				$val = call_user_func($val);

			return $val;
		});
	}

	public function getRecordClass() {
		return $this->__itsRecordClass;
	}

	/**
	 * Get attribute values for all or a single property.
	 * @param string $attribute The attribute identifier for the required attribute.
	 * @param string $property An optional property identifier to get the attribute
	 * for that property.
	 * @return mixed Returns either an array of attributes or a single attribute
	 * if a property was defined.
	 */
	private function getAttribute($attribute, $property = NULL, $callback = NULL) {
		$attributes = array_map(function($attributes) use ($attribute, $callback) {
			$value = isset($attributes[$attribute]) ? $attributes[$attribute] : NULL;

			if (is_callable($callback))
				$value = call_user_func($callback, $value);

			return $value;
		}, (array) $this);

		if (isset($property))
			return isset($attributes[$property]) ? $attributes[$property] : NULL;

		return $attributes;
	}

	// Setters
	
	public function setPrimaryKey($property) {
		foreach ($this as $name => &$obj) 
			$obj->primaryKey($property == $name);
		
		return $this;
	}

	public function setRecordClass($class) {
		$this->__itsRecordClass = $class;

		return $this;
	}

	// Static
	
	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\SchemaDocument";
	}

	/**
	 * Create a Schema object from a ReflectionClass instance.
	 * @param ReflectionClass $rc The ReflectionClass instance to reverse 
	 * engineer a schema from.
	 * @param array|NULL $defaults Default property definitions.
	 * @return Schema Returns a Schema instance on success.
	 */
	public static function fromReflection(ReflectionClass $rc, $defaults = NULL) {
		$properties = XArray::filterKeys(function($const) {
			return preg_match("/^PROPERTY_/i", $const);
		}, $rc->getConstants());

		$defaults = array_merge(array_fill_keys((array) $properties, NULL), (array) $defaults);

		$schema = new Schema($defaults);

		$schema->setRecordClass(get_called_class());

		foreach ($properties as $property) {
			if (isset($schema[$property]))
				continue;

			// Id

			if (preg_match("/[^[:alpha:]]id$/i", $property)) {
				$attributes = new properties\types\Int(array(
					Property::ATTRIBUTE_LENGTH => properties\types\Int::LENGTH_BIG // BIGINT
				));

				if (!$schema->getPrimaryKeys()) 
					$attributes[Property::ATTRIBUTE_INDEXES] = array(Property::INDEX_PRIMARY_KEY);

			} 

			// Date and time

			else if (preg_match("/timestamp|datetime|date|time|created|changed/i", $property, $matches)) {
				switch ($matches[0]) {
					case "date":
						$attributes = new properties\types\Date();
						break;

					case "time":
						$attributes = new properties\types\Time();
						break;

					default:
						$attributes = new properties\types\DateTime();

						switch ($matches[0]) {
							case "created":
								$attributes[Property::ATTRIBUTE_DEFAULT_VALUE] = function() {
									return DateTime::now();
								};
								break;

							case "changed":
								$attributes[Property::ATTRIBUTE_ON_UPDATE] = function() {
									return DateTime::now();
								};
								break;
						}

				}
			} 

			// Default

			else {
				$attributes = new properties\types\String(array(
					Property::ATTRIBUTE_LENGTH => properties\types\String::LENGTH_DEFAULT // TEXT
				));
			}

			$schema[$property] = $attributes;
		}

		return $schema;
	}

	// Deprecated

	/**
	 * @deprecated Too complex.
	 * @return [type] [description]
	 */
	public function mergeSchemata() {
		$schemata = func_get_args();

		if (isset($this) && $this instanceof Schema)
			array_unshift($schemata, $this);

		$schema = array();

		foreach ($schemata as $overwrite) {

			foreach ((array) $overwrite as $property => $attributes) 
				$schema[$property] = array_merge((array) @$schema[$property], (array) $attributes);

		}

		return new Schema($schema);
	}
}