<?php
/**
 * Template loader for PW Sample Plugin.
 *
 * @package   Prayer
 * @author 	  Kaleb Heitzman <kalebheitzman@gmail.com>
 * @link      https://github.com/kalebheitzman/prayer
 * @copyright 2016 Kaleb Heitzman
 * @license   GPL-3.0
 * @version   0.9.0
 */

class Prayer_Template_Loader extends Gamajo_Template_Loader {
 
	/**
	 * Prefix for filter names.
	 *
	 * @since 0.9.0
	 * @type string
	 */
	protected $filter_prefix = 'prayer';
 
	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * @since 0.9.0
	 * @type string
	 */
	protected $theme_template_directory = 'templates';
 
	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * @since 0.9.0
	 * @type string
	 */
	protected $plugin_directory = PRAYER_PLUGIN_DIR;
 
}