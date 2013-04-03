<?php
namespace lowtone\util\buildables\interfaces;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\buildables\interfaces
 */
interface Buildable {
	
	const BUILD_LOCALES = "build_locales";
	
	public function build(array $options = NULL);
	
}