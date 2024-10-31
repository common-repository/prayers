/*
 Prayer App frontend javascript
 */
(function($) {
	
	$(document).ready(function() {

		// create and access an echo object in localStorage. This is for
		// storing user interaction on the clients end. If we ever want to 
		// know who prays, at what time, etc then this will have to be saved
		// server side. 
		var db = localStorage.getItem('prayer');
		var items = JSON.parse(db);

		if ( items != null ) {
			// disable prayer button for prayers already clicked
			$('form.prayer-prayed').each(function( index, value ) {
				// get items from the database 
				var prayer_id = $(this).attr('data-prayer-id');
				// request has already been prayed for
				if (items.prayers.indexOf(prayer_id) > -1) {						
					$(this).addClass('prayed-for');
					$('input[type="submit"]', this).prop('disabled', true);
					$('input[type="submit"]', this).prop('value', 'Prayed');
				}
			});
		}

		// record prayed for request
		$('form.prayer-prayed').submit( function( event ) {
			// get the form, data, and prayer id
			var $form = $(this);
			var formData = $form.serialize();
			var prayer_id = $form.attr('data-prayer-id');

			// update the prayer count
			var count = parseInt( $('span.prayer-count.prayer-' + prayer_id).text(), 10 );

			// update visual display
			$('span.prayer-count.prayer-' + prayer_id).text(count+1);
			$form.addClass('prayed-for');
			$('input[type="submit"]', $form).prop('disabled', true);
			$('input[type="submit"]', $form).prop('value', 'Prayed');

			// post the form
			$.post('#', formData, function(data) {
							
				// store this click in local storage to prevent abuse
				var items = localStorage.getItem('prayer');

				// localStorage prayers key doesn't exist
				if (items == null) {
					items = { prayers: [ prayer_id ] };
				}
				else {
					items = localStorage.getItem('prayer');
					items = JSON.parse(items);

					if (items.prayers.indexOf(prayer_id) < 0) {						
						items.prayers.push(prayer_id);
					}
				}
				// store the data to localStorage
				data = JSON.stringify(items);
				localStorage.setItem( 'prayer', data );

				//console.log(localStorage);				
				//localStorage.clear();
			}, 'html');
			// prevent form submission, aka page reload
			event.preventDefault();
		});

		

	});
})(jQuery);