<?php
/*
Plugin Name: Blog Designer - Post and Widget
Plugin URL: https://www.wponlinesupport.com/
Description: Display Post on your website with 2 designs(Grid and Slider) with 1 widget.
Version: 1.1.3
Author: WP Online Support
Author URI: https://www.wponlinesupport.com/
Contributors: WP Online Support
Text Domain: blog-designer-for-post-and-widget
Domain Path: /languages/
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0.0
 */
if( !defined( 'BDPW_VERSION' ) ) {
    define( 'BDPW_VERSION', '1.1.3' ); // Version of plugin
}
if( !defined( 'BDPW_DIR' ) ) {
    define( 'BDPW_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if( !defined( 'BDPW_URL' ) ) {
    define( 'BDPW_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if( !defined( 'BDPW_PLUGIN_BASENAME' ) ) {
    define( 'BDPW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Plugin base name
}
if( !defined('BDPW_POST_TYPE') ) {
    define('BDPW_POST_TYPE', 'post'); // Post type name
}
if( !defined('BDPW_CAT') ) {
    define('BDPW_CAT', 'category'); // Plugin category name
}

/**
 * Load Text Domain
 * This gets the plugin ready for translation
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */
function bdpw_load_textdomain() {
    load_plugin_textdomain( 'blog-designer-for-post-and-widget', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

// Action to load plugin text domain
add_action('plugins_loaded', 'bdpw_load_textdomain');

/**
 * Activation Hook
 * 
 * Register plugin activation hook.
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'bdpw_install' );

function bdpw_install() {
    // Install functionality
}

// Functions file
require_once( BDPW_DIR . '/includes/bdpw-functions.php' );

// Script Class File
require_once( BDPW_DIR . '/includes/class-bdpw-script.php' );

// Admin Class File
require_once( BDPW_DIR . '/includes/admin/class-bdpw-admin.php' );

// Shortcode File
require_once( BDPW_DIR . '/includes/shortcode/wpsp-post.php' );
require_once( BDPW_DIR . '/includes/shortcode/wpsp-recent-post-slider.php' );

// Widget File
require_once( BDPW_DIR . '/includes/widget/latest-post-widget.php' );