<?php
namespace lowtone\types\datetime;
use DateTime as Base,
	lowtone\types\datetime\exceptions\DateTimeException;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\datetime
 */
class DateTime extends Base {

	protected $itsDefaultFormat = "Y-m-d H:i:s";

	const EXCEPTION_SET_DATE = 1,
		EXCEPTION_SET_ISO_DATE = 2,
		EXCEPTION_SET_TIME = 3,
		EXCEPTION_SET_TIMESTAMP = 4,
		EXCEPTION_SET_TIMEZONE = 5,
		EXCEPTION_SUB = 6;

	public function formatLocalized($format) {
		$dateTime = $this;

		$format = preg_replace_callback("/(^|[^\\\])([DFlMaA])/", function($matches) use ($dateTime) {
			global $wp_locale;

			$replace = $matches[2];

			switch ($replace) {
				case "D":
					$replace = $wp_locale->get_weekday_abbrev($wp_locale->get_weekday($dateTime->format("w")));
					break;

				case "F":
					$replace = $wp_locale->get_month($dateTime->format("m"));
					break;

				case "l":
					$replace = $wp_locale->get_weekday($dateTime->format("w"));
					break;

				case "M":
					$replace = $wp_locale->get_month_abbrev($wp_locale->get_month($dateTime->format("m")));
					break;

				case "a":
					$replace = $wp_locale->get_meridiem($dateTime->format("a"));
					break;

				case "A":
					$replace = $wp_locale->get_meridiem($dateTime->format("A"));
					break;
			}

			return $matches[1] . backslashit($replace);
		}, $format);

		return $this->format($format);
	}

	public function sameDay(DateTime $compare) {
		return $this->format("Y") == $compare->format("Y") && $this->format("z") == $compare->format("z");
	}

	// Interface functions

	public function year($year = NULL) {
		if (!isset($year))
			return $this->year;

		$this->setDate($year, $this->month, $this->day);

		return $this;
	}

	public function month($month = NULL) {
		if (!isset($month))
			return $this->month;

		$this->setDate($this->year, $month, $this->day);

		return $this;
	}

	public function day($day = NULL) {
		if (!isset($day))
			return $this->day;

		$this->setDate($this->year, $this->month, $day);

		return $this;
	}

	public function hours($hours = NULL) {
		if (!isset($hours))
			return $this->hours;

		$this->setTime($hours, $this->minutes, $this->seconds);

		return $this;
	}

	public function minutes($minutes = NULL) {
		if (!isset($minutes))
			return $this->minutes;

		$this->setTime($this->hours, $minutes, $this->seconds);

		return $this;
	}

	public function seconds($seconds = NULL) {
		if (!isset($seconds))
			return $this->seconds;

		$this->setTime($this->hours, $this->minutes, $seconds);

		return $this;
	}

	// Magic
	
	public function __toString() {
		return $this->format($this->itsDefaultFormat);
	}

	public function __invoke($time = NULL) {
		if (!is_null($time))
			$this->setDateTime($time);

		return $this;
	}

	public function __get($name) {
		switch ($name) {
			case "year":
				return $this->format("Y");

			case "month":
				return $this->format("m");
				
			case "day":
				return $this->format("d");
				
			case "hours":
				return $this->format("H");
				
			case "minutes":
				return $this->format("i");
				
			case "seconds":
				return $this->format("s");
				
		}
	}

	// Setters
	
	/**
	 * Set the date and time from a string or DateTime object.
	 * @param string|DateTime $time The new date and time value.
	 * @return DateTime Returns the DateTime object for chaining.
	 */
	public function setDateTime($time) {
		if (!($time instanceof DateTime))
			$time = DateTime::createFromString($time);

		$this->setTimestamp($time->getTimestamp());

		return $this;
	}

	public function setDefaultFormat($format) {
		$this->itsDefaultFormat = $format;

		return $this;
	}

	// Override some setters to throw DateTimeExceptions instead of returning FALSE.
	
	public function setDate($year, $month, $day) {
		if (false === ($result = parent::setDate($year, $month, $day)))
			throw new DateTimeException("Couldn't set date", self::EXCEPTION_SET_DATE);

		return $result;
	}

	public function setISODate($year, $week, $day = 1) {
		if (false === ($result = parent::setISODate($year, $week, $day)))
			throw new DateTimeException("Couldn't set ISO date", self::EXCEPTION_SET_ISO_DATE);

		return $result;
	}

	public function setTime($hour, $minute, $second = 0) {
		if (false === ($result = parent::setTime($hour, $minute, $second)))
			throw new DateTimeException("Couldn't set Unix timestamp", self::EXCEPTION_SET_TIME);

		return $result;
	}

	public function setTimestamp($unixtimestamp) {
		if (false === ($result = parent::setTimestamp($unixtimestamp)))
			throw new DateTimeException("Couldn't set Unix timestamp", self::EXCEPTION_SET_TIMESTAMP);

		return $result;
	}

	public function setTimezone(\DateTimeZone $timezone) {
		if (false === ($result = parent::setTimezone($timezone)))
			throw new DateTimeException("Couldn't set timezone", self::EXCEPTION_SET_TIMEZONE);

		return $result;
	}

	public function sub(\DateInterval $interval) {
		if (false === ($result = parent::sub($interval)))
			throw new DateTimeException("Couldn't subtract", self::EXCEPTION_SUB);

		return $result;
	}

	// Static

	public static function fromUnix($timestamp, \DateTimeZone $timezone = NULL) {
		$dateTime = new static("@" . $timestamp);

		if (!($timezone instanceof \DateTimeZone))
			$timezone = DateTimeZone::wp();

		$dateTime->setTimezone($timezone);

		return $dateTime;
	}

	public static function fromString($time, \DateTimeZone $timezone = NULL) {
		if (!($timezone instanceof \DateTimeZone))
			$timezone = DateTimeZone::wp();

		return new static($time, $timezone);
	}

	public static function now() {
		return static::createFromUnixTimestamp(time());
	}

	public static function abstractTimeArray($seconds) {
		if (0 == $seconds)
			return array();
		else if ($seconds < 1) 
			return array("ms" => (int) number_format($seconds * 1000));
		else if ($seconds >= 31557600) // 365.25 days == 1 year
			return array_merge(static::abstractTimeArray($seconds % 31557600), array("y" => (int) floor($seconds / 31557600)));
		else if ($seconds >= 86400) // 24 hours == 1 day
			return array_merge(static::abstractTimeArray($seconds % 86400), array("d" => (int) floor($seconds / 86400)));
		else if ($seconds >= 3600) // 60 minutes == 1 hour
			return array_merge(static::abstractTimeArray($seconds % 3600), array("h" => (int) floor($seconds / 3600)));
		else if ($seconds >= 60) // 60 seconds == 1 minute
			return array_merge(static::abstractTimeArray($seconds % 60), array("m" => (int) floor($seconds / 60)));

		return array_merge(static::abstractTimeArray($seconds - ($fseconds = (int) floor($seconds))), array("s" => $fseconds));
	}

	public static function abstractTime($seconds, $template = "%d%s") {
		for ($parts = static::abstractTimeArray($seconds), $order = array("y", "d", "h", "m", "s", "ms"), $strings = array(); list(, $part) = each($order);) {
			if (!isset($parts[$part]))
				continue;

			$strings[] = sprintf($template, $parts[$part], $part);
		}

		return implode(" ", $strings);
	}

	// Deprecated

	/**
	 * @deprecated Since v1.0. Awaiting replacement in plugins before removal.
	 */
	public static function createFromUnixTimestamp($timestamp, \DateTimeZone $timezone = NULL) {
		return static::fromUnix($timestamp, $timezone);
	}

	/**
	 * @deprecated Since v1.0. Awaiting replacement in plugins before removal.
	 */
	public static function createFromString($time, \DateTimeZone $timezone = NULL) {
		return static::fromString($time, $timezone);
	}

}