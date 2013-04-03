<?php
namespace lowtone\util\loading;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\loading
 */
class Loader {
	
	/**
	 * A list of include paths.
	 * @var array
	 */
	private static $includePath = array();
	
	private static $mergedPaths = array();

	private static $loaded = array();
	
	/**
	 * A list of class file extensions.
	 * @var array
	 */
	private static $extensions = array(
		".class.php",
		".interface.php"
	);

	public static function init() {
		self::$includePath = explode(PATH_SEPARATOR, get_include_path());
		
		self::updateExtensions();
		
		spl_autoload_register(array(__CLASS__, "incl"));

		return true;
	}
	
	/**
	 * Include a class.
	 * @param string $name The name of the required class
	 * @return bool Returns TRUE on success.
	 */
	public static function incl($name) {
		$name = strtolower(self::mergePath($name));
		
		if (in_array($name, self::$loaded))
			return $name;
		
		try {
			spl_autoload(str_replace("\\", DIRECTORY_SEPARATOR, $name));
		} catch (Exception $e) {
			return false;
		}
		
		self::$loaded[] = $name;
		
		return $name;
	}
	
	/**
	 * Merge path nodes defined in Util::$mergedPaths.
	 * @param string $name The subject path.
	 * @param string $merger The character used to merge the path nodes.
	 * @return string Returns the merged path.
	 */
	public static function mergePath($name, $merger = "-") {
		$pattern = "/(^|\\\\)(" . implode("|", array_map("preg_quote", self::$mergedPaths)) . ")(\\\\)/i";
		
		return preg_replace_callback($pattern, function($matches) use ($merger) {
			return $matches[1] . str_replace("\\", $merger, $matches[2]) . $matches[3];
		}, $name);
	}
	
	/**
	 * Add a path to the include path.
	 * @param string|array $path A single or multiple paths to add.
	 * @return array Returns a list of include paths.
	 */
	public static function addIncludePath($path) {
		self::$includePath = self::appendUnique(self::$includePath, (array) $path);
		
		self::updateIncludePath();
		
		return self::$includePath;
	}
	
	/**
	 * Update the include path with the values from Util::$includePath.
	 * @return string Returns the include path string.
	 */
	public static function updateIncludePath() {
		return set_include_path(implode(PATH_SEPARATOR, self::$includePath));
	}
	
	public static function addMergedPath($path) {
		$mergePaths = self::appendUnique(self::$mergedPaths, (array) $path);
		
		sort($mergePaths);

		$mergePaths = array_reverse($mergePaths);
		
		return (self::$mergedPaths = $mergePaths);
	}
	
	/**
	 * Update the SPL autoload extensions.
	 * @return string Returns the autoload extensions string.
	 */
	public static function updateExtensions() {
		return spl_autoload_extensions(implode(",", self::$extensions));
	}
	
	/**
	 * Append unique values to the given base array.
	 * @todo Move this function to ExtArray.
	 * @param array $base The base array.
	 * @param array $append The values to append.
	 * @return array Returns the merged array of unique values.
	 */
	private static function appendUnique(array $base, array $append) {
		return array_unique(array_merge($base, $append));
	}

}