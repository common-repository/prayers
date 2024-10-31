<div class="inside hidden">

	<div class="column">

	</div><!--.column-->

	<div class="column">
		<?php // build the submitter name input
			$name = $post_meta['prayer-name'][0];
		?>

		<p>
			<label for="prayer-name"><?php echo __('Name', 'prayer') ?></label>
			<input type="text" name="prayer-name" value="<?php echo $name; ?>" />
		</p>

		<?php // build the email input
			$email = $post_meta['prayer-email'][0];
		?>

		<p>
			<label for="prayer-email"><?php echo __('Email', 'prayer') ?></label>
			<input type="text" name="prayer-email" value="<?php echo $email; ?>" />
		</p>
	</div><!--.column-->

	<div class="column">
		
	</div><!--.column-->

</div>