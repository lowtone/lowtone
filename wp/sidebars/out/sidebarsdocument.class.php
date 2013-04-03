<?php
namespace lowtone\wp\sidebars\out;
use lowtone\dom\Document,
	lowtone\wp\sidebars\Sidebar;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\sidebars\out
 */
class SidebarsDocument extends Document {
	
	/**
	 * @var array
	 */
	protected $itsSidebars;
	
	const FILTER_SIDEBARS = "filter_sidebars",
		SIDEBAR_DOCUMENT_OPTIONS = "sidebar_document_options";
	
	public function __construct() {
		parent::__construct();
		
		$this->itsSidebars = $GLOBALS["wp_registered_sidebars"];
		
		
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		if (count($this->itsSidebars) < 1)
			return $this;
			
		$sidebarsElement = $this->createAppendElement("sidebars");
		
		foreach ($this->itsSidebars as $id => $sidebar) {
			$shortId = preg_replace("/-\d+$/", "", $id);
			
			if (is_array($filter = $this->getBuildOption(self::FILTER_SIDEBARS)) && !in_array($shortId, $filter))
				continue;
			
			$sidebarDocument = new SidebarDocument(new Sidebar($sidebar));
			
			$sidebarDocument->build($this->getBuildOption(self::SIDEBAR_DOCUMENT_OPTIONS));
			
			if ($sidebarElement = $this->importDocument($sidebarDocument))
				$sidebarsElement->appendChild($sidebarElement);
				
		}
		
		return $this;
	}
	
}