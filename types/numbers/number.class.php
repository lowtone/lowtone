<?php
namespace lowtone\types\numbers;

/**
 * OOP wrapper for GMP functions.
 * 
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\numbers
 */
class Number {

	protected $itsResource;
	
	public function __construct($number, $base = 0) {
		$this->init($number, $base);
	}

	public function __call($name, $arguments) {
		$name = "gmp_" . $name;

		if (!function_exists($name))
			throw new \Exception(sprintf("Call to undefined GMP function %s", $name));

		array_walk($arguments, function(&$arg) {
			if (is_string($arg) && 0 === strpos($arg, "+"))
				$arg = (int) $arg;
		});
		
		switch (strtolower($name)) {
			case "gmp_init":
				break;

			default:
				array_unshift($arguments, $this->itsResource);
		}

		if (!Number::isGmp($result = call_user_func_array($name, $arguments)))
			return $result;

		$this->itsResource = $result;

		return $this;
	}

	// Static
	
	public static function create($number, $base = 0) {
		return new static($number, $base);
	}

	public static function isGmp($resource) {
		return is_resource($resource) && "GMP integer" == get_resource_type($resource);
	}

}