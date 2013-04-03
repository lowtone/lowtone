<?php
namespace lowtone\wp\menus\out;
use lowtone\dom\Document,
	lowtone\wp\menus\Menu,
	lowtone\wp\menus\items\out\ItemsDocument;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\menus\out
 */
class MenuDocument extends Document {
	
	/**
	 * @var Menu
	 */
	protected $itsMenu;
	
	const ITEMS_DOCUMENT_OPTIONS = "items_document_options";
	
	public function __construct($menu) {
		parent::__construct();
		
		$this->itsMenu = new Menu($menu);
		
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		$menuElement = $this->createAppendElement("menu", array(
			"title" => $this->itsMenu->name()
		))->setAttributes(array(
			"id" => $this->itsMenu->slug()
		));
		
		if ($location = $this->itsMenu->getLocation())
			$menuElement->setAttribute("location", $location);
		
		if ($items = $this->itsMenu->getItems()) {
			$itemsDocument = new ItemsDocument($items);
			
			$itemsDocument->build($this->getBuildOption(self::ITEMS_DOCUMENT_OPTIONS));
			
			if ($itemsElement = $this->importDocument($itemsDocument))
				$menuElement->appendChild($itemsElement);
			
		}
		
		return $this;
	}
	
}