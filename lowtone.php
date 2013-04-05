<?php
/*
 * Plugin Name: Lowtone Library for WordPress
 * Plugin URI: http://wordpress.lowtone.nl/lib
 * Plugin Type: lib
 * Description: Lowtone library.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */

namespace lowtone {
	
	if (!class_exists("lowtone\\content\\packages\\Package"))
		return false;
	
	// Init utils
	
	include_once "lowtone.class.php";
	
	Lowtone::init();
	
}