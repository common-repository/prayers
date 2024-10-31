<?php
/**
 * Enqueue Admin Styles and Scripts
 * 
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */

class Prayer_Admin_Scripts 
{
	/**
	 * Class Construct
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
	}

	/**
	 * Register Admin Styles and Scripts
	 * @since  0.9.0
	 */
	function register_admin_scripts() {
		// styles	
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_style( 'prayer-admin-css', plugins_url( '/prayers/includes/css/prayer-admin.css', 'prayer' ), array(), '20151228', 'all' );
		wp_enqueue_style( 'prayer-admin-css');

		// scripts
		wp_register_script( 'prayer-admin-settings', plugins_url( '/prayers/includes/js/prayer-admin-settings.js', 'prayer' ), array( 'wp-color-picker' ), '20151228', 'all' );
		wp_register_script( 'prayer-admin-tabs', plugins_url( '/prayers/includes/js/prayer-admin-tabs.js', 'prayer' ), array( 'wp-color-picker' ), '20151228', 'all' );
		wp_register_script( 'prayer-admin-notes', plugins_url( '/prayers/includes/js/prayer-admin-notes.js', 'prayer' ), array( 'wp-color-picker' ), '20151228', 'all' );

		wp_enqueue_script( 'prayer-admin-settings');	
		wp_enqueue_script( 'prayer-admin-tabs');	
		wp_enqueue_script( 'prayer-admin-notes');	
	}
}