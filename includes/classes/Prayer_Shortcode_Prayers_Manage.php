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
class Prayer_Shortcode_Prayers_Manage
{
	static $add_script;

	static $token;

	static $decoded;

	/**
	 * Initialize Script
	 * @since 0.9.0
	 */
	static function init() 
	{
		add_shortcode( 'prayers_manage', array( __CLASS__, 'handle_shortcode' ) );

		add_action( 'set_current_user', array( __CLASS__, 'validate_token' ), 0 );
		add_action( 'init', array( __CLASS__, 'logout' ), 1 );
		add_action( 'init', array( __CLASS__, 'register_script' ), 2 );
		add_action( 'wp_footer', array( __CLASS__, 'print_script' ) ); 
	}

	/**
	 * Validate Token
	 * @since 0.9.0
	 */
	static function validate_token()
	{
		self::$token = $_GET['token'];
		$slug = trim( parse_url ( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );

		if ( $slug == 'prayers/manage' && is_null( self::$token ) ) {
			Prayer_Template_Helper::set_flash_message( __( 'Invalid login link.', 'prayer' ), 'error' );
			$url = get_site_url() . '/prayers';
			header( 'Location: ' . $url );
			exit;
		}

		if ( $slug == 'prayers/manage' && ! is_null( self::$token ) ) {
			// decode the token, if it fails, the authenticate method
			// will redirect to GET /prayers
			self::$decoded = Prayer_Auth::authenticate( self::$token );
		}

	}

	/**
	 * Logout
	 *
	 * @since  0.9.0
	 */
	static function logout()
	{
		$logout = $_GET['logout'];
		if ( $logout == "1" ) {
			unset( $_COOKIE['wp-prayer-jwt'] );
			setcookie( 'wp-prayer-jwt', '', time()-3600, '/' );
		}
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
				'email' => self::$decoded->sub
			), $atts );

		// paged
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		// Attributes
		extract( $shortcode_atts );

		// WP_Query arguments
		$args = array (
			'post_type' => array( 'prayer' ),
			'post_status' => array( 'publish' ),
			'paged' => $paged,
			'posts_per_page' => $limit,
			'meta_query' => array(
				array(
					'key' => 'prayer-email', // filters out anonymous prayers
					'value' => $email,
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
		$templates->get_template_part( 'content', 'prayers-manage' );
		return ob_get_clean();

	}

	static function register_script()
	{
		// register js
		wp_register_script( 'prayer-ui-js', plugins_url( '/prayers/elements/js/prayer-ui.js', 'prayer' ), array( 'jquery' ), null, 'all' );
		wp_register_script( 'prayer-management', plugins_url( '/prayers/elements/js/prayer-management.js', 'prayer' ), array( 'jquery' ), null, 'all' );
	}

	static function print_script()
	{
		if ( ! self::$add_script ) return;

		// load js
		wp_print_scripts( 'prayer-ui-js' );
		wp_print_scripts( 'prayer-management' );
	}
}