<?php 

// prayer navigation
Prayer_Template_Helper::get_navigation();

// show flash messages
Prayer_Template_Helper::flash_message();

// get echo options
$prayer_options = get_option( 'prayer_settings_options' );

if ( ! empty( $_SESSION['errors'] ) ) {
	$errors = $_SESSION['errors'];

	?>
	<div class="prayer form-errors">
		<ul>
		<?php foreach( $errors as $error ) {
			?>
				<li><?php echo $error ?></li>
			<?php
		} ?>
		</ul>
	</div>
<?php } 

if ( ! empty( $_SESSION['post'] ) ) {
	$post_data = $_SESSION['post'];
}

?>
		
<form method="post" id="prayer-form" class="prayer form prayer-js" action="">
	<?php wp_nonce_field( basename(__FILE__), 'prayer_nonce' ); ?>

	<p><strong>Your prayer request</strong></p>

	<p class="prayer-title">
		<label for="prayer_title" class="hide"><?php echo __('Prayer title', 'prayer') ?></label>
		<input type="text" name="prayer_title" minlength="6" required placeholder="Prayer title (required)" value="<?php echo $post_data['prayer_title'] ?>" />
	</p>

	<p class="prayer-content">
		<label for="prayer_content" class="hide"><?php echo __('Prayer Request', 'prayer') ?></label>
		<textarea name="prayer_content" minlength="6" required rows="4" placeholder="Please enter your request here (required)."><?php echo $post_data['prayer_content'] ?></textarea>
	</p>

	<p><strong>Your contact information</strong></p>

	<p class="prayer-name">
		<label for="prayer_name" class="hide"><?php echo __('Your name', 'prayer') ?></label>
		<input type="text" name="prayer_name" required placeholder="Your name (required)" value="<?php echo $post_data['prayer_name'] ?>" />
	</p>

	<p class="prayer-email">
		<label for="prayer_email" class="hide"><?php echo __('Your email', 'prayer') ?></label>
		<input type="email" name="prayer_email" required placeholder="Your email (required)" value="<?php echo $post_data['prayer_email'] ?>" />
	</p>

	<p><strong>Place your request on the map?</strong><br />
	<small>City and state is fine, we don't need a full address.</small></p>

	<p class="prayer-address">
		<label for="prayer_location" class="hide"><?php echo __('Your city, state, province, country', 'prayer') ?></label>
		<input type="text" name="prayer_location" placeholder="Your city, state, province, country, etc (optional)" value="<?php echo $post_data['prayer_location'] ?>" />
	</p>

	<?php
	// check to see if categories are enabled
	$categories_enabled = $prayer_options['categories_enabled'];

	if ($categories_enabled == '1'):
		$prayer_category = array( 'prayer-category' );
		$args = array(
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false 
		);
		$prayer_categories = get_terms($prayer_category, $args);
	?>

	<p class="prayer-categories inline-form-elements">
		<label for="prayer-category">
			<strong><?php echo __('Categories', 'prayer'); ?></strong>
		</label><br />
		<?php foreach ($prayer_categories as $key => $category): ?>
			<label for="<?php echo $category->slug; ?>">
				<input type="radio" name="prayer-category" value="<?php echo $category->slug; ?>"  <?php if ( $key == 0 ) echo "required"; ?>/> <?php echo $category->name ?> &nbsp;
			</label>
		<?php endforeach; ?>
		<br />
		<label for="prayer-category" class="error" style="display:none;"><?php echo __( 'Please choose a category.', 'prayer' ) ?></label>
	</p>

	<?php endif; // categories enabled

	// check to see if tags are enabled
	$tags_enabled = $prayer_options['tags_enabled'];
	if ( $tags_enabled == '1' ):

	?>

	<p class="prayer-tags">
		<label for="prayer-tags">
			<strong><?php echo __('Tags', 'prayer'); ?></strong>
			<input type="text" name="prayer-tags" placeholder="healing, doctors, africa (optional)" value="<?php echo $post_data['prayer-tags'] ?>" />
		</label>
		<small>Comma-separated list of tags for your request</small>
	</p>

	<?php endif; // tags enabled ?>
	
	<p class="prayer-anonymous inline-form-elements">
		<label>
			<strong><?php echo __('Would you like this prayer request to be anonymous?', 'prayer' ); ?></strong>
		</label>
		<label for="prayer_anonymous">
			<input type="checkbox" name="prayer_anonymous" value="1" />
			<span>Yes</span>
		</label>
	</p>

	<p>	
		<input type="submit" value="Send Prayer" />
		<input type="hidden" name="prayer-submission" value="1" />
		<input type="hidden" name="redirect" value="<?php echo Prayer_Template_Helper::current_page_url(); ?>" />
	</p>

</form>
