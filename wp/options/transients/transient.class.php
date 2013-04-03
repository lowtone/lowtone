<?php
namespace lowtone\wp\options\transients;
use lowtone\wp\options\Option;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\options\transients
 */
class Transient extends Option {
	
	const PREFIX = "_transient",
		PREFIX_TIMEOUT = "_timeout";

	// Static

	public static function __prefix() {
		return static::PREFIX;
	}

	public static function __prefix_timeout() {
		return __prefix() . static::PREFIX_TIMEOUT;
	}
	
}