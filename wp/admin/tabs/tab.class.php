<?php
namespace lowtone\wp\admin\tabs;
use lowtone\db\records\Record,
	lowtone\db\records\schemata\properties\Property,
	lowtone\net\URL;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\tabs
 */
class Tab extends Record {

	const PROPERTY_NAME = "name",
		PROPERTY_TITLE = "title",
		PROPERTY_ICON = "icon",
		PROPERTY_CONTENT = "content",
		PROPERTY_URI = "uri",
		PROPERTY_ACTIVE = "active",
		PROPERTY_HIDDEN = "hidden",
		PROPERTY_SORT = "sort";

	// Static

	public static function __getCollectionClass() {
		return __NAMESPACE__ . "\\collections\\Collection";
	}

	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\TabDocument";
	}

	public static function __createSchema($defaults = NULL) {
		$schema = parent::__createSchema($defaults);

		$schema[self::PROPERTY_URI] = array(
				Property::ATTRIBUTE_GET => function($value, $tab) {
					if (isset($value)) {
						if (is_callable($value)) 
							$value = call_user_func($value, $tab);
						
						return $value;
					}

					return (string) URL::fromCurrent()->appendQuery(array("tab" => $tab->name));
				}
			);

		return $schema;
	}

}