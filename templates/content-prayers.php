<?php
// prayer navigation
Prayer_Template_Helper::get_navigation();

// show flash messages
Prayer_Template_Helper::flash_message();

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$args['paged'] = $paged;

// The Query
$query = new WP_Query( $args );
if ( $query->have_posts() ) : ?>

	<ul id="prayers" class="prayers prayers-listing">

		<?php while ( $query->have_posts() ):
			$query->the_post();

			$id = get_the_ID();

			?>

			<li>
				<h3 class="prayer-title">
					<a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
				</h3>

				<div class="prayer prayer-meta">
					<ul><?php

							// html output
							$prayer_button = Prayer_Template_Helper::get_prayed_button( $id );
							$prayer_location = Prayer_Template_Helper::get_prayer_location($id);
							$prayer_category = Prayer_Template_Helper::get_terms_list($id, 'prayer-category');
							$prayer_tags = Prayer_Template_Helper::get_terms_list($id, 'prayer-tags');
							$prayer_answered = Prayer_Template_Helper::get_prayer_answered($id);
							$prayer_avatar = Prayer_Template_Helper::get_avatar( $id, 27 );

					  	?><li class="prayer-avatar-small"><?php echo $prayer_avatar; ?></li>
						<li><?php echo $prayer_button; ?></li>
						<?php if ( $prayer_location !== false ): ?>
							<li><a href="<?php echo site_url(); ?>/prayers/map"><?php echo $prayer_location; ?></a></li>
						<?php endif; ?>
						<li class="prayer-taxonomy"><?php echo $prayer_category; ?></li>
						<li class="prayer-taxonomy"><?php echo $prayer_tags; ?></li>
						<?php if ( $prayer_answered !== false ): ?>
							<li class="prayer prayer-answered"><span class="prayer prayer-answered prayer-box">Answered</span></li>
						<?php endif; ?>
					</ul>
				</div>

				<div class="prayer-content">
					<span class="prayer prayer-name"><?php echo Prayer_Template_Helper::get_prayer_name($id); ?></span>
					<?php the_content() ?>
				</div>

				<?php if ( $prayer_answered !== false ): ?>
					<div class="prayer-answered">
						<?php echo get_post_meta( $id, 'prayer-response', 1); ?>
					</div>
				<?php endif; ?>

			</li>

		<?php endwhile; ?>

	</ul>

	<?php

		Prayer_Template_Helper::pagination( $query->max_num_pages );
		/* Restore original Post Data */
		wp_reset_query();

	else: ?>

	s<h3>Sorry. No prayers have been submitted yet.</h3>

	<?php endif;
