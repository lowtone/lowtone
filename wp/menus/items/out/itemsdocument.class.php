<?php
namespace lowtone\wp\menus\items\out;
use lowtone\dom\Document;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\menus\items\out
 */
class ItemsDocument extends Document {
	
	/**
	 * @var array
	 */
	protected $itsItems;
	
	const ITEM_DOCUMENT_OPTIONS = "item_document_options",
		DEPTH = "depth";
	
	public function __construct($items) {
		parent::__construct();
		
		$this->itsItems = $items;
		
		$this->updateBuildOptions(array(
			self::DEPTH => 0
		));
		
	}
	
	public function updateBuildOptions(array $options) {
		parent::updateBuildOptions($options);
		
		$itemsDocumentOptions = (array) $this->itsBuildOptions;
		
		$itemsDocumentOptions[self::DEPTH] = $this->getBuildOption(self::DEPTH) + 1;
		
		$this->transferBuildOptions(self::ITEM_DOCUMENT_OPTIONS, array(
			ItemDocument::ITEMS_DOCUMENT_OPTIONS => $itemsDocumentOptions
		));
		
		return $this->itsBuildOptions;
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		$itemsElement = $this
			->createAppendElement("items")
			->setAttributes(array(
				"depth" => $this->getBuildOption(self::DEPTH)
			));

		_wp_menu_item_classes_by_context($this->itsItems); // Add classes to items
		
		foreach ($this->itsItems as $item) {
			$itemDocument = new ItemDocument($item);
			
			$itemDocument->build($this->getBuildOption(self::ITEM_DOCUMENT_OPTIONS));
			
			if ($itemElement = $this->importDocument($itemDocument))
				$itemsElement->appendChild($itemElement);
			
		}
		
		return $this;
	}
	
}