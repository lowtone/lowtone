<?php
namespace lowtone\db\records\schemata;
use lowtone\types\arrays\XArray,
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

	public function hasProperty($name) {
		return array_key_exists($name, (array) $this);
	}

	// Getters
	
	public function getPrimaryKeys() {
		return array_keys(array_filter((array) $this, function($property) {
			return in_array(Property::INDEX_PRIMARY_KEY, (array) @$property[Property::ATTRIBUTE_INDEXES]);
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
			$value = @$attributes[$attribute];

			if (is_callable($callback))
				$value = call_user_func($callback, $value);

			return $value;
		}, (array) $this);

		if ($property)
			return @$attributes[$property];

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
	 * Create a Schema object from a r
	 * @param  [type] $rc [description]
	 * @return [type]     [description]
	 */
	public static function fromReflection($rc, $defaults = NULL) {
		$properties = XArray::filterKeys(function($const) {
			return preg_match("/^PROPERTY_/i", $const);
		}, $rc->getConstants());

		$schema = new Schema($defaults);

		$schema->setRecordClass(get_called_class());

		$createConvertToDateTime = function($defaultFormat) {
			return function($val) use ($defaultFormat) {
				if (is_numeric($val)) $val = "@" . $val;
				return DateTime::createFromString($val)
					->setDefaultFormat($defaultFormat);
			};
		};

		foreach ($properties as $property) {
			$attributes = array();

			// Id

			if (preg_match("/[^[:alpha:]]id$/i", $property)) {
				$attributes = array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
					Property::ATTRIBUTE_LENGTH => 20 // BIGINT
				);

				if (!$schema->getPrimaryKeys()) 
					$attributes[Property::ATTRIBUTE_INDEXES] = array(Property::INDEX_PRIMARY_KEY);

			} 

			// Date and time

			else if (preg_match("/timestamp|datetime|date|time|created|changed/i", $property, $matches)) {
				switch ($matches[0]) {
					case "date":
						$attributes = array(
							Property::ATTRIBUTE_TYPE => Property::TYPE_DATE,
							Property::ATTRIBUTE_SET => $createConvertToDateTime("Y-m-d"),
							Property::ATTRIBUTE_UNSERIALIZE => $createConvertToDateTime("Y-m-d"),
							Property::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00"
						);
						break;

					case "time":
						$attributes = array(
							Property::ATTRIBUTE_TYPE => Property::TYPE_TIME,
							Property::ATTRIBUTE_SET => $createConvertToDateTime("H:i:s"),
							Property::ATTRIBUTE_UNSERIALIZE => $createConvertToDateTime("H:i:s"),
							Property::ATTRIBUTE_DEFAULT_VALUE => "00:00:00"
						);
						break;

					default:
						$attributes = array(
							Property::ATTRIBUTE_TYPE => Property::TYPE_DATETIME,
							Property::ATTRIBUTE_SET => $createConvertToDateTime("Y-m-d H:i:s"),
							Property::ATTRIBUTE_UNSERIALIZE => $createConvertToDateTime("Y-m-d H:i:s"),
							Property::ATTRIBUTE_DEFAULT_VALUE => "0000-00-00 00:00:00"
						);

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
				$attributes = array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_STRING,
					Property::ATTRIBUTE_LENGTH => 65535 // TEXT
				);
			}

			$schema[$property] = array_merge($attributes, (array) @$schema[$property]);
		}

		return $schema;
	}

}