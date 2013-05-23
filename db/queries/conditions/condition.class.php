<?php
namespace lowtone\db\queries\conditions;
use ArrayAccess,
	lowtone\db\records\Record,
	lowtone\db\queries\expressions\Expression;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\queries\conditions
 */
class Condition implements ArrayAccess {

	protected $itsPairs;

	protected $itsLogicalOperator = "AND",
		$itsRelationalOperator = "=";

	const OPTION_LOGICAL_OPERATOR = "logical_operator",
		OPTION_RELATIONAL_OPERATOR = "relational_operator";

	public function __construct($pairs = NULL, $options = NULL) {
		foreach ((array) $pairs as $a => $b) 
			$this->add($a, $b);

		if (isset($options[self::OPTION_LOGICAL_OPERATOR]))
			$this->itsLogicalOperator = $options[self::OPTION_LOGICAL_OPERATOR];

		if (isset($options[self::OPTION_RELATIONAL_OPERATOR]))
			$this->itsRelationalOperator = $options[self::OPTION_RELATIONAL_OPERATOR];

	}

	public function offsetSet($offset, $value) {
		$this->itsPairs[] = $value;

		return $this;
	}

	public function offsetExists($offset) {
		return isset($this->itsPairs[$offset]);
	}
		
	public function offsetUnset($offset) {
		unset($this->itsPairs[$offset]);
	}
	
	public function offsetGet($offset) {
		return isset($this->itsPairs[$offset]) ? $this->itsPairs[$offset] : null;
	}

	public function add($a, $b, $relationalOperator = NULL) {
		if (isset($relationalOperator) && $this->itsRelationalOperator != $relationalOperator) {

			if (0 < count($this->itsPairs)) {
				$condition = new Condition();

				$condition->add($a, $b, $relationalOperator);

				return ($this->itsPairs[] = $condition);
			} else
				$this->itsRelationalOperator = $relationalOperator;

		}

		$this->itsPairs[] = array($a, $b);

		return $this;
	}

	public function __toString() {
		$condition = $this;

		return (string) implode(" {$this->itsLogicalOperator} ", array_map(function($pair) use ($condition) {
			if ($pair instanceof Condition)
				return "({$pair})";

			list($a, $b) = $pair;

			if (is_array($a))
				$a = new Condition($a);
			
			if ($a instanceof Condition)
				return "({$a})";

			// Key

			if (!($a instanceof Expression))
				$a = Record::__escapeIdentifier($a);

			// Value
			
			$operator = $condition->relationalOperator();

			if (!($b instanceof Expression)) {

				if (NULL === $b) {
					$b = "NULL";

					switch ($operator) {
						case "=":
							$operator = "IS";
							break;

						case "!=":
						case "<>":
						case ">":
						case "<":
							$operator = "IS NOT";
							break;
					}

				} else
					$b = Record::__escape($b);

			}

			return $a . " " . $operator . " " . $b;
		}, (array) $this->itsPairs));
	}

	public function logicalOperator($logicalOperator = NULL) {
		if (!isset($logicalOperator))
			return $this->itsLogicalOperator;

		$this->itsLogicalOperator = $logicalOperator;

		return $this;
	}

	public function relationalOperator($relationalOperator = NULL) {
		if (!isset($relationalOperator))
			return $this->itsRelationalOperator;

		$this->itsRelationalOperator = $relationalOperator;

		return $this;
	}

	// Static
	
	public static function create($condition, $options = NULL) {
		return new static($condition, $options);
	}

}