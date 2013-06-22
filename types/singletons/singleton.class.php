<?php
namespace lowtone\types\singletons;
use ReflectionClass;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\singletons
 */
abstract class Singleton implements interfaces\Singleton {
	
	protected static $__instances;

	protected function __construct() {}

	/**
	 * Get the instance of the singleton class.
	 * @todo After a new object is created the constructor is manually called 
	 * for a second time to pass the arguments. This is because using a 
	 * ReflectionClass object is no option because it can not access the private 
	 * constructor.
	 * @return Singleton Returns the instance of the Singleton.
	 */
	public static function __instance() {
		$class = get_called_class();

		if (!(isset(self::$__instances[$class]) && self::$__instances[$class] instanceof $class)) {
			$instance = new $class();

			self::$__instances[$class] = $instance;
		}

		return self::$__instances[$class];
	}
}