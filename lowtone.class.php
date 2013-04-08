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

		// Define LIB_DIR
		
		if (!defined("LIB_DIR"))
			define("LIB_DIR", dirname(__DIR__));
		
		// Autoloading
		
		include_once "util/loading/loader.class.php";

		Loader::init();

		// Add include paths to Loader
		
		Loader::addIncludePath(array(
			LIB_DIR,
			realpath(ABSPATH.PLUGINDIR),
			realpath(get_theme_root())
		));

		// Define LIB_URL

		define("LIB_URL", site_url(preg_replace("#\\\\+#", "/", Util::getRelPath(LIB_DIR))));

		// Log
		
		if (!defined("LOWTONE_LOG"))
			define("LOWTONE_LOG", WP_CONTENT_DIR . "/logs/lowtone-%Y%m%d.log");
		
		$log = new io\logging\Log(strftime(LOWTONE_LOG));

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

		$scriptsUrl = LIB_URL . "/lowtone/assets/scripts";

		$scripts = array(
				"strings" => array(
						"src" => $scriptsUrl . "/strings/strings",
						"min" => true
					),
				"sprintf" => array(
						"src" => $scriptsUrl . "/strings/sprintf",
						"min" => true
					),
				"audio" => array(
						"src" => $scriptsUrl . "/audiojs/audio.min"
					),
				"underscore" => array(
						"src" => $scriptsUrl . "/underscore/underscore",
						"min" => true
					),
				"backbone" => array(
						"src" => $scriptsUrl . "/backbone/backbone",
						"min" => true, 
						"deps" => array("underscore")
					),
				"modernizr" => array(
						"src" => $scriptsUrl . "/modernizr/modernizr",
						"min" => true
					),
				"google-jsapi" => array(
						"raw_src" => "https://www.google.com/jsapi"
					),
				"google-maps" => array(
						"raw_src" => "http://maps.google.com/maps/api/js?sensor=true"
					),
				"jquery-extend" => array(
						"src" => $scriptsUrl . "/jquery/jquery.extend", 
						"deps" => array("jquery")
					),
				"jquery-regex-selector" => array(
						"src" => $scriptsUrl . "/jquery/jquery.regex-selector",
						"min" => true, 
						"deps" => array("jquery")
					),
				"jquery-cycle" => array(
						"src" => $scriptsUrl . "/jquery/ui/cycle/jquery.cycle",
						"min" => true, 
						"deps" => array("jquery")
					),
				"jquery-cycle-tiles" => array(
						"src" => $scriptsUrl . "/jquery/ui/cycle/transitions/jquery.cycle-tiles",
						"min" => true,
						"deps" => array("jquery-cycle")
					),
				"jquery-dom-obj" => array(
						"src" => $scriptsUrl . "/jquery/jquery.dom-obj", 
						"deps" => array("jquery")
					),
				"jquery-xml-json" => array(
						"src" => $scriptsUrl . "/jquery/jquery.xml-json", 
						"deps" => array("jquery")
					),
				"jquery-mutation-core" => array(
						"src" => $scriptsUrl . "/jquery/mutation-events/mutations.core", 
						"deps" => array("jquery")
					),
				"jquery-mutation-attr" => array(
						"src" => $scriptsUrl . "/jquery/mutation-events/mutations.attr", 
						"deps" => array("jquery-mutation-core")
					),
				"jquery-mutation-data" => array(
						"src" => $scriptsUrl . "/jquery/mutation-events/mutations.data", 
						"deps" => array("jquery-mutation-core")
					),
				"jquery-mutation-html" => array(
						"src" => $scriptsUrl . "/jquery/mutation-events/mutations.html", 
						"deps" => array("jquery-mutation-core")
					),
				"jquery-mutation-ie6css" => array(
						"src" => $scriptsUrl . "/jquery/mutation-events/mutations.ie6css", 
						"deps" => array("jquery-mutation-core")
					),
				"jquery-mutation-val" => array(
						"src" => $scriptsUrl . "/jquery/mutation-events/mutations.val", 
						"deps" => array("jquery-mutation-core")
					),
				"jquery-ui-tipsy" => array(
						"src" => $scriptsUrl . "/jquery/ui/tipsy/jquery.tipsy", 
						"deps" => array("jquery")
					),
				"jquery-ui-colorbox" => array(
						"src" => $scriptsUrl . "/jquery/ui/colorbox/jquery.colorbox",
						"min" => true, 
						"deps" => array("jquery")
					),
				"jquery-ui-map" => array(
						"src" => $scriptsUrl . "/jquery/ui/map/jquery.ui.map", 
						"deps" => array("jquery", "jquery-ui-core", "google-maps")
					),
				"jquery-ui-map-extensions" => array(
						"src" => $scriptsUrl . "/jquery/ui/map/jquery.ui.map.extensions", 
						"deps" => array("jquery-ui-map")
					),
				"jquery-ui-timepicker" => array(
						"src" => $scriptsUrl . "/jquery/ui/timepicker/jquery.timepicker", 
						"deps" => array("jquery-ui-datepicker", "jquery-ui-slider")
					),
				"jquery-ui-form" => array(
						"src" => $scriptsUrl . "/jquery/ui/form/jquery.form",
						"min" => true, 
						"deps" => array("jquery", "jquery-ui-core", "jquery-ui-timepicker")
					),
				"jquery-ui-form-location" => array(
						"src" => $scriptsUrl . "/jquery/ui/form/jquery.form-location",
						"min" => true, 
						"deps" => array("jquery-ui-form", "jquery-ui-map", "jquery-ui-map-extensions")
					),
				"jquery-ui-google-charts" => array(
						"src" => $scriptsUrl . "/jquery/ui/google/charts/jquery.ui.google.charts",
						"min" => true, 
						"deps" => array("jquery", "google-jsapi")
					),
				"jquery-ui-tablesorter" => array(
						"src" => $scriptsUrl . "/jquery/ui/tablesorter/jquery.tablesorter",
						"min" => true, 
						"deps" => array("jquery")
					),
				"jquery-ui-admin" => array(
						"src" => $scriptsUrl . "/jquery/ui/admin/jquery.admin",
						"min" => true, 
						"deps" => array("jquery"),
						"localize" => array(
								"name" => "jquery_ui_admin",
								"locales" => array(
										"ajax_url" => admin_url("admin-ajax.php"),
										"open" => array(
											"back" => __("Back")
										)
									)
							)
					),
				"jquery-lowtone" => array(
						"src" => $scriptsUrl . "/jquery/lowtone/jquery.lowtone",
						"min" => true, 
						"deps" => array("jquery"), 
						"enqueue" => true
					),
				"jquery-lowtone-wp-events" => array(
						"src" => $scriptsUrl . "/jquery/lowtone/wp/jquery.events", 
						"deps" => array("jquery")
					),
				"jquery-lowtone-ui-tables" => array(
						"src" => $scriptsUrl . "/jquery/lowtone/ui/jquery.tables", 
						"deps" => array("jquery", "jquery-ui-tablesorter")
					)
			);
		
		foreach (array("wp_enqueue_scripts", "admin_enqueue_scripts") as $action) {

			add_action($action, function() use ($scripts) {

				foreach ($scripts as $handle => $script) {

					call_user_func_array(
							(@$script["enqueue"] ? "wp_enqueue_script" : "wp_register_script"),
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

			// Context specific

			/*switch ($action) {
				case "wp_enqueue_scripts":
					break;

				case "admin_enqueue_scripts":
					break;
			}*/

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