<?php
namespace lowtone\types\arrays;
use ArrayObject,
	ReflectionClass;

/**
 * @todo Figure out return values.
 * 
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\types\arrays
 */
class XArray extends ArrayObject {

	const INSTANCE_NEW = 1,
		INSTANCE_THIS = 2;

	public function __construct($input = NULL, $flags = 0, $iterator_class = "ArrayIterator") {
		parent::__construct(array(), $flags, $iterator_class);

		$this->exchangeArray((array) $input); // Set properties using XArray::exchangeArray()
	}

	// Iteration
	
	/**
	 * Recursivly walk through the array and apply a callback to each value.
	 * @param function $callback The callback applied to the values. The 
	 * callback takes on two parameters. The first being the current value, the
	 * second being the path for the value.
	 * @param int $maxDepth The maximum numbers of levels to walk through.
	 * @param array|NULL $array The subject array. If the function was called 
	 * on an object this defaults to the object's array. 
	 * @param array|NULL $path The current path.
	 * @return array|bool Returns the resulting array on success or FALSE on 
	 * failure.
	 */
	public function walk($callback, $maxDepth = -1, array $array = NULL, array $path = NULL) {
		if (!is_array($array) && NULL === ($array = self::__instance(self::INSTANCE_THIS)))
			return false;

		$array = (array) $array;

		$path = new XArray($path);

		$extendPath = function($key) use ($path) {
			$p = clone $path;

			return $p->push($key);
		};
		
		foreach ($array as $key => &$val) {
			$curPath = $extendPath($key);
			$curDepth = count($curPath);
			
			if (is_array($val) && ($maxDepth < 1 || $curDepth < $maxDepth))
				$val = self::walk($callback, $maxDepth, $val, $curPath);
			else
				$val = call_user_func($callback, $val, $curPath);	
			
		}
		
		return new static($array);
	}
	
	/**
	 * Recursivly filter the array using a callback.
	 * @param function $callback The callback applied to the values. The 
	 * callback takes on two parameters. The first being the current value, the
	 * second being the path for the value. An entry is removed if the callback
	 * returns FALSE for its value. If no callback is supplied a default 
	 * function is applied that evaluates the value as a boolean.
	 * @param int $maxDepth The maximum depth the filter is applied to.
	 * @param array|NULL $array The subject array. If the function was called 
	 * on an object this defaults to the object's array. 
	 * @return array|bool Returns the resulting array on success or FALSE on 
	 * failure.
	 */
	public function filter($callback = NULL, $maxDepth = -1, array $array = NULL) {
		if (!is_callable($callback))
			$callback = function($value) {return (bool) $value;};
		
		$result = new Map();
		
		$filter = function($value, $path) use (&$result, $callback) {
			if (!call_user_func($callback, $value, $path)) 
				return;
			
			$result->path((array) $path, $value);
		};
		
		if (false === self::walk($filter, $maxDepth, $array))
			return false;
			
		return new static((array) $result);
	}

	public function filterKeys($callback = NULL, array $array = NULL) {
		if (!is_array($array) && NULL === ($array = self::__instance(self::INSTANCE_THIS)))
			return false;

		$array = (array) $array;
		
		if (!is_callable($callback))
			$callback = function($value) {return (bool) $value;};

		return new static(array_intersect_key($array, array_flip(array_filter(array_keys($array), $callback))));
	}

	public function map($callback) {
		$input = array_slice(func_get_args(), 1);
		
		if (NULL !== ($instance = self::__instance(self::INSTANCE_THIS))) 
			array_unshift($input, (array) $instance);
		
		array_unshift($input, $callback);
		
		return $instance->__getClone(call_user_func_array("array_map", $input));
	}
	
	public function mapKeys($callback, $input = NULL) {
		if (!is_array($input) && NULL === ($input = self::__instance(self::INSTANCE_THIS)))
			return false;

		$input = (array) $input;
		
		return new static(array_combine(array_map($callback, array_keys($input)), $input));
	}

	// Modification
	
	/**
	 * Push one or more elements onto the end of array.
	 * @param array $array The input array.
	 * @param mixed $value,... The value to push.
	 * @return array|bool Returns the modified array on success or FALSE if no
	 * values where supplied.
	 */
	public function push() {
		$values = func_get_args();

		$array = ($instance = self::__instance(self::INSTANCE_THIS)) instanceof XArray ? (array) $instance : array_shift($values);
			
		if (!is_array($array))
			return false;
		
		foreach ($values as $value)
			array_push($array, $value);
		
		return new static($array);
	}
	
	/**
	 * Pop the element off the end of array.
	 * @param mixed &$value A reference to a variable that will hold the removed
	 * value.
	 * @param array $array The input array.
	 * @return XArray|bool Returns the modified array on success or FALSE if no
	 * values where supplied.
	 */
	public function pop(&$value = NULL, array $array = NULL) {
		if (!is_array($array) && NULL === ($array = self::__instance(self::INSTANCE_THIS)))
			return false;

		$array = (array) $array;

		$value = array_pop($array);
		
		return new static($array);
	}
	
	/**
	 * Prepend one or more elements to the beginning of an array.
	 * @param array $array The input array.
	 * @param mixed $value,... The value to prepend.
	 * @return XArray|bool Returns the modified array on success or FALSE if no
	 * values where supplied.
	 */
	public function unshift() {
		$values = func_get_args();

		$array = ($instance = self::__instance(self::INSTANCE_THIS)) instanceof XArray ? (array) $instance : array_shift($values);
		
		if (!is_array($array))
			return false;
		
		foreach ($values as $value)
			array_unshift($array, $value);
		
		return new static($array);
	}
	
	/**
	 * Shift an element off the beginning of array.
	 * @param mixed &$value A reference to a variable that will hold the removed
	 * value.
	 * @param array $array The input array.
	 * @return XArray|bool Returns the modified array on success or FALSE if no
	 * values where supplied.
	 */
	public function shift(&$value = NULL, array $array = NULL) {
		if (!is_array($array) && NULL === ($array = self::__instance(self::INSTANCE_THIS)))
			return false;

		$array = (array) $array;

		$value = array_shift($array);
		
		return new static($array);
	}

	public function mergeRecursive() {
		$input = func_get_args();
		
		if ($instance = self::__instance(self::INSTANCE_THIS))
			array_unshift($input, (array) $instance);
		
		for ($values = array(); list(, $append) = each($input);) {
			
			if (!XArray::isArray($append))
				continue;
			
			foreach ($append as $key => $value) {
				
				if (is_numeric($key))
					array_push($values, $value);
				else
					$values[$key] = XArray::isArray(@$values[$key]) && XArray::isArray($value) ? (array) XArray::create($values[$key])->mergeRecursive($value) : $value;
					
			}
			
		}
		
		return new XArray($values);
	}

	public function slice($offset, $length = NULL, $preserveKeys = false, $array = NULL) {
		if (!is_array($array) && NULL === ($array = self::__instance(self::INSTANCE_THIS)))
			return false;

		return new static(array_slice((array) $array, $offset, $length, $preserveKeys));
	}

	public function reverse($preserveKeys = false, array $array = NULL) {
		if (!is_array($array) && NULL === ($array = self::__instance(self::INSTANCE_THIS)))
			return false;

		return new static(array_reverse((array) $array, $preserveKeys));
	}
	
	/**
	 * Add a new property.
	 * @param array $target The target array.
	 * @param mixed $insert One or more values to add to the array.
	 * @param string|NULL $key An optional key as a reference to where 
	 * to add the value.
	 * @param bool $before If TRUE the value 
	 * @return array Returns the merged array.
	 */
	public function insert($insert, $key = NULL, $before = false) {
		if ($instance = self::__instance(self::INSTANCE_THIS))
			$target = $instance;
		else
			list($target, $insert, $key, $before) = func_get_args();

		$target = (array) $target;
		$insert = (array) $insert;
		
		$parts = array(
			$target,
			$insert
		);
		
		if (!is_null($key) && ($offset = array_search($key, array_keys($target)))) {
			
			if (!$before)
				$offset += 1;
				
			$parts = array(
				array_slice($target, 0, $offset, true),
				$insert,
				array_slice($target, $offset, NULL, true)
			);
			
		}
		
		return new static(Util::call(true, "array_merge", $parts));
	}

	// Evaluation

	/**
	 * Match the keys and values in an array to the keys and values in another.
	 * @param array $match The key/values to match the array against.
	 * @param array|NULL $compare An optional array to match. If no $compare 
	 * array is provided and the function is called from non-static context the
	 * called object is matched against the compare array.
	 * @return bool Returns TRUE if the key/value pairs provided by the $match
	 * parameter are available in the array or FALSE if not.
	 */
	public function match($match, $compare = NULL) {
		if (!self::isArray($compare) && NULL === ($compare = self::__instance(self::INSTANCE_THIS)))
			return false;

		$compare = (array) $compare;
		$match = (array) $match;

		$compare = array_intersect_key($compare, $match);

		return $compare == $match;
	}

	public function isArray($var) {
		return is_array($var) || $var instanceof XArray;
	}

	public function isRealArray($array = NULL) {
		if (!self::isArray($array) && NULL === ($array = self::__instance(self::INSTANCE_THIS)))
			return false;
		
		for ($array = reset((array) $array), $i = 0; list($key) = each($array); $i++) {
			if ($key != $i)
				return false;
		}

		return true;
	}

	public function isAssociative($array = NULL) {
		return !self::isRealArray($array);
	}

	// Conversion
	
	public function implode($glue = "", $pieces = NULL) {
		if (self::isArray($glue))
			list($glue, $pieces) = array("", $glue);
		else if (!self::isArray($pieces) && NULL === ($pieces = self::__instance(self::INSTANCE_THIS)))
			return false;

		return implode($glue, (array) $pieces);
	}

	public function explode($delimiter, $string, $limit = NULL) {
		return new self(is_numeric($limit) ? explode($delimiter, $string, $limit) : explode($delimiter, $string));
	}

	// Context
	
	protected function __instance($options = 0) {
		return self::INSTANCE_THIS & $options && isset($this) && $this instanceof XArray ? $this : (self::INSTANCE_NEW & $options ? new static() : NULL);
	}

	public function __getClone($input = NULL) {
		$clone = clone $this;

		if (isset($input))
			$clone->exchangeArray((array) $input);

		return $clone;
	}
	
	// Static
	
	public static function create() {
		$reflector = new ReflectionClass(get_called_class());
		
		return $reflector->newInstanceArgs(func_get_args());
	}

	/**
	 * Convert a list of values to key and value pairs.
	 * @param array $args A single array of values to be combined (the keys are
	 * ignored).
	 * @param mixed,... $args Multiple parameters will be combined as keys and
	 * values.
	 * @return XArray Returns an XArray object.
	 */
	public static function remix($args) {
		for ($array = array(), $args = array_chunk(is_array($args) ? array_values($args) : func_get_args(), 2); list(, list($key, $value)) = each($args);) 
			$array[$key] = $value;
		
		return new XArray($array);
	}

}