<?php
namespace lowtone\wp\admin\notices;
use ErrorException;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\notices
 */
class Notice extends ErrorException {

	protected static $notices;

	public function __construct ($message = "" , $code = 0, $severity = 0, $filename = __FILE__, $lineno = __LINE__, Exception $previous = NULL) {
		parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
	}

	public function __sleep() {
		return array(
				"message",
				"code",
				"file",
				"line",
				"severity"
			);
	}
 
	public static function init() {
		add_action("admin_notices", function() {
			Notice::out();
		});

		add_action("admin_init", function() {
			Notice::retrieve();
		});

		add_action("wp_redirect", function($uri) {
			Notice::preserve();

			return $uri;
		});

		add_action("url_go", function() {
			Notice::preserve();
		});
	}

	public static function out() {
		foreach ((array) self::$notices as $notice) {
			echo '<div class="' . ($notice->getSeverity() > 0 ? "error" : "updated") . '">' . 
					$notice->getMessage() .
				'</div>';
		}

		self::clear();
	}

	public static function clear() {
		self::$notices = array();
	}

	public static function add($notice) {
		if (!($notice instanceof Notice))
			$notice = new static($notice);

		self::$notices[] = $notice;
	}

	public static function preserve() {
		$_SESSION["notices"] = serialize(self::$notices);
	}

	public static function retrieve() {
		if (isset($_SESSION["notices"]))
			self::$notices = array_merge((array) unserialize($_SESSION["notices"]), (array) self::$notices);

		unset($_SESSION["notices"]);
	}

}