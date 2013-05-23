<?php
namespace lowtone\db\records\queries\expressions\base;
use lowtone\db\records\Record;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\queries\expressions\base
 */
abstract class Base {
	
	public function __prop($prop, $newVal = NULL) {
		if (!isset($newVal))
			return $this->{$prop};

		$this->{$prop} = $newVal;

		return $this;
	}

	// Escape

	public function __escape($val) {
		return Record::__escape($val);
	}

	public function __escapeIdentifier($val) {
		return Record::__escapeIdentifier($val);
	}

	// Static

	public static function __cast($val) {
		if ($val instanceof static)
			return $val;

		return new static($val);
	}

}