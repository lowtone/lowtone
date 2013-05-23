<?php
namespace lowtone\db\records\queries\expressions;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\queries\expressions
 */
class Table extends Expression {
	
	protected $itsTable;

	public function __construct($table) {
		$this->itsTable = $table;
	}

	public function expression() {
		return $this->__escapeIdentifier($this->itsTable);
	}

	public function table($table = NULL) {
		return $this->__prop("itsTable", $table);
	}

}