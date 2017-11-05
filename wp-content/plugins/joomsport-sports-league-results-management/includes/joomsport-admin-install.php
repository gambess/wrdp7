<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-stages.php';
require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-extrafields.php';
require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-events.php';
require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-settings.php';
require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-help.php';
require_once JOOMSPORT_PATH_INCLUDES . 'moderator' . DIRECTORY_SEPARATOR . 'joomsport-moder-mday.php';
require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-boxfields.php';

require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-generator.php';

require_once JOOMSPORT_PATH_INCLUDES . 'pages' . DIRECTORY_SEPARATOR . 'joomsport-page-import.php';


class JoomSportAdminInstall {

  public static function init(){
    global $joomsportSettings;
    self::joomsport_languages();
    add_action( 'admin_menu', array('JoomSportAdminInstall', 'create_menu') );

    self::_defineTables();
    $joomsportSettings = new JoomsportSettings();
  }


  public static function create_menu() {
    $jsconfig =  new JoomsportSettings();
    add_menu_page( __('JoomSport', 'joomsport-sports-league-results-management'), __('JoomSport', 'joomsport-sports-league-results-management'),
      'manage_options', 'joomsport', array('JoomSportAdminInstall', 'action'),
      plugins_url( '../assets/images/cup.png', __FILE__ ) );
    add_submenu_page( 'joomsport', __('Settings', 'joomsport-sports-league-results-management'), __('Settings', 'joomsport-sports-league-results-management'),
      'manage_options', 'joomsport_settings', array('JoomsportPageSettings', 'action') );
    add_submenu_page( 'joomsport', __( 'Leagues', 'joomsport-sports-league-results-management' ), __( 'Leagues', 'joomsport-sports-league-results-management' ), 'manage_options', 'edit-tags.php?taxonomy=joomsport_tournament&post_type=joomsport_season');
    add_submenu_page( 'joomsport', __( 'Person categories', 'joomsport-sports-league-results-management' ), __( 'Person categories', 'joomsport-sports-league-results-management' ), 'manage_options', 'edit-tags.php?taxonomy=joomsport_personcategory&post_type=joomsport_person');

    if(current_user_can('manage_options')){
      add_submenu_page( 'joomsport', __( 'Matchday', 'joomsport-sports-league-results-management' ), __( 'Matchdays', 'joomsport-sports-league-results-management' ), 'manage_options', 'edit-tags.php?taxonomy=joomsport_matchday&post_type=joomsport_match');
    }
    if($jsconfig->get('enbl_club')){
      add_submenu_page( 'joomsport', __( 'Club', 'joomsport-sports-league-results-management' ), __( 'Clubs', 'joomsport-sports-league-results-management' ), 'manage_options', 'edit-tags.php?taxonomy=joomsport_club&post_type=joomsport_team',false);
    }

    $obj = JoomSportStages_Plugin::get_instance();
    $hook = add_submenu_page( 'joomsport', __( 'Game stage', 'joomsport-sports-league-results-management' ), __( 'Game stages', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-page-gamestages', function(){ $obj = JoomSportStages_Plugin::get_instance();$obj->plugin_settings_page();});

    add_action( "load-$hook", function(){ $obj = JoomSportStages_Plugin::get_instance();$obj->screen_option();}  );

    add_submenu_page( 'options.php', __( 'Game stage New', 'joomsport-sports-league-results-management' ), __( 'Game stages New', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-gamestages-form', array('JoomSportStagesNew_Plugin', 'view'));

    $obj = JoomSportExtraField_Plugin::get_instance();
    $hook = add_submenu_page( 'joomsport', __( 'Extra field', 'joomsport-sports-league-results-management' ), __( 'Extra fields', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-page-extrafields', function(){ $obj = JoomSportExtraField_Plugin::get_instance();$obj->plugin_settings_page();});

    add_action( "load-$hook", function(){ $obj = JoomSportExtraField_Plugin::get_instance();$obj->screen_option();}  );

    add_submenu_page( 'options.php', __( 'Extra field New', 'joomsport-sports-league-results-management' ), __( 'Extra field New', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-extrafields-form', array('JoomSportExtraFieldsNew_Plugin', 'view'));

    $obj = JoomSportBoxField_Plugin::get_instance();
    $hook = add_submenu_page( 'joomsport', __( 'Box score stats', 'joomsport-sports-league-results-management' ), __( 'Box score stats', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-page-boxfields', function(){ $obj = JoomSportBoxField_Plugin::get_instance();$obj->plugin_settings_page();});

    add_action( "load-$hook", function(){ $obj = JoomSportBoxField_Plugin::get_instance();$obj->screen_option();}  );

    add_submenu_page( 'options.php', __( 'Box score record', 'joomsport-sports-league-results-management' ), __( 'Box score record', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-boxfields-form', array('JoomSportBoxFieldsNew_Plugin', 'view'));


    $obj = JoomSportEvents_Plugin::get_instance();
    $hook = add_submenu_page( 'joomsport', __( 'Events stats', 'joomsport-sports-league-results-management' ), __( 'Events stats', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-page-events', function(){ $obj = JoomSportEvents_Plugin::get_instance();$obj->plugin_settings_page();});

    add_action( "load-$hook", function(){ $obj = JoomSportEvents_Plugin::get_instance();$obj->screen_option();} );

    add_submenu_page( 'options.php', __( 'Event New', 'joomsport-sports-league-results-management' ), __( 'Event New', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-events-form', array('JoomSportEventsNew_Plugin', 'view'));

    add_submenu_page( 'joomsport', __('Help', 'joomsport-sports-league-results-management'), __('Help', 'joomsport-sports-league-results-management'),
      'manage_options', 'joomsport_help', array('JoomsportPageHelp', 'action') );

        /*
         * Add CSV upload
         */
        add_submenu_page( 'joomsport', __('Import', 'joomsport-sports-league-results-management'), __('Import', 'joomsport-sports-league-results-management'),
          'manage_options', 'joomsport_import', array('JoomsportPageImport', 'action') );
        
        
        
        
        add_submenu_page( 'options.php', __( 'Match generator', 'joomsport-sports-league-results-management' ), __( 'Match generator', 'joomsport-sports-league-results-management' ), 'manage_options', 'joomsport-match-generator', array('JoomsportPageGenerator', 'action'));
        
        // javascript
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-uidp-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_script( 'joomsport-admin-nav-js', plugins_url('../assets/js/admin_nav.js', __FILE__) );
        wp_enqueue_style( 'joomsport-admin-nav-css', plugins_url('../assets/css/admin_nav.css', __FILE__) );
        add_action('admin_enqueue_scripts', array('JoomSportAdminInstall', 'joomsport_admin_js'));
        add_action('admin_enqueue_scripts', array('JoomSportAdminInstall', 'joomsport_admin_css'));
        
        wp_enqueue_style('jscssfont','//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');
        
      }

      public static function joomsport_fe_wp_head(){
        global $post,$post_type;
        $jsArray = array("joomsport_season","joomsport_match","joomsport_team","joomsport_match","joomsport_player","joomsport_venue","joomsport_person");
        if(in_array($post_type, $jsArray) || isset($_REQUEST['wpjoomsport']) || get_query_var('joomsport_tournament') || get_query_var('joomsport_matchday') || get_query_var('joomsport_club')){
         wp_enqueue_script('jsbootstrap-js','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',array ( 'jquery' ));

         wp_enqueue_script('jsnailthumb',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/jquery.nailthumb.1.1.js');
         wp_enqueue_script('jstablesorter',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/jquery.tablesorter.min.js');
         wp_enqueue_script('jsselect2',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/select2.min.js');
         wp_enqueue_script('jsjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/joomsport.js');

         wp_enqueue_style('jscssbtstrp',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/btstrp.css');
         wp_enqueue_style('jscssjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport.css');
         if (is_rtl()) {
           wp_enqueue_style( 'jscssjoomsport-rtl',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport-rtl.css' );
         }
         wp_enqueue_style('jscssbracket',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/drawBracket.css');
         wp_enqueue_style('jscssnailthumb',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/jquery.nailthumb.1.1.css');
         wp_enqueue_style('jscsslightbox',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/lightbox.css');
         wp_enqueue_style('jscssselect2',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/select2.min.css');
         wp_enqueue_style('jscssfont','//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');
         wp_enqueue_script('jquery-ui-datepicker');
         wp_enqueue_style('jquery-uidp-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
       }
     }

     public static function action(){

     }


     public static function joomsport_admin_js(){
      global $post_type;
      wp_enqueue_script( 'joomsport-common-js', plugins_url('../assets/js/common.js', __FILE__) );
      wp_enqueue_script( 'joomsport-jchosen-js', plugins_url('../assets/js/chosen.jquery.min.js', __FILE__),array('jquery') );
      wp_enqueue_media();
      if($post_type == 'joomsport_season'){
        wp_enqueue_script( 'joomsport-colorgrid-js', plugins_url('../includes/3d/color_piker/201a.js', __FILE__) );
      }
        //echo '<script type="text/javascript" src="'.plugins_url('js/buttons.js', __FILE__).'"></script>';
    }
    
    public static function joomsport_admin_css(){
      global $post_type;
      $post_type_array = array('joomsport_team','joomsport_season','joomsport_player','joomsport_match','joomsport_venue');
      if (in_array($post_type,$post_type_array)) :
        wp_enqueue_style( 'joomsport-customdash-css', plugins_url('../assets/css/customdash.css', __FILE__) );
      endif;
      if($post_type == 'joomsport_season'){
        wp_enqueue_style( 'joomsport-colorgrid-css', plugins_url('../includes/3d/color_piker/style.css', __FILE__) );
      }
      wp_enqueue_style( 'joomsport-common-css', plugins_url('../assets/css/common.css', __FILE__) );
      wp_enqueue_style( 'joomsport-jchosen-css', plugins_url('../assets/css/chosen.min.css', __FILE__) );
        //echo '<link rel="stylesheet" id="mgladmincss" type="text/css" href="'.plugins_url('../css/common.css', __FILE__).'" />';
    }
    
    public static function _defineTables()
    {
      global $wpdb;
      $wpdb->joomsport_config = $wpdb->prefix . 'joomsport_config';
      $wpdb->joomsport_maps = $wpdb->prefix . 'joomsport_maps';
      $wpdb->joomsport_ef = $wpdb->prefix . 'joomsport_extra_fields';
      $wpdb->joomsport_ef_select = $wpdb->prefix . 'joomsport_extra_select';
      $wpdb->joomsport_events = $wpdb->prefix . 'joomsport_events';
      $wpdb->joomsport_seasons = $wpdb->prefix . 'joomsport_seasons';
      $wpdb->joomsport_match_statuses = $wpdb->prefix . 'joomsport_match_statuses';
      $wpdb->joomsport_groups = $wpdb->prefix . 'joomsport_groups';
      $wpdb->joomsport_season_table = $wpdb->prefix . 'joomsport_season_table';
      $wpdb->joomsport_playerlist = $wpdb->prefix . 'joomsport_playerlist';
      $wpdb->joomsport_match_events = $wpdb->prefix . 'joomsport_match_events';
      $wpdb->joomsport_squad = $wpdb->prefix . 'joomsport_squad';
      $wpdb->joomsport_box = $wpdb->prefix . 'joomsport_box_fields';
      $wpdb->joomsport_box_match = $wpdb->prefix . 'joomsport_box_match';
    }

    public static function _installdb(){
      global $wpdb;
      flush_rewrite_rules();
      self::_defineTables();

      include_once( ABSPATH.'/wp-admin/includes/upgrade.php' );

      $charset_collate = '';
      if ( $wpdb->has_cap( 'collation' ) ) {
        if ( ! empty($wpdb->charset) )
          $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if ( ! empty($wpdb->collate) )
          $charset_collate .= " COLLATE $wpdb->collate";
      }


      $create_config_sql = "CREATE TABLE {$wpdb->joomsport_config} (
      `id` smallint NOT NULL AUTO_INCREMENT ,
      `cName` varchar( 100 ) NOT NULL default '',
      `cValue` longtext NOT NULL,
      PRIMARY KEY ( `id` )) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_config, $create_config_sql );

      if(!$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->joomsport_config}")){
        $wpdb->insert($wpdb->joomsport_config,array('cName' => 'general'),array("%s"));
        $wpdb->insert($wpdb->joomsport_config,array('cName' => 'player_reg'),array("%s"));
        $wpdb->insert($wpdb->joomsport_config,array('cName' => 'team_moder'),array("%s"));
        $wpdb->insert($wpdb->joomsport_config,array('cName' => 'season_admin'),array("%s"));
        $wpdb->insert($wpdb->joomsport_config,array('cName' => 'layouts'),array("%s"));
        $wpdb->insert($wpdb->joomsport_config,array('cName' => 'other'),array("%s"));
      }

      $create_config_sql = "CREATE TABLE {$wpdb->joomsport_maps} (
      `id` smallint NOT NULL AUTO_INCREMENT ,
      `m_name` varchar( 100 ) NOT NULL default '',
      `map_descr` longtext NOT NULL,
      PRIMARY KEY ( `id` )) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_maps, $create_config_sql );

      $create_ef_sql = "CREATE TABLE {$wpdb->joomsport_ef} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL DEFAULT '',
      `published` char(1) NOT NULL DEFAULT '1',
      `type` char(1) NOT NULL DEFAULT '0',
      `ordering` int(11) NOT NULL DEFAULT '0',
      `e_table_view` char(1) NOT NULL DEFAULT '0',
      `field_type` char(1) NOT NULL DEFAULT '0',
      `reg_exist` char(1) NOT NULL DEFAULT '0',
      `reg_require` char(1) NOT NULL DEFAULT '0',
      `fdisplay` char(1) NOT NULL DEFAULT '1',
      `season_related` varchar(1) NOT NULL DEFAULT '0',
      `faccess` varchar(1) NOT NULL DEFAULT '0',
      `display_playerlist` varchar(1) NOT NULL DEFAULT '0',
      PRIMARY KEY ( `id` )) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_ef, $create_ef_sql );
      $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_ef} LIKE 'options'");

      if (empty($is_col)) {
        $wpdb->query('ALTER TABLE '.$wpdb->joomsport_ef.' ADD `options` TEXT NULL DEFAULT NULL');
      }

      $create_ef_select_sql = "CREATE TABLE {$wpdb->joomsport_ef_select} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `fid` int(11) NOT NULL default '0',
      `sel_value` varchar(255) NOT NULL default '',
      `eordering` int(11) NOT NULL default '0',
      PRIMARY KEY  (`id`),
      KEY `fid` (`fid`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_ef_select, $create_ef_select_sql );

      $create_events_sql = "CREATE TABLE {$wpdb->joomsport_events} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `e_name` varchar(255) NOT NULL default '',
      `e_img` varchar(255) NOT NULL default '',
      `player_event` char(1) NOT NULL default '0',
      `result_type` VARCHAR( 1 ) NOT NULL DEFAULT  '0',
      `sumev1` INT NOT NULL,
      `sumev2` INT NOT NULL,
      `ordering` INT NOT NULL,
      PRIMARY KEY  (`id`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_events, $create_events_sql );

      $create_season_sql = "CREATE TABLE {$wpdb->joomsport_seasons} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `post_id` int(11) NOT NULL,
      `season_options` longtext,
      `s_descr` text NOT NULL,
      `s_rules` text NOT NULL,
      `season_columns` text NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY (`post_id`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_seasons, $create_season_sql );

      $create_match_statuses_sql = "CREATE TABLE {$wpdb->joomsport_match_statuses} (
      `id` int(11) NOT NULL auto_increment,
      `stName` varchar(100) NOT NULL,
      `stShort` varchar(20) NOT NULL,
      `ordering` tinyint(4) NOT NULL,
      PRIMARY KEY  (`id`)) $charset_collate AUTO_INCREMENT=2;";
      maybe_create_table( $wpdb->joomsport_match_statuses, $create_match_statuses_sql );

      $create_groups_sql = "CREATE TABLE {$wpdb->joomsport_groups} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `s_id` int(11) NOT NULL,
      `group_name` varchar(255) NOT NULL DEFAULT '',
      `group_partic` text NOT NULL,
      `ordering` int(11) NOT NULL,
      PRIMARY KEY  (`id`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_groups, $create_groups_sql );
      $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_groups} LIKE 'options'");

      if (empty($is_col)) {
        $wpdb->query('ALTER TABLE '.$wpdb->joomsport_groups.' ADD `options` TEXT NOT NULL');
      }

      $create_season_table_sql = "CREATE TABLE {$wpdb->joomsport_season_table} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `season_id` int NOT NULL,
      `group_id` int NOT NULL,
      `participant_id` int NOT NULL,
      `options` text NOT NULL,
      `ordering` tinyint NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `season` (`season_id`,`group_id`,`ordering`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_season_table, $create_season_table_sql );

      $create_playerlist_sql = "CREATE TABLE {$wpdb->joomsport_playerlist} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `player_id` int(11) NOT NULL,
      `season_id` int(11) NOT NULL,
      `team_id` int(11) NOT NULL,
      `played` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY  (`id`),
      UNIQUE KEY `player_id` (`player_id`,`season_id`,`team_id`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_playerlist, $create_playerlist_sql );

      $create_matchevents_sql = "CREATE TABLE {$wpdb->joomsport_match_events} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `e_id` int(11) NOT NULL default '0',
      `player_id` int(11) NOT NULL default '0',
      `match_id` int(11) NOT NULL default '0',
      `season_id` int(11) NOT NULL default '0',
      `ecount`  TINYINT NOT NULL default '0',
      `minutes` varchar(20) NOT NULL default '',
      `t_id` int(11) NOT NULL default '0',
      `eordering`  TINYINT NOT NULL,
      PRIMARY KEY  (`id`),
      KEY `player_id` (`player_id`,`match_id`,`t_id`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_match_events, $create_matchevents_sql );

      $create_squad_sql = "CREATE TABLE {$wpdb->joomsport_squad} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `player_id` int(11) NOT NULL default '0',
      `team_id` int(11) NOT NULL default '0',
      `match_id` int(11) NOT NULL default '0',
      `season_id` int(11) NOT NULL default '0',
      `is_subs`  varchar(2) NOT NULL default '0',
      `squad_type`  varchar(1) NOT NULL default '0',
      `minutes` varchar(20) NOT NULL default '',
      `player_subs` int(11) NOT NULL default '0',
      `ordering`  TINYINT NOT NULL,
      PRIMARY KEY  (`id`)) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_squad, $create_squad_sql );

      $create_box_sql = "CREATE TABLE {$wpdb->joomsport_box} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `complex` varchar(1) NOT NULL,
      `parent_id` int(11) NOT NULL,
      `ftype` varchar(1) NOT NULL,
      `published` varchar(1) NOT NULL DEFAULT '1',
      `options` text NOT NULL,
      `ordering` smallint(6) NOT NULL,
      `displayonfe` varchar(1) NOT NULL DEFAULT '1',
      PRIMARY KEY ( `id` )) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_box, $create_box_sql );

      $create_boxmatch_sql = "CREATE TABLE {$wpdb->joomsport_box_match} (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `player_id` int(11) NOT NULL default '0',
      `team_id` int(11) NOT NULL default '0',
      `match_id` int(11) NOT NULL default '0',
      `season_id` int(11) NOT NULL default '0',
      PRIMARY KEY ( `id` )) $charset_collate;";
      maybe_create_table( $wpdb->joomsport_box_match, $create_boxmatch_sql );


        //add columns to playerlist
      $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_playerlist} LIKE 'career_lineup'");

      if (empty($is_col)) {
        $wpdb->query("ALTER TABLE ".$wpdb->joomsport_playerlist." ADD `career_lineup` SMALLINT NOT NULL DEFAULT '0' , ADD `career_minutes` SMALLINT NOT NULL DEFAULT '0' , ADD `career_subsin` SMALLINT NOT NULL DEFAULT '0' , ADD `career_subsout` SMALLINT NOT NULL DEFAULT '0'");
        
        $wpdb->query("UPDATE {$wpdb->joomsport_squad} SET is_subs = '-1' WHERE is_subs='1' AND squad_type='2'");
        $wpdb->query("UPDATE {$wpdb->joomsport_squad} SET is_subs = '1' WHERE is_subs='-1' AND squad_type='1'");

      }

        //add columns to events
      $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_events} LIKE 'events_sum'");

      if (empty($is_col)) {
        $wpdb->query("ALTER TABLE ".$wpdb->joomsport_events." ADD `events_sum` VARCHAR(1) NOT NULL DEFAULT '0' , ADD `subevents` TEXT NOT NULL DEFAULT '' ");

        $sumev = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_events} WHERE player_event = '2'");
        for($intA=0;$intA<count($sumev);$intA++){
          $evs = array($sumev[$intA]->sumev1,$sumev[$intA]->sumev2);
          $wpdb->query("UPDATE {$wpdb->joomsport_events} SET events_sum = '1', player_event = '1',subevents='".json_encode($evs)."' WHERE id = {$sumev[$intA]->id}");

        }


      }
        //add minutes string field
      $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_match_events} LIKE 'minutes_input'");

      if (empty($is_col)) {
        $wpdb->query("ALTER TABLE ".$wpdb->joomsport_match_events." ADD `minutes_input` VARCHAR(20) NULL DEFAULT NULL");
      }

    }
    
    public static function joomsport_languages() {
      $locale = apply_filters( 'plugin_locale', get_locale(), 'joomsport-sports-league-results-management' );

      load_textdomain( 'joomsport-sports-league-results-management', plugin_basename( dirname( __FILE__ ) . "/../languages/joomsport-sports-league-results-management-$locale.mo" ));
      load_plugin_textdomain( 'joomsport-sports-league-results-management', false, plugin_basename( dirname( __FILE__ ) . "/../languages" ) );
    }
  }

  add_action( 'init', array( 'JoomSportAdminInstall', 'init' ), 4);
  add_action( 'wp_enqueue_scripts', array('JoomSportAdminInstall','joomsport_fe_wp_head') );
  add_filter( 'custom_menu_order', 'wpsejs_joomsport_submenu_order' );

  function wpsejs_joomsport_submenu_order( $menu_ord ) 
  {
    global $submenu;

    $sort_array = array(
      __('Leagues','joomsport-sports-league-results-management'),
      _x( 'Seasons', 'Admin menu name Seasons', 'joomsport-sports-league-results-management' ),
      __('Matchdays','joomsport-sports-league-results-management'),

      __('Clubs','joomsport-sports-league-results-management'),
      _x('Teams','Admin menu name Teams','joomsport-sports-league-results-management'),
      _x('Players','Admin menu name Players','joomsport-sports-league-results-management'),
      _x('Venues','Admin menu name Venues','joomsport-sports-league-results-management'),
      __('Persons','joomsport-sports-league-results-management'),
      __('Import','joomsport-sports-league-results-management'),
      __('Events stats','joomsport-sports-league-results-management'),
      __('Box score stats','joomsport-sports-league-results-management'),
      __('Person categories','joomsport-sports-league-results-management'),
      __('Game stages','joomsport-sports-league-results-management'),
      __('Extra fields','joomsport-sports-league-results-management'),
      __('Settings','joomsport-sports-league-results-management'),
      __('Help','joomsport-sports-league-results-management')
      );

    $arr = array();
    if(count($sort_array)){
      foreach ($sort_array as $sarr) {
        if(isset($submenu['joomsport']) && count($submenu['joomsport'])){
          foreach ($submenu['joomsport'] as $sub) {
            if($sub[0] == $sarr){
              $arr[] = $sub;
            }
          }
        }
      }
    }
    
    $submenu['joomsport'] = $arr;

    return $menu_ord;
  }

  function jsmatch_hide_that_stuff() {
    if('joomsport_match' == get_post_type()){
      echo '<style type="text/css">
          #favorite-actions {display:none;}
      .add-new-h2{display:none;}
      .tablenav{display:none;}
      .page-title-action{display:none;}
    </style>';
  }elseif('joomsport_season' == get_post_type()){
    if(!wp_count_terms('joomsport_tournament')){
      $txt = addslashes(sprintf(__('League required to create Season. Let\'s %s add league %s first.','joomsport-sports-league-results-management'),'<a href="'.(get_admin_url(get_current_blog_id(), 'edit-tags.php?taxonomy=joomsport_tournament')).'">','</a>'));
      echo '<script>jQuery( document ).ready(function() {jQuery(".wrap").html("<div class=\'jswarningbox\'><p>'.$txt.'</p></div>");});</script>';
    }

  }
}
function joomsport_setup_theme() {
  if ( ! current_theme_supports( 'post-thumbnails' ) ) {
    add_theme_support( 'post-thumbnails' );
  }

        // Add image sizes
  add_image_size( 'joomsport-thmb-medium',  310, 'auto', false );
  add_image_size( 'joomsport-thmb-mini',  60, 'auto', false );
}
add_action('admin_head', 'jsmatch_hide_that_stuff');
add_action( 'after_setup_theme', 'joomsport_setup_theme' );

if(!function_exists('joomsport_set_current_menu')){

  function joomsport_set_current_menu($parent_file){
    global $submenu_file, $current_screen, $pagenow, $plugin_page;

    $ptypes = array("joomsport_team","joomsport_season","joomsport_match");
        // Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
    if(in_array($current_screen->post_type,$ptypes)) {

      if($pagenow == 'post.php'){
        if($current_screen->post_type == 'joomsport_match'){

          $submenu_file = 'edit-tags.php?taxonomy=joomsport_matchday&post_type='.$current_screen->post_type;

        }else{
          $submenu_file = 'edit.php?post_type='.$current_screen->post_type;
        }
      }

      if($pagenow == 'edit-tags.php' || $pagenow == 'term.php'){
        switch ($current_screen->post_type) {
          case 'joomsport_season':
          $submenu_file = 'edit-tags.php?taxonomy=joomsport_tournament&post_type='.$current_screen->post_type;


          break;
          case 'joomsport_team':
          $submenu_file = 'edit-tags.php?taxonomy=joomsport_club&post_type='.$current_screen->post_type;


          break;
          case 'joomsport_match':
          $submenu_file = 'edit-tags.php?taxonomy=joomsport_matchday&post_type='.$current_screen->post_type;


          break;

          default:
          break;
        }
      }

      $parent_file = 'joomsport';

    }
    if($current_screen->id == 'admin_page_joomsport-events-form'){
      $parent_file = 'joomsport';
      $submenu_file = 'joomsport-page-events';
      $plugin_page = 'joomsport-page-events';
    }
    if($current_screen->id == 'admin_page_joomsport-boxfields-form'){
      $parent_file = 'joomsport';
      $submenu_file = 'joomsport-page-boxfields';
      $plugin_page = 'joomsport-page-boxfields';
    }
    if($current_screen->id == 'admin_page_joomsport-gamestages-form'){
      $parent_file = 'joomsport';
      $submenu_file = 'joomsport-page-gamestages';
      $plugin_page = 'joomsport-page-gamestages';
    }
    if($current_screen->id == 'admin_page_joomsport-extrafields-form'){
      $parent_file = 'joomsport';
      $submenu_file = 'joomsport-page-extrafields';
      $plugin_page = 'joomsport-page-extrafields';
    }

    if($current_screen->id == 'edit-joomsport_personcategory'){
      $parent_file = 'joomsport';
      $submenu_file = 'edit-tags.php?taxonomy=joomsport_personcategory&post_type=joomsport_person';
      $plugin_page = 'edit-tags.php?taxonomy=joomsport_personcategory&post_type=joomsport_person';
    }


    return $parent_file;

  }

  add_filter('parent_file', 'joomsport_set_current_menu',10,1);

}
add_action('init', 'joomsport_myStartSessionJS', 1);
function joomsport_myStartSessionJS() {
  if(!session_id()) {
    session_start();
  }
}
function joomsport_deactivation_popup() {
  $ignorePop = get_option('joomsport_deactivation_popup',0);
  if(!$ignorePop){
    wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
        wp_enqueue_script( 'utils' ); // for user settings
        ?>
        <script type="text/javascript">
          jQuery('tr[data-slug="joomsport-sports-league-results-management"] .deactivate a').click(function(){
            var content_html = '<h3><?php echo __('Please share the reason of deactivation.','joomsport-sports-league-results-management')?></h3>';
            content_html += '<div class="jsportPopUl"><ul style="overflow:hidden;">';
            content_html += '<li><input id="jsDeactivateReason1" type="radio" name="jsDeactivateReason" value="1" /><label for="jsDeactivateReason1"><?php echo __('Plugin is too complicated','joomsport-sports-league-results-management')?></label><textarea name="jsDeactivateReason1_text" id="jsDeactivateReason1_text" placeholder="<?php echo __('What step did actually stop you?','joomsport-sports-league-results-management')?>"></textarea></li>';
            content_html += '<li><input id="jsDeactivateReason2" type="radio" name="jsDeactivateReason" value="2" /><label for="jsDeactivateReason2"><?php echo __('I miss some features','joomsport-sports-league-results-management')?></label><textarea name="jsDeactivateReason2_text" id="jsDeactivateReason2_text" placeholder="<?php echo __('What features did you miss?','joomsport-sports-league-results-management')?>"></textarea></li>';
            content_html += '<li><input id="jsDeactivateReason3" type="radio" name="jsDeactivateReason" value="3" /><label for="jsDeactivateReason3"><?php echo __('I found the other plugin','joomsport-sports-league-results-management')?></label><textarea name="jsDeactivateReason3_text" id="jsDeactivateReason3_text" placeholder="<?php echo __('What plugin did you prefer?','joomsport-sports-league-results-management')?>"></textarea></li>';
            content_html += '<li><input id="jsDeactivateReason4" type="radio" name="jsDeactivateReason" value="4" /><label for="jsDeactivateReason4"><?php echo __('It is broken','joomsport-sports-league-results-management')?></label><textarea name="jsDeactivateReason4_text" id="jsDeactivateReason4_text" placeholder="<?php echo __('What was wrong?','joomsport-sports-league-results-management')?>"></textarea></li>';
            content_html += '<li><input id="jsDeactivateReason5" type="radio" name="jsDeactivateReason" value="5" /><label for="jsDeactivateReason5"><?php echo __('Other','joomsport-sports-league-results-management')?></label><textarea name="jsDeactivateReason5_text" id="jsDeactivateReason5_text" placeholder="<?php echo __('What is the reason?','joomsport-sports-league-results-management')?>"></textarea></li>';
            content_html += '</ul></div>';
            content_html += '<div style="text-align:center;"><?php echo __('THANK YOU IN ADVANCE!','joomsport-sports-league-results-management')?></div>';
            content_html += '<p><input id="jsDeactivateOpt1" type="checkbox" name="jsDeactivateOpt1" value="1" /><label for="jsDeactivateOpt1"><?php echo __('Do not show again','joomsport-sports-league-results-management')?></label></p>';
            content_html += '<p><a id="jsportPopSkip" class="button" href="'+jQuery('tr[data-slug="joomsport-sports-league-results-management"] .deactivate a').attr('href')+'"><?php echo __('Skip','joomsport-sports-league-results-management')?></a>';
            content_html += '<a id="jsportPopSend" class="button-primary button" href="'+jQuery('tr[data-slug="joomsport-sports-league-results-management"] .deactivate a').attr('href')+'"><?php echo __('Send','joomsport-sports-league-results-management')?></a></p>';    
            content_html += '<p class="joomsportPopupPolicy"><a href="http://joomsport.com/send-form-privacy.html" target="_blank"><?php echo __('Send Form Privacy Policy','joomsport-sports-league-results-management')?></a></p>';
            jQuery('tr[data-slug="joomsport-sports-league-results-management"] .deactivate a').pointer({
              content: content_html,
              position: {
                my: 'left top',
                at: 'center bottom',
                offset: '-1 0'
              },
              close: function() {
                        //
                      }
                    }).pointer('open');
            return false;
          });
</script><?php
}
}
add_action( 'admin_footer', 'joomsport_deactivation_popup' );

add_action( 'wp_ajax_joomsport-updoption', 'joomsport_update_option' );
function joomsport_update_option() {
  $option_name = 'joomsport_deactivation_popup';
  $option = intval($_POST['option']);


  update_option( $option_name, $option );
  die();
}

add_action( 'wp_ajax_joomsport-senddeactivation', 'joomsport_senddeactivation' );
function joomsport_senddeactivation() {
  global $current_user;
  get_currentuserinfo();
  if($current_user->user_email){
    $ch_type = intval($_POST['ch_type']);
    $reason = '';
    switch($ch_type){
      case '1':
      $reason = __('Plugin is too complicated','joomsport-sports-league-results-management');
      break;
      case '2':
      $reason = __('I miss some features','joomsport-sports-league-results-management');
      break;
      case '3':
      $reason = __('I found the other plugin','joomsport-sports-league-results-management');
      break;
      case '4':
      $reason = __('It is broken','joomsport-sports-league-results-management');
      break;
      case '5':
      $reason = __('Other','joomsport-sports-league-results-management');
      break;
    }
    $ch_text = ($_POST['ch_text']);
    $to = 'deactivate-js@beardev.com';
    $subject = 'JoomSport Deactivation';
    $body = $reason . ":<br /><br />" . $ch_text;
    $headers = array('Content-Type: text/html; charset=UTF-8','FROM:'.$current_user->user_email);

    wp_mail( $to, $subject, $body, $headers );
  }
  die();
}