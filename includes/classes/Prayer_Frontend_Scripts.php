<?php
/**
 * Enqueue Frontend Styles and Scripts
 * 
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */

class Prayer_Frontend_Scripts 
{
	/**
	 * Class Construct
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'prayer_register_scripts' ) );
	}

	/**
	 * Register Frontend Styles and Scripts
	 * @since  0.9.0
	 */
	public function prayer_register_scripts() {

		// styles
		wp_register_style( 'prayer-css', plugins_url( '/prayers/elements/css/prayer.css', 'prayer' ), array(), '0.9.0', 'all' );
		wp_enqueue_style( 'prayer-css');

		// load colors
		// get echo options
		$prayer_options = get_option( 'prayer_settings_options' );
		$button_primary_color = $prayer_options['button_primary_color'];
		$button_secondary_color = $prayer_options['button_secondary_color'];
		$button_text_color = $prayer_options['button_text_color'];
		$taxonomy_background_color = $prayer_options['taxonomy_background_color'];
		$taxonomy_text_color = $prayer_options['taxonomy_text_color'];
		ob_start();
		?>
	span.prayer-pray-button input[type="submit"],
	ul#prayer-navigation li a  {
		background: <?php echo $button_primary_color ?>;
		color: <?php echo $button_text_color ?>;
	}
	span.prayer-pray-button:hover input[type="submit"],
	span.prayer-pray-button form.prayed-for input[type="submit"],
	ul#prayer-navigation li a:hover,
	ul#prayer-navigation li.active a    {
		background: <?php echo $button_secondary_color ?>;
		color: <?php echo $button_text_color ?>;
	}
	span.prayer-count,
	span.prayer-box {
		background: <?php echo $taxonomy_background_color; ?>;
		color: <?php echo $taxonomy_text_color; ?>;
	}<?php
		$custom_css = ob_get_clean();
		wp_add_inline_style( 'prayer-css', $custom_css );

	}

}
