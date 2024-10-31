<?php
/**
 * Plugin Name: Prayers
 * Plugin URI: http://github.com/kalebheitzman/prayer
 * Description: Lets an organization share and update prayer requests via their website. This plugin also provides JSON feeds for other services to consume and requires the <a href="https://wordpress.org/plugins/rest-api/">WP REST API</a> be installed and activated first.
 * Version: 0.9.0
 * Author: Kaleb Heitzman
 * Author URI: http://github.com/kalebheitzman/prayer
 *
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 *
 * TODO: add internationalization 01/15/16
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin directory constant
define( 'PRAYER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Autoloader
 *
 * Autmagically loads classes from the echo/includes. Instantiates them in the
 * plugin file using the i.e. $prayers = new PrayerPrayers; format.
 */
spl_autoload_register(function ( $class ) {
	if ( is_readable( PRAYER_PLUGIN_DIR . "includes/classes/{$class}.php" ) )
		require PRAYER_PLUGIN_DIR . "includes/classes/{$class}.php";
});

/**
 * Prayer Post Type
 *
 * This defines the Prayer custom post type. A majority of the prayer app data
 * will be stored under this custom post type. Taxonomy and heavy use of meta
 * are used as well to construct the different data functionalities that this
 * plugin provides.
 *
 * @since 0.9.0
 */
$prayer_post_type_prayer = new Prayer_Post_Type_Prayer;

/**
 * Install and Uninstall hooks
 *
 * Creates settings, echo user, as well as cleans up the database on an
 * uninstall.
 *
 * @since 0.9.0
 */
if ( is_admin() )
	$prayer_setup = new Prayer_Plugin_Setup;

/**
 * Template Loader
 *
 * Allows template loading from plugin with prayer_get_template_part(). This
 * will load templates from your themes/your_theme/templates directory first
 * and then search for templates in plugins/prayers/templates
 *
 * @since  0.9.0
 */
$prayer_templates = new Prayer_Template_Loader;

/**
 * Mailer
 *
 * Instantiate a notifications class to notifiy users about various prayer
 * actions. This includes things like incoming prayer requests, prayers being
 * answered, etc.
 *
 * @since 0.9.0
 */
$prayer_mailer = new Prayer_Mailer;

/**
 * Front and Admin Styles
 *
 * Loads frontend and admin backend styles and scripts. These are vanilla css
 * and js files. In the future I may provide less/sass and coffeescript files
 * as well for advanced functionality.
 *
 * @since 0.9.0
 */

// load frontend styles
$prayer_frontend_scripts = new Prayer_Frontend_Scripts;

// load admin backend styles
if ( is_admin() )
	$prayer_admin_scripts = new Prayer_Admin_Scripts;

/**
 * Prayer Post Meta
 *
 * Meta is heavily used (instead of custom post fields) to save various data
 * that helps to define the context of the prayer request like subitter,
 * location, etc. You can find the Prayer Meta Box in the editing sidebar
 * area.
 *
 * @since 0.9.0
 */
$prayer_meta = new Prayer_Meta( 'prayer', '0.9.0' );

/**
 * Prayer Taxonomies
 *
 * Currently, a custom prayer category and tags taxonomy are associated with
 * the prayer post type to keep other taxonomies in your WP system clean. The
 * slugs used are prayer-category and prayer-tag. You can query off of these
 * slugs for any custom queries that you create.
 *
 * @since 0.9.0
 */
$prayer_taxonomy_category = new Prayer_Taxonomy_Category;
$prayer_taxonomy_tags = new Prayer_Taxonomy_Tags;

/**
 * Prayer Post Type Menu
 *
 * Creates a prayer menu to be used in the main editing sidebar menu of your
 * WP Install. Provides pages like settings, feeds, pending prayers, etc.
 *
 * Future ideas: MailChimp integration page
 *
 * @since  0.9.0
 */
if ( is_admin() )
    $prayer_submenu_pages = new Prayer_Submenu_Pages;

/**
 * Prayer Plugin Settings
 *
 * Creates a settings page for the plugin. Allows setting options like colors
 * enabling/disabling features, etc.
 *
 * @since  0.9.0
 */
if ( is_admin() )
	$prayer_settings = new Prayer_Settings;

/**
 * Shortcodes
 *
 * Provides various prayer related shortcodes to use in the WYSIWYG editor
 * of wordpress. This includes shortcodes for prayer listings, a front-end
 * submission form, and prayer locations-based map.
 *
 * @since 0.9.0
 */
Prayer_Shortcode_Prayers::init();
Prayer_Shortcode_Form::init();
Prayer_Shortcode_Map::init();
Prayer_Shortcode_Auth::init();
Prayer_Shortcode_Prayers_Manage::init();

/**
 * Admin Prayer Listing Page Columns
 *
 * Manipulates the prayer listing edit page to add columns to the listing
 * table with relevant data to the request like location, whether the prayer
 * has been answered, etc.
 *
 * @since 0.9.0
 */
if ( is_admin() )
	$prayer_admin_columns = new Prayer_Admin_Columns;

/**
 * Data Saving
 *
 * Prayer lets anonymous users submit prayers from the front end. These
 * submissions are saved to the Prayer custom post type and marked as pending
 * review. They are also associated with an Prayer user/author to help sort out
 * frontend submissions from submissions your authorized wordpress users can
 * make on the backend. Currently this includes a function to process frontend
 * form submissions and saving metadata on the backend.
 *
 * @since 0.9.0
 */
$prayer_form_processing = new Prayer_Form_Processing;

/**
 * MailChimp Integration
 *
 * This class establishes an api connection to MailChimp and allows you to
 * sync a list along with various segments to use in your MailChimp Campaigns.
 *
 * @since  0.9.0
 */
$prayer_mailchimp = new Prayer_Mailchimp;

/**
 * Prayer Auth
 *
 * Provides token authentication for users
 *
 * @since 0.9.0
 */
$prayer_auth = new Prayer_Auth;

/**
 * Prayer JSON API
 *
 * Outputs JSON responses from /prayers/api.
 *
 * @since  0.9.0
 */
$prayer_api = new Prayer_API;
