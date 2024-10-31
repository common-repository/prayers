<div id="prayer-navigation" class="prayer-navigation">

	<h2 class="nav-tab-wrapper current">
		<a class="nav-tab nav-tab-active" href="javascript:;"><?php echo __( 'Notes', 'prayer' ) ?></a>
		<a class="nav-tab" href="javascript:;"><?php echo __( 'Responses', 'prayer' ) ?></a>
		<a class="nav-tab" href="javascript:;"><?php echo __( 'Geolocation', 'prayer' ) ?></a>
		<a class="nav-tab" href="javascript:;"><?php echo __( 'Contact', 'prayer' ) ?></a>
		<a class="nav-tab" href="javascript:;"><?php echo __( 'Processing', 'prayer' ) ?></a>
	</h2>

	<?php
		include ( 'partials/meta-notes.php' );
		include ( 'partials/meta-responses.php' );
		include ( 'partials/meta-geolocation.php' );
		include ( 'partials/meta-contact.php' );
		include ( 'partials/meta-processing.php' );

		// Add a nonce field for security
        wp_nonce_field( 'prayer_save', 'prayer_nonce' );
	?>

</div>
