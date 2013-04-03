<?php
namespace lowtone\wp\widgets\simple;
use WP_Widget_Factory,
	lowtone\ui\forms\Form,
	lowtone\ui\forms\Input,
	lowtone\ui\forms\base\FormElement,
	lowtone\wp\widgets\Widget as Base;

/**
 * Simple Widget
 * The Simple Widget class provides a way to create anonymous widgets in a 
 * similar manner as anonymous functions are created.
 * 
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\widgets\simple
 */
final class Widget extends Base {

	/**
	 * @var Form|callable
	 */
	protected $itsForm;

	/**
	 * @var callable
	 */
	protected $itsUpdate;
		
	/**
	 * @var callable
	 */
	protected $itsWidget;

	const PROPERTY_FORM = "form",
		PROPERTY_UPDATE = "update",
		PROPERTY_WIDGET = "widget";

	public function __construct(array $properties = NULL) {
		$properties = (array) $properties;

		if (!($id = trim(@$properties[self::PROPERTY_ID])))
			trigger_error("Simple Widget created with empty ID", E_USER_NOTICE);

		if (!($name = trim(@$properties[self::PROPERTY_NAME])))
			trigger_error("Simple Widget created without a name", E_USER_NOTICE);

		$args = array_diff_key($properties, array_flip(array(
				self::PROPERTY_ID, 
				self::PROPERTY_NAME, 
				self::PROPERTY_FORM, 
				self::PROPERTY_UPDATE, 
				self::PROPERTY_WIDGET
			)));

		parent::__construct($id, $name, $args);

		$this->itsForm = @$properties[self::PROPERTY_FORM];
		$this->itsUpdate = @$properties[self::PROPERTY_UPDATE];
		$this->itsWidget = @$properties[self::PROPERTY_WIDGET];

	}
	
	public function form(array $instance = NULL) {
		$form = $this->itsForm;

		if (is_callable($form))
			$form = call_user_func($form, $instance);

		if (!($form instanceof Form))
			return true;

		$form = clone $form;

		$widget = $this;
		
		$out = $form
			->setValues($instance)
			->walkChildren(function($element) use ($widget) {
				if (!($element instanceof Input))
					return;
				
				$element[Input::PROPERTY_NAME] = $widget->get_field_name($element[Input::PROPERTY_NAME]);
			})
			->createDocument()
			->build()
			->setTemplate(realpath(LIB_DIR . "/lowtone/ui/forms/templates/form-content.xsl"))
			->transform()
			->saveHTML();

		echo $out;

		return true;
	}
	
	public function update(array $newInstance, array $oldInstance) {
		if (!is_callable($this->itsUpdate))
			return $newInstance;

		return call_user_func_array($this->itsUpdate, func_get_args());
	}
	
	public function widget(array $args, array $instance = NULL) {
		if (!is_callable($this->itsWidget))
			return true;

		return call_user_func_array($this->itsWidget, func_get_args());
	}

	// Static

	protected static function create(array $properties = NULL) {
		return new Widget((array) $properties);
	}

	public static function register(array $properties = NULL) {
		if (!(($factory = $GLOBALS["wp_widget_factory"])) instanceof WP_Widget_Factory) {
			trigger_error("Can't register Simple Widget without widget factory", E_USER_NOTICE);

			return false;
		}

		return ($factory->widgets[sprintf("Widget_%s", md5(@$properties[self::PROPERTY_ID]))] = static::create($properties));
	}

	public static function __register(array $options = NULL) {
		trigger_error("Can't register the Simple Widget class as a Widget; use lowtone\wp\widgets\simple\Widget::register() to register an Simple Widget instance");

		return false;
	}
	
}