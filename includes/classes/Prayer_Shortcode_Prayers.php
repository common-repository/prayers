<?php
/**
 * Shortcodes
 *
 * Provides various shortcodes to be used in templates in the WYSIWYG Editor. 
 * Params the shortcodes take are listed at the beginning of each function. 
 * The shortcodes themselves rely on templates in plugins/prayers/templates to 
 * output html code. You can copy these templates to your 
 * themes/your_theme/templates folder and tweak them to your site. 
 * 
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */
class Prayer_Shortcode_Prayers
{
	static $add_script;

	/**
	 * Initialize Script
	 * @since 0.9.0
	 */
	static function init() 
	{
		add_shortcode( 'prayers', array( __CLASS__, 'handle_shortcode' ) );

		add_action( 'init', array( __CLASS__, 'register_script' ) );
		add_action( 'wp_footer', array( __CLASS__, 'print_script' ) ); 
	}

	/**
	 * Prayers Shortcode
	 *
	 * Display a listing of prayers based on the attribues you pass. The attribute
	 * List is as follows:
	 * 
	 * - limit='10'
	 * - start_date='last_month'
	 * - end_date='today'
	 *
	 * @param  array Custom Attributes
	 * @return html
	 * @since  0.9.0 
	 */
	static function handle_shortcode( $atts ) 
	{
		self::$add_script = true;

		// set shortcode atts to pass to the template
		$shortcode_atts = shortcode_atts(
			array(
				'limit' => '10',
				'start_date' => 'last month',
				'end_date' => 'today',
				'order' => 'DESC',
			), $atts );

		// Attributes
		extract( $shortcode_atts );

		
		// WP_Query arguments
		$args = array (
			'post_type' => array( 'prayer' ),
			'post_status' => array( 'publish' ),
			'posts_per_page' => $limit,
			'order' => $order,
			'meta_query' => array(
				array(
					'key' => 'prayer-anonymous', // filters out anonymous prayers
					'value' => 0,
					'compare' => 'LIKE',
				),
			),
		);

		// set var to be accessible in the called template
		set_query_var( 'args', $args );
		// load templates
		$templates = new Prayer_Template_Loader;
		// start a buffer to capture output
		ob_start();
		$templates->get_template_part( 'content', 'prayers' );
		return ob_get_clean();

	}

	static function register_script()
	{
		// register js
		wp_register_script( 'prayer-ui-js', plugins_url( '/prayers/elements/js/prayer-ui.js', 'prayer' ), array( 'jquery' ), '0.9.0', 'all' );
	}

	static function print_script()
	{
		if ( ! self::$add_script ) return;

		// load js
		wp_print_scripts( 'prayer-ui-js' );
	}
}