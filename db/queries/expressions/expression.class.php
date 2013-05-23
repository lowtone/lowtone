<?php
namespace lowtone\db\queries\expressions;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\queries\expressions
 */
abstract class Expression extends base\Base {

	protected $itsVal,
		$itsAlias;

	protected $itsValEscape = "__escape";

	const NULL = "NULL";

	public function __construct($val, $alias = NULL) {
		$this->itsVal = $val;
	}
	
	public function expression() {
		return is_null($val = $this->val()) ? self::NULL : $this->escapeVal($val);
	}

	public function __toString() {
		$str = $this->expression();

		if (!is_null($this->itsAlias))
			$str .= " AS " . aliases\Alias::__cast($this->itsAlias);

		return $str;
	}

	public function val($val = NULL) {
		return $this->__prop("itsVal", $val);
	}

	public function alias($alias = NULL) {
		return $this->__prop("itsAlias", $alias);
	}

	protected function escapeVal($val) {
		return $this->{$this->itsValEscape}($val);
	}

	// Static
	
	public static function fromString($string) {
		 return new static($string);
	}

}