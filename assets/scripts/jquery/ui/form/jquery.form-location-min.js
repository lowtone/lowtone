(function(b){var a={zoom:12,autolocation:true};b(function(){b("form .text.location.map").each(function(){var h=b(this);var d=b('<div class="lowtone google map" />').insertAfter(h);var k;d.gmap({center:j(),zoom:a.zoom,disableDoubleClickZoom:true,streetViewControl:false}).bind("init",function(l,m){var n=b(m);n.dblclick(function(o){f(g(o.latLng.lat(),o.latLng.lng()))});k=d.gmap("addMarker",{visible:false}).dragend(function(o){f(g(o.latLng.lat(),o.latLng.lng()))});i();h.bind("change",i)});d.gmap("getCurrentPosition",function(l,m){switch(m){case"OK":c(g(l.coords.latitude,l.coords.longitude));break}});b(".meta-box-sortables").bind("sortstop.form.location",function(l,m){b(m.item).find(".lowtone.google.map").gmap("refresh")});function g(m,l){return new google.maps.LatLng(m,l)}function c(l){d.gmap("get","map").panTo(l)}function f(l,m){if(!m){m=l.lat()+", "+l.lng()}h.val(m);k[0].setPosition(l);k[0].setVisible(true);c(l);return true}function i(){if(inputLatLng=e()){f(inputLatLng)}else{k[0].setVisible(false)}}function j(){if(google.loader&&google.loader.ClientLocation!=null){return g(google.loader.ClientLocation.latitude,google.loader.ClientLocation.longitude)}return g(52.070498,4.3007)}function e(){return(match=(/(-?\d{1,4}(\.\d+)?)\s*,\s*(-?\d{1,4}(\.\d+)?)/).exec(h.val()))?g(match[1],match[3]):false}})})})(jQuery);