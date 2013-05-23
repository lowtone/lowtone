<?php
namespace lowtone\db\records\queries\expressions;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\queries\expressions
 */
abstract class Expression extends base\Base {

	protected $itsAlias;
	
	public function expression() {
		return "NULL";
	}

	public function __toString() {
		$str = $this->expression();

		if (!is_null($this->itsAlias))
			$str .= " AS " . aliases\Alias::__cast($this->itsAlias);

		return $str;
	}

	public function alias($alias = NULL) {
		return $this->__prop("itsAlias", $alias);
	}

}