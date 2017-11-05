<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class wpb_joomsport_standings extends WP_Widget {

  function __construct() {
    parent::__construct('wpb_joomsport_standings', __('JoomSport Standings', 'joomsport-sports-league-results-management'), 
      array( 'description' => __( 'JoomSport Standings', 'joomsport-sports-league-results-management' ), ) 
      );
  }


  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );
        // before and after widget arguments are defined by themes
    echo $args['before_widget'];
    if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output

    if(!$instance['season_id']){
      return '';
    }

    $group_id = isset($instance['group_id'])?intval($instance['group_id']):0;
    $place = isset($instance['place'])?intval($instance['place']):0;
    $partic_id = isset($instance['partic_id'])?intval($instance['partic_id']):0;
    $jsshrtcolumns = isset($instance['jsshrtcolumns'])?$instance['jsshrtcolumns']:array();
    wp_enqueue_style('jscssbtstrp',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/btstrp.css');
    wp_enqueue_style('jscssjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport.css');
    if (is_rtl()) {
     wp_enqueue_style( 'jscssjoomsport-rtl',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport-rtl.css' );
    }
    wp_enqueue_style( 'joomsport-moduletable-css', plugins_url('../sportleague/assets/css/mod_js_table.css', __FILE__) );
    wp_enqueue_script('jsjoomsport-standings',plugins_url('../sportleague/assets/js/joomsport_standings.js', __FILE__));

    require_once JOOMSPORT_PATH . DIRECTORY_SEPARATOR. 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';
    require_once JOOMSPORT_PATH_OBJECTS . 'class-jsport-season.php';
    $seasObj = new classJsportSeason($instance['season_id']);
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
    $child->calculateTable(true, $group_id);
    $seasObj->getLists();
    $row = $seasObj;
    $place_display 	= $place;
    $columns_list = array();
    if($jsshrtcolumns){
     $columns_list = $jsshrtcolumns;
   }
   $yteam_id = $partic_id;
   $s_id = $instance['season_id'];
   $gr_id = $group_id;
   $single = $row->getSingle();
   $row = $row->season;

   require JOOMSPORT_PATH_VIEWS . 'widgets' . DIRECTORY_SEPARATOR . 'standings.php';
 }


 echo $args['after_widget'];
}

    // Widget Backend 
public function form( $instance ) {
  global  $wpdb;
  $jsconfig =  new JoomsportSettings();

  if ( isset( $instance[ 'title' ] ) ) {
    $title = $instance[ 'title' ];
    $season_id = $instance['season_id'];
    $jsshrtcolumns = $instance['jsshrtcolumns'];
    $place = $instance['place'];
    $group_id = $instance['group_id'];
    $partic_id = $instance['partic_id'];
  }
  else {
    $title = __( 'Standing', 'joomsport-sports-league-results-management' );
    $season_id = 0;
    $place = 0;
    $jsshrtcolumns = array();
    $group_id = 0;
    $partic_id = 0;
  }
        // Widget admin form


  ?>
  <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title' ).':'; ?></label> 
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
  </p>
  <div class="JSshrtPop">
    <div>
      <label><?php echo __("Select season", "joomsport-sports-league-results-management");?></label>
      <?php $results =  JoomSportHelperObjects::getSeasons(-1);?>
      <?php echo JoomSportHelperSelectBox::Optgroup($this->get_field_name( 'season_id' ), $results,$season_id, ' class="jsshrtcodesid" id="'.$this->get_field_id( 'season_id' ).'"');?>
    </div>
    <div class="jsstandgroup">
      <?php
      if($season_id){


        $return = array("groups" => '', "partic" => '');        
        $groups = $wpdb->get_results("SELECT id,group_name as name FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} ORDER BY ordering"); 
        if (count($groups)) {
          $i = 0;
          echo '<label>'.__("Select group", "joomsport-sports-league-results-management").'</label>';
          echo JoomSportHelperSelectBox::Simple($this->get_field_name( 'group_id' ), $groups,$group_id,' class="jsshrtgroup_id"');
        }


      }
      ?>
    </div>
    <div>
      <label><?php echo __("Display places", "joomsport-sports-league-results-management");?></label>
      <input type="number" name="<?php echo $this->get_field_name( 'place' );?>" class='jsshrtcplace' maxlength="2" size="3" value="<?php echo $place?>" min="0" />
    </div>
    <div class="jsstandpartic">
      <?php
      if($season_id){

        if($group_id){
          $group = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} AND id={$group_id} ORDER BY ordering"); 
          $metadata = isset($group->group_partic)?  unserialize($group->group_partic):array();
          if($metadata && count($metadata)){
            $particarray = array();
            foreach ($metadata as $particA){

              $tmp = new stdClass();
              $tmp->name = get_the_title($particA);
              $tmp->id = $particA;
              $particarray[] = $tmp;
            }
            echo '<label>'.__("Select participant", "joomsport-sports-league-results-management").'</label>';
            echo JoomSportHelperSelectBox::Simple($this->get_field_name( 'partic_id' ), $particarray,$partic_id,' class="jspartic_id"');
          }
        }else{
          $partic = JoomSportHelperObjects::getParticipiants($season_id);

          if($partic && count($partic)){
            $particarray = array();
            foreach ($partic as $particA){

              $tmp = new stdClass();
              $tmp->name = $particA->post_title;
              $tmp->id = $particA->ID;
              $particarray[] = $tmp;
            }
            echo '<label>'.__("Select participant", "joomsport-sports-league-results-management").'</label>';
            echo JoomSportHelperSelectBox::Simple($this->get_field_name( 'partic_id' ), $particarray,$partic_id,' class="jspartic_id"');
          }
        }
      }?>    
    </div>
    <div>
      <?php
      $randclass = 'randclass_'.rand(0, 10000);
      $available_options = $jsconfig->getStandingColumns();
      ?>
      <label><?php echo __("Choose columns", "joomsport-sports-league-results-management");?></label>
      <?php
      printf (
        '<select multiple="multiple" name="%s[]" id="%s" class="jsshrtcolumns '.$randclass.'">',
        $this->get_field_name('jsshrtcolumns'),
        $this->get_field_id('jsshrtcolumns')
        );

        ?>
        <?php
        foreach ($available_options as $key => $value) {
          $selected = '';
          if(is_array($jsshrtcolumns) && in_array($key, $jsshrtcolumns)){
            $selected = ' selected';
          }
          echo '<option value="'.$key.'" '.$selected.'>'.$value['label'].'</option>';             
        }
        ?>
      </select>
    </div>  

    <script>
     jQuery(document).ready( function(){
                    // widgets-right
                    jQuery("#widgets-right").find(".<?php echo $randclass;?>").chosen({disable_search_threshold: 10,width: "95%",disable_search:false});;
                    //jQuery(".<?php echo $randclass;?>").trigger("chosen:updated");

                  });   
                </script>  


              </div> 
              <?php 
            }

        // Updating widget replacing old instances with new
            public function update( $new_instance, $old_instance ) {
              $instance = array();
              $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
              $instance['season_id'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['season_id'] ) : '';
              $instance['jsshrtcolumns'] = ( ! empty( $new_instance['jsshrtcolumns'] ) ) ? esc_sql( $new_instance['jsshrtcolumns'] ) : '';
              $instance['place'] = ( ! empty( $new_instance['place'] ) ) ? strip_tags( $new_instance['place'] ) : '';
              $instance['group_id'] = ( ! empty( $new_instance['group_id'] ) ) ? strip_tags( $new_instance['group_id'] ) : '';
              $instance['partic_id'] = ( ! empty( $new_instance['partic_id'] ) ) ? strip_tags( $new_instance['partic_id'] ) : '';
              return $instance;
            }
} // Class wpb_widget ends here

class wpb_joomsport_matches extends WP_Widget {

  function __construct() {
    parent::__construct('wpb_joomsport_matches', __('JoomSport Matches', 'joomsport-sports-league-results-management'), 
      array( 'description' => __( 'JoomSport Matches', 'joomsport-sports-league-results-management' ), ) 
      );
  }


  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );
        // before and after widget arguments are defined by themes
    echo $args['before_widget'];
    if ( ! empty( $title ) )
      echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output

    if(!$instance['season_id']){
      return '';
    }
    $season_id = isset($instance['season_id'])?intval($instance['season_id']):0;
    $group_id = isset($instance['group_id'])?intval($instance['group_id']):0;
    $partic_id = isset($instance['partic_id'])?intval($instance['partic_id']):0;

    $quantity = (int) ( ! empty( $instance['quantity'] ) ) ? strip_tags( $instance['quantity'] ) : '';
    $match_type = (int) ( ! empty( $instance['match_type'] ) ) ? strip_tags( $instance['match_type'] ) : '';
    $args['emblems'] = (int) ( ! empty( $instance['display_embl'] ) ) ? strip_tags( $instance['display_embl'] ) : '';
    $args['venue'] = (int) ( ! empty( $instance['display_venue'] ) ) ? strip_tags( $instance['display_venue'] ) : '';
    $args['season'] = (int) ( ! empty( $instance['display_seasname'] ) ) ? strip_tags( $instance['display_seasname'] ) : '';
    $args['slider'] = (int) ( ! empty( $instance['display_slider'] ) ) ? strip_tags( $instance['display_slider'] ) : '';
    $args['layout'] = (int) ( ! empty( $instance['display_layout'] ) ) ? strip_tags( $instance['display_layout'] ) : '';
    $args['groupbymd'] = (int) ( ! empty( $instance['display_grbymd'] ) ) ? strip_tags( $instance['display_grbymd'] ) : '';
    $args['morder'] = (int) ( ! empty( $instance['display_order'] ) ) ? strip_tags( $instance['display_order'] ) : '';
    $args['drange_past'] = (int) ( ! empty( $instance['drange_past'] ) ) ? strip_tags( $instance['drange_past'] ) : '';
    $args['drange_today'] = (int) ( ! empty( $instance['drange_today'] ) ) ? strip_tags( $instance['drange_today'] ) : '';
    $args['drange_future'] = (int) ( ! empty( $instance['drange_future'] ) ) ? strip_tags( $instance['drange_future'] ) : '';



    wp_enqueue_style('jscssbtstrp',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/btstrp.css');
    wp_enqueue_style('jscssjoomsport',plugin_dir_url( __FILE__ ).'../sportleague/assets/css/joomsport.css');
    wp_enqueue_script('jsjoomsport-carousel',plugins_url('../sportleague/assets/js/jquery.jcarousellite.min.js', __FILE__));

    wp_enqueue_style( 'joomsport-modulescrollmatches-css', plugins_url('../sportleague/assets/css/js_scrollmatches.css', __FILE__) );

    require_once JOOMSPORT_PATH . DIRECTORY_SEPARATOR. 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';

    require_once JOOMSPORT_PATH_CLASSES . 'class-jsport-matches.php';
    require_once JOOMSPORT_PATH_OBJECTS . 'class-jsport-match.php';
    require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
    require_once JOOMSPORT_PATH_MODELS . 'model-jsport-season.php';

    $options = array();
    $is_single = 0;
    if($season_id){
      $options["season_id"] = $season_id;
      $obj = new classJsportSeason($season_id);

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

    if($partic_id){
      $options["team_id"] = $partic_id;
    }

    if($quantity){
      $options["limit"] = $quantity;
    }
    if($match_type == '1'){
      $options["played"] = '0';
    }
    if($match_type == '2'){
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
                }
                
                require JOOMSPORT_PATH_VIEWS . 'widgets' . DIRECTORY_SEPARATOR . 'matches.php';

              }


              echo $args['after_widget'];
            }

    // Widget Backend 
            public function form( $instance ) {
              global  $wpdb;
              if ( isset( $instance[ 'title' ] ) ) {
                $title = $instance[ 'title' ];
                $season_id = $instance['season_id'];

                $quantity = $instance['quantity'];
                $match_type = $instance['match_type'];
                $group_id = $instance['group_id'];
                $partic_id = $instance['partic_id'];
                $display_embl = $instance['display_embl'];
                $display_venue = $instance['display_venue'];
                $display_seasname = $instance['display_seasname'];
                $display_slider = $instance['display_slider'];
                $display_layout = $instance['display_layout'];
                $display_order = isset($instance['display_order'])?$instance['display_order']:0;
                $drange_past = isset($instance['drange_past'])?$instance['drange_past']:0;
                $drange_today = isset($instance['drange_today'])?$instance['drange_today']:0;
                $drange_future = isset($instance['drange_future'])?$instance['drange_future']:0;

              }
              else {
                $title = __( 'matches', 'joomsport-sports-league-results-management' );
                $season_id = 0;
                $place = 0;
                $quantity = 0;
                $match_type = 0;
                $group_id = 0;
                $partic_id = 0;
                $display_embl = 0;
                $display_venue = 0;
                $display_seasname = 0;
                $display_slider = 0;
                $display_layout = 0;
                $display_order = 0;
                $drange_past = 0;
                $drange_today = 0;
                $drange_future = 0;
              }
        // Widget admin form
              ?>
              <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title' ).':'; ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
              </p>

              <?php
              $lists_radio = array();
              $lists_radio[] = JoomSportHelperSelectBox::addOption(0, __('No','joomsport-sports-league-results-management'));
              $lists_radio[] = JoomSportHelperSelectBox::addOption(1, __('Yes','joomsport-sports-league-results-management'));
              $lists_layout = array();
              $lists_layout[] = JoomSportHelperSelectBox::addOption(0, __('Horizontal','joomsport-sports-league-results-management'));
              $lists_layout[] = JoomSportHelperSelectBox::addOption(1, __('Vertical','joomsport-sports-league-results-management'));
              $lists_order = array();
              $lists_order[] = JoomSportHelperSelectBox::addOption(0, __('Asc','joomsport-sports-league-results-management'));
              $lists_order[] = JoomSportHelperSelectBox::addOption(1, __('Desc','joomsport-sports-league-results-management'));

              ?>
              <div class="JSshrtPop">
                <div>
                  <label><?php echo __("Select season", "joomsport-sports-league-results-management");?></label>
                  <?php $results =  JoomSportHelperObjects::getSeasons(-1);?>
                  <?php echo JoomSportHelperSelectBox::Optgroup($this->get_field_name( 'season_id' ), $results,$season_id, ' class="jsshrtcodesid" id="'.$this->get_field_id( 'season_id' ).'"');?>
                </div>
                <div class="jsstandgroup">
                  <?php
                  if($season_id){


                    $return = array("groups" => '', "partic" => '');        
                    $groups = $wpdb->get_results("SELECT id,group_name as name FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} ORDER BY ordering"); 
                    if (count($groups)) {
                      $i = 0;
                      echo '<label>'.__("Select group", "joomsport-sports-league-results-management").'</label>';
                      echo JoomSportHelperSelectBox::Simple($this->get_field_name( 'group_id' ), $groups,$group_id,' class="jsshrtgroup_id"');
                    }

                    
                  }
                  ?>
                </div>
                <div class="jsstandpartic">
                  <?php
                  if($season_id){

                    if($group_id){
                      $group = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} AND id={$group_id} ORDER BY ordering"); 
                      $metadata = isset($group->group_partic)?  unserialize($group->group_partic):array();
                      if($metadata && count($metadata)){
                        $particarray = array();
                        foreach ($metadata as $particA){

                          $tmp = new stdClass();
                          $tmp->name = get_the_title($particA);
                          $tmp->id = $particA;
                          $particarray[] = $tmp;
                        }
                        echo '<label>'.__("Select participant", "joomsport-sports-league-results-management").'</label>';
                        echo JoomSportHelperSelectBox::Simple($this->get_field_name( 'partic_id' ), $particarray,$partic_id,' class="jspartic_id"');
                      }
                    }else{
                      $partic = JoomSportHelperObjects::getParticipiants($season_id);

                      if($partic && count($partic)){
                        $particarray = array();
                        foreach ($partic as $particA){

                          $tmp = new stdClass();
                          $tmp->name = $particA->post_title;
                          $tmp->id = $particA->ID;
                          $particarray[] = $tmp;
                        }
                        echo '<label>'.__("Select participant", "joomsport-sports-league-results-management").'</label>';
                        echo JoomSportHelperSelectBox::Simple($this->get_field_name( 'partic_id' ), $particarray,$partic_id,' class="jspartic_id"');
                      }
                    }
                  }?>    
                </div>
                <div>
                  <label><?php echo __("Matches quantity", "joomsport-sports-league-results-management");?></label>
                  <input type="number" name="<?php echo $this->get_field_name( 'quantity' );?>" id='<?php echo $this->get_field_id( 'quantity' );?>' maxlength="2" size="3" value="<?php echo $quantity?>" min="0" />
                </div>
                <div>
                  <label><?php echo __("Display matches", "joomsport-sports-league-results-management");?></label>
                  <?php
                  $lists = array();
                  $lists[] = JoomSportHelperSelectBox::addOption(0, __('All','joomsport-sports-league-results-management'));
                  $lists[] = JoomSportHelperSelectBox::addOption(1, __('Fixtures','joomsport-sports-league-results-management'));
                  $lists[] = JoomSportHelperSelectBox::addOption(2, __('Played','joomsport-sports-league-results-management'));
                  ?>
                  <?php echo JoomSportHelperSelectBox::Simple($this->get_field_name( 'match_type' ), $lists,$match_type, ' ');?>
                </div>

                <div>
                  <label><?php echo __("Display emblems", "joomsport-sports-league-results-management");?></label>
                  <?php echo JoomSportHelperSelectBox::Radio($this->get_field_name( 'display_embl' ), $lists_radio,$display_embl, ' ');?>

                </div>
                <div>
                  <label><?php echo __("Display venue", "joomsport-sports-league-results-management");?></label>
                  <?php echo JoomSportHelperSelectBox::Radio($this->get_field_name( 'display_venue' ), $lists_radio,$display_venue, ' ');?>

                </div>
                <div>
                  <label><?php echo __("Display season name", "joomsport-sports-league-results-management");?></label>
                  <?php echo JoomSportHelperSelectBox::Radio($this->get_field_name( 'display_seasname' ), $lists_radio,$display_seasname, ' ');?>

                </div>
                <div>
                  <label><?php echo __("Enable slider", "joomsport-sports-league-results-management");?></label>
                  <?php echo JoomSportHelperSelectBox::Radio($this->get_field_name( 'display_slider' ), $lists_radio,$display_slider, ' ');?>

                </div>
                <div>
                  <label><?php echo __("Layout", "joomsport-sports-league-results-management");?></label>
                  <?php echo JoomSportHelperSelectBox::Radio($this->get_field_name( 'display_layout' ), $lists_layout,$display_layout, ' ');?>

                </div>
                <div>
                  <label class="jsradiodivlabel"><?php echo __("Ordering", "joomsport-sports-league-results-management");?></label>
                  <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio($this->get_field_name( 'display_order'), $lists_order,$display_order, ' ');?>
                  </div>
                </div>
                <div>
                  <label class="jsradiodivlabel"><?php echo __("Date range", "joomsport-sports-league-results-management");?></label>

                  <div class="jsradiodiv">
                    <table>
                      <thead>
                        <tr>
                          <th><?php echo __("Past", "joomsport-sports-league-results-management");?></th>
                          <th><?php echo __("Today", "joomsport-sports-league-results-management");?></th>
                          <th><?php echo __("Future", "joomsport-sports-league-results-management");?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><input style="width:40px;" type="number" min="0" name="<?php echo $this->get_field_name( 'drange_past' )?>" value="<?php echo $drange_past?>" /></td>
                          <td style="text-align:center;"><input type="checkbox" name="<?php echo $this->get_field_name( 'drange_today' )?>" value="1" <?php echo $drange_today?'checked="true"':'';?> /></td>
                          <td><input style="width:40px;" type="number" min="0" name="<?php echo $this->get_field_name( 'drange_future' )?>" value="<?php echo $drange_future?>" /></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div> 

              <?php 
            }

        // Updating widget replacing old instances with new
            public function update( $new_instance, $old_instance ) {
              $instance = array();
              $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
              $instance['season_id'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['season_id'] ) : '';
              $instance['group_id'] = ( ! empty( $new_instance['group_id'] ) ) ? strip_tags( $new_instance['group_id'] ) : '';
              $instance['partic_id'] = ( ! empty( $new_instance['partic_id'] ) ) ? strip_tags( $new_instance['partic_id'] ) : '';

              $instance['quantity'] = ( ! empty( $new_instance['quantity'] ) ) ? strip_tags( $new_instance['quantity'] ) : '';
              $instance['match_type'] = ( ! empty( $new_instance['match_type'] ) ) ? strip_tags( $new_instance['match_type'] ) : '';
              $instance['display_embl'] = ( ! empty( $new_instance['display_embl'] ) ) ? strip_tags( $new_instance['display_embl'] ) : '';
              $instance['display_venue'] = ( ! empty( $new_instance['display_venue'] ) ) ? strip_tags( $new_instance['display_venue'] ) : '';
              $instance['display_seasname'] = ( ! empty( $new_instance['display_seasname'] ) ) ? strip_tags( $new_instance['display_seasname'] ) : '';
              $instance['display_slider'] = ( ! empty( $new_instance['display_slider'] ) ) ? strip_tags( $new_instance['display_slider'] ) : '';
              $instance['display_layout'] = ( ! empty( $new_instance['display_layout'] ) ) ? strip_tags( $new_instance['display_layout'] ) : '';
              $instance['display_order'] = ( ! empty( $new_instance['display_order'] ) ) ? strip_tags( $new_instance['display_order'] ) : '';
              $instance['drange_past'] = ( ! empty( $new_instance['drange_past'] ) ) ? strip_tags( $new_instance['drange_past'] ) : '';
              $instance['drange_today'] = ( ! empty( $new_instance['drange_today'] ) ) ? strip_tags( $new_instance['drange_today'] ) : '';
              $instance['drange_future'] = ( ! empty( $new_instance['drange_future'] ) ) ? strip_tags( $new_instance['drange_future'] ) : '';

              return $instance;
            }
} // Class wpb_widget ends here



// Register and load the widget
function wpb_load_joomsport_widget() {
	register_widget( 'wpb_joomsport_standings' );
  register_widget( 'wpb_joomsport_matches' );
  
}
add_action( 'widgets_init', 'wpb_load_joomsport_widget' );