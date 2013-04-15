<?php
namespace lowtone\net;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\net
 */
class Http {

	protected $itsInfo;

	/**
	 * The Http object used for the last static call.
	 * @var Http
	 */
	protected static $__lastStatic;

	const METHOD_GET = "GET",
		METHOD_POST = "POST",
		METHOD_PUT = "PUT",
		METHOD_DELETE = "DELETE";

	public function request($method, $url, array $data = NULL, array $options = NULL) {
		$request = new Curl();

		$options = array(
				CURLOPT_CUSTOMREQUEST => strtoupper($method),
				CURLOPT_URL => $url,
				CURLOPT_POSTFIELDS => (array) $data,
				CURLOPT_FOLLOWLOCATION => true
			) + 
			(array) $options + 
			array(
				CURLOPT_RETURNTRANSFER => true
			);

		$request->setopt_array($options);

		$result = $request->exec();

		$this->itsInfo = $request->getInfo();
		
		return $result;
	}

	public function __invoke() {
		return call_user_func(array($this, "request"), func_get_args());
	}

	public function __call($name, $arguments) {
		switch (strtoupper($name)) {
			case self::METHOD_GET:
			case self::METHOD_PUT:
			case self::METHOD_POST:
			case self::METHOD_DELETE:
				$arguments = (array) $arguments;

				array_unshift($arguments, $name);

				return call_user_func_array(array($this, "request"), $arguments);
		}
	}

	// Getters

	public function info($get = NULL) {
		if (isset($get))
			return @$this->itsInfo[$get];

		return $this->itsInfo;
	}

	// Static

	public static function __callStatic($name, $arguments) {
		self::$__lastStatic = new Http();

		return call_user_func_array(array(self::$__lastStatic, $name), $arguments);
	}

	public static function __lastStatic() {
		return self::$__lastStatic;
	}

}