<?php
namespace lowtone\wp\interfaces;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\interfaces
 */
interface Registrable {

	const OPTION_TEXTDOMAIN = "textdomain";
	
	/**
	 * Register a custom post type.
	 * @return bool Returns TRUE on success.
	 */
	public static function __register(array $options = NULL);
	
}