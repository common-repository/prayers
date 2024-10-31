<?php
/**
 * Mailchimp Class
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
class Prayer_Mailchimp
{
	public $mc_api;

	public $mc_segments;

	public $mc_groups;

	public $current_list;

	public $current_list_name;

	/**
	 * Class Construct
	 *
	 * @since  0.9.0
	 */
	public function __construct()
	{
		$settings = get_option( 'prayer_settings_options' );
		$api_key = $settings['mailchimp_api_key'];

		// set the mailchimp api
		if ( ! empty($api_key) ) {
			$this->mc_api = new Mailchimp( $api_key );			
		}
		// set the current list
		$this->current_list = get_option( 'prayer_mailchimp_list_id' ); 

		// add a form processor 
		add_action( 'init', array( $this, 'set_mailchimp_list_submission' ) );
		add_action( 'init', array( $this, 'sync_mailchimp_list_submission' ) );
		add_action( 'init', array( $this, 'sync_mailchimp_segment' ) );
		add_action( 'init', array( $this, 'sync_mailchimp_groups' ) );

		// set a list of segments available in mc list
		$this->mc_segments = array(
			'unanswered-prayers' => __( 'Unanswered Prayers', 'prayer' ),
			'new-prayed-prayers' => __( 'Recently Prayed Prayers', 'prayer' ),
		);

		$this->mc_groups = array();
	}

	/**
	 * Capture List Sync Submission
	 *
	 * @since 0.9.0
	 */
	public function sync_mailchimp_list_submission()
	{
		// check to see if this is a prayer submission
		if ( isset( $_POST['mailchimp-sync-list']) && '1' == $_POST['mailchimp-sync-list']) 
		{
			// check for a valid nonce
			$is_valid_nonce = ( isset( $_POST[ 'mailchimp_nonce' ] ) && wp_verify_nonce( $_POST[ 'mailchimp_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false'; 
		    // Exits script depending on save status
		    if ( ! $is_valid_nonce ) {
		        return;
		    }
			$this->sync_mailchimp_list();
		}
	}

	/**
	 * Sync MailChimp List
	 */
	public function sync_mailchimp_list()
	{
		// get the list id	
		if ( ! empty($this->current_list) )
		{
			// get a list of all emails and names
			$emails = Prayer_Sql::get_all_emails();

			$batch = array();
			foreach ( $emails as $email )
			{
				$name = $item->name;
				$name_parts = explode( " ", $name );
				$fname = array_shift( $name_parts );
				$lname = implode( "", $name_parts );
				$batch[] = array(
					'email' => array( 'email' => $email->email ),
					'merge_vars' => array( 'fname' => $fname, 'lname' => $lname )
				);
			}

			// batch subscribe 
			try {
				$results = $this->mc_api->lists->batchSubscribe( $this->current_list, $batch, false, true, true );
				$add_count = $results['add_count'];
				$update_count = $results['update_count'];
				Prayer_Template_Helper::set_flash_message( __( 'Successfully synced your MailChimp List (' . $add_count . ' added, ' . $update_count . ' updated).' , 'prayer' ) );
			} catch ( Mailchimp_Error $e ) {
				if ( $e->getMessage() ) {
					Prayer_Template_Helper::set_flash_message( __( $e->getMessage(), 'prayer' ), 'error' );
				}
				else {
					Prayer_Template_Helper::set_flash_message( __( 'An unknown error occurred', 'prayer' ), 'error' );
				}
			}
		}
	}
	
	/**
	 * Sync Segment
	 *
	 * @since  0.9.0
	 */
	public function sync_mailchimp_segment()
	{
		// check to see if this is a prayer submission
		if ( isset( $_POST['mailchimp-sync-segment']) && '1' == $_POST['mailchimp-sync-segment']) 
		{
			// check for a valid nonce
			$is_valid_nonce = ( isset( $_POST[ 'mailchimp_nonce' ] ) && wp_verify_nonce( $_POST[ 'mailchimp_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false'; 
		    // Exits script depending on save status
		    if ( ! $is_valid_nonce ) {
		        return;
		    }
		    // get the post
			$post = $_POST;
			$segment = $post['segment'];

			switch ($segment)
			{
				case 'unanswered-prayers':
					$emails = Prayer_Sql::get_unanswered_prayers();
					$emails_filtered = Prayer_Sql::cleanup_by_email( $emails );
					$results = $this->sync_segment_by_name( 'Unanswered Prayers', $emails_filtered );
					if ( $results === true ) Prayer_Sql::set_emails_synced( $emails );
					break;

				case 'new-prayed-prayers':
					$emails = Prayer_Sql::get_newly_prayed();
					$emails_filtered = Prayer_Sql::cleanup_by_email( $emails );
					$results = $this->sync_segment_by_name( 'Newly Prayed', $emails_filtered );
					if ( $results === true ) Prayer_Sql::set_emails_synced( $emails );
					break;
			}
		}
	}
	
	/**
	 * Sync Segment by Name
	 * @param  string $segment Name of Segment
	 * @param  object $emails  WPDB Object
	 */
	public function sync_segment_by_name( $segment, $emails )
	{
		$this->sync_mailchimp_list();

		// get all segments to test against
		$segments = $this->mc_api->lists->staticSegments( $this->current_list );
		$segment_exists = Prayer_Plugin_Helper::in_array_rec($segment, $segments);

		// reset the segment if it exists
		if ( $segment_exists)
		{
			foreach( $segments as $key => $item )
			{
				if ( $item['name'] == $segment) {
					$segment_id = $segments[$key]['id'];
				}
			}
			$this->mc_api->lists->staticSegmentReset( $this->current_list, $segment_id );
		}
		// create a new segment if it doesn't exist
		else
		{
			$segment_id = $this->mc_api->lists->staticSegmentAdd( $this->current_list, $segment );
		}

		// build the batch
		foreach ( $emails as $email )
		{
			$batch[] = array(
				'email' => $email->email
			);
		}

		if ( is_null( $batch ) || empty( $batch ) ) {
			Prayer_Template_Helper::set_flash_message( __( 'There are no emails to sync at this time.', 'prayer' ) );
			return false;
		}
		// batch subscribe 
		try {
			$results = $this->mc_api->lists->staticSegmentMembersAdd( $this->current_list, $segment_id, $batch );
			$count = $results['success_count'];
			Prayer_Template_Helper::set_flash_message( __( 'Successfully synced ' . $count . ' people to your MailChimp Segment: ' . $segment, 'prayer' ) );
			return true;
		} catch ( Mailchimp_Error $e ) {
			if ( $e->getMessage() ) {
				Prayer_Template_Helper::set_flash_message( __( $e->getMessage(), 'prayer' ), 'error' );
			}
			else {
				Prayer_Template_Helper::set_flash_message( __( 'An unknown error occurred', 'prayer' ), 'error' );
			}
		}
		return false;
	}

	/**
	 * MailChimp List Selection
	 *
	 * @since  0.9.0 
	 */
	public function set_mailchimp_list_submission()
	{
		// check to see if this is a prayer submission
		if ( isset( $_POST['mailchimp-submission']) && '1' == $_POST['mailchimp-submission']) 
		{
			// check for a valid nonce
			$is_valid_nonce = ( isset( $_POST[ 'mailchimp_nonce' ] ) && wp_verify_nonce( $_POST[ 'mailchimp_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false'; 
		    // Exits script depending on save status
		    if ( ! $is_valid_nonce ) {
		        return;
		    }
		    // get the post
			$post = $_POST;
			$options = explode("|", $post['prayer_mailchimp_list']);
			update_option( 'prayer_mailchimp_list_id', $options[0] );
			update_option( 'prayer_mailchimp_list_name', $options[1] );

			Prayer_Template_Helper::set_flash_message( __( 'Successfully set list to ' . $options[1], 'prayer' ) );
		}
	}

	/**
	 * Sync Groups
	 */
	public function sync_mailchimp_groups()
	{
		// check to see if this is a prayer submission
		if ( isset( $_POST['mailchimp-sync-groups']) && '1' == $_POST['mailchimp-sync-groups']) 
		{
			// check for a valid nonce
			$is_valid_nonce = ( isset( $_POST[ 'mailchimp_nonce' ] ) && wp_verify_nonce( $_POST[ 'mailchimp_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false'; 
		    // Exits script depending on save status
		    if ( ! $is_valid_nonce ) {
		        return;
		    }

		    // get the categories to create/update groups
		    $prayer_category = array( 'prayer-category' );
			$args = array(
				'orderby' => 'name',
				'order' => 'ASC',
				'hide_empty' => false 
			);
			$prayer_categories = get_terms($prayer_category, $args);

			// build the groups and add additional groups
			foreach( $prayer_categories as $group ) {
	    		$groups[] = $group->name;
	    	} 
	    	$groups[] = __( 'Answered Prayers', 'prayer' );

	    	// Create an interest grouping called Your Interests if it doesn't
	    	// exist already. If it does, use add the groups to it.
	    	try
	    	{
	    		// get all groups
				$groupings = $this->mc_api->lists->interestGroupings( $this->current_list );
				foreach( $groupings as $key => $group )
				{
					if ( $group['name'] == 'Prayer Interests' )
					{
						$grouping = $group;
					}
				}
				if ( is_null( $grouping ) )
				{
					$grouping = $this->mc_api->lists->interestGroupingAdd( $this->current_list, 'Prayer Interests', 'checkboxes', $groups );
				}
	    	}
	    	catch( Mailchimp_Error $e )
	    	{
	    		$grouping = $this->mc_api->lists->interestGroupingAdd( $this->current_list, 'Prayer Interests', 'checkboxes', $groups );
	    	}

	    	// interest groupings have been created or found.
	    	// updated the groupings with any new categories or groups
	    	$new_groupings = $groupings = $this->mc_api->lists->interestGroupings( $this->current_list );
	    	
	    	// get the current grouping
	    	foreach( $new_groupings as $key => $group )
			{
				if ( $group['name'] == 'Prayer Interests' )
				{
					$new_grouping = $group;
				}
			}
			$current_groups = $new_grouping['groups'];

			// build an index to search against
			$current_group_index = array();
			foreach( $current_groups as $group )
			{
				$current_group_index[] = $group['name'];
			}

			// add any new groups not a part of current grouping
			foreach ( $groups as $group )
			{
				if ( ! in_array( $group, $current_group_index ) )
				{
					$this->mc_api->lists->interestGroupAdd( $this->current_list, $group );
				}
			}

			Prayer_Template_Helper::set_flash_message( __( 'Successfully synced your groups.', 'prayer' ) );
		}
	}

}