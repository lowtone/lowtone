<?php
namespace lowtone\net;
use lowtone\db\records\Record,
	lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\net
 */
class URL extends Record {

	/**
	 * Cached permalink URL objects aquired through URL::permalink().
	 * @var array
	 */
	private static $__permalink = array();

	const PROPERTY_SCHEME = "scheme",
		PROPERTY_HOST = "host",
		PROPERTY_PORT = "port",
		PROPERTY_USER = "user",
		PROPERTY_PASS = "pass",
		PROPERTY_PATH = "path",
		PROPERTY_QUERY = "query",
		PROPERTY_FRAGMENT = "fragment";

	public function appendQuery($query) {
		if (is_string($query))
			parse_str($query, $query);

		$this->query = array_merge((array) $this->query, (array) $query);
		
		return $this;
	}

	public function pathinfo($options = NULL) {
		return pathinfo($this->{self::PROPERTY_PATH}, $options);
	}

	public function __toString() {
		return (!is_null($this[self::PROPERTY_SCHEME]) ? $this[self::PROPERTY_SCHEME] . "://" : "") .
			(!is_null($this[self::PROPERTY_USER]) ? $this[self::PROPERTY_USER] . (!is_null($this[self::PROPERTY_PASS]) ? ":" . $this[self::PROPERTY_PASS] : "") . "@" : "") .
			(!is_null($this[self::PROPERTY_HOST]) ? $this[self::PROPERTY_HOST] : "") .
			(!is_null($this[self::PROPERTY_PORT]) ? ":" . $this[self::PROPERTY_PORT] : "") .
			(!is_null($this[self::PROPERTY_PATH]) ? $this[self::PROPERTY_PATH] : "") .
			(!empty($this[self::PROPERTY_QUERY]) ? "?" . http_build_query($this[self::PROPERTY_QUERY]) : "") .
			(!is_null($this[self::PROPERTY_FRAGMENT]) ? "#" . $this[self::PROPERTY_FRAGMENT] : "");
	}

	public function go($exit = true) {
		if (headers_sent())
			throw new \ErrorException("Cannot redirect after headers were sent");

		do_action("url_go", $this);

		header("Location: " . $this);

		if ($exit)
			exit;
	}

	// Static
	
	public static function __createSchema($defaults = NULL) {
		return parent::__createSchema(array(
			self::PROPERTY_QUERY => array(
				Property::ATTRIBUTE_SET => function($val) {
					if (is_array($val))
						return $val;

					parse_str($val, $val);

					return $val;
				}
			)
		));
	}

	/**
	 * Create a URL object from a given string.
	 * @param string $url A string representation of the URL to create an object
	 * from.
	 * @return URL Returns a URL object created from the given string.
	 */
	public static function fromString($url) {
		return new static(parse_url((string) $url));
	}
	
	/**
	 * Create a URL object for the current location.
	 * @return URL Returns a URL object created from the location as defined in 
	 * the $_SERVER variable.
	 */
	public static function fromCurrent() {
		return new static(array(
				self::PROPERTY_SCHEME => preg_match("/https/i", $_SERVER["SERVER_PROTOCOL"]) ? "https" : "http",
				self::PROPERTY_HOST => $_SERVER["HTTP_HOST"],
				self::PROPERTY_PATH => $_SERVER["REQUEST_URI"],
				self::PROPERTY_QUERY => $_SERVER["QUERY_STRING"]
			));
	}

	public static function permalink($id = 0) {
		if (0 == $id && isset($GLOBALS["wp_query"]))
			$id = $GLOBALS["wp_query"]->queried_object_id;

		return isset(self::$__permalink[$id]) ? self::$__permalink[$id] : (false !== ($permalink = (0 == $id ? site_url("/") : get_permalink($id))) ? (self::$__permalink[$id] = URL::fromString($permalink)) : false);
	}

	public static function __cast($url) {
		if ($url instanceof static)
			return $url;

		return static::fromString((string) $url);
	}

	// Deprecated
	
	public static function queryParam(array $parts) {
		if (!$parts)
			return false;
			
		$first = array_shift($parts);
		
		$parts = array_map(function($name) {
			return "[" . strtolower($name) . "]";
		}, $parts);
		
		array_unshift($parts, $first);
		
		return implode($parts);
	}

	public static function splitQueryParam($name) {
		if (!($name = trim($name)))
			return array();
		
		return preg_split("/(\])?\[|\]/", preg_replace("/\]$/", "", (string) $name));
	}
	
}