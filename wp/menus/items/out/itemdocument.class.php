<?php
namespace lowtone\wp\menus\items\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\menus\items\Item;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\menus\items\out
 */
class ItemDocument extends ObjectDocument {
	
	const ITEMS_DOCUMENT_OPTIONS = "items_document_options";
	
	public function __construct($item) {
		parent::__construct($item);
		
		/*if ($item instanceof Item)
			$this->itsItem->setChildren($item->getChildren());*/

		$this->updateBuildOptions(array(
				self::BUILD_ELEMENTS =>array(
						Item::PROPERTY_URL,
						Item::PROPERTY_TITLE,
						Item::PROPERTY_TARGET,
						Item::PROPERTY_ATTR_TITLE,
						Item::PROPERTY_DESCRIPTION
					)
			));
		
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		$itemElement = $this->documentElement;
		
		$classesElement = $itemElement->createAppendElement(Item::PROPERTY_CLASSES);
		
		foreach (array_filter((array) $this->itsObject->getClasses()) as $class) 
			$classesElement->createAppendElement("class", $class);

		// Build children
		
		if ($children = $this->itsObject->getChildren()) {
			$itemsDocument = new ItemsDocument($children);
			
			$itemsDocument->build($this->getBuildOption(self::ITEMS_DOCUMENT_OPTIONS));
			
			if ($itemsElement = $this->importDocument($itemsDocument))
				$itemElement->appendChild($itemsElement);
			
		}
		
		return $this;
	}
	
}