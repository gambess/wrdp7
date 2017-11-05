<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomsportPageGenerator{
    public static function action(){
        global $wpdb;
        
         echo '<div class="jslinktopro jscenterpage">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
    }
    
    public static function generate()
    {

        $seasonVar = isset($_POST['season_id'])?  ($_POST['season_id']):0;
        if ($seasonVar == '0') {
            $season_id = 0;
            $group_id = 0;
        } else {
            $ex = explode('|', $seasonVar);
            $season_id = $ex[0];
            $group_id = $ex[1];
        }
        $md_type = isset($_POST['md_type'])?  intval($_POST['md_type']):0;
        
        
        if ($md_type) {
            $format_post = isset($_POST['format_post'])?  intval($_POST['format_post']):0;
            $teams_knock = isset($_POST['teams_knock'])?  ($_POST['teams_knock']):array();

            return self::algoritm_knock($format_post, $teams_knock);
        } else {
            $team_number_id = isset($_POST['team_number_id'])?  ($_POST['team_number_id']):array();

            $rounds = isset($_POST['rounds'])?  intval($_POST['rounds']):0;
            if (count($team_number_id) < 4) {
                //something wrong
                return __("Generating failed! At least 4 participants are required.",'joomsport-sports-league-results-management');
            } else {
                if(isset($_POST['sc_algorithm']) && $_POST['sc_algorithm'] == 1){
                    return self::algoritm2($team_number_id, $rounds);
                }else{
                    return self::algoritm1($team_number_id, $rounds);
                }
                
            }
        }
    }

    public static function algoritm1($teams, $rounds)
    {
        if (count($teams) % 2 != 0) {
            array_push($teams, 0);
        }
        $halfarr = count($teams) / 2;
        $md_name = isset($_POST['mday_name'])?esc_attr($_POST['mday_name']):'Matchday';
        $round_day = 1;
        for ($intR = 0; $intR < $rounds; ++$intR) {
            $duo_teams = array_chunk($teams,  $halfarr);
            $duo_teams[1] = array_reverse($duo_teams[1]);
            $continue = true;
            $first_team = $duo_teams[0][0];
            $last_team = $duo_teams[1][0];
            while ($continue) {
                $intB = 0;
                $matchday_id = self::create_mday(0, $md_name.' '.$round_day, $round_day);
                if( is_wp_error( $matchday_id ) ) {
                    return $matchday_id;
                }
                foreach ($duo_teams[0] as $home) {
                    if ($intR % 2 == 0) {
                        $row['home'] = $home;
                        $row['away'] = $duo_teams[1][$intB];
                    } else {
                        $row['away'] = $home;
                        $row['home'] = $duo_teams[1][$intB];
                    }
                    if($matchday_id){
                        if ($row['home'] && $row['away']) {
                            self::addMatch($row, $matchday_id, $intB);
                        }
                    }    
                    ++$intB;
                }
                ++$round_day;

                $tmp = $duo_teams[0][$halfarr - 1];
                $to_top = $duo_teams[1][0];
                unset($duo_teams[1][0]);
                unset($duo_teams[0][$halfarr - 1]);
                array_push($duo_teams[1], $tmp);
                $duo_teams[1] = array_values($duo_teams[1]);
                $arr_start = array($duo_teams[0][0], $to_top);
                $arr_end = array_slice($duo_teams[0], 1);
                if (count($arr_end)) {
                    $arr_start = array_merge($arr_start, $arr_end);
                }
                $duo_teams[0] = $arr_start;
                if ($duo_teams[1][0] == $last_team) {
                    $continue = false;
                }
            }
        }
        return '';
    }
    public static function algoritm2($teams, $rounds){
        $number = count($teams);
        
        if($number % 2 !=0){
            $number++;
            array_push($teams, 0);
        }
        $array = $teams;
        $last_child = $teams[$number-1];
        $initial = $array;
        array_pop($array);
        $cirle = $number / 2;
        $cuts = $cirle - 1;
        $round_day = 1;
        $md_name = isset($_POST['mday_name'])?esc_attr($_POST['mday_name']):'Matchday';
        
        for($intRounds=0;$intRounds<$rounds;$intRounds++){

            for($intR=0;$intR<$number-1;$intR++){
                $row = array();
                
                $output1 = array_slice($array,1,$cuts);
                $output2 = array_slice($array,$cuts+1);
                $output2 = array_reverse($output2);
                $intB = 0;
                
                $matchday_id = self::create_mday(0, $md_name.' '.$round_day, $round_day);
                
                if( is_wp_error( $matchday_id ) ) {
                    return $matchday_id;
                }
                //echo " Round {$intR}<br />";
                if($intR % 2 == 0 ){
                    if($intRounds % 2 == 0){
                        //echo $array[0] . ' vs ' .$last_child."<br />";
                        $row['home'] = $array[0];
                        $row['away'] = $last_child;
                    }else{
                        //echo $last_child . ' vs ' .$array[0]."<br />";
                        $row['home'] = $last_child;
                        $row['away'] = $array[0];
                    }

                }else{
                    if($intRounds % 2 == 0){
                        //echo $last_child . ' vs ' .$array[0]."<br />";
                        $row['home'] = $last_child;
                        $row['away'] = $array[0];
                    }else{
                        //echo $array[0] . ' vs ' .$last_child."<br />";
                        $row['home'] = $array[0];
                        $row['away'] = $last_child;
                    }
                }
                if($matchday_id){
                    if ($row['home'] && $row['away']) {
                        self::addMatch($row, $matchday_id, $intB);
                    }
                } 
                foreach($output1 as $op){
                    if($intRounds % 2 == 0){
                        //echo $op . ' vs ' .$output2[$intB]."<br />";
                        $row['home'] = $op;
                        $row['away'] = $output2[$intB];
                    }else{
                        //echo $output2[$intB] . ' vs ' .$op."<br />";
                        $row['home'] = $output2[$intB];
                        $row['away'] = $op;
                    }

                    $intB++;
                    if($matchday_id){
                        if ($row['home'] && $row['away']) {
                            self::addMatch($row, $matchday_id, $intB);
                        }
                    } 
                }
                //echo "<br /><br />";
                for($intC=0;$intC<$cirle-1;$intC++){
                    array_unshift($array, array_pop($array));
                }
                $round_day++;
            }
        }
        
        
        
        return '';
    }
    public static function addMatch($row, $matchday_id, $ordering)
    {
        $md_type = isset($_POST['md_type'])?  intval($_POST['md_type']):0;
        $seasonVar = isset($_POST['season_id'])?  ($_POST['season_id']):0;
        if ($seasonVar == '0') {
            $season_id = 0;
            $group_id = 0;
        } else {
            $ex = explode('|', $seasonVar);
            $season_id = $ex[0];
            $group_id = $ex[1];
        }
        
        $home_team = get_the_title(intval($row['home']));
        $away_team = get_the_title(intval($row['away']));

        $title = $home_team.' vs '.$away_team;
        $arr = array(
                'post_type' => 'joomsport_match',
                'post_title' => wp_strip_all_tags( $title ),
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => get_current_user_id()
        );
        
        $post_id = wp_insert_post( $arr );

        if($post_id){
            update_post_meta($post_id, '_joomsport_home_team', intval($row['home']));
            update_post_meta($post_id, '_joomsport_away_team', intval($row['away']));
            update_post_meta($post_id, '_joomsport_home_score', '');
            update_post_meta($post_id, '_joomsport_away_score', '');
            update_post_meta($post_id, '_joomsport_groupID', $group_id);
            update_post_meta($post_id, '_joomsport_seasonid', $season_id);
            update_post_meta($post_id, '_joomsport_match_date', '');
            update_post_meta($post_id, '_joomsport_match_time', '');
            update_post_meta($post_id, '_joomsport_match_played', '0');
            
            

        }

        wp_set_post_terms( $post_id, array((int) $matchday_id), 'joomsport_matchday');
        return $post_id;
    }

    public static function algoritm_knock($format_post, $teams_knock)
    {
        $md_name = isset($_POST['mday_name'])?esc_attr($_POST['mday_name']):'Matchday';
        $participiants = array();
        array_rand($teams_knock);
        $participiants = $teams_knock;
        $half = intval($format_post / 2);
        if (count($teams_knock) >= $format_post) {
            $participiants = array_slice($participiants, 0, $format_post);
            $duo_teams = array_chunk($participiants,  $half);
        }

        if (count($teams_knock) < $format_post) {
            $duo_teams = array_chunk($participiants,  $half);
            for ($intA = 0; $intA < $half; ++$intA) {
                if (!isset($duo_teams[1][$intA])) {
                    $duo_teams[1][$intA] = -1;
                }
            }
            array_rand($duo_teams[1]);
        }
        
        $matchday_id = self::create_mday($format_post, $md_name,0);
        if( is_wp_error( $matchday_id ) ) {
            return $matchday_id;
        }
        $matches_knock = array();
        for ($intA = 0; $intA < count($duo_teams[0]); ++$intA) {
            $row['home'] = $duo_teams[0][$intA];
            $row['away'] = $duo_teams[1][$intA];
            if($matchday_id){
                $matchID = self::addMatch($row, $matchday_id, $intA);
               
                $match = array();
                $match["match_id"] = array($matchID);
                $match["home"] = $row['home'];
                $match["away"] = $row['away'];
                $match["score1"] = array('');
                $match["score2"] = array('');
                $match["intA"] = $intA;
                $match["intB"] = 0;
                
                $matches_knock[0][$intA] = $match;
            }
        }
        //
        $matrix_stages = array(
            2 => 1,
            4 => 2,
            8 => 3,
            16 => 4,
            32 => 5,
            64 => 6,
            128 => 7
        );
        for($intA=1; $intA < intval($matrix_stages[$format_post]); $intA++){
            
            $matchesKnCount = $format_post/(4*$intA);
            for($intB=0;$intB<($matchesKnCount);$intB++){
                $row['home'] = 0;
                $row['away'] = 0;
                $matchID = self::addMatch($row, $matchday_id, 0);
               
                $match = array();
                $match["match_id"] = array($matchID);
                $match["home"] = 0;
                $match["away"] = 0;
                $match["score1"] = array('');
                $match["score2"] = array('');
                $match["intA"] = $intB*2*$intA;
                $match["intB"] = $intA;
                
                $matches_knock[$intA][$intB*2*$intA] = $match;
            }
        }        
        //
        //var_dump($matches_knock);die();
        $term_metas = get_option("taxonomy_{$matchday_id}_metas");
        if (!is_array($term_metas)) {
            $term_metas = Array();
        }
        $term_metas['knockout'] = $matches_knock;
        //var_dump($matches);
        update_option( "taxonomy_{$matchday_id}_metas", $term_metas );
    }

    public static function create_mday($format, $name, $ordering)
    {
        $seasonVar = isset($_POST['season_id'])?  ($_POST['season_id']):0;
        if ($seasonVar == '0') {
            $season_id = 0;
            $group_id = 0;
        } else {
            $ex = explode('|', $seasonVar);
            $season_id = $ex[0];
            $group_id = $ex[1];
        }
        
        $md_type = isset($_POST['md_type'])?  intval($_POST['md_type']):0;
        
        $res = wp_insert_term($name, 'joomsport_matchday',array('slug'=>$name.'_'.$season_id.'_'.$group_id));

        if( is_wp_error( $res ) ) {
            //echo "<p class='notice notice-error'>".$res->get_error_message()."</p>";
            return $res;
        }
        if(isset($res['term_id']) && $res['term_id']){
            $term_id = $res['term_id'];
            $term_metas = array();
            $term_metas['season_id'] = $season_id;
            $term_metas['matchday_type'] = $md_type;
            if(isset($_POST['format_post']) && intval($_POST['format_post'])){
                $term_metas['knockout_format'] = intval($_POST['format_post']);
            }
            update_option( "taxonomy_{$term_id}_metas", $term_metas );
            return $term_id;
        }
        
        return 0;
        
    }
}