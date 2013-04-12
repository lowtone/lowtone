<?php
$scriptsUrl = LIB_URL . "/lowtone/assets/scripts";

return apply_filters("lowtone_scripts", array(
		"strings" => array(
				"src" => $scriptsUrl . "/strings/strings",
				"min" => true
			),
		"sprintf" => array(
				"src" => $scriptsUrl . "/strings/sprintf",
				"min" => true
			),
		"audio" => array(
				"src" => $scriptsUrl . "/audiojs/audio.min"
			),
		"angular" => array(
				"src" => $scriptsUrl . "/angular/angular",
				"min" => true
			),
		"underscore" => array(
				"src" => $scriptsUrl . "/underscore/underscore",
				"min" => true
			),
		"backbone" => array(
				"src" => $scriptsUrl . "/backbone/backbone",
				"min" => true, 
				"deps" => array("underscore")
			),
		"modernizr" => array(
				"src" => $scriptsUrl . "/modernizr/modernizr",
				"min" => true
			),
		"google-jsapi" => array(
				"raw_src" => "https://www.google.com/jsapi"
			),
		"google-maps" => array(
				"raw_src" => "http://maps.google.com/maps/api/js?sensor=true"
			),
		"jquery-extend" => array(
				"src" => $scriptsUrl . "/jquery/jquery.extend", 
				"deps" => array("jquery")
			),
		"jquery-regex-selector" => array(
				"src" => $scriptsUrl . "/jquery/jquery.regex-selector",
				"min" => true, 
				"deps" => array("jquery")
			),
		"jquery-cycle" => array(
				"src" => $scriptsUrl . "/jquery/ui/cycle/jquery.cycle",
				"min" => true, 
				"deps" => array("jquery")
			),
		"jquery-cycle-tiles" => array(
				"src" => $scriptsUrl . "/jquery/ui/cycle/transitions/jquery.cycle-tiles",
				"min" => true,
				"deps" => array("jquery-cycle")
			),
		"jquery-dom-obj" => array(
				"src" => $scriptsUrl . "/jquery/jquery.dom-obj", 
				"deps" => array("jquery")
			),
		"jquery-xml-json" => array(
				"src" => $scriptsUrl . "/jquery/jquery.xml-json", 
				"deps" => array("jquery")
			),
		"jquery-mutation-core" => array(
				"src" => $scriptsUrl . "/jquery/mutation-events/mutations.core", 
				"deps" => array("jquery")
			),
		"jquery-mutation-attr" => array(
				"src" => $scriptsUrl . "/jquery/mutation-events/mutations.attr", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-data" => array(
				"src" => $scriptsUrl . "/jquery/mutation-events/mutations.data", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-html" => array(
				"src" => $scriptsUrl . "/jquery/mutation-events/mutations.html", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-ie6css" => array(
				"src" => $scriptsUrl . "/jquery/mutation-events/mutations.ie6css", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-val" => array(
				"src" => $scriptsUrl . "/jquery/mutation-events/mutations.val", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-ui-tipsy" => array(
				"src" => $scriptsUrl . "/jquery/ui/tipsy/jquery.tipsy", 
				"deps" => array("jquery")
			),
		"jquery-ui-colorbox" => array(
				"src" => $scriptsUrl . "/jquery/ui/colorbox/jquery.colorbox",
				"min" => true, 
				"deps" => array("jquery")
			),
		"jquery-ui-map" => array(
				"src" => $scriptsUrl . "/jquery/ui/map/jquery.ui.map", 
				"deps" => array("jquery", "jquery-ui-core", "google-maps")
			),
		"jquery-ui-map-extensions" => array(
				"src" => $scriptsUrl . "/jquery/ui/map/jquery.ui.map.extensions", 
				"deps" => array("jquery-ui-map")
			),
		"jquery-ui-timepicker" => array(
				"src" => $scriptsUrl . "/jquery/ui/timepicker/jquery.timepicker", 
				"deps" => array("jquery-ui-datepicker", "jquery-ui-slider")
			),
		"jquery-ui-form" => array(
				"src" => $scriptsUrl . "/jquery/ui/form/jquery.form",
				"min" => true, 
				"deps" => array("jquery", "jquery-ui-core", "jquery-ui-timepicker")
			),
		"jquery-ui-form-location" => array(
				"src" => $scriptsUrl . "/jquery/ui/form/jquery.form-location",
				"min" => true, 
				"deps" => array("jquery-ui-form", "jquery-ui-map", "jquery-ui-map-extensions")
			),
		"jquery-ui-google-charts" => array(
				"src" => $scriptsUrl . "/jquery/ui/google/charts/jquery.ui.google.charts",
				"min" => true, 
				"deps" => array("jquery", "google-jsapi")
			),
		"jquery-ui-tablesorter" => array(
				"src" => $scriptsUrl . "/jquery/ui/tablesorter/jquery.tablesorter",
				"min" => true, 
				"deps" => array("jquery")
			),
		"jquery-ui-admin" => array(
				"src" => $scriptsUrl . "/jquery/ui/admin/jquery.admin",
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
				"src" => $scriptsUrl . "/jquery/lowtone/jquery.lowtone",
				"min" => true, 
				"deps" => array("jquery"), 
				"enqueue" => true
			),
		"jquery-lowtone-wp-events" => array(
				"src" => $scriptsUrl . "/jquery/lowtone/wp/jquery.events", 
				"deps" => array("jquery")
			),
		"jquery-lowtone-ui-tables" => array(
				"src" => $scriptsUrl . "/jquery/lowtone/ui/jquery.tables", 
				"deps" => array("jquery", "jquery-ui-tablesorter")
			)
	));