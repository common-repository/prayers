<?php
/**
 * Authentication Class
 *
 * Allows frontened users to login via thier email and a token link
 * 
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */
class Prayer_Auth
{
	private $key;

	/**
	 * Class Construct
	 *
	 * @since  0.9.0
	 */
	public function __construct()
	{
		add_action( 'init', array( $this, 'send_email' ) );

		// setup php based validation
		$this->gump = new GUMP();
		$this->set_validation_rules();
		$this->set_validation_filters();

		$this->key = get_option( 'prayer_jwt_key' );
	}

	/**
	 * Set Validation Rules for the frontend form
	 * @since  0.9.0 
	 */
	function set_validation_rules() {
		$rules = array(
			'prayer_email' => 'required,valid_email',
		);
		$this->gump->validation_rules( $rules );
	}

	/**
	 * Set Validation Filters
	 * @since  0.9.0 
	 */
	function set_validation_filters() {
		$filters = array(
			'prayer_email' => 'trim|sanitize_email',
		);
		$this->gump->filter_rules( $filters );
	}

	/**
	 * Send Email
	 *
	 * Captures email address form prayers-send-token form and generates a 
	 * token, stories in the database, and send a link via email to the user.
	 * This allows users to manage thier content without a password. 
	 *
	 * @since 0.9.0
	 */
	public function send_email()
	{
		// check to see if this is a token submission
		if ( isset( $_POST['prayer-send-token']) && '1' == $_POST['prayer-send-token']) {
			// check for a valid nonce
			$is_valid_nonce = ( isset( $_POST[ 'prayer_nonce' ] ) && wp_verify_nonce( $_POST[ 'prayer_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false'; 
		    // Exits script depending on save status
		    if ( ! $is_valid_nonce ) {
		        return;
		    }
		    // validate the data
			$post = $_POST;
			$validated_data = $this->gump->run( $post );
			// failed validation
			if ( $validated_data === false ) {
				session_start();
				// get the errors
				$errors = $this->gump->get_readable_errors( false );
				// set an error flash
				Prayer_Template_Helper::set_flash_message( __( 'Something went wrong. Please try again later.', 'prayer' ), 'error' );

			// passed validation
			} else {
				$email = $validated_data['prayer_email'];
				$jwt = $this->generate_token( $email );
			}

			// send the email
			$mailer = Prayer_Mailer::send_jwt_email( $email, $jwt );

			if ( $mailer === true )
			{
				// show flash messages
				Prayer_Template_Helper::set_flash_message( __( 'Please check your email for a login link.', 'prayer' ) );
			}
			else
			{
				// set an error flash
				Prayer_Template_Helper::set_flash_message( __( 'Something went wrong. Please try again later.', 'prayer' ), 'error' );
			}

		}
	}

	/**
	 * Generate Token
	 * @param  string $email Email
	 * @return string        JWT
	 */
	public function generate_token( $email = null )
	{
		if ( is_null( $email ) ) return false;
		// generate a token
		$token = array(
			"iss" => get_site_url(), // plugin url
			"aud" => get_site_url(), // site url
			"sub" => $email,
		);
		$jwt = JWT::encode( $token, $this->key );

		return $jwt;
	}

	/**
	 * Static Authenticate Method
	 * @param  string $token  JWT 
	 * @return boolean        Authenticated
	 */
	public static function authenticate( $token )
	{
		// get the key
		$key = get_option( 'prayer_jwt_key' );

		try {
			// decode the token
			$decoded = JWT::decode($token, $key, array('HS256'));

			// save the token in storage for use on the site
			setcookie( 'wp-prayer-jwt', $token, (time()+3600), "/" );

			// return the decoded token
			return $decoded;
		} 
		catch ( Exception $e ) 
		{
			Prayer_Template_Helper::set_flash_message( $e->getMessage(), 'error' );
			
			$location = get_site_url() . '/prayers';
			header( 'Location: ' . $location );
			exit;
		}
	}

	/**
	 * Check for authenticated user via JWT
	 * @return array
	 */
	public static function authenticated()
	{
		if ( ! isset( $_COOKIE['wp-prayer-jwt'] ) ) { return false; }
		return true;
	}

	/**
	 * Get the JWT
	 * @return  string JWT
	 *
	 * @since  0.9.0
	 */
	public static function get_token()
	{
		if ( ! isset( $_COOKIE['wp-prayer-jwt'] ) ) { return false; }
		return $_COOKIE['wp-prayer-jwt'];
	}

}