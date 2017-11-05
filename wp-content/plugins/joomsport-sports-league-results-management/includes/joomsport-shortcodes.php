<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

class JoomsportShortcodes {

  public static function init() {

    add_shortcode( 'jsStandings', array('JoomsportShortcodes','joomsport_standings') );
    add_shortcode( 'jsMatches', array('JoomsportShortcodes','joomsport_matches') );
    add_shortcode( 'jsPlayerStat', array('JoomsportShortcodes','joomsport_plstat') );
    add_shortcode( 'jsMatchDayStat', array('JoomsportShortcodes','joomsport_mday') );
    add_shortcode( 'jsMatchPlayerList', array('JoomsportShortcodes','joomsport_playerlist') );

    add_filter("mce_external_plugins", array('JoomsportShortcodes',"enqueue_plugin_scripts"));
    add_filter("mce_buttons", array('JoomsportShortcodes',"register_buttons_editor"));
  }


  public static function joomsport_standings($attr){

    $args = shortcode_atts( array(
      'id' => 0,
      'group_id' => 0,
      'partic_id' => 0,
      'place' => 0,
      'columns' => '',

      ), $attr );
    wp_enqueue_style('jscssbtstrp',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/btstrp.css');
    wp_enqueue_style('jscssjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport.css');
    if (is_rtl()) {
     wp_enqueue_style( 'jscssjoomsport-rtl',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport-rtl.css' );
    }
    wp_enqueue_style( 'joomsport-moduletable-css', plugins_url('../sportleague/assets/css/mod_js_table.css', __FILE__) );
    wp_enqueue_script('jsjoomsport-standings',plugins_url('../sportleague/assets/js/joomsport_standings.js', __FILE__));

    ob_start();

    require_once JOOMSPORT_PATH . DIRECTORY_SEPARATOR. 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';
    require_once JOOMSPORT_PATH_OBJECTS . 'class-jsport-season.php';
    $seasObj = new classJsportSeason($args['id']);

    if($seasObj->isComplex() == '1'){
      $childrens = $seasObj->getSeasonChildrens();

      if(count($childrens)){
        foreach ($childrens as $ch) {
          $classChild = new classJsportSeason($ch->ID);
          $child = $classChild->getChild();
          $child->calculateTable(true, $args['group_id']);
          $classChild->getLists();
          $row = $classChild;
          $place_display 	= $args['place'];
          $columns_list = array();
          if($args['columns']){
           $columns_list = explode(';', $args['columns']); 
         }
         $yteam_id = $args['partic_id'];
         $s_id = $args['id'];
         $gr_id = $args['group_id'];
         $single = $row->getSingle();
         $row = $row->season;

         require JOOMSPORT_PATH_VIEWS . 'widgets' . DIRECTORY_SEPARATOR . 'standings.php';
       }
     }    

   }else{
    $child = $seasObj->getChild();
    $child->calculateTable(true, $args['group_id']);
    $seasObj->getLists();
    $row = $seasObj;
    $place_display 	= $args['place'];
    $columns_list = array();
    if($args['columns']){
     $columns_list = explode(';', $args['columns']); 
   }
   $yteam_id = $args['partic_id'];
   $s_id = $args['id'];
   $gr_id = $args['group_id'];
   $single = $row->getSingle();
   $row = $row->season;

   require JOOMSPORT_PATH_VIEWS . 'widgets' . DIRECTORY_SEPARATOR . 'standings.php';
 }    
 return ob_get_clean();
}


public static function joomsport_matches($attr){

  $args = shortcode_atts( array(
    'id' => 0,
    'group_id' => 0,
    'partic_id' => 0,
    'quantity' => 0,
    'matchtype' => 0,
    'emblems' => 0,
    'venue' => 0,
    'season' => 0,
    'slider' => 0,
    'layout' => 0,
    'groupbymd' => 0,
    'morder' => 0,
    'drange_past' => 0,
    'drange_future' => 0,
    'drange_today' => 0,

    ), $attr );

  wp_enqueue_script('jsjoomsport-carousel',plugins_url('../sportleague/assets/js/jquery.jcarousellite.min.js', __FILE__));
  wp_enqueue_style( 'joomsport-modulescrollmatches-css', plugins_url('../sportleague/assets/css/js_scrollmatches.css', __FILE__) );
  ob_start();
  require_once JOOMSPORT_PATH . DIRECTORY_SEPARATOR. 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';

  require_once JOOMSPORT_PATH_CLASSES . 'class-jsport-matches.php';
  require_once JOOMSPORT_PATH_OBJECTS . 'class-jsport-match.php';
  require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
  require_once JOOMSPORT_PATH_MODELS . 'model-jsport-season.php';

  $options = array();
  $is_single = 0;
  if($args['id']){
    $options["season_id"] = $args['id'];
    $obj = new classJsportSeason($args['id']);

    $is_single = (int)$obj->getSingle();


    $season_array = array();

    if($obj->isComplex() == '1'){
      $childrens = $obj->getSeasonChildrens();
      if(count($childrens)){
        foreach($childrens as $ch){
          array_push($season_array, $ch->ID);
        }
        $options["season_id"] = $season_array;
      }
    } 

  }

  if($args['partic_id']){
    $options["team_id"] = $args['partic_id'];
  }

  if($args['quantity']){
    $options["limit"] = $args['quantity'];
  }
  if($args['matchtype'] == '1'){
    $options["played"] = '0';
  }
  if($args['matchtype'] == '2'){
    $options["played"] = '1';
                //$options["ordering"] = 'm.m_date DESC, m.m_time DESC, m.id DESC';
  }
  if($args['morder'] == '1'){
    $options["ordering_dest"] = 'desc';
  }

  if($args['drange_past']){
    $options['date_from'] = date("Y-m-d", strtotime("-{$args['drange_past']} day"));
  }elseif($args['drange_today']){
    $options['date_from'] = date("Y-m-d");
  }
  if($args['drange_future']){
    $options['date_to'] = date("Y-m-d", strtotime("+{$args['drange_future']} day"));
  }elseif($args['drange_today']){
    $options['date_to'] = date("Y-m-d");
  }

  if(isset($options['date_to']) && !isset($options['date_from'])){
    $options['date_from'] = date("Y-m-d", strtotime("+1 day"));
  }
  if(!isset($options['date_to']) && isset($options['date_from'])){
    $options['date_to'] = date("Y-m-d", strtotime("-1 day"));
  }

  if(isset($options['date_to']) && isset($options['date_from']) && (!$args['drange_today'])){
    $options['date_exclude'] = date("Y-m-d");
  }

  $obj = new classJsportMatches($options);
  $rows = $obj->getMatchList($is_single);


  $matches = array();

  if($rows['list']){
    foreach ($rows['list'] as $row) {
      $match = new classJsportMatch($row->ID, false);
      $matches[] = $match->getRowSimple();
    }
  }
  $list = $matches;
  if(count($list)){
                /*$document		= JFactory::getDocument();
                $document->addStyleSheet(JURI::root() . 'modules/mod_js_scrollmatches/css/js_scrollmatches.css'); 
                $document->addScript(JURI::root() . 'modules/mod_js_scrollmatches/js/jquery.jcarousellite.min.js');
                $baseurl = JUri::base();*/

                $module_id = rand(0, 2000);
                $enbl_slider = $args['slider'];
                $classname = $enbl_slider ? "jsSliderContainer":"jsDefaultContainer";
                if($enbl_slider){
                  $curpos = 0;
                  $date = date("Y-m-d");
                  for($intA = 0;$intA < count($matches); $intA++){
                    $mdate  = get_post_meta($matches[$intA]->id,'_joomsport_match_date',true);
                    if(isset($options["ordering_dest"]) && $options["ordering_dest"] == 'desc'){
                      if($mdate > $date){

                        $curpos =  $intA;
                      }
                    }else
                    if($mdate < $date){

                      $curpos =  $intA+1;
                    }
                  }

                    //$curpos = $curpos > 1 ? $curpos : 0;
                }
                
                require JOOMSPORT_PATH_VIEWS . 'widgets' . DIRECTORY_SEPARATOR . 'matches.php';

              }


              return ob_get_clean();
            }
            public static function joomsport_plstat($attr){

              $args = shortcode_atts( array(
                'id' => null,
                'group_id' => null,
                'partic_id' => null,
                'event' => null,
                'quantity' => 0,
                'photo' => 0,
                'teamname' => 0
                ), $attr );
              ob_start();
              wp_enqueue_style('jscssbtstrp',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/btstrp.css');
              wp_enqueue_style('jscssjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport.css');

              wp_enqueue_style( 'joomsport-moduleevents-css', plugins_url('../sportleague/assets/css/mod_js_player.css', __FILE__) );

              require_once JOOMSPORT_PATH . DIRECTORY_SEPARATOR. 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';
              require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-getplayers.php';
              require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-player.php';
              require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-team.php';
              require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-event.php';
              $options = array();
              $eventid = 'eventid_'.$args['event'];
              $options['season_id'] = $args['seasonid'] = $args['id'];
              $options['team_id'] = $args['partic_id'];
              $options['limit'] = $args['quantity'];
              $options['ordering'] = $eventid.' DESC';
              $eventObj = new classJsportEvent($args['event']);
              $players = classJsportgetplayers::getPlayersFromTeam($options);
              if(count($players['list'])){

                require JOOMSPORT_PATH_VIEWS . 'widgets' . DIRECTORY_SEPARATOR . 'players.php';
              }
              return ob_get_clean();
            }

            public static function joomsport_mday($attr){

              $args = shortcode_atts( array(
                'matchday_id' => null
                ), $attr );
              ob_start();
              wp_enqueue_style('jscssbtstrp',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/btstrp.css');
              wp_enqueue_style('jscssjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport.css');
              $term_meta = get_option( "taxonomy_".$args['matchday_id']."_metas");
              require_once JOOMSPORT_PATH . DIRECTORY_SEPARATOR. 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';

              require_once JOOMSPORT_PATH_CLASSES . 'class-jsport-matches.php';
              require_once JOOMSPORT_PATH_OBJECTS . 'class-jsport-match.php';
              require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
              require_once JOOMSPORT_PATH_MODELS . 'model-jsport-season.php';
              if($term_meta['matchday_type'] == '0'){
                $args = array('id' => $term_meta['season_id'],
                  'group_id' => 0,
                  'partic_id' => 0,
                  'quantity' => 0,
                  'matchtype' => 0,
                  'emblems' => 0,
                  'venue' => 0,
                  'season' => 0,
                  'slider' => 0,
                  'layout' => 0,
                  'groupbymd' => 0,
                  'morder' => 0,
                  'matchday_id' => $args['matchday_id']);
                $options["matchday_id"] = $args['matchday_id'];
                $options["season_id"] = $term_meta['season_id'];
                $obj = new classJsportSeason($term_meta['season_id']);

                $is_single = (int)$obj->getSingle();

                $obj = new classJsportMatches($options);
                $rows = $obj->getMatchList($is_single);


                $matches = array();

                if($rows['list']){
                  foreach ($rows['list'] as $row) {
                    $match = new classJsportMatch($row->ID, false);
                    $matches[] = $match->getRowSimple();
                  }
                }
                $list = $matches;
                if(count($list)){
                /*$document		= JFactory::getDocument();
                $document->addStyleSheet(JURI::root() . 'modules/mod_js_scrollmatches/css/js_scrollmatches.css'); 
                $document->addScript(JURI::root() . 'modules/mod_js_scrollmatches/js/jquery.jcarousellite.min.js');
                $baseurl = JUri::base();*/

                $module_id = rand(0, 2000);
                $enbl_slider = 0;
                $classname = $enbl_slider ? "jsSliderContainer":"jsDefaultContainer";
                
                
                require JOOMSPORT_PATH_VIEWS . 'widgets' . DIRECTORY_SEPARATOR . 'matches.php';

              }
            }else{
              wp_enqueue_script('jsbootstrap-js','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',array ( 'jquery' ));

              wp_enqueue_script('jsselect2',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/select2.min.js');

              wp_enqueue_script('jsjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/joomsport.js');

              require_once JOOMSPORT_SL_PATH. '/../includes/classes/matchday_types/joomsport-class-matchday-knockout.php';
              $knockObj = new JoomSportClassMatchdayKnockout($args['matchday_id']);
              echo '<div id="joomsport-container" class="jsmodtbl_responsive">';
              echo $knockout_view = $knockObj->getView();
              echo '</div>';
            }

            return ob_get_clean();
          }


          public static function joomsport_playerlist($attr){

            $args = shortcode_atts( array(
              'season_id' => null,
              'team_id' => null,
              'pview' => 0,
              'pgroup' => 0
              ), $attr );
            if(!$args['season_id'] || !$args['team_id']){
              return false;
            }
            ob_start();
            wp_enqueue_style('jscssbtstrp',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/btstrp.css');
            wp_enqueue_style('jscssjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport.css');
            wp_enqueue_script('jsjoomsport-tbl-sort',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/jquery.tablesorter.min.js');
            wp_enqueue_style('jscssfont','//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');
            wp_enqueue_script('jsbootstrap-js','https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js',array ( 'jquery' ));

            wp_enqueue_script('jsselect2',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/select2.min.js');

            wp_enqueue_script('jsjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/js/joomsport.js');

            require_once JOOMSPORT_PATH . DIRECTORY_SEPARATOR. 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';

            require_once JOOMSPORT_PATH_OBJECTS . 'class-jsport-team.php';

            $obj = new classJsportTeam($args['team_id'], $args['season_id']);
            $obj->getPlayers(array('groupBySelect'=>$args['pgroup'], 'playerPhotoTab'=>$args['pview']));
            $rows = $obj->getRow();
            echo '<div id="joomsport-container" class="jsmodpll_responsive">';
            if($args['pview']){
              require JOOMSPORT_PATH_VIEWS . 'elements' . DIRECTORY_SEPARATOR . 'player-list-photo.php';
            }else{
              require JOOMSPORT_PATH_VIEWS . 'elements' . DIRECTORY_SEPARATOR . 'player-list.php';
            }
            echo '</div>';


            return ob_get_clean();
          }

          public static function enqueue_plugin_scripts($plugin_array)
          {
        //enqueue TinyMCE plugin script with its ID.
            $plugin_array["joomsport_shortcodes_button"] =  plugin_dir_url(__FILE__) . "../assets/js/shortcodes.js";
            return $plugin_array;
          }
          public static function register_buttons_editor($buttons)
          {
        //register buttons with their id.
            array_push($buttons, "joomsport_shortcodes_button");
            return $buttons;
          }

        }


        JoomsportShortcodes::init();