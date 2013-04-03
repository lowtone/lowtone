<?php
namespace lowtone\wp\menus\out;
use lowtone\dom\Document,
	lowtone\wp\menus\Menu;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\menus\out
 */
class MenusDocument extends Document {
	
	/**
	 * @var array
	 */
	protected $itsMenus;
	
	const MENU_DOCUMENT_OPTIONS = "menu_document_options";
	
	public function __construct() {
		parent::__construct();
		
		$this->itsMenus = Menu::loadMenus();
		
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		$menusElement = $this->createAppendElement("menus");
		
		foreach ($this->itsMenus as $menu) {
			$menuDocument = new MenuDocument($menu);
			
			$menuDocument->build($this->getBuildOption(self::MENU_DOCUMENT_OPTIONS));
			
			if ($menuElement = $this->importDocument($menuDocument))
				$menusElement->appendChild($menuElement);
			
		}
		
		return $this;
	}
	
}