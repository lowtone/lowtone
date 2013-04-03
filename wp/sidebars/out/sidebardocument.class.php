<?php
namespace lowtone\wp\sidebars\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\wp\sidebars\Sidebar,
	lowtone\wp\sidebars\widgets\out\WidgetsDocument,
	lowtone\wp\sidebars\widgets\out\WidgetDocument;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\sidebars\out
 */
class SidebarDocument extends ObjectDocument {
	
	const BUILD_WIDGETS = "build_widgets",
		WIDGETS_DOCUMENT_OPTIONS = "widgets_document_options";
	
	public function __construct(Sidebar $sidebar) {
		parent::__construct($sidebar);
		
		$this->updateBuildOptions(array(
			self::BUILD_ATTRIBUTES => array(
					Sidebar::PROPERTY_ID
				),
			self::BUILD_ELEMENTS => array(
					Sidebar::PROPERTY_NAME,
					Sidebar::PROPERTY_DESCRIPTION,
					Sidebar::PROPERTY_CLASS,
					Sidebar::PROPERTY_BEFORE_WIDGET,
					Sidebar::PROPERTY_AFTER_WIDGET,
					Sidebar::PROPERTY_BEFORE_TITLE,
					Sidebar::PROPERTY_AFTER_TITLE
				),
			self::BUILD_WIDGETS => true,
			self::WIDGETS_DOCUMENT_OPTIONS => array(
				WidgetsDocument::WIDGET_DOCUMENT_OPTIONS => array(
					WidgetDocument::SIDEBAR => $this->itsObject
				)
			)
		));
		
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		$sidebarElement = $this->documentElement;
				
		if ($this->getBuildOption(self::BUILD_WIDGETS)) {
			$sidebarsWidgets = wp_get_sidebars_widgets();
			
			$widgets = array_map(function($id) {
				return @$GLOBALS["wp_registered_widgets"][$id];
			}, (array) @$sidebarsWidgets[$this->itsObject->getId()]);
			
			$widgets = array_filter($widgets);
			
			$widgetsDocument = new WidgetsDocument($widgets);
			
			$widgetsDocument->build($this->getBuildOption(self::WIDGETS_DOCUMENT_OPTIONS));
			
			if ($widgetsElement = $this->importDocument($widgetsDocument))
				$sidebarElement->appendChild($widgetsElement);
			
		}
			
		return $this;
	}
	
}