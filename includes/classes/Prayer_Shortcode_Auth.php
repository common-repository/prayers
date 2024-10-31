<?php
/**
 * Auth Shortcode
 *
 * Provides a form to allow users to login and see the requests they have made
 * as well as send in updates about thier prayer request.
 * 
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */
class Prayer_Shortcode_Auth
{
	static $add_script;

	/**
	 * Initialize Script
	 * @since 0.9.0
	 */
	static function init() 
	{
		add_shortcode( 'prayer_auth_form', array( __CLASS__, 'handle_shortcode' ) );

		add_action( 'init', array( __CLASS__, 'register_script' ) );
		add_action( 'wp_footer', array( __CLASS__, 'print_script' ) ); 
	}

	/**
	 * Prayer Form Shortcode
	 *
	 * Provides a frontend prayer submission form. This allows frontend users to 
	 * submit requests to the prayer app. It currently accepts the following
	 * custom attributes:
	 *
	 * - None at this time
	 * 
	 * @param  array Custom Attributes
	 * @return html
	 * @since  0.9.0 
	 */
	static function handle_shortcode( $atts ) 
	{
		self::$add_script = true;

		// Attributes
		extract( shortcode_atts(
			array(
				'anonymous' => true
			), $atts )
		);
		// load templates
		$templates = new Prayer_Template_Loader;
		// start a buffer to capture output
		ob_start();
		$templates->get_template_part( 'content', 'prayers-auth-form' );
		return ob_get_clean();

	}

	static function register_script()
	{
		// register js
		wp_register_script( 'jquery-validation', plugins_url( '/prayers/elements/css/jquery.validate.min.js', 'prayer' ), array( 'jquery' ) );
		wp_register_script( 'jquery-validation-extras', plugins_url( '/prayers/elements/css/additional-methods.min.js', 'prayer' ), array( 'jquery' ) );
		wp_register_script( 'prayer-auth-form-js', plugins_url( '/prayers/elements/js/prayer-auth-form.js', 'prayer' ), array( 'jquery' ), '0.9.0', 'all' );
	}

	static function print_script()
	{
		if ( ! self::$add_script ) return;

		// load js
		wp_print_scripts( 'jquery-validation' );
		wp_print_scripts( 'jquery-validation-extras' );
		wp_print_scripts( 'prayer-auth-form-js' );
	}
}