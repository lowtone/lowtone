<?php
namespace lowtone\wp\admin\tabs\collections\out;
use lowtone\types\objects\collections\out\CollectionDocument as Base,
	lowtone\wp\admin\tabs\collections\Collection;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\admin\tabs\collections\out
 */
class CollectionDocument extends Base {

	const DEFAULT_ICON = "default_icon",
		TITLE = "title";

	public function __construct(Collection $collection) {
		parent::__construct($collection);
		
		$this->setTemplate(realpath(dirname(dirname(__DIR__)) . "/templates/tabs.xsl"));
	}

	public function build(array $options = NULL) {
		$collection = $this->itsCollection;

		$this->itsCollection->each(function($object) use ($collection) {
			$object->active = ($collection->active() === $object->name) ?: NULL;
		});

		$return = parent::build($options);

		if ($title = $this->getBuildOption(self::TITLE)) 
			$this->documentElement->setAttribute("title", $title);

		if ($defaultIcon = $this->getBuildOption(self::DEFAULT_ICON)) 
			$this->documentElement->setAttribute("default_icon", $defaultIcon);

		return $return;
	}
	
}