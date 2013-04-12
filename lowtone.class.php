<?php
namespace lowtone;
use lowtone\content\packages\Package,
	lowtone\util\loading\Loader;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone
 */
abstract class Lowtone {
	
	private static $initCalls = 0;
	
	// Init
	
	/**
	 * Initiate the Lowtone Util environment.
	 * @return bool Returns TRUE on success.
	 */
	public static function init() {
		if (self::$initCalls++ > 0)
			return true;

		if (!session_id())
			session_start();
		
		// Autoloading
		
		include_once "util/loading/loader.class.php";

		Loader::init();

		// Add include paths to Loader
		
		Loader::addIncludePath(array(
			LIB_DIR,
			realpath(ABSPATH.PLUGINDIR),
			realpath(get_theme_root())
		));

		// Log
		
		if (!defined("LOG_DIR"))
			define("LOG_DIR", WP_CONTENT_DIR . DIRECTORY_SEPARATOR . "log");
		
		if (!defined("LOWTONE_LOG"))
			define("LOWTONE_LOG", strftime(LOG_DIR . DIRECTORY_SEPARATOR . "lowtone-%Y%m%d.log"));
		
		$log = new io\logging\Log(LOWTONE_LOG);

		$log->disabled(!Util::isDebug());
		
		Globals::set("log", $log)->lock("log", true);

		// Request
		
		Globals::set("request", new net\Request())->lock("request", true);
		
		// Database
		
		define("LOWTONE_DB_PREFIX", $GLOBALS["wpdb"]->prefix . "lowtone_");
		
		// Error handler

		if (!defined("LOWTONE_LOG_ERRORS"))
			define("LOWTONE_LOG_ERRORS", Util::isDebug());
		
		if (!defined("LOWTONE_ERROR_LOGGING"))
			define("LOWTONE_ERROR_LOGGING", E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
		
		$prevErrorHandler = set_error_handler(function($severity, $message, $filename, $lineno) use ($log) {
			
			// Always returns FALSE to have the default error handler pick up the error.
			
			if (!LOWTONE_LOG_ERRORS) 
				return false;

			if (!($severity & LOWTONE_ERROR_LOGGING)) 
				return false;
			
			$types = array(
					E_ERROR => "Error",
					E_WARNING => "Warning",
					E_PARSE => "Parse error",
					E_NOTICE => "Notice",
					E_CORE_ERROR => "Core error",
					E_CORE_WARNING => "Core warning",
					E_COMPILE_ERROR => "Compile error",
					E_COMPILE_WARNING => "Compile warning",
					E_USER_ERROR => "User error",
					E_USER_WARNING => "User warning",
					E_USER_NOTICE => "User notice",
					E_STRICT => "Strict",
					E_RECOVERABLE_ERROR => "Recoverable error",
					E_DEPRECATED => "Deprecated",
					E_USER_DEPRECATED => "User deprecated"
				);

			$type = isset($types[$severity]) ? $types[$severity] : "Unknown error";

			$log->write(sprintf("%s: %s in %s on line %s", $type, $message, $filename, $lineno));

			return false;
		});
		
		// Styles
		
		add_action("admin_print_styles", function() {
			wp_enqueue_style(__NAMESPACE__, LIB_URL . "/lowtone/assets/styles/admin.css");
		});

		// Scripts

		if (is_array($scripts = include LIB_DIR . "/lowtone/assets/scripts/scripts.array.php")) {
		
			foreach (array("wp_enqueue_scripts", "admin_enqueue_scripts") as $action) {

				add_action($action, function() use ($scripts) {

					foreach ($scripts as $handle => $script) {

						call_user_func_array(
								(isset($script["enqueue"]) && $script["enqueue"] ? "wp_enqueue_script" : "wp_register_script"),
								array(
										$handle,
										@$script["raw_src"] ?: @$script["src"] . (@$script["min"] && !Util::isScriptDebug() ? "-min" : "") . ".js",
										@$script["deps"]
									)
							);

						if (@$script["localize"]) 
							wp_localize_script($handle, @$script["localize"]["name"], @$script["localize"]["locales"]);
						
					}
					
				});

			}

		}

		// Extend CRON schedules
		
		add_filter("cron_schedules", function($schedules) {
			$schedules["15_minutes"] = array(
					"interval" => 900,
					"display" => __("Once every 15 minutes", "lowtone")
				);

			$schedules["30_minutes"] = array(
					"interval" => 1800,
					"display" => __("Once every 30 minutes", "lowtone")
				);

			return $schedules;
		});

		// Add merged path on package init
		
		add_action("lowtone_content_package_init", function($options) {

			if (!($mergedPath = @$options[Package::INIT_MERGED_PATH]))
				return;

			Loader::addMergedPath($mergedPath);

		});
		
		return true;
	}
	
}