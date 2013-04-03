<?php
namespace lowtone;
use lowtone\types\arrays\Map,
	lowtone\types\singletons\Singleton;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone
 */
class Globals extends Singleton {

	protected $itsStorage;

	protected function __construct() {
		$this->itsStorage = new Map();
	}

	public function get() {
		return call_user_func_array(array(self::__instance()->itsStorage, "__pathGet"), func_get_args());
	}

	public function set() {
		return call_user_func_array(array(self::__instance()->itsStorage, "__pathSet"), func_get_args());
	}

	// Static

	public static function __callStatic($name, $arguments) {
		return call_user_func_array(array(self::__instance(), $name), $arguments);
	}
	
}