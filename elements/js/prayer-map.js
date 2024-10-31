/*
 Prayer Map
 */
(function($) {
	$(document).ready(function() {

		$.getJSON( '/wp-json/prayers/v1/prayers', function( data ) {

			var map = L.map('prayer-map');

			L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
			    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
			}).addTo(map);

			var bounds = [];
			var markers = [];
			$.each(data, function( index, item ) {
				if ( item.geocode.place != "" ) {
					markers[item.category[0].slug ] = L.marker([item.geocode.latitude,item.geocode.longitude]).addTo(map)
						.bindPopup(
							'<h3>' + item.title + '</h3><br />' + item.content + 
							'<p><strong>' + item.geocode.formatted + '</strong><br />' + 
							'<em>' + item.category[0].name + '</em></p>'
						);
						
					bounds[index] = [ item.geocode.latitude, item.geocode.longitude ];
				} 
			});
		
			map.fitBounds(bounds);
		});

	});
})(jQuery);