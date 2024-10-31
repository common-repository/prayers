<?php
/**
 * Prayer JSON API
 *
 * Outputs JSON responses from /prayers/api.
 *
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */

class Prayer_API
{
	/**
	 * Class construct
	 * @since 	0.9.0
	 */
	public function __construct()
	{
		add_action( 'rest_api_init', array( $this, 'register_get_api' ) );
		add_action( 'rest_api_init', array( $this, 'register_get_prayers' ) );
	}

	/**
	 * Register /prayers/v1/
	 * @since  0.9.0
	 */
	public function register_get_api()
	{
		return register_rest_route( 'prayers/v1', '/', array(
				'methods' => 'GET',
				'callback' => array( $this, 'get_api' )
		) );
	}

	/**
	 * Register echo/v1/prayers
	 * @since  0.9.0
	 */
	public function register_get_prayers()
	{
		return register_rest_route( 'prayers/v1', '/prayers', array(
				'methods' => 'GET',
				'callback' => array( $this, 'get_prayers' ),
				'args' => array(
					'category' => array(
						'default' => false,
						'sanitize_callback' => 'sanitize_title',
					),
					'tags' => array(
						'default' => false,
						'sanitize_callback' => false,
					),
					'country' => array(
						'default' => null,
						'sanitize_callback' => 'sanitize_title',
					),
					/*'coords' => array(
						'default' => null,
						'sanitize_callback' => false,
					),*/
					'answered' => array(
						'default' => null,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	/**
	 * GET echo/v1
	 * @since 0.9.0
	 */
	public function get_api( WP_REST_Request $request )
	{
			// var_dump($request); die();
			// You can access parameters via direct array access on the object:
	    $param = $request['some_param'];

	    // Or via the helper method:
	    $param = $request->get_param( 'some_param' );

	    // You can get the combined, merged set of parameters:
	    $parameters = $request->get_params();

	    // The individual sets of parameters are also available, if needed:
	    $parameters = $request->get_url_params();
	    $parameters = $request->get_query_params();
	    $parameters = $request->get_body_params();
	    $parameters = $request->get_default_params();

		return [];
	}

	/**
	 * GET echo/v1/prayers
	 * @since  0.9.0
	 */
	public function get_prayers( WP_REST_Request $request )
	{
		// get parameters
		$category = $request['category'];
		$tags = $request['tags'];
		$answered = $request['answered'];
		$country = $request['country'];

		// build the query args
		$args['post_type'] = array( 'prayer' );
		$args['post_status'] = array( 'publish' );
		$args['paged'] = $paged;
		$args['posts_per_page'] = $limit;
		$args['meta_query'][] = array(
			'key' => 'prayer-anonymous', // filters out anonymous prayers
			'value' => 0,
			'compare' => 'LIKE',
		);

		// check for single category
		if ( $category !== false ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'prayer-category',
				'field' => 'slug',
				'terms' => $category,
			);
		}

		// check for multiple tags
		if ( $tags !== false ) {
			$tags_e = explode( ',', $tags );
			$args['tax_query'][] = array(
				'taxonomy' => 'prayer-tag',
				'field' => 'slug',
				'terms' => $tags_e,
			);
		}

		// check to see if this prayer has been answered
		if ( ! is_null( $answered ) )
		{
			$args['meta_query'][] = array(
				'key' => 'prayer-answered',
				'value' => $answered,
				'compare' => 'LIKE',
			);
		}

		// check to see if this prayer has been answered
		if ( ! is_null( $country ) )
		{
			$args['meta_query'][] = array(
				'key' => 'prayer-location-country-short',
				'value' => $country,
				'compare' => 'LIKE',
			);
		}

		// The Query
		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		foreach ($posts as $key => $post) {

			$prayer = $this->run_prayer_template( $post );
			$prayers[] = $prayer;
			// var_dump($prayers[$key]);
		}

		return $prayers;
	}

	/**
	 * GET echo/v1/prayers
	 * @since  0.9.0
	 */
	public function get_prayers_by_category()
	{
		// WP_Query arguments
		$args = array (
			'post_type' => array( 'prayer' ),
			'post_status' => array( 'publish' ),
			'paged' => $paged,
			'posts_per_page' => $limit,
			'meta_query' => array(
				array(
					'key' => 'prayer-anonymous', // filters out anonymous prayers
					'value' => 0,
					'compare' => 'LIKE',
				),
			),
		);

		// The Query
		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		foreach ($posts as $key => $post) {

			$prayer = $this->run_prayer_template( $post );
			$prayers[] = $prayer;
			// var_dump($prayers[$key]);
		}

		return $prayers;
	}

	/**
	 * Prayer Template
	 * @param  object $post WP Post Object
	 * @return object       Prayer to output to JSON
	 */
	private function run_prayer_template( $post ) {

		// instantiate a prayer object
		$prayer = new stdClass();

		// get the post meta
		$meta = get_post_meta( $post->ID );

		// standard wp fields filtered down
		$prayer->ID = $post->ID;
		$prayer->post_date = $post->post_date;
		$prayer->post_date_gmt = $post->post_date_gmt;
		$prayer->title = $post->post_title;
		$prayer->content = $post->post_content;
		$prayer->excerpt = $post->post_excerpt;

		if ( isset($meta['prayer-response'][0])) {
			$prayer->content_answered = $meta['prayer-response'][0];
			$prayer->excerpt_answered = $this->get_excerpt( $meta['prayer-response'][0] );
		}

		$prayer->slug = $post->post_name;
		$prayer->guid = $post->guid;
		$prayer->answered = $meta['prayer-answered'][0];

		// set the prayer count
		$prayer->prayer_count = $meta['prayer-count'][0];

		// set the user info
		$prayer->submitter = array(
			'name' => $meta['prayer-name'][0]
		);

		// set the location data
		$lon = $meta['prayer-location-longitude'];
		$lat = $meta['prayer-location-latitude'];
		$add = $meta['prayer-location-formatted-address'];
		$c_long = $meta['prayer-location-country-long'];
		$c_short = $meta['prayer-location-country-short'];
		$prayer->geocode = array(
			'place' => $meta['prayer-location'][0],
			'longitude' => $lon[ sizeof($lon)-1 ],
			'latitude' => $lat[ sizeof($lat)-1 ],
			'formatted' => $add[ sizeof($add)-1 ],
			'c_long' => $c_long[0],
			'c_short' => $c_short[0],
			'lang' => $meta['prayer-lang'][0],
		);

		// set the category data
		$categories = get_the_terms( $prayer->ID, 'prayer-category' );
		foreach( $categories as $category )
		{
			$prayer->category[] = array(
				'name' => $category->name,
				'slug' => $category->slug,
				'id' => $category->term_id,
			);
		}

		// set the tags data
		$tags = get_the_terms( $prayer->ID, 'prayer-tag' );
		if ( $tags != false )
		{
			foreach( $tags as $tag )
			{
				$prayer->tag[] = array(
					'name' => $tag->name,
					'slug' => $tag->slug,
					'id' => $tag->term_id,
				);
			}
		}

		return $prayer;
	}

	/**
	 * GET /prayers/api/category/{category}
	 * @since 0.9.0
	 */
	public function api_category()
	{
		return [];
	}

	/**
	 * GET /prayers/api/tags/{tags}
	 * @since 0.9.0
	 */
	public function api_tags()
	{
		return [];
	}

	/**
	 * GET /prayers/api/location/{location}
	 * @since 0.9.0
	 */
	public function api_location()
	{
		return [];
	}

	/**
	 * Get excerpt from string
	 *
	 * @param String $str String to get an excerpt from
	 * @param Integer $startPos Position int string to start excerpt from
	 * @param Integer $maxLength Maximum length the excerpt may be
	 * @return String excerpt
	 */
	function get_excerpt($str, $startPos=0, $maxLength=100) {
		if(strlen($str) > $maxLength) {
			$excerpt   = substr($str, $startPos, $maxLength-3);
			$lastSpace = strrpos($excerpt, ' ');
			$excerpt   = substr($excerpt, 0, $lastSpace);
			$excerpt  .= '...';
		} else {
			$excerpt = $str;
		}

		return $excerpt;
	}

}
