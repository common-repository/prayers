/*
 Prayer Management JS
 */
(function($) {
	
	$(document).ready(function() {

		$('form.prayer-response').hide();

		$('a.prayer-response').click(function( event ) {

			var id = $(this).attr('data-id');
			$('form[data-id="' + id + '"]').slideToggle(100);

			event.preventDefault();
		});

	});

})(jQuery);