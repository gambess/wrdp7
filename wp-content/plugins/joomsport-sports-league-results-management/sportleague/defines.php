<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php
global $wpdb;
//environment
define('JOOMSPORT_ENV', 'wordpress');
//define root
define('JSPLW_PATH_MAINCOMP', ABSPATH);
//environment
define('JOOMSPORT_TEMPLATE', 'default');
// main directory
define('JOOMSPORT_SL_PATH', __DIR__.DIRECTORY_SEPARATOR);
// css directory
define('JOOMSPORT_PATH_CSS', JOOMSPORT_SL_PATH.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR);
// js directory
define('JOOMSPORT_PATH_JS', JOOMSPORT_SL_PATH.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR);
// images directory
define('JOOMSPORT_PATH_IMAGES', ABSPATH.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'bearleague'.DIRECTORY_SEPARATOR);

// event images directory
define('JOOMSPORT_PATH_IMAGES_EVENTS', JOOMSPORT_PATH_IMAGES.'events'.DIRECTORY_SEPARATOR);
//thumb
define('JOOMSPORT_PATH_IMAGES_THUMB', JOOMSPORT_PATH_IMAGES.'thumb'.DIRECTORY_SEPARATOR);

// classes directory
define('JOOMSPORT_PATH_CLASSES', JOOMSPORT_SL_PATH.'classes'.DIRECTORY_SEPARATOR);
// helpers directory
define('JOOMSPORT_PATH_SL_HELPERS', JOOMSPORT_SL_PATH.'helpers'.DIRECTORY_SEPARATOR);
// views directory
define('JOOMSPORT_PATH_VIEWS', JOOMSPORT_SL_PATH.'views'.DIRECTORY_SEPARATOR.JOOMSPORT_TEMPLATE.DIRECTORY_SEPARATOR);
// views elements directory
define('JOOMSPORT_PATH_VIEWS_ELEMENTS', JOOMSPORT_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR);
// objects directory
define('JOOMSPORT_PATH_OBJECTS', JOOMSPORT_PATH_CLASSES.'objects'.DIRECTORY_SEPARATOR);

// plugins directory
define('JOOMSPORT_PATH_PLUGINS', JOOMSPORT_SL_PATH.'plugins'.DIRECTORY_SEPARATOR);

// classes directory
define('JOOMSPORT_PATH_ENV', JOOMSPORT_SL_PATH.'base'.DIRECTORY_SEPARATOR.JOOMSPORT_ENV.DIRECTORY_SEPARATOR);
// classes directory
define('JOOMSPORT_PATH_ENV_CLASSES', JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR);
// models directory
define('JOOMSPORT_PATH_MODELS', JOOMSPORT_PATH_ENV.'models'.DIRECTORY_SEPARATOR);

//

define('JOOMSPORT_LIVE_URL', get_site_url());
define('JOOMSPORT_LIVE_URL_IMAGES', JOOMSPORT_LIVE_URL.'media'.DIRECTORY_SEPARATOR.'bearleague'.DIRECTORY_SEPARATOR);
define('JOOMSPORT_LIVE_URL_IMAGES_DEF', plugin_dir_url( __FILE__ ).'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR);

define('JOOMSPORT_LIVE_ASSETS', plugin_dir_url( __FILE__ ).'assets'.DIRECTORY_SEPARATOR);

//defines database table names


define('DB_TBL_EVENTS', $wpdb->joomsport_events);

define('DB_TBL_MATCH_EVENTS', $wpdb->joomsport_match_events);

//nuevo
define('DB_TBL_SEASON_TABLE', $wpdb->joomsport_season_table);
define('DB_TBL_PLAYER_LIST', $wpdb->joomsport_playerlist);
define('DB_TBL_BOX_FIELDS', $wpdb->joomsport_box);
define('DB_TBL_BOX_MATCH', $wpdb->joomsport_box_match);
//some config
define('JSCONF_SCORE_SEPARATOR', ' - ');
define('JSCONF_SCORE_SEPARATOR_VS', ' v ');
define('JSCONF_PLAYER_DEFAULT_IMG', 'player_st.png');
define('JSCONF_TEAM_DEFAULT_IMG', 'teams_st.png');
define('JSCONF_VENUE_DEFAULT_IMG', 'event_st.png');

define('JSCONF_ENBL_MATCH_TOOLTIP', true);
// Google map API KEY
define('JSCONF_GMAP_API_KEY', 'AIzaSyA1NR_RmgpTgzBwKwrvt_yGXw5Cw4Kj_io');
?>