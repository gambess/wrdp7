<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomsportActions {
    
    public static function init() {
        add_action('joomsport_update_standings', array('JoomsportActions','joomsport_update_standings'));
        add_action('joomsport_update_playerlist', array('JoomsportActions','joomsport_update_playerlist'));
        add_action('joomsport_calculate_boxscore', array('JoomsportActions','joomsport_calculate_boxscore'));
    }


    public static function joomsport_update_standings($season_id){
        if (!$season_id) {
            return;
        }
        new JoomSportcalcTable($season_id);
    }
    public static function joomsport_update_playerlist($season_id){
        if (!$season_id) {
            return;
        }
        new JoomSportcalcPlayerList($season_id);
    }
    public static function joomsport_calculate_boxscore($match_id){
        if (!$match_id) {
            return;
        }
        new JoomSportcalcBoxScore($match_id);
    }
}
JoomsportActions::init();



class JoomSportcalcTable
{
    public $lists = null;
    public $id = null;
    public $object = null;
    public function __construct($season_id)
    {
        global $wpdb;

        $this->id = $season_id;
        
            //get groups
            $groups = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$this->id} ORDER BY ordering"); ;
            $this->lists['columns'] = $this->getTournColumns();
            $this->lists['groups'] = $groups;
            $columnsCell = array();
            //get participants
            if (count($groups)) {
                foreach ($groups as $group) {
                    $columnsCell[$group->group_name] = $this->getTable($group->id);
                }
            } else {
                $columnsCell[] = $this->getTable(0);
            }
            $this->lists['columnsCell'] = $columnsCell;
        //}
    }

    public function getTournColumns()
    {
        
        $lists = array();


        $listsss = get_post_meta($this->id,'_joomsport_season_standindgs',true);
        if(count($listsss) && $listsss){
            foreach ($listsss as $key => $value) {
                $lists[$key] = $value;
            }
        }


        return $lists;
    }
    public function getTable($group_id)
    {
        $table = $this->getTournColumnsVar($group_id);
    }
    public function getTournColumnsVar($group_id)
    {
        global $wpdb;
        $participants = array();
        $grtype = 0;
        if($group_id){
            $group = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_groups} WHERE id = {$group_id}");
            $participants_array = unserialize($group->group_partic);
            if($participants_array){
                
                for($intA=0;$intA<count($participants_array);$intA++){
                    $participants[] = get_post($participants_array[$intA]);
                }
            }
            $groptions = unserialize($group->options);
            if(isset($groptions['grtype'])){
                $grtype = $groptions['grtype'];
            }
        }else{
            $participants = JoomSportHelperObjects::getParticipiants($this->id);
        }
            
        $seasonOpt = get_post_meta($this->id,'_joomsport_season_ranking',true);
        $equalpts_chk = isset($seasonOpt['equalpts_chk'])?$seasonOpt['equalpts_chk']:0;
        
        $season_options = get_post_meta($this->id,'_joomsport_season_point',true);
        
        $s_win_point = isset($season_options['s_win_point'])?  floatval($season_options['s_win_point']):0;
        $s_win_away = isset($season_options['s_win_away'])?  floatval($season_options['s_win_away']):0;
        $s_draw_point = isset($season_options['s_draw_point'])?  floatval($season_options['s_draw_point']):0;
        $s_draw_away = isset($season_options['s_draw_away'])?  floatval($season_options['s_draw_away']):0;
        $s_lost_point = isset($season_options['s_lost_point'])?  floatval($season_options['s_lost_point']):0;
        $s_lost_away = isset($season_options['s_lost_away'])?  floatval($season_options['s_lost_away']):0;
        $s_extra_win = isset($season_options['s_extra_win'])?  floatval($season_options['s_extra_win']):0;
        $s_extra_lost = isset($season_options['s_extra_lost'])?  floatval($season_options['s_extra_lost']):0;
        $s_enbl_extra = isset($season_options['s_enbl_extra'])?  intval($season_options['s_enbl_extra']):0;
       
        $array = array();
        $intA = 0;
        if (count($participants)) {
            foreach ($participants as $participant) {
                
                $winhome_chk = 0;
                $winaway_chk = 0;
                $drawhome_chk = 0;
                $drawaway_chk = 0;
                $losthome_chk = 0;
                $lostaway_chk = 0;
                $goalscore_chk = 0;
                $goalconc_chk = 0;
                $winextra = 0;
                $loosextra = 0;
                $points = 0;
                $points_home = 0;
                $points_away = 0;
                $played = 0;

                $seas_bonus = get_post_meta($participant->ID, '_joomsport_team_bonuses_'.$this->id,true);
                
                $points += intval($seas_bonus);
                
                /*$matches_home = get_posts(array(
                    'post_type' => 'joomsport_match',
                    'post_status'      => 'publish',
                    'posts_per_page'   => -1,
                    'meta_query' => array(
                        array(
                        'key' => '_joomsport_seasonid',
                        'value' => $this->id),
                        array(
                        'key' => '_joomsport_home_team',
                        'value' => $participant->ID),
                        array(
                        'key' => '_joomsport_match_played',
                        'value' => '1'),
                        
                    ))
                );*/
                $matches_home = $this->_getCalcMatches($participant->ID, $grtype, true, $participants);
                
                for($intM=0; $intM < count($matches_home); $intM++){
                    
                    $md = wp_get_post_terms($matches_home[$intM]->ID,'joomsport_matchday');
                    $mdID = $md[0]->term_id;
                    $metas = get_option("taxonomy_{$mdID}_metas");
                    if(!isset($metas['matchday_type']) || !$metas['matchday_type']){
        
                        $home_score = get_post_meta( $matches_home[$intM]->ID, '_joomsport_home_score', true );
                        $away_score = get_post_meta( $matches_home[$intM]->ID, '_joomsport_away_score', true );
                        if($home_score != '' && $away_score != ''){
                            $goalscore_chk += $home_score;
                            $goalconc_chk += $away_score;
                            $jmscore = get_post_meta($matches_home[$intM]->ID, '_joomsport_match_jmscore',true);
                            $is_extra = 0;
                            $new_points = null;
                            $bonus = 0;
                            if($jmscore){
                                $is_extra = (isset($jmscore['is_extra']))?$jmscore['is_extra']:0;
                                $bonus = isset($jmscore['bonus1'])?$jmscore['bonus1']:0;
                                if(isset($jmscore['new_points']) && $jmscore['new_points']){
                                    $new_points = isset($jmscore['points1'])?$jmscore['points1']:null;
                                }
                            }

                            if($home_score > $away_score){
                                if($is_extra){
                                   $winextra ++; 
                                }else{
                                   $winhome_chk ++;
                                }
                                if($new_points === null){
                                    if($is_extra){
                                        $points += $s_extra_win;
                                    }else{
                                        $points += $s_win_point;
                                    }

                                }else{
                                    $points += $new_points;
                                }
                                $points += $bonus;

                            }elseif($home_score < $away_score){
                                if($is_extra){
                                   $loosextra ++; 
                                }else{
                                   $losthome_chk ++;
                                }
                                if($new_points === null){
                                    if($is_extra){
                                        $points += $s_extra_lost;
                                    }else{
                                        $points += $s_lost_point;
                                    }

                                }else{
                                    $points += $new_points;
                                }
                                $points += $bonus;

                            }else{
                                $drawhome_chk ++;
                                if($new_points === null){ 
                                    $points += $s_draw_point;
                                }else{
                                    $points += $new_points;
                                }
                                $points += $bonus;
                            }
                            $played++;
                        }
                        
                    }
                }
                
                $points_home = $points;
                //$played += count($matches_home);
                
                /*$matches_away = get_posts(array(
                    'post_type' => 'joomsport_match',
                    'post_status'      => 'publish',
                    'posts_per_page'   => -1,
                    'meta_query' => array(
                        array(
                        'key' => '_joomsport_seasonid',
                        'value' => $this->id),
                        array(
                        'key' => '_joomsport_away_team',
                        'value' => $participant->ID),
                        array(
                        'key' => '_joomsport_match_played',
                        'value' => '1')
                    ))
                );*/
                $matches_away = $this->_getCalcMatches($participant->ID, $grtype,false, $participants);
                
                for($intM=0; $intM < count($matches_away); $intM++){
                    $md = wp_get_post_terms($matches_away[$intM]->ID,'joomsport_matchday');
                    $mdID = $md[0]->term_id;
                    $metas = get_option("taxonomy_{$mdID}_metas");
                    if(!isset($metas['matchday_type']) || !$metas['matchday_type']){
                        $home_score = get_post_meta( $matches_away[$intM]->ID, '_joomsport_home_score', true );
                        $away_score = get_post_meta( $matches_away[$intM]->ID, '_joomsport_away_score', true );
                        if($home_score != '' && $away_score != ''){
                            $goalscore_chk += $away_score;
                            $goalconc_chk += $home_score;
                            $jmscore = get_post_meta($matches_away[$intM]->ID, '_joomsport_match_jmscore',true);
                            $is_extra = 0;
                            $new_points = null;
                            $bonus = 0;
                            if($jmscore){
                                $is_extra = (isset($jmscore['is_extra']))?$jmscore['is_extra']:0;
                                $bonus = isset($jmscore['bonus2'])?$jmscore['bonus2']:0;
                                if(isset($jmscore['new_points']) && $jmscore['new_points']){
                                    $new_points = isset($jmscore['points2'])?$jmscore['points2']:null;
                                }
                            }

                            if($home_score < $away_score){
                                if($is_extra){
                                   $winextra ++; 
                                }else{
                                   $winaway_chk ++;
                                }
                                if($new_points === null){
                                    if($is_extra){
                                        $points += $s_extra_win;
                                    }else{
                                        $points += $s_win_away;
                                    }

                                }else{
                                    $points += $new_points;
                                }
                                $points += $bonus;

                            }elseif($home_score > $away_score){
                                if($is_extra){
                                   $loosextra ++; 
                                }else{
                                   $lostaway_chk ++;
                                }
                                if($new_points === null){
                                    if($is_extra){
                                        $points += $s_extra_lost;
                                    }else{
                                        $points += $s_lost_away;
                                    }

                                }else{
                                    $points += $new_points;
                                }
                                $points += $bonus;

                            }else{
                                $drawaway_chk ++;
                                if($new_points === null){ 
                                    $points += $s_draw_away;
                                }else{
                                    $points += $new_points;
                                }
                                $points += $bonus;
                            }
                            $played++;
                        }
                    }
                }
                
                $points_away = $points - $points_home;
                //$played += count($matches_away);
                
                $wins = $winaway_chk + $winhome_chk;
                $lose = $lostaway_chk + $losthome_chk;
                $draw = $drawaway_chk + $drawhome_chk;
                
                if ($played) {
                    $percent_chk = sprintf("%0.3f",($wins + ($draw / 2)) / $played);
                } else {
                    $percent_chk = 0;
                }
                
                

                $array[$intA] = array();
                $array[$intA]['id'] = $participant->ID;
                $array[$intA]['sortname'] = get_the_title($participant->ID);
                
                $array[$intA]['winhome_chk'] = $winhome_chk;
                $array[$intA]['winaway_chk'] = $winaway_chk;
                $array[$intA]['drawhome_chk'] = $drawhome_chk;
                $array[$intA]['drawaway_chk'] = $drawaway_chk;
                $array[$intA]['losthome_chk'] = $losthome_chk;
                $array[$intA]['lostaway_chk'] = $lostaway_chk;
                $array[$intA]['goalscore_chk'] = $goalscore_chk;
                $array[$intA]['goalconc_chk'] = $goalconc_chk;
                $array[$intA]['win_chk'] = $wins;
                $array[$intA]['draw_chk'] = $draw;
                $array[$intA]['lost_chk'] = $lose;
                $array[$intA]['diff_chk'] = $goalscore_chk.' - '.$goalconc_chk;
                $array[$intA]['gd_chk'] = $goalscore_chk - $goalconc_chk;
                $array[$intA]['point_chk'] = $points;
                $array[$intA]['pointshome_chk'] = $points_home;
                $array[$intA]['pointsaway_chk'] = $points_away;
                $array[$intA]['otwin_chk'] = $winextra;
                $array[$intA]['otlost_chk'] = $loosextra;
                $array[$intA]['percent_chk'] = $percent_chk;
                $array[$intA]['played_chk'] = $played;
                
                
                
                if ($group_id) {
                    $this->inGroupsVar($array[$intA], $group_id);
                }
                
                

                if ($equalpts_chk) {
                    $array[$intA]['avulka_v'] = '';
                    $array[$intA]['avulka_cf'] = '';
                    $array[$intA]['avulka_cs'] = '';
                    $array[$intA]['avulka_qc'] = '';
                }
                
                ++$intA;
            }
            $this->sortTable($array);
            $this->saveToDB($array, $group_id);
            //$array = $this->getTable($group_id);
        }else{
            $query = 'DELETE FROM '.$wpdb->joomsport_season_table.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id;
                $wpdb->query($query);
        }
        //return $array;
    }

    public function sortTable(&$table_view)
    {
        $seasonOpt = get_post_meta($this->id,'_joomsport_season_ranking',true);
        $equalpts_chk = isset($seasonOpt['equalpts_chk'])?$seasonOpt['equalpts_chk']:0;
        if(!isset($seasonOpt['ranking'])){
            $default_criteria = array(1, 4, 5, 7, 0);
            $seasonOpt['ranking'] = array(
                array('sortfield' => 1, 'sortway' => 0),
                array('sortfield' => 4, 'sortway' => 0),
                array('sortfield' => 5, 'sortway' => 0),
                array('sortfield' => 7, 'sortway' => 0),
                array('sortfield' => 0, 'sortway' => 0),
            );

        }
        if ($equalpts_chk) {
            $season_options = get_post_meta($this->id,'_joomsport_season_point',true);
        
            $s_win_point = isset($season_options['s_win_point'])?  floatval($season_options['s_win_point']):0;
            $s_win_away = isset($season_options['s_win_away'])?  floatval($season_options['s_win_away']):0;
            $s_draw_point = isset($season_options['s_draw_point'])?  floatval($season_options['s_draw_point']):0;
            $s_draw_away = isset($season_options['s_draw_away'])?  floatval($season_options['s_draw_away']):0;
            $s_lost_point = isset($season_options['s_lost_point'])?  floatval($season_options['s_lost_point']):0;
            $s_lost_away = isset($season_options['s_lost_away'])?  floatval($season_options['s_lost_away']):0;

            $pts_arr = array();
            $pts_equal = array();
            foreach ($table_view as $tv) {
                if (!in_array($tv['point_chk'], $pts_arr)) {
                    $pts_arr[] = $tv['point_chk'];
                } else {
                    if (!in_array($tv['point_chk'], $pts_equal)) {
                        $pts_equal[] = $tv['point_chk'];
                    }
                }
            }
            $k = 0;
            $team_arr = array();
            foreach ($pts_equal as $pts) {
                foreach ($table_view as $tv) {
                    if ($tv['point_chk'] == $pts) {
                        $team_arr[$k][] = $tv['id'];
                    }
                }
                ++$k;
            }

            foreach ($team_arr as $tm) {

                foreach ($tm as $tm_one) {
                    
                    $matches_home = get_posts(array(
                        'post_type' => 'joomsport_match',
                        'post_status'      => 'publish',
                        'posts_per_page'   => -1,
                        'meta_query' => array(
                            array(
                            'key' => '_joomsport_seasonid',
                            'value' => $this->id),
                            array(
                            'key' => '_joomsport_home_team',
                            'value' => $tm_one),
                            array(
                            'key' => '_joomsport_match_played',
                            'value' => '1'),
                            array(
                            'key' => '_joomsport_away_team',
                            'value' => implode(',', $tm),
                            'compare' => 'IN'),

                        ))
                    );
                    $matchs_avulsa_win = 0;
                    $matchs_avulsa_draw = 0;
                    $matchs_avulsa_lost = 0;
                    $matchs_avulsa_points = 0;
                    $score1 = 0;
                    $score2 = 0;
                    for($intM = 0; $intM < count($matches_home); $intM ++){
                        $home_score = get_post_meta( $matches_home[$intM]->ID, '_joomsport_home_score', true );
                        $away_score = get_post_meta( $matches_home[$intM]->ID, '_joomsport_away_score', true );
                        if($home_score > $away_score){
                            $matchs_avulsa_win ++;
                        }elseif($home_score < $away_score){
                            $matchs_avulsa_lost ++;
                        }else{
                            $matchs_avulsa_draw ++;
                        }
                        $score1 += $home_score;
                        $score2 += $away_score;
                    }
                    $matchs_avulsa_points = $matchs_avulsa_win * $s_win_point + $matchs_avulsa_draw * $s_draw_point + $matchs_avulsa_lost * $s_lost_point;
                    
                    $matches_away = get_posts(array(
                        'post_type' => 'joomsport_match',
                        'post_status'      => 'publish',
                        'posts_per_page'   => -1,
                        'meta_query' => array(
                            array(
                            'key' => '_joomsport_seasonid',
                            'value' => $this->id),
                            array(
                            'key' => '_joomsport_away_team',
                            'value' => $tm_one),
                            array(
                            'key' => '_joomsport_match_played',
                            'value' => '1'),
                            array(
                            'key' => '_joomsport_home_team',
                            'value' => implode(',', $tm),
                            'compare' => 'IN'),

                        ))
                    );
                    $matchs_avulsa_win = 0;
                    $matchs_avulsa_draw = 0;
                    $matchs_avulsa_lost = 0;
                    for($intM = 0; $intM < count($matches_away); $intM ++){
                        $home_score = get_post_meta( $matches_away[$intM]->ID, '_joomsport_home_score', true );
                        $away_score = get_post_meta( $matches_away[$intM]->ID, '_joomsport_away_score', true );
                        if($home_score < $away_score){
                            $matchs_avulsa_win ++;
                        }elseif($home_score > $away_score){
                            $matchs_avulsa_lost ++;
                        }else{
                            $matchs_avulsa_draw ++;
                        }
                        $score1 += $away_score;
                        $score2 += $home_score;
                    }
                    $matchs_avulsa_points += $matchs_avulsa_win * $s_win_away + $matchs_avulsa_draw * $s_draw_away + $matchs_avulsa_lost * $s_lost_away;
                    
                    $matchs_avulsa_win_c = 3 * $matchs_avulsa_points;
                    $matchs_avulsa_res = $score1;
                    $matchs_avulsa_res2 = $score2;
                    

                    for ($b = 0;$b < count($table_view);++$b) {
                        if ($table_view[$b]['id'] == $tm_one) {
                            $table_view[$b]['avulka_v'] = $matchs_avulsa_win_c;
                            $table_view[$b]['avulka_cf'] = $matchs_avulsa_res;
                            $table_view[$b]['avulka_cs'] = $matchs_avulsa_res2;
                            $table_view[$b]['avulka_qc'] = $matchs_avulsa_res - $matchs_avulsa_res2;
                        }
                    }
                }
            }
        }
        //--/playeachother---///

        $sort_arr = array();
        foreach ($table_view as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $sort_arr[$key][$uniqid] = $value;
            }
        }

        if (count($sort_arr)) {
            // sort fields 1-points, 2-wins percent, /*3-if equal between teams*/, 4-goal difference, 5-goal score
            
            $savedsort = $seasonOpt['ranking'];
            $argsort = array();
            $argsort_way = array();
            if (count($savedsort)) {
                foreach ($savedsort as $sortop) {
                    switch ($sortop['sortfield']) {
                        case '1': $argsort[][0] = $sort_arr['point_chk'];        break;
                        case '2': $argsort[][0] = $sort_arr['percent_chk'];        break;
                        case '3': $argsort[][0] = $sort_arr['point_chk'];        break; /* not used */
                        case '4': $argsort[][0] = $sort_arr['gd_chk'];            break;
                        case '5': $argsort[][0] = $sort_arr['goalscore_chk'];    break;
                        case '6': $argsort[][0] = $sort_arr['played_chk'];        break;
                        case '7': $argsort[][0] = $sort_arr['win_chk'];        break;
                    }

                    $argsort_way[] = $sortop['sortway'];
                }
            }

            if ($equalpts_chk) {
                array_multisort($sort_arr['point_chk'], SORT_DESC, $sort_arr['avulka_v'], SORT_DESC, $sort_arr['avulka_qc'], SORT_DESC, $sort_arr['avulka_cf'], SORT_DESC, $sort_arr['gd_chk'], SORT_DESC, $sort_arr['goalscore_chk'], SORT_DESC,$sort_arr['sortname'],SORT_ASC, $table_view);
            } else {
                
                array_multisort((isset($argsort[0][0]) ? $argsort[0][0] : $sort_arr['point_chk']), (isset($argsort_way[0]) ? ($argsort_way[0] ? SORT_ASC : SORT_DESC) : SORT_DESC), (isset($argsort[1][0]) ? $argsort[1][0] : $sort_arr['gd_chk']), (isset($argsort_way[1]) ? ($argsort_way[1] ? SORT_ASC : SORT_DESC) : SORT_DESC), (isset($argsort[2][0]) ? $argsort[2][0] : $sort_arr['goalscore_chk']), (isset($argsort_way[2]) ? ($argsort_way[2] ? SORT_ASC : SORT_DESC) : SORT_DESC), (isset($argsort[3][0]) ? $argsort[3][0] : $sort_arr['win_chk']), (isset($argsort_way[3]) ? ($argsort_way[3] ? SORT_ASC : SORT_DESC) : SORT_DESC), $sort_arr['sortname'],SORT_ASC, $table_view);

            }
        }
    }

    public function inGroupsVar(&$array, $group_id)
    {
        global $jsDatabase;
        // in groups
        
        
        $winhome_chk = 0;
        $winaway_chk = 0;
        $drawhome_chk = 0;
        $drawaway_chk = 0;
        $losthome_chk = 0;
        $lostaway_chk = 0;

        $played = 0;


        $matches_home = get_posts(array(
            'post_type' => 'joomsport_match',
            'post_status'      => 'publish',
            'posts_per_page'   => -1,
            'meta_query' => array(
                array(
                'key' => '_joomsport_seasonid',
                'value' => $this->id),
                array(
                'key' => '_joomsport_home_team',
                'value' => $array['id']),
                array(
                'key' => '_joomsport_match_played',
                'value' => '1'),
                array(
                'key' => '_joomsport_match_groupID',
                'value' => $group_id),

            ))
        );
                
        for($intM=0; $intM < count($matches_home); $intM++){
            $home_score = get_post_meta( $matches_home[$intM]->ID, '_joomsport_home_score', true );
            $away_score = get_post_meta( $matches_home[$intM]->ID, '_joomsport_away_score', true );
            if($home_score != '' && $away_score != ''){

                    $winhome_chk ++;


            }elseif($home_score < $away_score){

                    $losthome_chk ++;


            }else{
                 $drawhome_chk ++;


            }
        }

                
        $matches_away = get_posts(array(
            'post_type' => 'joomsport_match',
            'post_status'      => 'publish',
            'posts_per_page'   => -1,
            'meta_query' => array(
                array(
                'key' => '_joomsport_seasonid',
                'value' => $this->id),
                array(
                'key' => '_joomsport_away_team',
                'value' => $array['id']),
                array(
                'key' => '_joomsport_match_played',
                'value' => '1'),
                array(
                'key' => '_joomsport_match_groupID',
                'value' => $group_id)
            ))
        );
                
        for($intM=0; $intM < count($matches_away); $intM++){
            $home_score = get_post_meta( $matches_away[$intM]->ID, '_joomsport_home_score', true );
            $away_score = get_post_meta( $matches_away[$intM]->ID, '_joomsport_away_score', true );
            if($home_score != '' && $away_score != ''){
                $winaway_chk ++;

            }elseif($home_score > $away_score){
                $lostaway_chk ++;

            }else{
                $drawaway_chk ++;

            }

        }

                
        $array['grwin_chk'] =$wins_gr= $winaway_chk + $winhome_chk;
        $array['grlost_chk'] =$loose_gr= $lostaway_chk + $losthome_chk;
        $gr_array['draw_home'] =$draw_gr= $drawaway_chk + $drawhome_chk;
        
        if (($wins_gr + $loose_gr + $draw_gr) > 0) {
            $array['grwinpr_chk'] = sprintf("%0.3f",($wins_gr + $draw_gr / 2) / ($wins_gr + $loose_gr + $draw_gr));
        } else {
            $array['grwinpr_chk'] = 0;
        }
        

        //}
    }

    public function saveToDB($array, $group_id)
    {
        global $wpdb;
        $query = 'DELETE FROM '.$wpdb->joomsport_season_table.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id;
        $wpdb->query($query);
        $intA = 1;

        foreach ($array as $tbl) {

            unset($tbl['sortname']);
            $options = json_encode($tbl);
            $query = 'INSERT INTO '.$wpdb->joomsport_season_table.' (season_id,group_id,participant_id,options,ordering) '
                    ." VALUES({$this->id},{$group_id},{$tbl['id']},'".$options."',{$intA})";
            $wpdb->query($query);
            ++$intA;
        }
    }
    private function _getCalcMatches($partic_id, $grtype, $ishome = true, $participants){
        $selteam = $ishome?'_joomsport_home_team':'_joomsport_away_team';
        $matches = array();
        switch ($grtype) {
            case '1':
                $previd = JoomSportHelperObjects::getPreviousSeason($this->id);
                if($previd){
                    $matches = get_posts(array(
                        'post_type' => 'joomsport_match',
                        'post_status'      => 'publish',
                        'posts_per_page'   => -1,
                        'meta_query' => array(
                            array(
                            'key' => '_joomsport_seasonid',
                            'value' => array($this->id,$previd),
                            'compare' => 'IN',    
                            ),
                            array(
                            'key' => $selteam,
                            'value' => $partic_id),
                            array(
                            'key' => '_joomsport_match_played',
                            'value' => '1')
                        ))
                    );
                }

                break;
            case '2':
                $previd = JoomSportHelperObjects::getPreviousSeason($this->id);
                $selteam_reverse = $ishome?'_joomsport_away_team':'_joomsport_home_team';
                if($previd && count($participants)){
                    
                    $participants_in = array();
                    foreach ($participants as $p) {
                        $participants_in[] = $p->ID;
                    }
                    
                    
                    $matches1 = get_posts(array(
                        'post_type' => 'joomsport_match',
                        'post_status'      => 'publish',
                        'posts_per_page'   => -1,
                        'meta_query' => array(
                            array(
                            'key' => '_joomsport_seasonid',
                            'value' => $previd, 
                            ),
                            array(
                            'key' => $selteam,
                            'value' => $partic_id),
                            array(
                            'key' => $selteam_reverse,
                            'value' => implode(',', $participants_in),
                            'compare' => 'IN'),
                            array(
                            'key' => '_joomsport_match_played',
                            'value' => '1')
                        ))
                    );
                    $matches2 = get_posts(array(
                        'post_type' => 'joomsport_match',
                        'post_status'      => 'publish',
                        'posts_per_page'   => -1,
                        'meta_query' => array(
                            array(
                            'key' => '_joomsport_seasonid',
                            'value' => $this->id),
                            array(
                            'key' => $selteam,
                            'value' => $partic_id),
                            array(
                            'key' => '_joomsport_match_played',
                            'value' => '1')
                        ))
                    );
                    $matches = array_merge($matches1,$matches2);
                }

                break;

            default:
                $matches = get_posts(array(
                    'post_type' => 'joomsport_match',
                    'post_status'      => 'publish',
                    'posts_per_page'   => -1,
                    'meta_query' => array(
                        array(
                        'key' => '_joomsport_seasonid',
                        'value' => $this->id),
                        array(
                        'key' => $selteam,
                        'value' => $partic_id),
                        array(
                        'key' => '_joomsport_match_played',
                        'value' => '1')
                    ))
                );
                break;
        }

        return $matches;
        
    }
}

class JoomSportcalcPlayerList
{
    private $match_id = null;
    private $matchObj = null;
    private $season_id = null;
    private $single = null;
    public function __construct($season_id)
    {
        $this->season_id = $season_id;
        
        $this->single = JoomSportHelperObjects::getTournamentType($this->season_id);
        
        $this->recalculateColumn();
    }
    public function recalculateColumn()
    {
        global $wpdb;
        $jsconfig =  new JoomsportSettings();
        $duration = $jsconfig->get('jsmatch_duration','');
        
        $players = array();
        $participants = JoomSportHelperObjects::getParticipiants($this->season_id);
        if ($this->single == '1') {
            if($participants && count($participants)){
                foreach($participants as $part){
                    $players[] = array("player" => $part->ID, "team" => 0);
                }
            }
        } else {
            if($participants && count($participants)){
                foreach($participants as $part){
                    $playersin = get_post_meta($part->ID,'_joomsport_team_players_'.$this->season_id,true);
                    $playersin = JoomSportHelperObjects::cleanJSArray($playersin);
                    if($playersin && count($playersin)){
                        foreach ($playersin as $pl) {
                            $players[] = array("player" => $pl, "team" => $part->ID);
                        }
                    }
                }
            }
                
        }
        //update season id temporary for all players
        $query = 'UPDATE '.$wpdb->joomsport_playerlist.' SET season_id = "-100" WHERE season_id = '.intval($this->season_id);
        $wpdb->query($query);
        
        /*$query = 'DELETE FROM '.$wpdb->joomsport_playerlist.' WHERE season_id = '.intval($this->season_id);
        $wpdb->query($query);*/

        $query = 'SELECT * FROM '.$wpdb->joomsport_events."  WHERE player_event = '1'";
        $events = $wpdb->get_results($query);
        for ($intA = 0; $intA < count($events); ++$intA) {
            $event = $events[$intA];
            $tblCOl = 'eventid_'.$event->id;
            $is_col = $wpdb->get_var('SHOW COLUMNS FROM '.$wpdb->joomsport_playerlist." LIKE '".$tblCOl."'");

            if (!$is_col) {
                $wpdb->query('ALTER TABLE '.$wpdb->joomsport_playerlist.' ADD `'.$tblCOl."` FLOAT NOT NULL DEFAULT  '0'");
                //$database->query();
            }
        }

        for ($intC = 0; $intC < count($players); ++$intC) {
            
            $query = 'SELECT COUNT(player_id) FROM '.$wpdb->joomsport_playerlist.' WHERE season_id = "-100" AND player_id = '.intval($players[$intC]["player"]).' AND team_id = '.intval($players[$intC]["team"]);
            $player_exist = $wpdb->get_var($query);
            
            if($player_exist){
                $query = 'UPDATE '.$wpdb->joomsport_playerlist.' SET season_id = '.$this->season_id.' WHERE season_id = "-100" AND player_id = '.intval($players[$intC]["player"]).' AND team_id = '.intval($players[$intC]["team"]);
                $wpdb->query($query);
            }
            
            if(count($events)){
                for ($intA = 0; $intA < count($events); ++$intA) {
                    $event = $events[$intA];

                    

                    $sum = ($event->result_type == 1) ? 'ROUND(AVG(me.ecount),3)' : 'SUM(me.ecount)';
                    if ($this->single == '1') {
                        if($event->events_sum == '1' && $event->subevents){
                            $events_ids = json_decode($event->subevents,true);
                            if(count($events_ids)){
                                $query = 'SELECT '.$sum.' as esum'
                                .' FROM '.$wpdb->joomsport_match_events.' as me'
                                    .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=me.match_id '
                                . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
                                .' WHERE me.e_id IN ('.  implode(',', $events_ids).')'
                                ." AND me.player_id = {$players[$intC]["player"]}"
                                ." AND me.season_id = {$this->season_id}"
                                .' GROUP BY me.player_id'; 
                            }
                        }else{
                           $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.$wpdb->joomsport_match_events.' as me'
                                .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=me.match_id '
                        . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
                            .' WHERE '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").''
                            ." AND me.player_id = {$players[$intC]["player"]}"
                            ." AND me.season_id = {$this->season_id}"
                            .' GROUP BY me.player_id'; 
                        }
                        
                    } else {
                        if($event->events_sum == '1' && $event->subevents){
                            $events_ids = json_decode($event->subevents,true);
                            if(count($events_ids)){
                                $query = 'SELECT '.$sum.' as esum'
                                .' FROM '.$wpdb->joomsport_match_events.' as me'
                                    .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=me.match_id '
                                . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
                                .' WHERE me.e_id IN ('.  implode(',', $events_ids).')'
                                ." AND me.player_id = {$players[$intC]["player"]}"
                                ." AND me.season_id = {$this->season_id}"
                                ." AND me.t_id = {$players[$intC]["team"]}"
                                .' GROUP BY me.player_id';
                            }
                        }else{
                            $query = 'SELECT '.$sum.' as esum'
                            .' FROM '.$wpdb->joomsport_match_events.' as me'
                                .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=me.match_id '
                             . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
                            .' WHERE '.($event->player_event == '2' ? '(me.e_id = '.$event->sumev1.' OR me.e_id = '.$event->sumev2.')' : "me.e_id = '".intval($event->id)."'").''
                            ." AND me.player_id = {$players[$intC]["player"]}"
                            ." AND me.season_id = {$this->season_id}"
                            ." AND me.t_id = {$players[$intC]["team"]}"
                            .' GROUP BY me.player_id';
                        }    
                    }
                    if($query){
                        $value = $wpdb->get_var($query);
                    }else{
                        $value = 0;
                    }

                    $tblCOl = 'eventid_'.$event->id;
                    
                    
                    
                    
                    $query = 'INSERT INTO '.$wpdb->joomsport_playerlist.' (player_id, team_id, season_id, `'.$tblCOl.'`)'
                            ." VALUES({$players[$intC]["player"]},{$players[$intC]["team"]},{$this->season_id},'".floatval($value)."')"
                            .' ON DUPLICATE KEY UPDATE `'.$tblCOl."` = '".floatval($value)."'";
                    $wpdb->query($query);
                }
            }else{
                $query = 'INSERT IGNORE INTO '.$wpdb->joomsport_playerlist.' (player_id, team_id, season_id)'
                            ." VALUES({$players[$intC]["player"]},{$players[$intC]["team"]},{$this->season_id})";
                    $wpdb->query($query);
            }
            //played matches
            if ($this->single == 1) {
                $matches = get_posts(array(
                    'post_type' => 'joomsport_match',
                    'post_status'      => 'publish',
                    'posts_per_page'   => -1,
                    'meta_query' => array(
                        array(
                        'key' => '_joomsport_seasonid',
                        'value' => $this->season_id),
                        array(
                        'key' => '_joomsport_match_played',
                        'value' => '1'),
                        array(
                            'relation' => 'OR',
                            array(
                            'key' => '_joomsport_home_team',
                            'value' => $players[$intC]["player"]),
                            array(
                            'key' => '_joomsport_away_team',
                            'value' => $players[$intC]["player"]),
                        )


                    ))
                );
                
                $mplayed = count($matches);
                $mplayed_in = 0;
                $mplayed_in = 0;
                $mplayed_out = 0;
                $played_min = 0;
            } else {
                $query = 'SELECT COUNT(s.id)'
                        .' FROM '.$wpdb->joomsport_squad.' as s'
                        .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=s.match_id '
                        . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
                        .' WHERE s.season_id='.$this->season_id
                        .' AND s.team_id = '.$players[$intC]["team"]
                        ." AND s.squad_type='1'"
                        .' AND s.player_id='.$players[$intC]["player"];
                $mplayed = (int) $wpdb->get_var($query);

                $query = 'SELECT COUNT(s.id)'
                        .' FROM '.$wpdb->joomsport_squad.' as s'
                        .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=s.match_id '
                        . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
                        .' WHERE s.season_id='.$this->season_id
                        .' AND s.team_id = '.$players[$intC]["team"]
                        ." AND s.squad_type='2' AND s.is_subs='-1'"
                        .' AND s.player_id='.$players[$intC]["player"];
                $mplayed_in = (int) $wpdb->get_var($query);
                
                $query = 'SELECT COUNT(s.id)'
                        .' FROM '.$wpdb->joomsport_squad.' as s'
                        .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=s.match_id '
                        . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
                        .' WHERE s.season_id='.$this->season_id
                        .' AND s.team_id = '.$players[$intC]["team"]
                        ." AND  s.is_subs='1' AND s.player_subs != 0"
                        .' AND s.player_id='.$players[$intC]["player"];
                $mplayed_out = (int) $wpdb->get_var($query);
                
                $played_min = $this->calcMinutes($players[$intC]["team"], $players[$intC]["player"], $duration);

            }
            $query = 'UPDATE '.$wpdb->joomsport_playerlist.' SET played = '.($mplayed + $mplayed_in)
                    .", career_lineup = ".intval($mplayed)
                    .", career_subsin = ".intval($mplayed_in)
                    .", career_subsout = ".intval($mplayed_out)
                    .", career_minutes = ".intval($played_min)
                    ." WHERE player_id = {$players[$intC]['player']} AND team_id = {$players[$intC]['team']} AND season_id = {$this->season_id}";
            $wpdb->query($query);
        }
        
        $query = 'DELETE FROM '.$wpdb->joomsport_playerlist.' WHERE season_id = "-100"';
        $wpdb->query($query);

    }
    public function calcMinutes($team_id, $player_id, $duration){
        global $wpdb;
        $played_minutes = 0;
            
        $query = 'SELECT s.*,p.post_id'
            .' FROM '.$wpdb->joomsport_squad.' as s'
            .' JOIN '.$wpdb->postmeta.' as p ON p.post_id=s.match_id '
            . ' AND p.meta_key="_joomsport_match_played" AND p.meta_value="1"'
            .' WHERE s.season_id='.$this->season_id
            .' AND s.team_id = '.$team_id
            .' AND s.squad_type != 0 '    
            .' AND s.player_id='.$player_id;
        $matches = $wpdb->get_results($query);
        for($intA=0; $intA < count($matches); $intA++){
            $match = $matches[$intA];
            $match_duration = $duration;
            $metadata = get_post_meta($match->post_id,'_joomsport_match_general',true);
            if(isset($metadata['match_duration']) && $metadata['match_duration'] != ''){
                $match_duration = $metadata['match_duration'];
            }
            if(!$match_duration){
                return 0;
            }
            if($match->squad_type == 1){
                $min = 0;
                if($match->minutes){
                    $min = $match->minutes;
                }
                if(!$min){
                    $min = $match_duration;
                }
                $played_minutes += $min;
            }else{
                $min = 0;
                if($match->minutes){
                    $min = $match->minutes;
                }
                
                if($min){
                    if($match->is_subs == -1){
                        $played_minutes += $match_duration - $min;
                    }else if($match->is_subs == 1){
                        $played_minutes += $min - $match_duration;
                    }
                    
                }
            } 
        }
        return $played_minutes;
    }
}

class JoomSportcalcBoxScore{
    private $match_id = null;

    public function __construct($match_id)
    {
        $this->match_id = $match_id;

        $this->Calculate();
    }
    public function Calculate(){
        global $wpdb;
        $complexBox = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_box.' WHERE complex="0" AND ftype="1" ORDER BY ordering,name', 'OBJECT') ;
        for($intA=0;$intA<count($complexBox);$intA++){
            $field = 'boxfield_'.$complexBox[$intA]->id;
            $options = json_decode($complexBox[$intA]->options,true);
            if(isset($options['depend1'])
                    && $options['depend1']
                    && isset($options['depend2'])
                    && $options['depend2']
                    && $options['depend1'] != $options['depend2']
                    )
            {
                $fieldF = 'boxfield_'.$options['depend1'];
                $fieldT = 'boxfield_'.$options['depend2'];
                $boxm = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_box_match.' WHERE match_id='.$this->match_id, 'OBJECT') ;
                for($intB=0;$intB<count($boxm);$intB++){
                    if(isset($boxm[$intB]->{$fieldF})
                        && $boxm[$intB]->{$fieldF} !== NULL
                        && isset($boxm[$intB]->{$fieldT})
                        && $boxm[$intB]->{$fieldT} !== NULL
                    ){
                        $val = '';
                        switch ($options['calc']) {
                            case 0: //
                                    if($boxm[$intB]->{$fieldT}){
                                        $val = $boxm[$intB]->{$fieldF} / $boxm[$intB]->{$fieldT};
                                    }
                                break;
                            case 1: //
                                    $val = $boxm[$intB]->{$fieldF} * $boxm[$intB]->{$fieldT};
                                break;
                            case 2: //
                                    $val = $boxm[$intB]->{$fieldF} + $boxm[$intB]->{$fieldT};
                                break;
                            case 3: //
                                    $val = $boxm[$intB]->{$fieldF} - $boxm[$intB]->{$fieldT};
                                break;
                            case 4: //
                                    //$val = $boxm[$intB]->{$fieldF} .'/'. $boxm[$intB]->{$fieldT};
                                break;
                            default:
                                break;
                        }
                        if($val){
                            $wpdb->query("UPDATE {$wpdb->joomsport_box_match} SET {$field} = '".$val."' WHERE id={$boxm[$intB]->id}");
                        }
                    }
                }
            }
        }
    }
}