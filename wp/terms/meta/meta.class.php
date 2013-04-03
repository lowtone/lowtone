<?php
namespace lowtone\wp\terms\meta;
use lowtone\wp\meta\Meta as Base,
	lowtone\db\records\schemata\Schema,
	lowtone\db\records\schemata\properties\Property,
	lowtone\wp\options\Option;

/**
 * Term Meta
 * This class provides meta support for terms which WordPress doesn't
 * natively support. It requires a table to be created in the database 
 * which happens the first time the class is inlcuded (see the code at 
 * the bottom).
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\terms\meta
 */
class Meta extends Base {
		
	const PROPERTY_TERM_ID = "term_id";

	const OPTION_VERSION = "lowtone_term_meta_version";

	const VERSION = "1.0";

	// Getters
	
	public function getTermId() {return $this->__get(self::PROPERTY_TERM_ID);}

	// Install
	
	public static function install() {
		if (version_compare(Option::getOption(self::OPTION_VERSION), self::VERSION, ">="))
			return true;

		$query = 'CREATE TABLE IF NOT EXISTS  `%1$s` (' .
					'`%2$s` bigint(20) unsigned NOT NULL AUTO_INCREMENT,' .
					'`%3$s` bigint(20) unsigned NOT NULL DEFAULT \'0\',' .
					'`%4$s` varchar(255) DEFAULT NULL,' .
					'`%5$s` longtext,' .
					'PRIMARY KEY (`%2$s`),' .
					'KEY `%3$s` (`%3$s`),' .
					'KEY `%4$s` (`%4$s`)' .
				') ENGINE=MyISAM DEFAULT CHARSET=utf8;';

		$args = array(
				static::__getTable(),
				self::PROPERTY_META_ID,
				self::PROPERTY_TERM_ID,
				self::PROPERTY_META_KEY,
				self::PROPERTY_META_VALUE
			);

		$query = vsprintf($query, $args);

		if (!$GLOBALS["wpdb"]->query($query))
			return false;

		Option::updateOption(self::OPTION_VERSION, self::VERSION, Option::AUTOLOAD_YES);

		return true;
	}

	// Static

	public static function __getTableBase() {
		return "termmeta";
	}

	public static function __getSchema() {
		return parent::__getSchema()
			->__set(self::PROPERTY_TERM_ID, array(
					Property::ATTRIBUTE_TYPE => Property::TYPE_INT,
					Property::ATTRIBUTE_LENGTH => 20
				));
	}
	
}

// Install on include

Meta::install();