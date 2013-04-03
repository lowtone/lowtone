<?php
namespace lowtone\wp\widgets\interfaces;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\wp\widgets\interfaces
 */
interface Widget {
	
	/**
	 * Outputs the options form on admin.
	 * @param array $instance Current settings.
	 */
	public function form(array $instance = NULL);
	
	/**
	 * Processes widget options to be saved.
	 * @param array $newInstance New settings for this instance as input by the 
	 * user via form().
	 * @param array $oldInstance Old settings for this instance.
	 * @return array|bool Returns the settings to save or FALSE to cancel 
	 * saving.
	 */
	public function update(array $newInstance, array $oldInstance);
	
	/**
	 * Outputs the content of the widget.
	 * @param array $args Display arguments including before_title, after_title, 
	 * before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the 
	 * widget.
	 */
	public function widget(array $args, array $instance = NULL);
	
}