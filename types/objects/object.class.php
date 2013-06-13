<?php
namespace lowtone\types\objects;
use lowtone\types\arrays\XArray,
	lowtone\util\documentable\interfaces\Documentable;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\objects
 */
class Object extends XArray implements Documentable {

	const OPTION_CLASS = "class";

	// Property access

	public function __get($name) {
		return isset($this[$name]) ? $this[$name] : NULL;
	}

	public function __set($name, $value) {
		$this[$name] = $value;

		return $this;
	}

	public function __call($name, $arguments) {
		return isset($arguments[0]) ? $this->__set($name, $arguments[0]) : $this->__get($name);
	}

	public function __invoke($properties = NULL) {
		if (func_num_args() < 1)
			return $this;

		if (!is_array($properties))
			$properties = self::remix(func_get_args());

		foreach ($properties as $name => $value)
			$this->__set($name, $value);

		return $this;
	}

	// Property & key modification
	
	/**
	 * Apply a set of filters to property values.
	 * @param array $filters The filters for each property. Use the property 
	 * identifiers as keys and use arrays to define multiple filters for a 
	 * single property.
	 * @param array|NULL $args Optional additional parameters for the filters.
	 * @param array|NULL $properties The subject properties. If no properties 
	 * are provided the properties will be taken from the current context.
	 * @return array Returns the filtered properties.
	 */
	public function filterProperties(array $filters, array $args = NULL, $properties = NULL) {
		$properties = (array) (!is_null($properties) ? $properties : $this);
		$keys = array_keys($properties);

		$object = $this; // Object::applyFilters() needs to be called from object context to provide the object as a parameter.
		
		return array_combine($keys, array_map(function($property, $key) use ($filters, $args, $object) {
			if (isset($filters[$key]))
				$property = $object->applyFilters($property, $filters[$key], $args);

			return $property;
		}, $properties, $keys));
	}

	/**
	 * Apply the given filter callbacks to the given value.
	 * @param mixed $value The subject value.
	 * @param mixed $filters One or more callbacks.
	 * @param array|NULL $args Optional additional parameters for the filters.
	 * @return mixed Returns the filtered value.
	 */
	public function applyFilters($value, $filters, array $args = NULL) {
		$basicArgs = isset($this) && $this instanceof Object ? array(&$value, $this) : array(&$value);
		$args = array_merge($basicArgs, (array) $args);
		
		if (is_callable($filters)) 
			$filters = array($filters);

		foreach ((array) $filters as $filter) {
			if (!is_callable($filter)) {
				trigger_error("Non-callable filter provided", E_USER_NOTICE);
				
				continue;
			}
			
			$value = call_user_func_array($filter, $args);
		}
		
		return $value;
	}
	
	/**
	 * Remove the given prefix from the property identifiers.
	 * @param string $prefix The prefix to be removed.
	 * @param array $properties|NULL The subject properties. If no properties 
	 * are provided the properties will be taken from the current context.
	 * @return array Returns the properties with modified keys.
	 */
	public function stripPropertyPrefix($prefix, $properties = NULL) {
		$properties = (array) (!is_null($properties) ? $properties : $this);
		
		return array_combine(array_map(function($property) use ($prefix) {
			return strtolower(preg_replace("/^" . preg_quote($prefix, "/") . "/", "", $property));
		}, array_keys($properties)), $properties);
	}

	public function __toString() {
		return (string) $this
			->__toDocument()
			->build();
	}

	// Output

	public function createDocument() {
		return $this->__toDocument();
	}

	// Exports
	
	public function __toCollection() {
		$class = static::__getCollectionClass();

		return new $class(array($this));
	}
	
	public function __toDocument() {
		$class = static::__getDocumentClass();

		return new $class($this);
	}
	
	public function __toJson() {
		return json_encode($this);
	}
	
	public function __getClass() {
		return get_called_class();
	}

	// Static

	/**
	 * Create a new Object instance.
	 * @param array|NULL $properties The properties for the new object.
	 * @return Object Returns a new Object instance.
	 */
	public static function create($properties = NULL, array $options = NULL) {
		$class = isset($options[self::OPTION_CLASS]) ? $options[self::OPTION_CLASS] : get_called_class();

		return new $class($properties);
	}
	
	public static function __cast($object) {
		if ($object instanceof static)
			return $object;

		return new static($object);
	}
	
	public static function __getDocumentClass() {
		return "lowtone\\types\\objects\\out\\ObjectDocument";
	}

	public static function __getCollectionClass() {
		return "lowtone\\types\\objects\\collections\\Collection";
	}

	public static function __createCollection($objects = NULL) {
		$class = static::__getCollectionClass();

		$collection = new $class();

		$collection->setObjectClass(get_called_class());

		$collection->exchangeArray($objects);

		return $collection;
	}
	
}