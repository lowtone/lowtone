<?php
namespace lowtone\ui\forms\special;
use lowtone\ui\forms\Input,
	lowtone\types\datetime\DateTimeZone as DTZ;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\special
 */
class DateTimeZone extends Input {

	public function init() {
		$values = array();
		$altValues = array();

		foreach (DTZ::listIdentifiers() as $identifier) {
			@list($continent, $city, $subCity)  = explode("/", str_replace("_", " ", $identifier));

			$values[$continent][] = $identifier;
			$altValues[$continent][] = implode(", ", array_filter(array($city, $subCity)));
		}
		
		return $this(array(
				self::PROPERTY_TYPE => self::TYPE_SELECT,
				self::PROPERTY_LABEL => $this[self::PROPERTY_LABEL] ?: __("Timezone", "lowtone"),
				self::PROPERTY_VALUE => $values,
				self::PROPERTY_ALT_VALUE => $altValues
			));
	}

}