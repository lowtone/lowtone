<?php
namespace lowtone;
use ErrorException,
	lowtone\util\loading\Loader;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone
 */
abstract class Util {
	
	// Including & autoloading
	
	/**
	 * Add a merged path to the Loader class.
	 * @param string $path The path template register as merged path.
	 */
	public static function addMergedPath($path) {
		return Loader::addMergedPath($path);
	}
	
	// File data
	
	private static function extractFileData($file, $property = NULL) {
		include_once ABSPATH . "/wp-admin/includes/plugin.php";
		
		$data = @get_plugin_data($file);
		
		if (!is_null($property))
			return @$data[$property];
			
		return $data;
	}
	
	public static function getFileData($file, $property = NULL) {
		return self::extractFileData($file, $property);
	}
	
	// Libarary resources
	
	/**
	 * Extract library information.
	 * @param string|NULL $property The name for a specific property.
	 * @return array|mixed Returns all data in an array or the value for the 
	 * specified property.
	 */
	public static function getLibData($property = NULL) {
		return self::extractFileData(__DIR__ . "/lowtone.php", $property);
	}
	
	/**
	 * Compare a given value to the libary's version.
	 * @param string $compare The version number to compare with the version 
	 * number for the library.
	 * @param string|NULL $operator An optional comparison operator to use for 
	 * the comparison.
	 * @return int|bool Returns -1 if the given value is lower than the 
	 * library's, 0 if the version are equel or 1 if the given version value is 
	 * higher. If an operator is specified TRUE is the version number meets the 
	 * requirements defined by the operator or FALSE if not
	 */
	public static function libVersionCompare($compare, $operator = NULL) {
		return !is_null($operator) ? version_compare(self::getLibData("Version"), $compare, $operator) : version_compare(self::getLibData("Version"), $compare);
	}
	
	/**
	 * Shorthand to check if the library's version is equel or higher than the
	 * given version.
	 * @param string $check The version number to compare to the library's 
	 * version.
	 * @return bool Returns TRUE if the library's version number is higher than
	 * or equel to the required version number.
	 */
	public static function libVersionCheck($check) {
		return self::libVersionCompare($check, ">=");
	}
	
	// Class resources
	
	private static function getClassFile($class) {
		$class = strtolower(self::mergePath($class));
		
		foreach (self::$includePath as $includePath) {
			foreach (self::$extensions as $extension) {
				
				if (is_file($file = realpath($includePath . DIRECTORY_SEPARATOR . $class . $extension)))
					return $file;
				
			}
		}
		
		return false;
	}
	
	public static function getClassData($class, $property = NULL) {
		return self::extractFileData(self::getClassFile($class), $property);
	}
	
	// Cron
	
	/**
	 * Test whether the script is executed as a cron task. Execution as a cron 
	 * task doesn't ensure that the script was initiated by an internel 
	 * scheduled server call, just that the script was executed in the context 
	 * of wp-cron.php.
	 * @return bool Returns TRUE if the script is executed as a cron task or 
	 * FALSE if not.
	 */
	public static function doingCron() {
		return (defined(DOING_CRON) && DOING_CRON);
	}

	// Debug
	
	public static function isDebug() {
		return (defined("WP_DEBUG") && WP_DEBUG);
	}

	public static function isScriptDebug() {
		return self::isDebug() || (defined("SCRIPT_DEBUG") && SCRIPT_DEBUG);
	}
	
	// Query
	
	public static function getContext() {
		$query = new wp\queries\Query();
		
		return $query->getContext();
	}
	
	// Other
	
	/**
	 * Catch a function's output using output buffering.
	 * @see Util::call()
	 * @return string Returns the output generated during execution of the 
	 * function.
	 */
	public static function catchOutput($callback) {
		ob_start();
		
		$result = call_user_func_array(array(__CLASS__, "call"), func_get_args());
			
		$output = ob_get_contents();
		
		ob_end_clean();
		
		if (@$result === false)
			return false;
		
		return $output;
	}
	
	/**
	 * Execute the provided callback function. This is in particular useful for 
	 * calling anonymous functions without defining a variable.
	 * @param callable|bool $callback The function to catch the output from. If 
	 * a boolean value is provided the function takes a second parameter for the
	 * callback function and if the boolean value is TRUE a third as an array 
	 * with parameters for the callback or if the value is FALSE it takes all 
	 * parameters after the second parameter as parameters for the callback.
	 * @param mixed $param,... Optional parameters for the callback function or 
	 * the callback function followed by a parameter array if $callback is TRUE.
	 * @throws Throws an ErrorException if a non-callable value is provided for
	 * $callback.
	 * @return mixed Returns the result from the callback.
	 */
	public static function call($callback) {
		if (is_bool($callback)) {
			
			if (!$callback) {
				$callback = func_get_arg(1);
				$params = array_slice(func_get_args(), 2);
			} else 
				list(, $callback, $params) = @func_get_args();
				
		} else
			$params = array_slice(func_get_args(), 1);
			
		if (!is_callable($callback)) 
			throw new ErrorException(sprintf("Non-callable function provided to %", __FUNCTION__), 0, E_NOTICE);
			
		return call_user_func_array($callback, (array) $params);
	}
	
	/**
	 * Check if the script execution was initialized by a call to wp-login.php.
	 * @return boolean Returns TRUE if wp-login.php was called or FALSE if not.
	 */
	public static function isLogin() {
		return (strtolower(basename($_SERVER["PHP_SELF"])) == "wp-login.php");
	}

	public static function isAutosave() {
		return (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE);

	}
	
	/**
	 * Get the client IP address.
	 * @return string Returns a string representation for the client't IP 
	 * address.
	 */
	public static function getIp() {
		foreach (array("HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_X_CLUSTER_CLIENT_IP", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR") as $key) {
			if (!array_key_exists($key, $_SERVER)) 
				continue;
				
			foreach (explode(",", $_SERVER[$key]) as $ip) {
				$ip = trim($ip);
				
				if (filter_var($ip, FILTER_VALIDATE_IP/*, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE*/) !== false)
					return $ip;
				
			}
		}
		
		return NULL;
	}
	
	/**
	 * Get the time passed in milliseconds since the request was made.
	 * @return int The time the script has been running in milliseconds.
	 */
	public static function getExecutionTime() {
		return (microtime(true) - $_SERVER["REQUEST_TIME"]);
	}
	
	/**
	 * Get the time left in millisecconds until the maximum script execution 
	 * time is reached.
	 * @return int The time left in milliseconds.
	 */
	public static function getTimeLeft() {
		return (ini_get("max_execution_time") - self::getExecutionTime());
	}
	
	/**
	 * Get a path relative to the WordPress root directory.
	 * @param string $dir The subject path.
	 * @return string Returns a relative path for the given directory path.
	 */
	public static function getRelPath($dir, $from = NULL) {
		if (!is_string($from))
			$from = ABSPATH;

		$from = explode(DIRECTORY_SEPARATOR, rtrim(realpath($from), DIRECTORY_SEPARATOR));
		$to = explode(DIRECTORY_SEPARATOR, rtrim(realpath($dir), DIRECTORY_SEPARATOR));
		
		while(count($from) && count($to) && ($from[0] == $to[0])) {
			array_shift($from);
			array_shift($to);
		}
		
		return str_pad("", count($from) * 3, '..'. DIRECTORY_SEPARATOR) . implode(DIRECTORY_SEPARATOR, $to);
	}

	public static function pathToUrl($path) {
		return site_url(str_replace("\\", "/", self::getRelPath($path)));
	}

	public static function urlToPath($url) {
		$url = preg_split("#/+#", $url);
		$siteUrl = preg_split("#/+#", site_url());
		
		while (count($url) && count($siteUrl) && ($url[0] == $siteUrl[0])) {
			array_shift($url);
			array_shift($siteUrl);
		}

		return realpath(ABSPATH . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $url));
	}

	// Context file
	
	public static function locateFile(array $folders, $name, $ext = NULL) {
		if (!is_array($name))
			$name = explode("-", (string) $name);

		foreach ($folders as $folder) {
			for ($parts = $name; $parts; array_pop($parts)) {
				if (!($file = realpath($folder . DIRECTORY_SEPARATOR . implode("-", $parts) . $ext)))
					continue;

				return $file;
			}
		}

		return false;
	}

	// Functions
	
	/**
	 * Merge values from multiple arrays.
	 * @param array $args An array container multiple arrays to be merged.
	 * @return array Returns an array.
	 */
	public static function mergeArgs(array $args) {
		return call_user_func_array('array_merge', array_map(function($param) {return (array) $param;}, (array) $args));
	}
	
}