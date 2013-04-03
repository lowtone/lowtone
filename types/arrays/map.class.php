<?php
namespace lowtone\types\arrays;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\types\arrays
 */
class Map extends XArray {

	protected $__itsLocked = array();

	const PATH_SEPARATOR = ".";

	// Property access
	
	/**
	 * Search for a value using a path. The path consists of all keys to the 
	 * value in an array or a string separated by dots.
	 * @param string|array $path The keys needed to reach the value either 
	 * provided as an array or in a string separated by dots.
	 * @return mixed|NULL Returns the value if available or NULL if not.
	 */
	public function __pathGet($path) {
		$path = $this->__splitPath($path);
		
		for ($value = $this; list(, $key) = each($path);) {
			if (!isset($value[$key]))
				return NULL;
				
			$value = &$value[$key];
		}
		
		return $value;
	}
	
	/**
	 * Set or update a value using a path.
	 * @param string|array $path The path to the value either provided as an 
	 * array or in a string separated by dots.
	 * @param mixed $value The value to be assigned.
	 * @return Map Returns the Map object for chaining.
	 */
	public function __pathSet($path, $value) {
		$path = $this->__splitPath($path);

		if (true === $this->lock($path)) {
			trigger_error("Can not modify locked property");

			return $this;
		}
		
		for ($ref = $this; list(, $key) = each($path);)
			$ref = &$ref[$key];
		
		$ref = $value;
		
		return $this;
	}

	public function path($path, $value = NULL) {
		return isset($value) ? $this->__pathSet($path, $value) : $this->__pathGet($path);
	}

	public function __invoke() {
		return call_user_func_array(array($this, "path"), func_get_args());
	}
	
	/**
	 * Remove a value using a path.
	 * @param string|array $path The path to the value either provided as an 
	 * array or in a string separated by dots.
	 * @return Map Returns the Map object for chaining.
	 */
	public function pathRemove($path) {
		$path = $this->__splitPath($path);

		if (true === $this->lock($path)) {
			trigger_error("Can not remove locked property");

			return $this;
		}
		
		$lastKey = array_pop($path);
		$array = $this->getArrayCopy();
		
		for ($value =& $array; list(, $key) = each($path);) {
			if (!isset($value[$key]) || !is_array($value[$key])) 
				return;
			
			$value = &$value[$key];
		}
		
		$value = array_diff_key($value, array($lastKey => true));
		
		$this->exchangeArray($array);
		
		return $this;
	}

	// Locks

	/**
	 * When a value is set to read-only it is prevented from accidently being 
	 * overwritten. The read-only flag however is not meant as a secure method 
	 * to protect variables since it can as easily be disabled as it has been 
	 * enabled.
	 * @param string|array $path The path to the variable.
	 * @param bool $isLocked The new setting for the read-only flag.
	 * @return bool|string Returns TRUE if the required variable is locked or 
	 * FALSE if not or, if a new lock is set, the key for that lock.
	 */
	final public function lock($path, $isLocked = NULL, $key = NULL) {
		$path = $this->__mergePath($path);

		$lock = @$this->__itsLocked[$path];

		if (!is_bool($isLocked))
			return (bool) $lock;

		if ($isLocked) 
			return $lock ? true : ($this->__itsLocked[$path] = is_null($key) ? $this->__generateKey() : $key);

		if ($key !== $lock)
			return true;

		unset($this->__itsLocked[$path]);

		return false;
	}

	final private function __generateKey() {
		return sha1(uniqid(AUTH_SALT, true));
	}

	// Path helpers
	
	/**
	 * Convert a path to an array.
	 * @param string|array $path The path. If an array is provided it remains
	 * untouched.
	 * @return array Returns the path as an array.
	 */
	public function __splitPath($path) {
		if (!is_array($path))
			$path = explode(static::PATH_SEPARATOR, (string) $path);
			
		return $path;
	}

	public function __mergePath($path) {
		if (is_array($path))
			$path = implode(static::PATH_SEPARATOR, $path);

		return $path;
	}

}