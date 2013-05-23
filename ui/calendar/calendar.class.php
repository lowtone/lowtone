<?php
namespace lowtone\ui\calendar;
use lowtone\dom\Document,
	lowtone\types\datetime\DateTime;

/**
 * Create a simple calendar.
 * 
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 0.1
 * @package wordpress\libs\lowtone\ui\calendar
 */
class Calendar extends Document {

	protected $itsToday,
		$itsFirstOfMonth,
		$itsPreviousMonth,
		$itsNextMonth,
		$itsFirstDay;

	const YEAR = "year",
		MONTH = "month",
		DAY = "day",
		WEEK_OFFSET = "week_offset",
		BEFORE_DAYS = "before_days",
		MONTH_URL = "month_url",
		CREATE_MONTH = "build_month",
		DAY_URL = "day_url",
		CREATE_DAY = "build_day",
		LOCALES = "locales";

	/**
	 * Create a URL for the month from the given date/time object.
	 * @param DateTime $dateTime The subject date/time.
	 * @return URL Returns a URL object.
	 */
	public function createMonthUrl(DateTime $dateTime) {
		if (is_callable($callback = $this->getBuildOption(self::MONTH_URL)))
			return call_user_func($callback, $dateTime, $this);

		return $this->__url(array(
				self::YEAR => $dateTime->year,
				self::MONTH => $dateTime->month,
			));
	}

	/**
	 * Create a URL for the day from the given date/time object.
	 * @param DateTime $dateTime The subject date/time.
	 * @return URL Returns a URL object.
	 */
	public function createDayUrl(DateTime $dateTime) {
		if (is_callable($callback = $this->getBuildOption(self::DAY_URL)))
			return call_user_func($callback, $dateTime, $this);

		return $this->__url(array(
				self::YEAR => $dateTime->year,
				self::MONTH => $dateTime->month,
				self::DAY => $dateTime->day,
			));
	}

	/**
	 * Create a URL using the given options.
	 * @param array|NULL $options Options for the URL.
	 * @return URL Returns a URL object.
	 */
	protected function __url($options = NULL) {
		$url = URL::fromString(bloginfo("url"));

		$path = explode("/", trim($url->path, "/"));

		if (isset($options[self::YEAR]))
			$path[] = $options[self::YEAR];

		$pad = function($val) {
			return str_pad($val, 2, "0", STR_PAD_LEFT);
		};

		if (isset($options[self::MONTH]))
			$path[] = $pad($options[self::MONTH]);

		if (isset($options[self::DAY]))
			$path[] = $pad($options[self::DAY]);

		return $url
			->path("/" . implode("/", $path) . "/")
			->query($query);
	}
	
	/**
	 * Build the document.
	 * @param array|NULL $options Build options.
	 * @return Calendar Returns the Calendar instance for method chaining.
	 */
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);

		// Current month

		$this->itsToday = DateTime::now();

		$this->itsFirstOfMonth = clone $this->itsToday;

		$this->itsFirstOfMonth
			->day(1)
			->hours(0)
			->minutes(0)
			->seconds(0);

		if (is_numeric($year = $this->getBuildOption(self::YEAR))) {
			$this->itsFirstOfMonth->year($year);

			$this->itsFirstOfMonth->month(is_numeric($month = $this->getBuildOption(self::MONTH)) ? $month : 1);
		}

		// Previous month

		$this->itsPreviousMonth = clone $this->itsFirstOfMonth;

		$this->itsPreviousMonth->month($this->itsFirstOfMonth->month - 1);

		// Next month

		$this->itsNextMonth = clone $this->itsFirstOfMonth;

		$this->itsNextMonth->month($this->itsFirstOfMonth->month + 1);

		// Document
		
		$document = $this;

		$month = function($dateTime, $append = NULL) use ($document) {
			$month = array_merge(array(
					"name" => $dateTime->formatLocalized("F"),
					"short_name" => $dateTime->formatLocalized("M"),
					"year" => $dateTime->year,
					"monthnum" => $dateTime->month,
					"url" => (string) $document->createMonthUrl($dateTime),
				), (array) $append);

			if (is_callable($callback = $document->getBuildOption(Calendar::CREATE_MONTH)))
				$month = call_user_function($callback, $month, $document);

			return $month;
		};

		$calendarElement = $document
			->createAppendElement("calendar", array(
				"current_month" => $month($this->itsFirstOfMonth),
				"previous_month" => $month($this->itsPreviousMonth, array(
					"locales" => array(
						"title" => $this->getBuildOption(array(self::LOCALES, "previous_month")) ?: __("Previous month"),
					)
				)),
				"next_month" => $month($this->itsNextMonth, array(
					"locales" => array(
						"title" => $this->getBuildOption(array(self::LOCALES, "next_month")) ?: __("Next month"),
					)
				)),
				"locales" => array(

				),
			));

		// Weekdays

		$weekdays = array(
				__("Mon"),
				__("Tue"),
				__("Wed"),
				__("Thu"),
				__("Fri"),
				__("Sat"),
				__("Sun"),
			);

		$weekOffset = $this->getBuildOption(self::WEEK_OFFSET) ?: 0;

		if (0 <> $weekOffset) {
			$slice = (1 > $weekOffset ? 7 : 0) + $weekOffset;

			$weekdays = array_merge(array_slice($weekdays, $slice), array_slice($weekdays, 0, $slice));
		}

		$weekdaysElement = $calendarElement->createAppendElement("weekdays");

		foreach ($weekdays as $day) 
			$weekdaysElement->appendCreateElement("day", $day);

		// Start

		$start = 0 - ($this->itsFirstOfMonth->day_of_week - 1);

		$start += $weekOffset;

		$this->itsFirstDay = clone $this->itsFirstOfMonth;

		$this->itsFirstDay->day($start + 1);

		// Before days

		if (is_callable($beforeDays = $this->getBuildOption(self::BEFORE_DAYS)))
			call_user_func($beforeDays, $this);

		// Days
		
		$daysElement = $calendarElement->createAppendElement("days");

		$relText = array("previous", "current", "next");

		$dateTime = clone $this->itsFirstOfMonth;

		for ($i = $start, $j = 0, $daysInMonth = $this->itsFirstOfMonth->days_in_month; $i < $daysInMonth; $j++) {
			$weekElement = $daysElement->createAppendElement("week");

			for ($k = 0; $k < 7; $k++, $i++) {
				$dayNum = $i + 1;

				$monthRel = 1 > $dayNum ? -1 : ($dayNum > $this->itsFirstOfMonth->days_in_month ? 1 : 0);

				$month = $this->itsFirstOfMonth;

				if (0 > $monthRel) {

					$month = $this->itsPreviousMonth;

					$dayNum = $this->itsPreviousMonth->days_in_month + $dayNum;

				} else if (0 < $monthRel) {

					$month = $this->itsNextMonth;

					$dayNum -= $this->itsFirstOfMonth->days_in_month;

				}

				$dateTime
					->year($month->year)
					->month($month->month)
					->day($dayNum);

				$attributes = array(
						"year" => $month->year,
						"month" => $month->month,
						"url" => (string) $this->createDayUrl($dateTime),
						"today" => (int) ($month->year == $this->itsToday->year && $month->month == $this->itsToday->month && $dayNum == $this->itsToday->day),
						"month_rel" => $relText[$monthRel + 1],
					);

				if ($createDay = $this->getBuildOption(self::CREATE_DAY))
					$attributes = call_user_func($createDay, $attributes, $dateTime, $this);

				$weekElement
					->createAppendElement("day", $dayNum)
						->setAttributes($attributes);
			}
		}

		return $this;
	}

	// Property access

	public function today() {
		return $this->itsToday;
	}

	public function firstOfMonth() {
		return $this->itsFirstOfMonth;
	}

	public function previousMonth() {
		return $this->itsPreviousMonth;
	}

	public function nextMonth() {
		return $this->itsNextMonth;
	}

	public function firstDay() {
		return $this->itsFirstDay;
	}

}