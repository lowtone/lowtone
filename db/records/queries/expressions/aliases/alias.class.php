<?php
namespace lowtone\db\records\queries\expressions\aliases;
use lowtone\db\records\queries\expressions\base\Base;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\db\records\queries\expressions\aliases
 */
class Alias extends Base {

	protected $itsAlias = $alias;
	
	public function __construct($alias) {
		$this->itsAlias = $alias;
	}

	public function __toString() {
		return $this->__escapeIdentifier($this->itsAlias);
	}

	public function alias($alias = NULL) {
		return $this->__prop("itsAlias", $alias);
	}

}