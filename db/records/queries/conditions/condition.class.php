<?php
namespace lowtone\db\records\queries\conditions;
use ArrayAccess,
	lowtone\db\records\Record,
	lowtone\db\records\queries\expressions\Expression;

class Condition implements ArrayAccess {

	protected $itsConditions;

	protected $itsLogicalOperator = "AND",
		$itsRelationalOperator = "=";

	const OPTION_LOGICAL_OPERATOR = "logical_operator",
		OPTION_RELATIONAL_OPERATOR = "relational_operator";

	public function __construct($conditions = NULL, $options = NULL) {
		$this->itsConditions = (array) $conditions;

		if (isset($options[self::OPTION_LOGICAL_OPERATOR]))
			$this->itsLogicalOperator = $options[self::OPTION_LOGICAL_OPERATOR];

		if (isset($options[self::OPTION_RELATIONAL_OPERATOR]))
			$this->itsRelationalOperator = $options[self::OPTION_RELATIONAL_OPERATOR];

	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) 
			$this->itsConditions[] = $value;
		else 
			$this->itsConditions[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->itsConditions[$offset]);
	}
		
	public function offsetUnset($offset) {
		unset($this->itsConditions[$offset]);
	}
	
	public function offsetGet($offset) {
		return isset($this->itsConditions[$offset]) ? $this->itsConditions[$offset] : null;
	}

	public function __toString() {
		$condition = $this;

		$conditions = (array) $this->itsConditions;

		return (string) implode(" {$this->itsLogicalOperator} ", array_map(function($key, $value) use ($condition) {
			if (is_array($value))
				$value = new Condition($value);
			
			if ($value instanceof Condition)
				return "({$value})";

			// Key

			if (!($key instanceof Expression))
				$key = Record::__escapeIdentifier($key);

			// Value
			
			$operator = $condition->relationalOperator();

			if (!($value instanceof Expression)) {

				if (NULL === $value) {
					$value = "NULL";

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
					$value = Record::__escape($value);

			}

			return $key . " " . $operator . " " . $value;
		}, array_keys($conditions), $conditions));
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