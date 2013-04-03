<?php
namespace lowtone\wp\admin\notices;
use Exception;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\notices
 */
class Error extends Notice {

	public function __construct ($message = "" , $code = 0, $severity = 1, $filename = __FILE__, $lineno = __LINE__, Exception $previous = NULL) {
		parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
	}
	
}