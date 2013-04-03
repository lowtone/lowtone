<?php
namespace lowtone\wp\plugins;

include_once ABSPATH . "wp-admin/includes/plugin.php";

class Plugin {

	public static function isActive($file) {
		return is_plugin_active($file);
	}

}