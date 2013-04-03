<?php
namespace lowtone\types\datetime;
use DateTimeZone as Base,
	lowtone\types\numbers\Number;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\datetime
 */
class DateTimeZone extends Base {

	/**
	 * Takes an input timezone string and converts it to a valid PHP timezone 
	 * (see http://php.net/manual/en/timezones.php for a list of PHP supported 
	 * timezones).
	 * @param string $timezone The input timezone.
	 * @return string Returns a valid PHP timezone string.
	 */
	public static function validTimezone($timezone) {
		if (preg_match("/^UTC([\+-]\d{1,2})$/i", $timezone, $matches)) 
			$timezone = "Etc/GMT" . sprintf("%+d", Number::create($matches[1])->neg()->intval());
		
		return $timezone;
	}

	/**
	 * Get a DateTimeZone object for the timezone setting for WordPress.
	 * @return DateTimeZone Returns a DateTimeZone object on success.
	 */
	public static function wp() {
		return new static(static::validTimezone(get_option("timezone_string") ?: "UTC" . sprintf("%+d", get_option("gmt_offset"))));
	}

}