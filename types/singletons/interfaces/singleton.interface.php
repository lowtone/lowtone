<?php
namespace lowtone\types\singletons\interfaces;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\singletons\interfaces
 */
interface Singleton {

	// protected function __construct();
	
	public static function __instance();

}