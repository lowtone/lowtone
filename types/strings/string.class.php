<?php
namespace lowtone\types\strings;
use ArrayObject;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\types\strings
 */
class String extends ArrayObject {
	
	public function __construct($string) {
		parent::__construct($this->split($string));
	}
	
	/**
	 * Convert a string to an array with support for multi-byte strings.
	 * @param string $string The subject string.
	 * @param int $length The maximum length for each chunk.
	 * @return array|bool Returns the split string as an array or FALSE if the
	 * required chunk length is less than 1. 
	 */
	public static function split($string, $length = 1) {
		if ($length < 1)
			return false;
			
		if (!($encoding = mb_detect_encoding($string)))
			$encoding = mb_internal_encoding();
			
		for ($result = array(), $i = 0; $i < self::length($string); $i += $length)
			$result[] = mb_substr($string, $i, $length, $encoding);
			
		return $result;
	}
	
	public static function stripWhiteSpace($string, $replacement = "") {
		return preg_replace("//", $replacement, $string);
	}
	
}