<?php
namespace lowtone\buildables;
use lowtone\util\options\Options;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\buildables
 */
abstract class Buildable implements interfaces\Buildable {
	
	protected $itsBuildOptions;
	
	public function __construct() {
		$this->itsBuildOptions = new Options();
	}
	
	public function updateBuildOptions(array $options) {
		$this->itsBuildOptions->updateOptions($options);
	}
	
}