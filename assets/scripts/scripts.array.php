<?php
return apply_filters("lowtone_scripts", array(
		"strings" => array(
				"src" => "strings/strings",
				"min" => true
			),
		"sprintf" => array(
				"src" => "strings/sprintf",
				"min" => true
			),
		"google-jsapi" => array(
				"raw_src" => "https://www.google.com/jsapi"
			),
		"google-maps" => array(
				"raw_src" => "http://maps.google.com/maps/api/js?sensor=true"
			),
		"jquery-ui-form" => array(
				"src" => "jquery/ui/form/jquery.form",
				"min" => true, 
				"deps" => array("jquery", "jquery-ui-core", "jquery-ui-timepicker")
			),
		"jquery-ui-form-location" => array(
				"src" => "jquery/ui/form/jquery.form-location",
				"min" => true, 
				"deps" => array("jquery-ui-form", "jquery-ui-map", "jquery-ui-map-extensions")
			),
		"jquery-ui-google-charts" => array(
				"src" => "jquery/ui/google/charts/jquery.ui.google.charts",
				"min" => true, 
				"deps" => array("jquery", "google-jsapi")
			),
		"jquery-ui-admin" => array(
				"src" => "jquery/ui/admin/jquery.admin",
				"min" => true, 
				"deps" => array("jquery"),
				"localize" => array(
						"name" => "jquery_ui_admin",
						"locales" => array(
								"ajax_url" => admin_url("admin-ajax.php"),
								"open" => array(
									"back" => __("Back")
								)
							)
					)
			),
		"jquery-lowtone" => array(
				"src" => "jquery/lowtone/jquery.lowtone",
				"min" => true, 
				"deps" => array("jquery"), 
				"enqueue" => true
			),
		"jquery-lowtone-wp-events" => array(
				"src" => "jquery/lowtone/wp/jquery.events", 
				"deps" => array("jquery")
			),
		"jquery-lowtone-ui-tables" => array(
				"src" => "jquery/lowtone/ui/jquery.tables", 
				"deps" => array("jquery", "jquery-ui-tablesorter")
			),
	));