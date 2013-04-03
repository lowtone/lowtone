<?php
namespace lowtone\wp\terms\collections;
use lowtone\db\records\collections\Collection as Base;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\terms\collections
 */
class Collection extends Base {

	// Static

	public static function __getObjectClass() {
		return "lowtone\\wp\\terms\\Term";
	}

}