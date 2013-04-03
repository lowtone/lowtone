<?php
namespace lowtone\net;

/**
 * The Request object provides access to the unescaped values from the request. 
 * The values within the object are read-only and therefore can not be modified 
 * after the object was created.
 * 
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\net
 */
class Request {

	protected $itsData;

	protected $itsCreated;

	public function __construct() {
		$strip = ($pluginsLoaded = did_action("plugins_loaded")) || get_magic_quotes_gpc();

		$data = array(
				"get" => $strip ? stripslashes_deep($_GET) : $_GET,
				"post" => $strip ? stripslashes_deep($_POST) : $_POST,
				"cookie" => $strip ? stripslashes_deep($_COOKIE) : $_COOKIE,
				"server" => $strip && $pluginsLoaded ? stripslashes_deep($_SERVER) : $_SERVER
			);

		$data["request"] = array_merge($data["get"], $data["post"]);

		$this->itsData = $data;

		$this->itsCreated = microtime(true);
	}

	public function __get($name) {
		return @$this->itsData[strtolower($name)];
	}
}