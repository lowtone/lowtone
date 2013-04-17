<?php
namespace lowtone\db\records\queries\conditions;
use lowtone\db\records\Record,
	lowtone\db\records\queries\expressions\Expression;

class Condition {

	protected $itsConditions;

	protected $itsLogicalOperator = "=",
		$itsRelationalOperator = "AND";

	const OPTION_LOGICAL_OPERATOR = "logical_operator",
		OPTION_RELATIONAL_OPERATOR = "relational_operator";

	public function __construct($conditions, $options = NULL) {
		$this->itsConditions = $conditions;

		if (isset($options[self::OPTION_LOGICAL_OPERATOR]))
			$this->itsLogicalOperator = $options[self::OPTION_LOGICAL_OPERATOR];

		if (isset($options[self::OPTION_RELATIONAL_OPERATOR]))
			$this->itsRelationalOperator = $options[self::OPTION_RELATIONAL_OPERATOR];

	}

	public function __toString() {
		$condition = $this;

		$conditions = (array) $this->itsConditions;

		return (string) implode(" {$this->itsRelationalOperator} ", array_map(function($key, $value) use ($condition) {
			if ($value instanceof Condition)
				return "({$value})";

			if (!($key instanceof Expression))
				$key = Record::__escapeIdentifier($key);

			if (!($value instanceof Expression))
				$value = Record::__escape($value);

			return $key . " " . $condition->logicalOperator() . " " . $value;
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