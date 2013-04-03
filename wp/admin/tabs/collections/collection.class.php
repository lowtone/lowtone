<?php
namespace lowtone\wp\admin\tabs\collections;
use lowtone\db\records\collections\Collection as Base;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\tabs\collections
 */
class Collection extends Base {

	protected $itsActive;

	public function active($active = NULL) {
		if (!isset($active)) 
			return $this->itsActive;

		$this->itsActive = $active;

		return $this;
	}

	// Output
	
	public function out(array $options = NULL) {
		$document = $this
			->createDocument()
			->build($options);

		if ($template = @$options["template"])
			$document->setTemplate($template);
		
		echo $document
			->transform()
			->saveHTML();
	}

	// Static
	
	public static function __getObjectClass() {
		return "lowtone\\wp\\admin\\tabs\\Tab";
	}

	public static function __getDocumentClass() {
		return __NAMESPACE__ . "\\out\\CollectionDocument";
	}
	
}