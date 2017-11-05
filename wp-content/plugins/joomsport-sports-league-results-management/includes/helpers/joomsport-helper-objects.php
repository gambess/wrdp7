<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

class JoomSportHelperObjects{
    public static function getCurrentTournamentType(){
        global $joomsportSettings;
        return $joomsportSettings->get('tournament_type');
    }
    public static function getSeasons($type = null, $show_complex = true){
        $results = array();
        if($type === NULL){
            $type = self::getCurrentTournamentType();
        }
        $args = array(
            'posts_per_page' => -1,
            'offset'           => 0,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_type'        => 'joomsport_season',
            'post_status'      => 'publish',
            
        );

        if($show_complex == false){
            $args['meta_query'] = array(
                // meta query takes an array of arrays, watch out for this!
                array(
                   array(
                        'key'     => '_joomsport_season_complex',
                        'value'   => '1',
                        'compare' => 'NOT EXISTS',
                        )
                )
             );
        }
        
        $posts_array = get_posts( $args );

        for($intA=0;$intA<count($posts_array);$intA++){
            $term_list = wp_get_post_terms($posts_array[$intA]->ID, 'joomsport_tournament', array("fields" => "all"));
            if(count($term_list)){
                $term_meta = get_option( "taxonomy_".$term_list[0]->term_id."_metas");
                if($type == -1 || $term_meta['t_single'] == $type){
                    $std = new stdClass();
                    $std->name = esc_attr($posts_array[$intA]->post_title);
                    $std->id = $posts_array[$intA]->ID;
                    if(!isset($results[$term_list[0]->name])){
                        $results[esc_attr($term_list[0]->name)] = array();
                    }
                    array_push($results[esc_attr($term_list[0]->name)], $std);
                }
            }
        }
        
        //var_dump($results);
        return $results;
    }
    public static function getParticipiants($season_id, $group_id = 0){
        global $wpdb;
        $t_single = self::getTournamentType($season_id);
        $partObj = array();
        $participiants = get_post_meta($season_id,'_joomsport_season_participiants',true);
        if($group_id){
            $group_partic = $wpdb->get_var("SELECT group_partic FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} AND id={$group_id} ORDER BY ordering"); 
            $participiants = isset($group_partic) ? unserialize($group_partic):array();
            
        }
        if($participiants && count($participiants)){
            $partObj = get_posts(array(
                'post_type' => $t_single?'joomsport_player':'joomsport_team',
                'include' => $participiants,
                'orderby' => 'title',
                'order' => 'ASC')
            );
        }
        return $partObj;
    }
    public static function getTournamentType($season_id){
        $term_list = wp_get_post_terms($season_id, 'joomsport_tournament', array("fields" => "all"));
        if(count($term_list)){
            $term_meta = get_option( "taxonomy_".$term_list[0]->term_id."_metas");
            return $term_meta['t_single'];
            
        }
    }
    public static function getMatchType($mID){
        $md = wp_get_post_terms($mID,'joomsport_matchday');
        $mdID = $md[0]->term_id;

        $metas = get_option("taxonomy_{$mdID}_metas");

        $t_single = JoomSportHelperObjects::getTournamentType($metas['season_id']);

        switch ($metas['matchday_type']){
            case '1': //knockout
                if($t_single == '1'){
                    $obj = new JoomSportClassMatchRoundSingle($mID);
                }else{
                    $obj = new JoomSportClassMatchRoundTeam($mID);
                }
                break;
            default:
                if($t_single == '1'){
                    $obj = new JoomSportClassMatchRoundSingle($mID);
                }else{
                    $obj = new JoomSportClassMatchRoundTeam($mID);
                }
                
                break;
        }
        return $obj;
        
    }
    public static function getMatchSeason($mID){
        $md = wp_get_post_terms($mID,'joomsport_matchday');
        if(isset($md[0])){
            $mdID = $md[0]->term_id;

            $metas = get_option("taxonomy_{$mdID}_metas");
            return $metas['season_id'];
        }else{
            return 0;
        }
    }
    
    public static function getParticipiantSeasons($participiant_id){
        $results = array();
        $args = array(
            'posts_per_page' => -1,
            'offset'           => 0,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_type'        => 'joomsport_season',
            'post_status'      => 'publish',
        );
        $posts_array = get_posts( $args );
        for($intA=0;$intA<count($posts_array);$intA++){
            $metadata = get_post_meta($posts_array[$intA]->ID,'_joomsport_season_participiants',true);
            $term_list = wp_get_post_terms($posts_array[$intA]->ID, 'joomsport_tournament', array("fields" => "all"));
            if(count($term_list) && count($metadata) && $metadata != ''){
                $term_meta = get_option( "taxonomy_".$term_list[0]->term_id."_metas");
                if(in_array($participiant_id, $metadata)){
                    $std = new stdClass();
                    $std->name = esc_attr($posts_array[$intA]->post_title);
                    $std->id = $posts_array[$intA]->ID;
                    if(!isset($results[$term_list[0]->name])){
                        $results[esc_attr($term_list[0]->name)] = array();
                    }
                    array_push($results[esc_attr($term_list[0]->name)], $std);
                }
            }
        }
        //var_dump($results);
        return $results;
    }
    public static function getPlayerSeasons($participiant_id){
        $results = array();
        $args = array(
            'posts_per_page' => -1,
            'offset'           => 0,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_type'        => 'joomsport_season',
            'post_status'      => 'publish',
        );
        $posts_array = get_posts( $args );
        for($intA=0;$intA<count($posts_array);$intA++){
            $metadata = get_post_meta($posts_array[$intA]->ID,'_joomsport_season_participiants',true);
            $term_list = wp_get_post_terms($posts_array[$intA]->ID, 'joomsport_tournament', array("fields" => "all"));
            if(count($term_list) && count($metadata) && $metadata != ''){
                $term_meta = get_option( "taxonomy_".$term_list[0]->term_id."_metas");
                if($term_meta['t_single'] == '1'){
                    if(in_array($participiant_id, $metadata)){
                        
                        if($posts_array[$intA]->post_parent){
                            $std = new stdClass();
                            $std->name = esc_attr(get_the_title($posts_array[$intA]->post_parent));
                            $std->id = $posts_array[$intA]->post_parent;
                            if(!isset($results[$term_list[0]->name])){
                                $results[esc_attr($term_list[0]->name)] = array();
                            }
                            if(!in_array($std, $results[esc_attr($term_list[0]->name)]))
                            array_push($results[esc_attr($term_list[0]->name)], $std);
                        }
                        
                        
                        $std = new stdClass();
                        $std->name = esc_attr($posts_array[$intA]->post_title);
                        $std->id = $posts_array[$intA]->ID;
                        if(!isset($results[$term_list[0]->name])){
                            $results[esc_attr($term_list[0]->name)] = array();
                        }
                        array_push($results[esc_attr($term_list[0]->name)], $std);
                    }
                }else{

                    if($metadata){
                        foreach($metadata as $meta){
                            $playersin = get_post_meta($meta,'_joomsport_team_players_'.$posts_array[$intA]->ID,true);
                            $playersin = JoomSportHelperObjects::cleanJSArray($playersin);
                            if($playersin && count($playersin)){
                                if(in_array($participiant_id, $playersin)){
                                    
                                    if($posts_array[$intA]->post_parent){
                                        $std = new stdClass();
                                        $std->name = esc_attr(get_the_title($posts_array[$intA]->post_parent));
                                        $std->id = $posts_array[$intA]->post_parent;
                                        if(!isset($results[$term_list[0]->name])){
                                            $results[esc_attr($term_list[0]->name)] = array();
                                        }
                                        if(!in_array($std, $results[esc_attr($term_list[0]->name)]))
                                        array_push($results[esc_attr($term_list[0]->name)], $std);
                                    }
                                    
                                    
                                    $std = new stdClass();
                                    $std->name = esc_attr($posts_array[$intA]->post_title);
                                    $std->id = $posts_array[$intA]->ID;
                                    if(!isset($results[$term_list[0]->name])){
                                        $results[esc_attr($term_list[0]->name)] = array();
                                    }
                                    if(!in_array($std, $results[esc_attr($term_list[0]->name)]))
                                    array_push($results[esc_attr($term_list[0]->name)], $std);
                                    
                                    
                                    
                                }
                            }
                        }
                    }
                }
            }
        }
        //var_dump($results);
        return $results;
    }
    public static function getPlayerTeams($season_id, $player_id){
        $result = array();
        $teams = self::getParticipiants($season_id);
        for($intA=0;$intA<count($teams);$intA++){
            $playersin = get_post_meta($teams[$intA]->ID,'_joomsport_team_players_'.$season_id,true);
            $playersin = JoomSportHelperObjects::cleanJSArray($playersin);
            if($playersin && in_array($player_id, $playersin)){
                $result[] = $teams[$intA]->ID;
            }
        }
        return $result;
    }
    public static function wp_dropdown_posts( $post, $tax = null ) {
            $dropdown_args = array(
                    'post_type'        => 'joomsport_season',
                    'selected'         => $post->post_parent,
                    'name'             => 'parent_id',
                    'show_option_none' => __('(no parent)'),
                    'sort_column'      => 'menu_order, post_title',
                    'echo'             => 0,

            );
            if(!$tax){
                return '<select name="parent_id" id="parent_id">
                                <option value="">(no parent)</option>
                        </select>';
            }
            if($tax){
                $houseQuery = new WP_Query(
                    array(
                        'post_type' => 'joomsport_season',
                        'order'     => 'ASC',
                        'post_status' => 'publish',
                        'orderby'   => 'title',
                        'nopaging' => true,
                        'tax_query' => array(
                        array(
                            'taxonomy' => 'joomsport_tournament',
                            'field' => 'term_id',
                            'terms' => $tax)
                        ))
                );

                $incl = array();
                if($houseQuery->have_posts()){
                    for($intA = 0; $intA < count($houseQuery->posts);$intA++){
                        $incl[] = $houseQuery->posts[$intA]->ID;
                    }
                }

                $dropdown_args['include'] = $incl;
            }

            /**
             * Filter the arguments used to generate a Pages drop-down element.
             *
             * @since 3.3.0
             *
             * @see wp_dropdown_pages()
             *
             * @param array   $dropdown_args Array of arguments used to generate the pages drop-down.
             * @param WP_Post $post          The current WP_Post object.
             */
            $dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
            $pages = wp_dropdown_pages( $dropdown_args );
            return $pages;
    }
    
    public static function getGroupEdit( $group_id, $post_id ) {
        global $joomsportSettings,$wpdb;
        $group = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$post_id} AND id={$group_id} ORDER BY ordering"); 
        $metadata = isset($group->group_partic)?  unserialize($group->group_partic):array();
        $groptions = isset($group->options)?  unserialize($group->options):array();
        $grtype = isset($groptions['grtype'])?$groptions['grtype']:0;
        $posts_array = self::getParticipiants($post_id);
        $postObj = get_post($post_id);
        ?>
        <div>
            <label><?php echo __('Group name','joomsport-sports-league-results-management');?></label>
            <input type="text" name="group_title" id="js_group_title" value="<?php echo isset($group->group_name)?$group->group_name:""?>" />
        </div>
        <br />
        <div>
            <label><?php echo __('Participants','joomsport-sports-league-results-management');?></label>
            <select name="group_part[]" id="js_group_part" class="jswf-chosen-select" data-placeholder="<?php echo __('Add item','joomsport')?>" multiple>
            <?php
            if($posts_array){
                foreach ($posts_array as $tm) {
                    $selected = '';
                    if(in_array($tm->ID, $metadata)){
                        $selected = ' selected';
                    }
                    echo '<option value="'.$tm->ID.'" '.$selected.'>'.$tm->post_title.'</option>';
                }
            }
            ?>
            </select>
        </div>
        <?php 
        if($postObj->post_parent){
        ?>
        <br />
        <div>
            <label><?php echo __('Use previous season match results','joomsport-sports-league-results-management');?></label>
            <br />
            <?php
            $is_field = array();
            $is_field[] = JoomSportHelperSelectBox::addOption(0, __("None", "joomsport-sports-league-results-management"));
            $is_field[] = JoomSportHelperSelectBox::addOption(1, __("All", "joomsport-sports-league-results-management"));
            $is_field[] = JoomSportHelperSelectBox::addOption(2, __("Between group participants", "joomsport-sports-league-results-management"));
            
            echo JoomSportHelperSelectBox::Simple('groptions[roundtype]', $is_field,$grtype,' id="grroundtype" ',false);

            ?>
        </div>
        <?php } ?>
        <input type="hidden" name="js_seas_groupid" id="js_seas_groupid" value="<?php echo $group_id?>" />
        <?php
    }  
    public static function getPreviousSeason($season_id){
        $seasonObj = get_post($season_id);
        $houseQuery = new WP_Query(
            array(
                'post_type' => 'joomsport_season',
                'post_parent' => $seasonObj->post_parent,
                'order'     => 'ASC',
                'post_status' => 'publish',
                'orderby'   => 'menu_order title',
            )    
        );

        $prev_id = 0;
        $prevchild = 0;
        if($houseQuery->have_posts()){
            for($intA = 0; $intA < count($houseQuery->posts);$intA++){
                if($houseQuery->posts[$intA]->ID == $season_id){
                    $prevchild = $prev_id;
                }
                $prev_id = $houseQuery->posts[$intA]->ID;
            }
        }
        return $prevchild;
    }
    
    public static function getSeasonsGroups($type = -1, $show_complex = false){
        global $wpdb;
        $results = array();
        if($type === NULL){
            $type = self::getCurrentTournamentType();
        }
        $args = array(
            'posts_per_page' => -1,
            'offset'           => 0,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_type'        => 'joomsport_season',
            'post_status'      => 'publish',
            
        );

        if($show_complex == false){
            $args['meta_query'] = array(
                // meta query takes an array of arrays, watch out for this!
                array(
                   array(
                        'key'     => '_joomsport_season_complex',
                        'value'   => '1',
                        'compare' => 'NOT EXISTS',
                        )
                )
             );
        }
        
        $posts_array = get_posts( $args );

        for($intA=0;$intA<count($posts_array);$intA++){
            $term_list = wp_get_post_terms($posts_array[$intA]->ID, 'joomsport_tournament', array("fields" => "all"));
            if(count($term_list)){
                $term_meta = get_option( "taxonomy_".$term_list[0]->term_id."_metas");
                if($type == -1 || $term_meta['t_single'] == $type){
                    
                    $season_name = esc_attr($posts_array[$intA]->post_title);
                    $season_id = $posts_array[$intA]->ID;
                    $key = esc_attr($term_list[0]->name).' '.$season_name;
                    if(!isset($results[$key])){
                        $results[$key] = array();
                    }
                    $std = new stdClass();
                    $std->name = __('Display All season groups', 'joomsport-sports-league-results-management');
                    $std->id = $season_id.'|0';
                    array_push($results[$key], $std);
                    $groups = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} ORDER BY ordering"); 
                    if(is_array($groups)){
                        foreach ($groups as $group) {
                            $std = new stdClass();
                            $std->name = $group->group_name;
                            $std->id = $season_id.'|'.$group->id;
                            array_push($results[$key], $std);
                        }
                    }
                }
            }
        }
        
        //var_dump($results);
        return $results;
    }
    
    
    public static function getPlayersByEFonFE($team_id,$season_id,$efid,$efval){
        //$all_players = get_post_meta($team_id,'_joomsport_team_players_'.$season_id,true);
        $all_players = classJsportgetplayers::getPlayersFromTeam(array('team_id' => $team_id, 'season_id' => $season_id));
        $all_players = $all_players['list'];
        $group_players = array();
        for($intA=0;$intA<count($all_players);$intA++){
            $meta = get_post_meta($all_players[$intA]->player_id,'_joomsport_player_ef',true);
            if(isset($meta[$efid]) && $meta[$efid] == $efval){
                $group_players[] = $all_players[$intA]->player_id;
            }
        }
        return $group_players;
    }
    public static function getPlayersByEF($team_id,$season_id,$efid,$efval){
        $all_players = get_post_meta($team_id,'_joomsport_team_players_'.$season_id,true);
        $all_players = JoomSportHelperObjects::cleanJSArray($all_players);
        $group_players = array();
        if($all_players){
            for($intA=0;$intA<count($all_players);$intA++){
                $meta = get_post_meta($all_players[$intA],'_joomsport_player_ef',true);
                if(isset($meta[$efid]) && $meta[$efid] == $efval){
                    $group_players[] = $all_players[$intA];
                }
            }
        }
        return $group_players;
    }
    public static function cleanJSArray($array){
        $new_array = array();
        if(is_array($array)){
            for ($intA=0;$intA<count($array);$intA++) {
                $post = get_post($array[$intA]);
                if($post instanceof WP_Post){
                    $new_array[] = $array[$intA];
                }
            }
        }
        return $new_array;
    }
}