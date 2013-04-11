<?php
namespace lowtone\net;

/**
 * OOP wrapper for cURL.
 * 
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\net
 */
class Curl {

	protected $itsHandle;

	protected $itsOptions;
	
	public function __construct($url = NULL) {
		$this->itsHandle = $this->init();

		$this->setopt(CURLOPT_URL, $url);
	}

	public function __destruct() {
		@curl_close($this->itsHandle);
	}

	public function __call($name, $arguments) {
		$name = "curl_" . $name;

		if (!function_exists($name))
			throw new \Exception(sprintf("Call to undefined cURL function %s", $name));

		array_unshift($arguments, $this->itsHandle);

		return call_user_func_array($name, $arguments);
	}

	public function getopt($option) {
		return @$this->itsOptions[$option];
	}

	public function setopt($option, $value) {
		$this->itsOptions[$option] = $value;

		return $this->__call("setopt", func_get_args());
	}

	public function setopt_array(array $options) {
		array_merge($this->itsOptions, $options);

		return $this->__call("setopt_array", func_get_args());
	}

}