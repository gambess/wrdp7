<?php
/**
 * Script Class
 *
 * Handles the script and style functionality of plugin
 *
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Bdpw_Script {
	
	function __construct() {

		// Action to add style at front side
		add_action('wp_enqueue_scripts', array($this, 'bdpw_front_style'));

		// Action to add script at front side
		add_action( 'wp_enqueue_scripts', array($this, 'bdpw_front_script') );
	}

	/**
	 * Function to add style at front side
	 * 
	 * @package Blog Designer - Post and Widget
	 * @since 1.0
	 */
	function bdpw_front_style() {

		// Registring and enqueing slick css
		if( !wp_style_is( 'wpos-slick-style', 'registered' ) ) {
			wp_register_style( 'wpos-slick-style', BDPW_URL.'assets/css/slick.css', array(), BDPW_VERSION );
			wp_enqueue_style( 'wpos-slick-style');	
		}
		
		// Registring and enqueing public css
		wp_register_style( 'bdpw-public-css', BDPW_URL.'assets/css/bdpw-public.css', null, BDPW_VERSION );
		wp_enqueue_style( 'bdpw-public-css' );
	}

	/**
	 * Function to add script at front side
	 * 
	 * @package Blog Designer - Post and Widget
	 * @since 1.0
	 */
	function bdpw_front_script() {

		// Registring slick slider script
		if( !wp_script_is( 'wpos-slick-jquery', 'registered' ) ) {
	        wp_register_script( 'wpos-slick-jquery', BDPW_URL.'assets/js/slick.min.js', array('jquery'), BDPW_VERSION, true );
	    }
	    
	    // Registring public script
	    wp_register_script( 'bdpw-public-js', BDPW_URL.'assets/js/bdpw-public-js.js', array('jquery'), BDPW_VERSION, true );
	    wp_localize_script( 'bdpw-public-js', 'Bdpw', array(
	                                                        'is_mobile' => (wp_is_mobile()) ? 1 : 0,
	                                                        'is_rtl'    => (is_rtl()) ? 1 : 0
	                                                    ));
	}
}

$bdpw_script = new Bdpw_Script();