<?php
namespace lowtone\util\modules;
use ReflectionClass,
	lowtone\types\objects\Object;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\modules
 */
abstract class Module extends Object {

	public function enabled() {
		return true;
	}

	// Static

	public static final function modules() {
		$static = get_called_class();
		$reflector = new ReflectionClass($static);
		$class = $reflector->getShortName();
		$namespace = $reflector->getNamespaceName();
		$dir = dirname($reflector->getFileName());
		$plugin = reset(explode("/", plugin_basename($dir)));
		$filter = sprintf("lowtone_%s_%ss", $plugin, strtolower($class));
		
		$default = array_filter(array_map(function($file) use ($namespace) {
			if (!include_once $file)
				return false;

			$className = $namespace . "\\defaults\\" . basename($file, ".class.php");

			$instance = new $className();

			return $instance;
		}, glob(implode(DIRECTORY_SEPARATOR, array($dir, "defaults", "*.class.php")))));

		return array_filter((array) apply_filters($filter, $default), function($module) use ($static) {
			return ($module instanceof $static) && $module->enabled();
		});
	}

	public static final function instance($class) {
		if (!class_exists($class))
			throw new \ErrorException(sprintf("Class '%s' doesn't exist", $class));

		$args = array_slice(func_get_args(), 1);
		
		$reflection = new \ReflectionClass($class);

		$module = $reflection->newInstanceArgs($args);

		if (!($module instanceof static))
			throw new \ErrorException(sprintf("Requested module not instance of '%s'", __CLASS__));

		return $module;
	}

}