<?php
namespace lowtone\wp\posts\comments\meta;
use lowtone\wp\meta\Meta as Base;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\posts\comments\meta
 */
class Meta extends Base {
	
	const PROPERTY_COMMENT_ID = "comment_id";

	public static function __getTableBase() {
		return "commentmeta";
	}
	
}