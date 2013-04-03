<?php
namespace lowtone\wp\sidebars\widgets\out;
use lowtone\types\objects\out\ObjectDocument,
	lowtone\Util,
	lowtone\wp\sidebars\Sidebar,
	lowtone\wp\sidebars\widgets\Widget;

/**
 * @author Paul van der Meijs <code@paulvandermeijs.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.1
 * @package wordpress\libs\lowtone\wp\sidebars\widgets\out
 */
class WidgetDocument extends ObjectDocument {
	
	const SIDEBAR = "sidebar",
		BUILD_OUTPUT = "build_output";
	
	public function __construct(Widget $widget) {
		parent::__construct($widget);
		
		$this->updateBuildOptions(array(
				self::BUILD_ELEMENTS => array(
						Widget::PROPERTY_NAME,
						Widget::PROPERTY_ID,
						Widget::PROPERTY_CLASSNAME,
						Widget::PROPERTY_DESCRIPTION
					)
			));
		
	}
	
	public function build(array $options = NULL) {
		parent::build($options);

		$widgetElement = $this->documentElement;
			
		$params = array_merge(
			array(
				array_merge(
					(array) $this->getBuildOption(self::SIDEBAR), 
					array(
						"widget_id" => $this->itsObject->getId(), 
						"widget_name" => $this->itsObject->getName()
					)
				)
			),
			(array) $this->itsObject->getParams()
		);

		$class = array("widget");

		foreach ((array) $GLOBALS["wp_registered_widgets"][$this->itsObject->getId()]["classname"] as $classname) {

			if (is_string($classname))
				$class[] = $classname;
			else if (is_object($classname))
				$class[] = get_class($classname);

		}

		$class = (array) apply_filters("widget_class", $class);

		$params[0][Sidebar::PROPERTY_BEFORE_WIDGET] = sprintf($params[0][Sidebar::PROPERTY_BEFORE_WIDGET], $this->itsObject->getId(), implode(" ", $class));
		
		$params = apply_filters("dynamic_sidebar_params", $params);
		
		do_action("dynamic_sidebar", $this->itsObject->getId());
		
		/*
		 * The callback's result is checked to be FALSE to disable the output
		 * element, however it appears WP_Widget::display_callback function 
		 * doesn't the WP_Widget::widget() function's return value.
		 */
		
		if ($this->getBuildOption(self::BUILD_OUTPUT)) {
			
			if (is_callable($callback = $this->itsObject->getCallback()) && ($output = Util::catchOutput(true, $callback, $params)) !== false) 
				$widgetElement->appendCreateElement("output", (string) $output);
				
		}
		
		return $this;
	}
	
}