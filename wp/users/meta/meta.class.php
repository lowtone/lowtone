<?php
namespace lowtone\wp\users\meta;
use lowtone\wp\meta\Meta as Base;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\users\meta
 */
class Meta extends Base {
	
	const PROPERTY_UMETA_ID = "umeta_id",
		PROPERTY_USER_ID = "user_id";

	public static function __getTableBase() {
		return "usermeta";
	}
	public static function __getObjectIdProperty() {
		return self::PROPERTY_USER_ID;
	}
	
}