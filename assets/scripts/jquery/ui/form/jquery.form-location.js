;(function($) {

	var options = {
		zoom: 12,
		autolocation: true
	}

	$(function() {

		$('form .text.location.map')
			.each(function() {
				var $input = $(this);
				
				// Create map
				
				var $mapElement = $('<div class="lowtone google map" />').insertAfter($input);

				// Declare marker

				var $marker;

				$mapElement
					.gmap({
						center: getDefaultLatLng(),
						zoom: options.zoom,
						disableDoubleClickZoom: true,
						streetViewControl: false
					})
					.bind('init', function(event, map) {
						var $map = $(map);

						// Set click event
						
						$map.dblclick(function(event) {
							updatePosition(createLatLng(event.latLng.lat(), event.latLng.lng()));
						});

						// Create marker
						
						$marker = $mapElement.gmap('addMarker', {
							visible: false
						})
						.dragend(function(event) {
							updatePosition(createLatLng(event.latLng.lat(), event.latLng.lng()));
						});

						// Update position
						
						updateFromInput();

						$input.bind('change', updateFromInput);

					});

				// Center browser location

				$mapElement
					.gmap('getCurrentPosition', function(position, status) {
						switch (status) {
							case 'OK':
								center(createLatLng(position.coords.latitude, position.coords.longitude));
								break;
						}
					});

				// Refresh on meta box sort

				$('.meta-box-sortables')
					.bind('sortstop.form.location', function(event, ui) {
						$(ui.item)
							.find('.lowtone.google.map')
							.gmap('refresh');
					});

				// Functions
				
				function createLatLng(lat, lng) {
					return new google.maps.LatLng(lat, lng);
				}

				function center(latLng) {
					$mapElement.gmap('get', 'map').panTo(latLng);
				}

				function updatePosition(latLng, text) {
					if (!text)
						text = latLng.lat() + ', ' + latLng.lng();
					
					$input.val(text);

					$marker[0].setPosition(latLng);
					$marker[0].setVisible(true);
					
					center(latLng);
					
					return true;	
				}

				function updateFromInput() {
					if (inputLatLng = getInputLatLng())
						updatePosition(inputLatLng);
					else 
						$marker[0].setVisible(false);
				}

				function getDefaultLatLng() {
					if (google.loader && google.loader.ClientLocation != null)
						return createLatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);

					return createLatLng(52.070498, 4.3007);
				}

				function getInputLatLng() {
					return (match = (/(-?\d{1,4}(\.\d+)?)\s*,\s*(-?\d{1,4}(\.\d+)?)/).exec($input.val())) ? createLatLng(match[1], match[3]) : false;
				}
				
			});
	});

})(jQuery);