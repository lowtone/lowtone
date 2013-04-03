<?php
namespace lowtone\ui\table;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\ui\table
 */
abstract class Rows {
	
	/**
	 * @var array
	 */
	protected $itsRows = array();
	
	public function appendRow(Row $row) {$this->itsRows[] = $row; return $this;}
	public function prependRow(Row $row) {array_unshift($this->itsRows, $row); return $this;}
	public function removeRow($offset) {$this->itsRows = array_diff_key($this->itsRows, array_flip(array((int) $offset)));}
	
}