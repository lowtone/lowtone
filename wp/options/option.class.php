<?php
namespace lowtone\wp\options;
use lowtone\db\records\Record;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\options
 */
class Option extends Record {

	const PROPERTY_OPTION_ID = "option_id",
		PROPERTY_OPTION_NAME = "option_name",
		PROPERTY_OPTION_VALUE = "option_value",
		PROPERTY_AUTOLOAD = "autoload";

	const AUTOLOAD_YES = "yes",
		AUTOLOAD_NO = "no";

	// Static

	public static function update($option, $value, $autoload = self::AUTOLOAD_NO) {
		if (self::get_option($option) != $value) 
			return update_option($option, $value);
		
		return add_option($option, $value, '', $autoload);
	}

	public static function get($option) {
		return get_option($option);
	}
}