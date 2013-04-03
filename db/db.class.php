<?php
namespace lowtone\db;
use PDO,
	WPDB;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db
 */
class DB extends PDO {

	public static function createDsn(array $settings = NULL) {
		if (isset($settings["alias"])) 
			return $settings["alias"];
		else if (isset($settings["uri"]))
			return "uri:" . $settings["uri"];

		if (!isset($settings["driver"]))
			throw new exceptions\DsnException("No driver defined");

		$driver = $settings["driver"];
		$params = array_intersect_key($settings, array_flip(array("host", "dbname")));

		return $driver . ":" . implode(";", array_map(function($key, $value) {return implode("=", array($key, $value));}, array_keys($params), array_values($params)));
	}

	public static function createFromWpdb() {
		global $wpdb;

		$dsn = static::createDsn(array(
				"driver" => "mysql",
				"host" => $wpdb->dbhost,
				"dbname" => $wpdb->dbname
			));
		
		return new static($dsn, $wpdb->dbuser, $wpdb->dbpassword);
	}
	
}