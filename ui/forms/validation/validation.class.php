<?php
namespace lowtone\ui\forms\validation;
use ArrayObject;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\forms\validation
 */
class Validation extends ArrayObject {

	public function offsetSet($name, $value) {
		if (!is_callable($value))
			throw new exceptions\CallbackException();

		return parent::offsetSet($name, $value);
	}

	public function add($callback) {
		$this[] = $callback;

		return $this;
	}

	public function validate($input) {
		foreach ((array) $this as $callback) {

			if (false === call_user_func($callback, $input))
				return false;

		}
		
		return true;
	}

}