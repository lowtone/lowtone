<?php
namespace lowtone\locales;
use lowtone\db\records\Record;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\locales
 */
class Locale extends Record {

	const PROPERTY_LANGUAGE = "language",
		PROPERTY_TERRITORY = "territory",
		PROPERTY_CHARSET = "charset",
		PROPERTY_MODIFIER = "modifier";
	
	public static function parse($string) {
		if (preg_match("/^([a-z]{2})(_([a-z]{2}))?(\.([a-z0-9\-]+))?(\@([a-z0-9]+))?$/i", $locale, $matches)) {
			@list(, $language,, $territory,, $charset,, $modifier) = $matches;

			return new Locale(array(
					self::PROPERTY_LANGUAGE => $language,
					self::PROPERTY_TERRITORY => $territory,
					self::PROPERTY_CHARSET => $charset,
					self::PROPERTY_MODIFIER => $modifier,
					"type" => "POSIX",
				));
		}
		
		return false;
	}

}