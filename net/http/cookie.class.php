<?php
namespace lowtone\net\http;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\net\http
 */
class Cookie extends Record {

	public static function get($name) {
		return @$_COOKIE[$name];
	}

	public static function set($name, $value) {

	}

}