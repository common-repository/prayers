<?php
/**
 * Template Helpers
 *
 * Provideds helpers to be used inside of template files for things like
 * buttons, lists, etc.
 * 
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */
class Prayer_Template_Helper
{

	/**
	 * Terms List
	 * @param  integer ID
	 * @param  string Taxonomy Name
	 * @return html Output for template
	 * @since  0.9.0
	 */
	static public function get_terms_list( $id = 0, $taxonomy = null ) {
		if ( is_null($taxonomy) ) { return 'Taxonomy not supplied'; }

		// start the output
		$output = "";

		// get the terms for custom post type
		$terms = get_the_terms( $id, $taxonomy );
		// return a wrapped list
		if ( $terms && ! is_wp_error( $terms ) ) {
			// create a terms array to store term names
			$terms_list = array();
			foreach ( $terms as $term ) {
				$terms_list[] = $term->name;
			} 
			// build the output for each term
			foreach ($terms_list as $term) {
				$output .= '<span class="prayer prayer-box term taxonomy-' . $taxonomy . '">';
				$output .= '<a href="' . get_term_link( $term, $taxonomy ) . '">' . $term . '</a>';
				$output .= '</span>';
			}
		}

		// return the html output to the template
		return $output;
	}

	/**
	 * Build Prayer Button
	 *
	 * Gets the prayer count and builds an interactive button that allows users
	 * to say they've prayed for the request on screen. Thier response is stored
	 * in html5 localStorage to check against whether they've prayed for a
	 * specific request before or not.
	 * 
	 * @param  integer ID
	 * @return string html content
	 * @since  0.9.0
	 */
	static public function get_prayed_button( $id = 0 ) {
		if ( $id == 0 ) return;
		// get the current prayer count and set to 0 if it's empty
		$prayer_count = get_post_meta( $id, 'prayer-count', 1);
		if ( empty($prayer_count) ) {
			$prayer_count = 0;
		}
		// build the prayer button
		ob_start();
		?>
			<div class="prayer-button" data-prayer-id="<?php the_ID(); ?>">
				<span class="prayer-count prayer-<?php the_ID(); ?>"><?php echo $prayer_count; ?></span>
				<span class="prayer-pray-button">
					<form class="prayer-prayed" action="" method="post" data-prayer-id="<?php the_ID(); ?>">
						<?php wp_nonce_field( basename(__FILE__), 'prayer_nonce' ); ?>	
						<input type="hidden" name="prayer_id" value="<?php the_ID(); ?>" />
						<input type="submit" value="Pray" />
						<input type="hidden" name="prayer-click" value="1" />
					</form>
				</span>
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get Prayer Location
	 * @param  integer ID
	 * @return html Location string
	 * @since  0.9.0
	 */
	static public function get_prayer_location( $id = 0 ) {
		if ( $id == 0) { return; }
		// get the prayer location
		$prayer_location = get_post_meta( $id, 'prayer-location', 1);
		if ( empty($prayer_location) ) { return false; }
		// build the html
		ob_start(); ?>
			<span class="prayer-box"><?php echo $prayer_location; ?></span>
		<?php return trim( ob_get_clean() );
	}

	/**
	 * Get Name of Prayer Request Submitter
	 * @param  integer ID
	 * @return string Name of submitter
	 * @since  0.9.0
	 */
	static public function get_prayer_name( $id = 0 ) {
		if ( $id == 0) { return; }
		// get the submitter name
		$prayer_name = get_post_meta( $id, 'prayer-name', 1);
		if ( empty($prayer_name) ) { return false; }
		// build the html
		echo $prayer_name;
	}

	/**
	 * Get Name of Prayer Request Submitter
	 * @param  integer ID
	 * @return string Name of submitter
	 * @since  0.9.0
	 */
	static public function get_prayer_answered( $id = 0 ) {
		if ( $id == 0) { return false; }
		// get the submitter name
		$prayer_answered = get_post_meta( $id, 'prayer-answered', 1);
		if ( $prayer_answered == "1" ) return true;
		return false;
	}

	/**
	 * Get Prayer Count
	 * @param  integer ID
	 * @return string
	 * @since  0.9.0
	 */
	static public function get_prayer_count( $id = 0 ) {
		if ($id == 0) return 0;
		// get the prayer count and set to 0 if it doesn't exist
		$prayer_count = get_post_meta( $id, 'prayer-count', 1);
		if ( empty($prayer_count) ) {
			$prayer_count = 0;
		}
		// set language specific output
		if ($prayer_count == 1) {
			$output = __("Prayed once", "prayer");
		}
		else {
			$output = __("Prayed", "prayer") . " " . $prayer_count . " " . __("times", "prayer");		
		}
		// return the html
		return $output;
	}

	/**
	 * Get Avatar
	 * @param  integer ID
	 * @param  integer Size of avatar
	 * @return string Html string with avatar image
	 * @since  0.9.0 
	 */
	static public function get_avatar( $id = 0, $size = 26 ) {
		if ($id == 0) return;
		// get te email associated with the prayer posting and build avatar
		$email = get_post_meta( $id, 'prayer-email', 1);
		// return the html
		return get_avatar( $email, $size );
	}

	/**
	 * Get Current URL
	 * @return string Current url
	 */
	static public function current_page_url() {
		global $wp;
		$current_url = home_url(add_query_arg(array(),$wp->request));
		return $current_url;
	}

	/**
	 * Set Flash Message
	 */
	static public function set_flash_message( $message, $type = 'success' )
	{
		session_start();
		// set a flash message
		$_SESSION['flash']['message'] = array(
			'type' => $type,
			'message' => $message,
		);
	}

	/**
	 * Get Flash Message
	 */
	static public function flash_message()
	{
		$flash_message = $_SESSION['flash']['message'];

		if ( ! empty( $flash_message ) ) 
		{
			echo '<div class="flash flash-' . $flash_message['type'] . '">'. $flash_message['message'] . '</div>';
		}
	}

	/**
     * Get Categories
     * @return object WP Taxonomy
     */
    public static function get_categories()
    {
        $prayer_category = array( 'prayer-category' );
        $args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false 
        );
        $prayer_categories = get_terms($prayer_category, $args);
        return $prayer_categories;
    }

    /**
     * Get Categories
     * @return object WP Taxonomy
     */
    public static function get_tags()
    {
        $prayer_tag = array( 'prayer-tag' );
        $args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => false 
        );
        $prayer_tags = get_terms($prayer_tag, $args);
        return $prayer_tags;
    }

    /**
     * Navigation
     *
     * @since  0.9.0 
     */
    public static function get_navigation()
    {
    	
    	// get the slug
    	$slug = self::get_slug();
    	// set an active class for manage if on GET prayers/manage
    	$manage_class = ( $slug == 'prayers/manage' ? 'manage active' : 'manage' );


    	// build the links
    	$links = array();

    	// Authenticated Pages
    	if ( Prayer_Auth::authenticated() ) 
    	{
    		$links[] = array(
    			'href' => "/prayers/manage?token=" . Prayer_Auth::get_token(),
    			'title' => "My Prayers",
    			'class' => $manage_class
    		);

    		$links[] = array(
    			'href' => "/prayers?logout=1",
    			'title' => 'Logout',
    			'class' => 'logout',
    		);
    	}
    	else 
    	{
			$links[] = array(
	    		'href' => "/prayers/login",
	    		'title' => "Login",
	    		'class' => 'login'
	    	);
    	}

    	// set var to be accessible in the called template
		set_query_var( 'links', $links );

    	// load templates
		$templates = new Prayer_Template_Loader;

		// start a buffer to capture output
		ob_start();
		$templates->get_template_part( 'navigation', 'prayers' );
		echo ob_get_clean();
	}

	public function get_slug()
	{
		return trim( parse_url ( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
	}

	/**
	 * Pagination
	 * @return string HTML output
	 */
	public static function pagination( $max_num_pages = 1 )
	{
		global $paged;

		$big = 999999999; // need an unlikely integer
		$translated = __( 'Page', 'prayer' ); // Supply translatable string

		echo paginate_links( array(
	   		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	   		'format' => '?paged=%#%',
	   		'current' => max( 1, $paged ),
	   		'total' => $max_num_pages, //$q is your custom query
	   		'before_page_number' => '<span class="screen-reader-text">' . $translated . ' </span>'
	 	) );

	}
}