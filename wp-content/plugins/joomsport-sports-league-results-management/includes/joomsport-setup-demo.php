<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */


class JoomSportSetupDemo {
    
    public static function init(){
        global $pagenow;
        
        add_action( 'admin_menu', array('JoomSportSetupDemo', 'create_setup_page') );
        
        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'joomsport_setup'){
            if(isset($_POST['js_demotype'])){
                self::installDemoData(intval($_POST['js_demotype']));
            }
        }
    }


    public static function create_setup_page() {
        add_dashboard_page('JoomSport', '', 'manage_options', 'joomsport_setup', array('JoomSportSetupDemo', 'showSetupPage'));
    }
    public static function showSetupPage(){
        
        
        
        $lists_radio = array();
        $lists_radio[] = JoomSportHelperSelectBox::addOption(0, __('Single sport','joomsport-sports-league-results-management'));
        $lists_radio[] = JoomSportHelperSelectBox::addOption(1, __('Team sport','joomsport-sports-league-results-management'));
        $lists_radio[] = JoomSportHelperSelectBox::addOption(2, __('Both','joomsport-sports-league-results-management'));
        
        
       ?>
        <div class="jsportWizzardDiv">
            
            <form method="post" action="">
                <div class="jsportWizzardDivInner">
                    <h1><?php echo __('JoomSport setup wizard', 'joomsport-sports-league-results-management');?></h1>
                    <div class="jsportWizzardDivCenter">
                        <label><?php echo __('Select your sport type', 'joomsport-sports-league-results-management');?></label>
                        <div style="margin-left:90px;">
                            <?php echo JoomSportHelperSelectBox::Radio('js_demotype', $lists_radio,1)?>
                        </div>
                    </div>
                    <div>
                        <br />
                        <div class="jsportWizzardDivCenter">
                            <label><?php echo __('Install basic demo data', 'joomsport-sports-league-results-management');?></label>
                        </div>
                        
                        <fieldset>
                            <?php echo __("The following items are included",'joomsport-sports-league-results-management')?>:<br /><br />
                            <?php 
                            
                            echo " - ".__("Leagues",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".__("Seasons",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".__("Teams and Players (depending on sport)",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".__("Round robin Matchdays with Matches",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".__("Basic statistic like Player and Match Event stats",'joomsport-sports-league-results-management');
                            echo "<br />";
                            echo " - ".__("Box score stats",'joomsport-sports-league-results-management');
                            echo "<br />";
                            ?>
                        </fieldset>
                        
                    </div>
                    <div style="padding-top:25px;">
                        <div style="display: inline-block">
                            <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=joomsport_tournament&post_type=joomsport_season' ) ;?>">
                            <input type="button" class="button button-large" value="<?php echo __('No, start empty database', 'joomsport-sports-league-results-management');?>" />
                            </a>
                        </div>
                        <div style="display: inline-block; float: right;">
                            <input type="submit" class="button button-primary button-large" value="<?php echo __('Yes, install basic demo data', 'joomsport-sports-league-results-management');?>" />
                        </div>
                    </div>
                </div>   
            </form>    
        </div>    
       <?php
    }
    public static function installDemoData($type){
        switch($type){
            case '0':
                self::demoDataSingle();
                break;
            case '1':
                self::demoDataTeam();
                break;
            case '2':
                self::demoDataTeam();
                self::demoDataSingle();
                break;
            default:
                break;
        }
        exit( wp_redirect( admin_url( 'edit-tags.php?taxonomy=joomsport_tournament&post_type=joomsport_season' ) ) );
    }
    
    public static function demoDataSingle(){
        global $wpdb;
        
        $eventsArray = array();
        $events = array(
            array(
                'id' => 0,
                'e_name' => 'Aces',
                'e_img' => '',
                'player_event' => '1',
                'result_type' => '0',
                'sumev1' => 0,
                'sumev2' => 0,
                'ordering' => 0,
                'events_sum' => 0,
                'subevents' => '',
            ),
            array(
                'id' => 0,
                'e_name' => 'Double Faults',
                'e_img' => '',
                'player_event' => '1',
                'result_type' => '0',
                'sumev1' => 0,
                'sumev2' => 0,
                'ordering' => 0,
                'events_sum' => 0,
                'subevents' => '',
            )
            
        );
        foreach($events as $event){
            $wpdb->insert($wpdb->joomsport_events, $event);
            $eventsArray[] = $wpdb->insert_id;
        }
        
        $tourn_id = self::demoCreateTaxonomy('Professional single league', 'joomsport_tournament', array('t_single' => 1));
        $season_id = self::demoCreatePost('2031-2032', 'joomsport_season');
        wp_set_object_terms($season_id,$tourn_id,'joomsport_tournament');
                
        self::demoSeasonData($season_id);
        
        $players = array('Alex Svensen', 'Ron Kross', 'Vin Lee', 'Samir Petee');
        $playersArray = array();
        foreach ($players as $player) {
            $player_id = self::demoCreatePost($player, 'joomsport_player');
            update_post_meta($player_id, '_joomsport_player_personal', '');
            $playersArray[] = $player_id;
        }

        update_post_meta($season_id, '_joomsport_season_participiants', $playersArray);
        
        $week1 = self::demoCreateTaxonomy('Week s1', 'joomsport_matchday', array('season_id' => $season_id, 'matchday_type' => 0));
        $week2 = self::demoCreateTaxonomy('Week s2', 'joomsport_matchday', array('season_id' => $season_id, 'matchday_type' => 0));
        
        $Moptions = array(
            "home_team" => $playersArray[0],
            "away_team" => $playersArray[1],
            "score1" => 1,
            "score2" => 0,
            "m_date" => "2017-06-02",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week1
        );
        $match_id = self::demoMatch($Moptions);
        
        //player events
        self::demoEvents($eventsArray[0],$playersArray[0],$match_id,$season_id,'',0,5);
        self::demoEvents($eventsArray[0],$playersArray[1],$match_id,$season_id,'',0,2);
        self::demoEvents($eventsArray[1],$playersArray[0],$match_id,$season_id,'',0,4);
        self::demoEvents($eventsArray[1],$playersArray[1],$match_id,$season_id,'',0,0);
        
        $Moptions = array(
            "home_team" => $playersArray[2],
            "away_team" => $playersArray[3],
            "score1" => 1,
            "score2" => 2,
            "m_date" => "2017-06-02",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week1
        );
        $match_id = self::demoMatch($Moptions);
        
        //player events
        self::demoEvents($eventsArray[0],$playersArray[2],$match_id,$season_id,'',0,1);
        self::demoEvents($eventsArray[0],$playersArray[3],$match_id,$season_id,'',0,5);
        self::demoEvents($eventsArray[1],$playersArray[2],$match_id,$season_id,'',0,0);
        self::demoEvents($eventsArray[1],$playersArray[3],$match_id,$season_id,'',0,6);
        
        $Moptions = array(
            "home_team" => $playersArray[3],
            "away_team" => $playersArray[0],
            "score1" => 0,
            "score2" => 1,
            "m_date" => "2017-06-09",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week2
        );
        $match_id = self::demoMatch($Moptions);
        
        //player events
        self::demoEvents($eventsArray[0],$playersArray[3],$match_id,$season_id,'',0,0);
        self::demoEvents($eventsArray[0],$playersArray[0],$match_id,$season_id,'',0,1);
        self::demoEvents($eventsArray[1],$playersArray[3],$match_id,$season_id,'',0,11);
        self::demoEvents($eventsArray[1],$playersArray[0],$match_id,$season_id,'',0,4);
        
        $Moptions = array(
            "home_team" => $playersArray[1],
            "away_team" => $playersArray[2],
            "score1" => 3,
            "score2" => 1,
            "m_date" => "2017-06-09",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week2
        );
        $match_id = self::demoMatch($Moptions);
        
        //player events
        self::demoEvents($eventsArray[0],$playersArray[1],$match_id,$season_id,'',0,0);
        self::demoEvents($eventsArray[0],$playersArray[2],$match_id,$season_id,'',0,3);
        self::demoEvents($eventsArray[1],$playersArray[1],$match_id,$season_id,'',0,2);
        self::demoEvents($eventsArray[1],$playersArray[2],$match_id,$season_id,'',0,5);
        
        do_action('joomsport_update_standings',$season_id);
        
    }
    public static function demoDataTeam(){
        global $wpdb;
        $eventsArray = $boxArray = array();
        $events = array(
            array(
                'id' => 0,
                'e_name' => 'Goal',
                'e_img' => '',
                'player_event' => '1',
                'result_type' => '0',
                'sumev1' => 0,
                'sumev2' => 0,
                'ordering' => 0,
                'events_sum' => 0,
                'subevents' => '',
            ),
            array(
                'id' => 0,
                'e_name' => 'Assist',
                'e_img' => '',
                'player_event' => '1',
                'result_type' => '0',
                'sumev1' => 0,
                'sumev2' => 0,
                'ordering' => 0,
                'events_sum' => 0,
                'subevents' => '',
            ),
            array(
                'id' => 0,
                'e_name' => 'Fouls',
                'e_img' => '',
                'player_event' => '0',
                'result_type' => '0',
                'sumev1' => 0,
                'sumev2' => 0,
                'ordering' => 0,
                'events_sum' => 0,
                'subevents' => '',
            ),
            array(
                'id' => 0,
                'e_name' => 'Possession',
                'e_img' => '',
                'player_event' => '0',
                'result_type' => '0',
                'sumev1' => 0,
                'sumev2' => 0,
                'ordering' => 0,
                'events_sum' => 0,
                'subevents' => '',
            )
            
        );
        foreach($events as $event){
            $wpdb->insert($wpdb->joomsport_events, $event);
            $eventsArray[] = $wpdb->insert_id;
        }
        
        $boxes = array(
            array(
                'id' => 0,
                'name' => 'Attempts',
                'published' => '1',
                'complex' => '0',
                'ordering' => '0',
                'parent_id' => '0',
                'ftype' => '0',
                'options' => '',
                'displayonfe' => '1'
            ),
            array(
                'id' => 0,
                'name' => 'Successful attempts',
                'published' => '1',
                'complex' => '0',
                'ordering' => '1',
                'parent_id' => '0',
                'ftype' => '0',
                'options' => '',
                'displayonfe' => '1'
            )
        );
        
        foreach($boxes as $box){
            $wpdb->insert($wpdb->joomsport_box, $box);
            $boxArray[] = $wpdb->insert_id;
            
            $tblCOl = 'boxfield_'.$wpdb->insert_id;
            $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_box_match} LIKE '".$tblCOl."'");

            if (empty($is_col)) {
                $wpdb->query('ALTER TABLE '.$wpdb->joomsport_box_match.' ADD `'.$tblCOl."` FLOAT NULL DEFAULT NULL");
            }
        }
        
        $box = array(
                'id' => 0,
                'name' => 'Success rate',
                'published' => '1',
                'complex' => '0',
                'ordering' => '1',
                'parent_id' => '0',
                'ftype' => '1',
                'options' => '{"depend1":"'.$boxArray[1].'","calc":"0","depend2":"'.$boxArray[0].'"}',
                'displayonfe' => '1'
            );
        $wpdb->insert($wpdb->joomsport_box, $box);
        $boxArray[] = $wpdb->insert_id;
        
        $tblCOl = 'boxfield_'.$wpdb->insert_id;
        $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_box_match} LIKE '".$tblCOl."'");

        if (empty($is_col)) {
            $wpdb->query('ALTER TABLE '.$wpdb->joomsport_box_match.' ADD `'.$tblCOl."` FLOAT NULL DEFAULT NULL");
        }
        
        
        $tourn_id = self::demoCreateTaxonomy('Professional team league', 'joomsport_tournament', array('t_single' => 0));
        $season_id = self::demoCreatePost('2035-2036', 'joomsport_season');
        wp_set_object_terms($season_id,$tourn_id,'joomsport_tournament');
                
        self::demoSeasonData($season_id);
        $teams = array('New Yorkers', 'Berliners', 'Milaners', 'Londoners');
        $players = array('Peter Johnson', 'John Dow', 'Ron Lacky', 'Van Rader', 'Stephen Bow', 'Ben Vault', 'Yan Rosicky', 'Andrew Tkins');
        $intA = 0;
        $teamsArray = $playersArray = array();
        foreach ($players as $player) {
            $player_id = self::demoCreatePost($player, 'joomsport_player');
            update_post_meta($player_id, '_joomsport_player_personal', '');
            $playersArray[] = $player_id;
        }
        
        foreach ($teams as $team) {
            $team_id = self::demoCreatePost($team, 'joomsport_team');
            update_post_meta($team_id, '_joomsport_team_about', '');
            update_post_meta($team_id, '_joomsport_team_venue', '');
            $teamsArray[] = $team_id;
            update_post_meta($team_id, '_joomsport_team_players_'.$season_id, array($playersArray[$intA*2],$playersArray[$intA*2+1]));
            $intA++;
        }
        update_post_meta($season_id, '_joomsport_season_participiants', $teamsArray);
        
        $week1 = self::demoCreateTaxonomy('Week 1', 'joomsport_matchday', array('season_id' => $season_id, 'matchday_type' => 0));
        $week2 = self::demoCreateTaxonomy('Week 2', 'joomsport_matchday', array('season_id' => $season_id, 'matchday_type' => 0));
        
        $Moptions = array(
            "home_team" => $teamsArray[0],
            "away_team" => $teamsArray[1],
            "score1" => 3,
            "score2" => 2,
            "m_date" => "2017-06-02",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week1
        );
        $match_id = self::demoMatch($Moptions);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[0],
            'team_id' => $teamsArray[0],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 5;
        $box_match['boxfield_'.$boxArray[1]] = 3;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);

        // box stat player
        $box_match = array(
            'player_id' => $playersArray[1],
            'team_id' => $teamsArray[0],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 1;
        $box_match['boxfield_'.$boxArray[1]] = 1;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[2],
            'team_id' => $teamsArray[1],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 11;
        $box_match['boxfield_'.$boxArray[1]] = 2;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[3],
            'team_id' => $teamsArray[1],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 2;
        $box_match['boxfield_'.$boxArray[1]] = 0;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        //player events
        self::demoEvents($eventsArray[0],$playersArray[0],$match_id,$season_id,'22',$teamsArray[0]);
        self::demoEvents($eventsArray[0],$playersArray[1],$match_id,$season_id,'53',$teamsArray[0]);
        self::demoEvents($eventsArray[0],$playersArray[1],$match_id,$season_id,'90',$teamsArray[0]);
        self::demoEvents($eventsArray[0],$playersArray[2],$match_id,$season_id,'15',$teamsArray[1]);
        self::demoEvents($eventsArray[1],$playersArray[3],$match_id,$season_id,'15',$teamsArray[1]);
        self::demoEvents($eventsArray[0],$playersArray[3],$match_id,$season_id,'75',$teamsArray[1]);
        
        $meta_array = array(
            $eventsArray[2] => array("mevents1"=>3,"mevents2"=>1),
            $eventsArray[3] => array("mevents1"=>7,"mevents2"=>9),
        );
        
        update_post_meta($match_id, '_joomsport_matchevents', $meta_array);
        
        $Moptions = array(
            "home_team" => $teamsArray[2],
            "away_team" => $teamsArray[3],
            "score1" => 0,
            "score2" => 0,
            "m_date" => "2017-06-02",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week1
        );
        $match_id = self::demoMatch($Moptions);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[4],
            'team_id' => $teamsArray[2],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 2;
        $box_match['boxfield_'.$boxArray[1]] = 1;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[5],
            'team_id' => $teamsArray[2],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 3;
        $box_match['boxfield_'.$boxArray[1]] = 1;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[6],
            'team_id' => $teamsArray[3],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 3;
        $box_match['boxfield_'.$boxArray[1]] = 2;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[7],
            'team_id' => $teamsArray[3],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 0;
        $box_match['boxfield_'.$boxArray[1]] = 0;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        $meta_array = array(
            $eventsArray[2] => array("mevents1"=>5,"mevents2"=>8),
            $eventsArray[3] => array("mevents1"=>11,"mevents2"=>9),
        );
        
        update_post_meta($match_id, '_joomsport_matchevents', $meta_array);
       
        
        $Moptions = array(
            "home_team" => $teamsArray[1],
            "away_team" => $teamsArray[2],
            "score1" => 0,
            "score2" => 2,
            "m_date" => "2017-06-09",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week2
        );
        $match_id = self::demoMatch($Moptions);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[2],
            'team_id' => $teamsArray[1],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 5;
        $box_match['boxfield_'.$boxArray[1]] = 1;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[3],
            'team_id' => $teamsArray[1],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 1;
        $box_match['boxfield_'.$boxArray[1]] = 1;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[4],
            'team_id' => $teamsArray[2],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 4;
        $box_match['boxfield_'.$boxArray[1]] = 2;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[5],
            'team_id' => $teamsArray[2],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 2;
        $box_match['boxfield_'.$boxArray[1]] = 0;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        //player events
        self::demoEvents($eventsArray[0],$playersArray[4],$match_id,$season_id,'14',$teamsArray[2]);
        self::demoEvents($eventsArray[1],$playersArray[5],$match_id,$season_id,'14',$teamsArray[2]);
        self::demoEvents($eventsArray[0],$playersArray[4],$match_id,$season_id,'74',$teamsArray[2]);
        self::demoEvents($eventsArray[1],$playersArray[5],$match_id,$season_id,'74',$teamsArray[2]);
        
        $meta_array = array(
            $eventsArray[2] => array("mevents1"=>11,"mevents2"=>8),
            $eventsArray[3] => array("mevents1"=>2,"mevents2"=>3),
        );
        
        update_post_meta($match_id, '_joomsport_matchevents', $meta_array);
        
        $Moptions = array(
            "home_team" => $teamsArray[3],
            "away_team" => $teamsArray[0],
            "score1" => 1,
            "score2" => 0,
            "m_date" => "2017-06-09",
            "m_time" => "21:45",
            "season_id" => $season_id,
            "matchday" => $week2
        );
        $match_id = self::demoMatch($Moptions);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[6],
            'team_id' => $teamsArray[3],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 0;
        $box_match['boxfield_'.$boxArray[1]] = 0;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[7],
            'team_id' => $teamsArray[3],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 6;
        $box_match['boxfield_'.$boxArray[1]] = 1;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[0],
            'team_id' => $teamsArray[0],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 0;
        $box_match['boxfield_'.$boxArray[1]] = 0;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        // box stat player
        $box_match = array(
            'player_id' => $playersArray[1],
            'team_id' => $teamsArray[0],
            'match_id' => $match_id,
            'season_id' => $season_id
        );
        
        $box_match['boxfield_'.$boxArray[0]] = 4;
        $box_match['boxfield_'.$boxArray[1]] = 3;
        
        $wpdb->insert($wpdb->joomsport_box_match, $box_match);
        
        //player events
        self::demoEvents($eventsArray[0],$playersArray[6],$match_id,$season_id,'53',$teamsArray[3]);
        self::demoEvents($eventsArray[1],$playersArray[7],$match_id,$season_id,'53',$teamsArray[3]);
        
        $meta_array = array(
            $eventsArray[2] => array("mevents1"=>6,"mevents2"=>6),
            $eventsArray[3] => array("mevents1"=>5,"mevents2"=>9),
        );
        
        update_post_meta($match_id, '_joomsport_matchevents', $meta_array);
        
        //update
        
        do_action('joomsport_update_standings',$season_id);
        do_action('joomsport_update_playerlist',$season_id);
        
    }
    
    public static function demoCreateTaxonomy($tax_name, $tax, $options = array()){
        $term = wp_insert_term(
            $tax_name,
            $tax
        );
        if(isset($term['term_id']) && $term['term_id']){
             
             $term_metas = array();
             if($options){
                 foreach ($options as $key => $value) {
                     $term_metas[$key] = $value;
                 }
             }
             
             update_option( "taxonomy_{$term['term_id']}_metas", $term_metas );
             return $term['term_id'];
        }if( is_wp_error( $term ) ) {
            echo $term->get_error_message(). "({$tax_name})";
            return null;
        }
        
    }
    public static function demoCreatePost($post_name,$post){

        $arr = array(
                'post_type' => $post,
                'post_title' => wp_strip_all_tags( $post_name ),
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => get_current_user_id()
        );

        $post_id = wp_insert_post( $arr );
        return $post_id;
    }
    
    public static function demoSeasonData($post_id){
        $meta_array = array();
        $meta_array['s_win_point'] = $meta_array['s_win_away'] = 3;
        $meta_array['s_draw_point'] = $meta_array['s_draw_away'] = 1;
        $meta_array['s_lost_point'] = $meta_array['s_lost_away'] = 0;
        $meta_array['s_extra_win'] = $meta_array['s_extra_lost'] = $meta_array['s_enbl_extra'] = 0;
        update_post_meta($post_id, '_joomsport_season_point', $meta_array);

        update_post_meta($post_id, '_joomsport_season_rules', '');
        $standings = array("emblem_chk" => "1",
            "played_chk" => "1",
            "win_chk" => "1",
            "draw_chk" => "1",
            "lost_chk" => "1",
            "otwin_chk" => "0",
            "otlost_chk" => "0",
            "diff_chk" => "1",
            "gd_chk" => "1",
            "point_chk" => "1",
            "percent_chk" => "0",
            "goalscore_chk" => "0",
            "goalconc_chk" => "0",
            "winhome_chk" => "0",
            "winaway_chk" => "0",
            "drawhome_chk" => "0",
            "drawaway_chk" => "0",
            "losthome_chk" =>"0",
            "lostaway_chk" => "0",
            "pointshome_chk" => "0",
            "pointsaway_chk" => "0",
            "grwin_chk" => "0",
            "grlost_chk" => "0",
            "grwinpr_chk" => "0",
            "curform_chk" => "1");
        update_post_meta($post_id, '_joomsport_season_standindgs', $standings);
        update_post_meta($post_id, '_joomsport_season_stages', '');
        
        update_post_meta($post_id, '_joomsport_season_ranking', '');
    }
    
    public static function demoMatch($options){
        extract($options);
        $title = get_the_title($home_team).' vs '.get_the_title($away_team);
        $arr = array(
                'post_type' => 'joomsport_match',
                'post_title' => wp_strip_all_tags( $title ),
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => get_current_user_id()
        );

        $post_id = wp_insert_post( $arr );

        if($post_id){
            update_post_meta($post_id, '_joomsport_home_team', intval($home_team));
            update_post_meta($post_id, '_joomsport_away_team', intval($away_team));
            update_post_meta($post_id, '_joomsport_home_score', intval($score1));
            update_post_meta($post_id, '_joomsport_away_score', intval($score2));
            //update_post_meta($post_id, '_joomsport_groupID', $groupID);
            update_post_meta($post_id, '_joomsport_seasonid', $season_id);

            update_post_meta($post_id, '_joomsport_match_played', 1); 

            update_post_meta($post_id, '_joomsport_match_date', $m_date); 

            update_post_meta($post_id, '_joomsport_match_time', $m_time); 
            update_post_meta($post_id, '_joomsport_match_venue', 0); 

            wp_set_post_terms( $post_id, array((int) $matchday), 'joomsport_matchday');


        }
        return $post_id;
    }
    
    public static function demoEvents($event,$player,$match_id,$season_id,$minute,$team,$count=1){
        global $wpdb;
        
        //player events
        $event_match = array(
            "e_id" => $event,
            "player_id" => $player,
            "match_id" => $match_id,
            "season_id" => $season_id,
            "ecount" => $count,
            "minutes" => $minute,
            "t_id" => $team,
            "eordering" => 0
        );
        $wpdb->insert($wpdb->joomsport_match_events, $event_match);
    }
    
    public static function setTournType(){
        global $wpdb;
        if(isset($_REQUEST['ttype'])){
            $ttype = $_REQUEST['ttype']?0:1;
            $general = $wpdb->get_var("SELECT cValue FROM {$wpdb->joomsport_config} WHERE cName ='general'");
            if($general){
                $general = json_decode($general,true);
            }
            $general['tournament_type'] = $ttype;
            
            $wpdb->update($wpdb->joomsport_config, array('cValue' => json_encode($general)), array('cName' => 'general'), array('%s'), array('%s'));
            
        }    
    }
    
}

add_action( 'init', array( 'JoomSportSetupDemo', 'init' ), 5);
add_action( 'wp_ajax_joomsport_demo_ttype', array("JoomSportSetupDemo",'setTournType') );
        