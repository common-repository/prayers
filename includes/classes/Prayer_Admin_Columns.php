<?php
/**
 * Admin Prayer Listing Columns
 *
 * Manipulates the prayer listing edit page to add columns to the listing
 * table with relevant data to the request like location, whether the prayer
 * has been answered, etc.
 *
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */

class Prayer_Admin_Columns
{
	/**
	 * Class Construct
	 */
	public function __construct() {
		add_filter( 'manage_prayer_posts_columns', array( $this, 'prayers_columns_head' ) );
		add_action( 'manage_prayer_posts_custom_column', array( $this, 'prayers_columns' ), 10, 2 );
	}

	/**
	 * Admin Prayer Listing Columns
	 *
	 * Builds a column list to be used for displaying relevant info on the Admin
	 * Prayer Listing page. This will include data like location, submitter, and
	 * whether the prayer has been answered or not, etc.
	 *
	 * @param  array Columns to generate for the admin page
	 * @return array Columns to be displayed for the padmin page
	 * @since  0.9.0
	 */
	function prayers_columns_head( $columns ) {
		// unset the author column
		$author = $columns['author'];
		unset($columns['author']);
		// store the data column in order to shift it to the end of the table
		$date = $columns['date'];
		unset($columns['date']);
		// store the category column in order to shift it to the end of the table
		$category = $columns['taxonomy-prayer-category'];
		unset($columns['taxonomy-prayer-category']);
		// build additional columns for the listing
		$columns['prayer_location'] = __( 'Location', 'prayer' );
		$columns['taxonomy-prayer-category'] = $category;
		$columns['prayer_count'] = __( 'Prayed', 'prayer' );
		$columns['prayer_answered'] = __( 'Answered?', 'prayer' );
		// $columns['prayer_anonymous'] = __( 'Anon?', 'prayer' );
		$columns['author'] = $author;
		// reinit the data column
		$columns['date'] = $date;
		// return the updated columns list
		return $columns;
	}

	/**
	 * Generate HTML Prayer Columns
	 *
	 * Takes columns specificed in prayers_columns_head and generates html
	 * output relevant to each column and prayer post.
	 *
	 * @param  string Column Name to check agains
	 * @param  integer Post ID to generate info on
	 * @since  0.9.0
	 */
	function prayers_columns( $column_name, $post_ID ) {
		// get the post meta to use in building the new column values
		$post_meta = get_post_meta( $post_ID );
		// geocoded info
		$lat = $post_meta['prayer-location-latitude'][0];
		$long = $post_meta['prayer-location-longitude'][0];
		// prayer count column
		if ($column_name == 'prayer_count' ) {
			$prayer_count = $post_meta['prayer-count'][0];
			echo $prayer_count;
		}
		// prayer location column
		if ($column_name == 'prayer_location' ) {
			// get needed vars to build the html
			$prayer_name = $post_meta['prayer-name'][0];
			$prayer_email = $post_meta['prayer-email'][0];
			$prayer_location = $post_meta['prayer-location'][0];
			$prayer_country = $post_meta['prayer-location-country-long'][0];
			// build the html for the prayer location column
			?>
				<div class="prayer-admin prayer-location">
					<div class="avatar">
						<?php echo get_avatar( $prayer_email, 26 ); ?>
					</div>
					<div class="details">
						<?php echo $prayer_name; ?><br />
						<?php if ( ! empty($lat) && ! empty($long) ): ?>
						<?php echo $prayer_location . " (" . $prayer_country . ")"; ?><br />
						Coord: <?php echo '<a href="http://maps.google.com/?ie=UTF8&hq=&ll=' . $lat . ',' . $long . '&z=13" target="_blank">' . $lat . ' / ' . $long . '</a>'; ?>
						<?php endif; ?>
					</div>
				<div>
			<?php

		}
		// Answered prayer column using conditional to check
		if ($column_name == 'prayer_answered') {
			$answered = $post_meta['prayer-answered'][0];
			if ($answered) {
				echo '<span class="prayer-">Yes</span>';
			}
			else {
				echo "No";
			}
		}
		// Is Anonymous? column using conditional to check
		if ($column_name == 'prayer_anonymous') {
			$anonymous = $post_meta['prayer-anonymous'][0];
			if ($anonymous) {
				echo "Yes";
			}
			else {
				echo "No";
			}
		}
	}

}
