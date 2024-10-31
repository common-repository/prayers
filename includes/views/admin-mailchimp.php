<h1>MailChimp</h1>
<?php
	// show flash messages
	Prayer_Template_Helper::flash_message();

	// get mailchimp
	$mc = new Prayer_Mailchimp;

	if ( ! isset( $mc->mc_api->apikey ) ):
	// display a message telling the user to update thier key
		?>
		<p>Please enter a MailChimp API Key on the <a href="<?php echo get_site_url() ?>/wp-admin/edit.php?post_type=prayer&amp;page=settings">Prayer Settings page.</a></p>
	<?php else:
	// display the mailchimp integration page
	?>

		<p>Your API Key: <code><?php echo $mc->mc_api->apikey; ?></code></p>

		<h2>Select List</h2>

		<?php
			// lists
			$lists = $mc->mc_api->lists->getList();
		?>
		<form method="post" action="">

			<p>Sync a <a href="http://mailchimp.com" target="_blank">MailChimp</a> list and pre-defined segments from people who have
submitted prayer requests on your website.</p>

			<select name="prayer_mailchimp_list">
				<option value="">Select a list</option>
				<?php foreach($lists['data'] as $list): ?>
					<option value="<?php echo $list['id'] ?>|<?php echo $list['name'] ?>" <?php if( $list['id'] == $mc->current_list ) { echo 'selected'; } ?>><?php echo $list['name'] ?></option>
				<?php endforeach; ?>
			</select>

			<?php wp_nonce_field( basename(__FILE__), 'mailchimp_nonce' ); ?>
			<input type="hidden" name="mailchimp-submission" value="1" />
			<input type="submit" value="Submit" class="prayer-button" />
		</form>

		<?php if ( ! empty( $mc->current_list ) ): ?>

			<h2>Actions</h2>

			<ul>
				<li>
					<form action="" method="post">
						<?php wp_nonce_field( basename(__FILE__), 'mailchimp_nonce' ); ?>
						<input type="hidden" name="mailchimp-sync-list" value="1" />
						<input type="submit" value="Sync List" class="prayer-button button-sync" />
						<span>All <?php echo get_option( 'prayer_mailchimp_list_name' ) ?> Emails</span>
					</form>
				</li>
				<li>
					<form action="" method="post">
						<?php wp_nonce_field( basename(__FILE__), 'mailchimp_nonce' ); ?>
						<input type="hidden" name="mailchimp-sync-segment" value="1" />
						<input type="submit" value="Sync Segment" class="prayer-button button-sync" />
						<select name="segment">
							<option>Choose a segment to sync</option>
							<?php $prayer_segments = $mc->mc_segments;
								foreach( $prayer_segments as $key => $segment): ?>
								<option value="<?php echo $key ?>"><?php echo $segment ?></option>
							<?php endforeach; ?>
						</select>
						<span>(For people who have submitted prayer requests)</span>
					</form>
				</li>
				<li>
					<form action="" method="post">
						<?php wp_nonce_field( basename(__FILE__), 'mailchimp_nonce' ); ?>
						<input type="hidden" name="mailchimp-sync-groups" value="1" />
						<input type="submit" value="Sync Groups" class="prayer-button button-sync" />
						<?php
							$prayer_category = array( 'prayer-category' );
							$args = array(
								'orderby' => 'name',
								'order' => 'ASC',
								'hide_empty' => false
							);
							$prayer_categories = get_terms($prayer_category, $args);
							foreach( $prayer_categories as $term )
							{
								$prayer_cat_list[] = $term->name;
							}
							$prayer_cat_list = implode(", ", $prayer_cat_list);
						?>
						<span><?php echo $prayer_cat_list ?>, Answered Prayers (For people who have subscribed to updates)</span>
					</form>
				</li>
			</ul>
		<?php endif; ?>

		<h2>Instructions</h2>

		<p><strong>1. Select a list</strong> that you have created in MailChimp. The Prayer Plugin
		can sync groups and segments to a MailChimp to let you communicate with
		users of your website.</p>

		<p><strong>2. Actions</strong> allow you keep your List, Segments, and Groups up-to-date. The
		Prayer plugin interacts with one List on your MailChimp account. It adds
		Segments to the list to allow you to communicate with anyone who has
		submitted a prayer request to your website. Groups are for allowing
		other people to subscribe to different prayer updates from your website.</p>

		<p><strong>3. Sync List.</strong> When you sync your list, every email
		associated with prayers that have been submitted to your website are
		synced with MailChimp. This makes up your master prayer list where you
		can communicate timely information to everyone who uses your website
		when it's needed.</p>

		<p><strong>4. Sync Segment.</strong> Segments are used to sort
		different interactions for interacting with people who have submitted
		prayer requests to your website. For example, you can send out an
		email to everyone who has recently had thier prayer request prayed for
		by your organization by syncing the Newly prayed-for segment. Under
		the Prayer information box for each individual prayer, you can click
		the processing tab, click "Prayed for", and this will add any new
		prayed for requests to a queue to be synced with MailChimp.

		<p><strong>5. Sync Groups.</strong> Creates groups for your MailChimp
		list that allows endusers to subscribe to incoming prayer requests or
		communication based on prayer categories, when the prayer has been
		answered, etc. You can use one of the feeds this plugin provides to
		send out automatic updates using an
		<a href="http://kb.mailchimp.com/campaigns/rss-in-campaigns/create-an-rss-campaign" target="_blank">
		RSS Campaign</a> in MailChimp.</p>

	<?php endif; ?>
