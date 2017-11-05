<?php
/*
Plugin Name: JoomSport
Plugin URI: http://joomsport.com
Description: Sport league plugin
Version: 3.0
Author: BearDev
Author URI: http://BearDev.com
License: GPLv3
Requires at least: 4.0
Text Domain: joomsport-sports-league-results-management
Domain Path: /languages/
*/

/* Copyright 2016
BearDev, JB SOFT LLC, BY (sales@beardev.com)
This program is free licensed software; 

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
//error_reporting(E_ALL);
//ini_set("display_errors", 1); 
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define('JOOMSPORT_PATH', plugin_dir_path( __FILE__ ));
define('JOOMSPORT_PATH_INCLUDES', JOOMSPORT_PATH . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR);
define('JOOMSPORT_PATH_HELPERS', JOOMSPORT_PATH_INCLUDES  . 'helpers' . DIRECTORY_SEPARATOR);
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-settings.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-permalink.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-admin-install.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-setup-demo.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-post-types.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-user-rights.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-templates.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-shortcodes.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-actions.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-widgets.php';
require_once JOOMSPORT_PATH_INCLUDES . 'joomsport-delete.php';
$joomsportSettings = null;
require_once JOOMSPORT_PATH_HELPERS . 'joomsport-helper-selectbox.php';
require_once JOOMSPORT_PATH_HELPERS . 'joomsport-helper-ef.php';
require_once JOOMSPORT_PATH_HELPERS . 'joomsport-helper-objects.php';
require_once JOOMSPORT_PATH_INCLUDES . '3d'. DIRECTORY_SEPARATOR . 'gallery-metabox-master' . DIRECTORY_SEPARATOR . 'gallery.php';
register_activation_hook(__FILE__, array('JoomSportAdminInstall', '_installdb') );
register_activation_hook(__FILE__, array('JoomSportUserRights', 'jsp_add_theme_caps') );





function joomsport_activation_redirect( $plugin ) {
    global $wpdb;
    if( $plugin == plugin_basename( __FILE__ ) ) {
        
        $var = $wpdb->get_var("SELECT term_id FROM {$wpdb->term_taxonomy} WHERE taxonomy='joomsport_tournament'");
        if(!$var ){
            exit( wp_redirect( admin_url( 'admin.php?page=joomsport_setup' ) ) );
        }
    }
}
add_action( 'activated_plugin', 'joomsport_activation_redirect' );
