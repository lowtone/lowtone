<?php
namespace lowtone\wp\sidebars\widgets\out;
use lowtone\dom\Document,
	lowtone\wp\sidebars\widgets\Widget;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\sidebars\widgets\out
 */
class WidgetsDocument extends Document {
	
	/**
	 * @var array
	 */
	protected $itsWidgets;
	
	const WIDGET_DOCUMENT_OPTIONS = "widget_document_options";
	
	public function __construct(array $widgets) {
		parent::__construct();
		
		$this->itsWidgets = $widgets;
		
	}
	
	public function build(array $options = NULL) {
		$this->updateBuildOptions((array) $options);
		
		if (count($this->itsWidgets) < 1)
			return false;
		
		$widgetsElement = $this->createAppendElement("widgets");
		
		foreach ($this->itsWidgets as $widget) {
			$widgetDocument = new WidgetDocument(new Widget($widget));
			
			$widgetDocument->build($this->getBuildOption(self::WIDGET_DOCUMENT_OPTIONS));
			
			if ($widgetElement = $this->importDocument($widgetDocument))
				$widgetsElement->appendChild($widgetElement);
				
		}
		
		return $this;
	}
	
}