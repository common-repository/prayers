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
class Prayer_Shortcode_Map
{
	static $add_script;

	/**
	 * Initialize Script
	 * @since 0.9.0
	 */
	static function init() 
	{
		add_shortcode( 'prayer_map', array( __CLASS__, 'handle_shortcode' ) );

		add_action( 'init', array( __CLASS__, 'register_script' ) );
		add_action( 'wp_footer', array( __CLASS__, 'print_script' ) ); 
	}

	/**
	 * Prayer Form Shortcode
	 *
	 * Provides a frontend prayer map that displays where prayer requests are
	 * coming from. It currently accepts the following custom attributes:
	 *
	 * - None at this time
	 * 
	 * @param  array Custom Atafsdftributes
	 * @return html
	 * @since  0.9.0 
	 */
	static function handle_shortcode( $atts ) 
	{
		self::$add_script = true;

		// Attributes
		extract( shortcode_atts(
			array(
				'px_height' => null,
				'px_width' => null,
				'pct_height' => null,
				'pct_width' => null,
			), $atts)
		);

		// set var to be accessible in the called template
		set_query_var( 'px_height', $px_height );
		set_query_var( 'px_width', $px_width );
		set_query_var( 'pct_height', $pct_height );
		set_query_var( 'pct_width', $pct_width );

		// load templates
		$templates = new Prayer_Template_Loader;
		// start a buffer to capture output
		ob_start();
		$templates->get_template_part( 'content', 'prayers-map' );
		return ob_get_clean();
	}

	static function register_script()
	{
		// register css
		wp_register_style( 'leaflet-css', plugins_url( '/prayers/elements/css/leaflet.css', 'prayer' ), array(), null, 'all' );

		wp_register_script( 'leaflet-js', plugins_url( '/prayers/elements/js/leaflet.js', 'prayer' ), array(), null, 'all' );
		wp_register_script( 'prayer-map-js', plugins_url( '/prayers/elements/js/prayer-map.js', 'prayer' ), array( 'leaflet-js'), null, 'all' );
	}

	static function print_script()
	{
		if ( ! self::$add_script ) return;

		// load css
		wp_enqueue_style( 'leaflet-css' );

		// load js
		wp_enqueue_script( 'leaflet-js' );

		$tax = array( 'prayer-category' );
		$args = array(
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false 
		);
		$categories = get_terms($tax, $args);
		$params = array(
			'categories' => $categories,
		);
		wp_localize_script( 'prayer-map-js', 'map_params', $params );
		wp_enqueue_script( 'prayer-map-js' );

	}
}