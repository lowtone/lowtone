<?php
namespace lowtone\wp\meta\collections;
use lowtone\db\records\collections\Collection as Base,
	lowtone\wp\meta\Meta;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\meta\collections
 */
class Collection extends Base {

	public function findByKey($key) {
		return $this->find(array(
				Meta::PROPERTY_META_KEY => $key
			));
	}

	public function offsetGet($index) {
		if (is_string($index))
			return $this->findByKey($index);

		return parent::offsetGet($index);
	}

	public function offsetSet($index, $newval) {
		if (is_string($index)) {

			if (($collection = $this[$index]) && $collection->length) {
				foreach ($collection as $meta) {
					$meta->setMetaValue($newval);
				}

				return $collection;
			} else {
				$meta = new Meta(array(
						Meta::PROPERTY_META_KEY => $index,
						Meta::PROPERTY_META_VALUE => $newval
					));

				parent::offsetSet(NULL, $meta);

				return $this[$index];
			}
		}

		return parent::offsetSet($index, $newval);
	}

	// Static

	public static function __getObjectClass() {
		return "lowtone\\wp\\meta\\Meta";
	}

}