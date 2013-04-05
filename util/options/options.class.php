<?php
namespace lowtone\util\options;
use lowtone\types\arrays\Map;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\util\options
 */
class Options extends Map {
	
	/**
	 * Update the build options with the given set of options
	 * @param array $options A set of new options to overwrite previous options.
	 * @return array Returns the resulting option set.
	 */
	public function updateOptions(array $options) {
		return $this->setOptions((array) $this->mergeRecursive($options));
	}
	
	// Getters
	
	public function getOptions() {return $this->getArrayCopy();}
	
	public function getOption($option) {return @$this[strtolower($option)];}
		
	// Setters
	
	public function setOptions(array $options) {$this->exchangeArray($options); return $this;}
	
	public function setOption($option, $value) {$this[strtolower($option)] = $value; return $this;}
	
}