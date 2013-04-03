<?php
namespace lowtone\wp\widgets;
use WP_Widget,
	lowtone\net\URL,
	lowtone\wp\interfaces\Registrable;

/**
 * <b>This is not a wrapper for the widget object!</b> The wrapper class can be 
 * found at lowtone\wp\sidebars\widget\Widget.
 * 
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\widgets
 */
abstract class Widget extends WP_Widget implements interfaces\Widget, Registrable {

	const PROPERTY_ID = "id",
		PROPERTY_NAME = "name",
		PROPERTY_DESCRIPTION = "description";

	/**
	 * Widget::widget() is NOT your constructor!
	 * @see WP_Widget::__construct()
	 */
	public function __construct($id_base = false, $name, $widget_options = array(), $control_options = array()) {
		parent::__construct($id_base, $name, $widget_options, $control_options);
	}
	
	// Required function definitions to match the Widget interface (since WP_Widget doesn't). 
	
	public function form(array $instance = NULL) {
		return parent::form($instance);
	}
	
	public function update(array $newInstance, array $oldInstance) {
		return parent::update($newInstance, $oldInstance);
	}
	
	public function widget(array $args, array $instance = NULL) {
		return parent::widget($args, $instance);
	}

	// Getters
	
	public function get_field_name($fieldName) {
		if (!is_array($fieldName))
			$fieldName = URL::splitQueryParam($fieldName);

		return parent::get_field_name(implode("][", $fieldName));
	}
	
	// Static
	
	/**
	 * Register the widget.
	 * @return NULL Returns the result of register_widget() (that is nothing).
	 */
	public static function __register(array $options = NULL) {
		return register_widget(get_called_class());
	}
	
}