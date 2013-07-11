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
		"audio" => array(
				"src" => "audiojs/audio.min"
			),
		"angular" => array(
				"src" => "angular/angular",
				"min" => true
			),
		"underscore" => array(
				"src" => "underscore/underscore",
				"min" => true
			),
		"backbone" => array(
				"src" => "backbone/backbone",
				"min" => true, 
				"deps" => array("underscore")
			),
		"modernizr" => array(
				"src" => "modernizr/modernizr",
				"min" => true
			),
		"d3" => array(
				"src" => "d3/d3.v3",
				"min" => true
			),
		"google-jsapi" => array(
				"raw_src" => "https://www.google.com/jsapi"
			),
		"google-maps" => array(
				"raw_src" => "http://maps.google.com/maps/api/js?sensor=true"
			),
		"jquery-extend" => array(
				"src" => "jquery/jquery.extend", 
				"deps" => array("jquery")
			),
		"jquery-regex-selector" => array(
				"src" => "jquery/jquery.regex-selector",
				"min" => true, 
				"deps" => array("jquery")
			),
		"jquery-cycle" => array(
				"src" => "jquery/ui/cycle/jquery.cycle",
				"min" => true, 
				"deps" => array("jquery", "jquery-ui-core", "jquery-effects-core")
			),
		"jquery-cycle-tiles" => array(
				"src" => "jquery/ui/cycle/transitions/jquery.cycle-tiles",
				"min" => true,
				"deps" => array("jquery-cycle")
			),
		"jquery-dom-obj" => array(
				"src" => "jquery/jquery.dom-obj", 
				"deps" => array("jquery")
			),
		"jquery-xml-json" => array(
				"src" => "jquery/jquery.xml-json", 
				"deps" => array("jquery")
			),
		"jquery-mutation-core" => array(
				"src" => "jquery/mutation-events/mutations.core", 
				"deps" => array("jquery")
			),
		"jquery-mutation-attr" => array(
				"src" => "jquery/mutation-events/mutations.attr", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-data" => array(
				"src" => "jquery/mutation-events/mutations.data", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-html" => array(
				"src" => "jquery/mutation-events/mutations.html", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-ie6css" => array(
				"src" => "jquery/mutation-events/mutations.ie6css", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-mutation-val" => array(
				"src" => "jquery/mutation-events/mutations.val", 
				"deps" => array("jquery-mutation-core")
			),
		"jquery-ui-chosen" => array(
				"src" => "jquery/ui/chosen/jquery.chosen",
				"min" => true, 
				"deps" => array("jquery")
			),
		"jquery-ui-tipsy" => array(
				"src" => "jquery/ui/tipsy/jquery.tipsy", 
				"deps" => array("jquery")
			),
		"jquery-ui-colorbox" => array(
				"src" => "jquery/ui/colorbox/jquery.colorbox",
				"min" => true, 
				"deps" => array("jquery")
			),
		"jquery-ui-map" => array(
				"src" => "jquery/ui/map/jquery.ui.map", 
				"deps" => array("jquery", "jquery-ui-core", "google-maps")
			),
		"jquery-ui-map-extensions" => array(
				"src" => "jquery/ui/map/jquery.ui.map.extensions", 
				"deps" => array("jquery-ui-map")
			),
		"jquery-ui-timepicker" => array(
				"src" => "jquery/ui/timepicker/jquery.timepicker", 
				"deps" => array("jquery-ui-datepicker", "jquery-ui-slider")
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
		"jquery-ui-tablesorter" => array(
				"src" => "jquery/ui/tablesorter/jquery.tablesorter",
				"min" => true, 
				"deps" => array("jquery")
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
		"raphael" => array(
				"src" => "raphael/raphael",
				"min" => true
			)
	));