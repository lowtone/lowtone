<?php
namespace lowtone\db\records\collections;
use lowtone\types\objects\collections\Collection as Base;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\collections
 */
class Collection extends Base {
	
	public function save($defaults = NULL, $options = NULL) {
		return $this->each(function($object) use ($defaults, $options) {
			$object->save($defaults, $options);
		});
	}

	public function delete() {
		return $this
			->each(function($object) {
				$object->delete();
			})
			->drain();
	}

	// Static
	
	public static function __getObjectClass() {
		return "lowtone\\db\\records\\Record";
	}

}