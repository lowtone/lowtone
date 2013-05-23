<?php
namespace lowtone\db\queries\expressions;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\queries\expressions
 */
class Column extends Expression {

	protected $itsTable,
		$itsColumn;

	const ALL = "*";

	public function __construct($column, $table = NULL) {
		$this->itsColumn = $column;
		$this->itsTable = $table;
	}
	
	public function expression() {
		$expr = self::ALL === $this->itsColumn ? $this->itsColumn : $this->__escapeIdentifier($this->itsColumn);

		if (!is_null($this->itsTable)) {
			$table = Table::__cast($this->itsTable);

			if (NULL !== ($alias = $table->alias()))
				$table = aliases\Alias::__cast($alias);

			$expr = $table . "." . $expr;
		}

		return $expr;
	}

	public function column($column = NULL) {
		return $this->__prop("itsColumn", $column);
	}

	public function table($table = NULL) {
		return $this->__prop("itsColumn", $table);
	}

}