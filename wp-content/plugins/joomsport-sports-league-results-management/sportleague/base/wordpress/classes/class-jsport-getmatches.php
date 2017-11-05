<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportgetmatches
{
    public static function getMatches($options, $single = 0)
    {

        $result_array = array();

        if ($options) {
            extract($options);
        }
        global $jsDatabase;
        
        
        $argsSeasons = array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'post_type'        => 'joomsport_season',
                'post_status'      => 'publish'
        );
        $aSeasons = get_posts( $argsSeasons );
        $seasonsArray = array();
        foreach($aSeasons as $aSeason){
            $seasonsArray[] = $aSeason->ID;
        }
        
        if (!isset($ordering)) {
            $ordering = 'md.ordering, m.m_date, m.m_time';
        }
        
        if (isset($ordering_dest) && $ordering_dest == 'desc'){
            $orderfunc = 'joomsport_ordermatchbydatetimeDesc';
        }else{
            $orderfunc = 'joomsport_ordermatchbydatetime';
        } 
        
        $mArray = array(
            'posts_per_page' => isset($team_id)?-1:((isset($limit) && $limit != 0)?$limit:-1),
            'offset'           => isset($team_id)?0:(isset($offset)?$offset:0),
            'post_type'        => 'joomsport_match',
            'post_status'      => 'publish',
            );
        
        
        if(isset($matchday_id) && $matchday_id){
            $mArray['tax_query'] = array(
                array(
                'taxonomy' => 'joomsport_matchday',
                'field' => 'term_id',
                'terms' => $matchday_id)
            );
        }
        if(isset($season_id) && is_array($season_id)){
            $mArray['meta_query'][] = 
                array(
                'key' => '_joomsport_seasonid',
                'value' => $season_id,
                'compare' => 'IN'    
            );
        }else if(isset($season_id) && $season_id > 0){
            $mArray['meta_query'][] = 
                array(
                'key' => '_joomsport_seasonid',
                'value' => $season_id
            );


        }else{
            if(count($seasonsArray)){
                $mArray['meta_query'][] = 
                    array(
                    'key' => '_joomsport_seasonid',
                    'value' => $seasonsArray,
                    'compare' => 'IN'    
                );
            }else{
                $mArray['meta_query'][] = 
                    array(
                    'key' => '_joomsport_seasonid',
                    'value' => '-1',
                    'compare' => '='    
                );
            }
            
            
        }
        $mArray['meta_query'][] = array(
                    'key'     => '_joomsport_match_date',
                );
        $mArray['meta_query'][] = array(
                    'key'     => '_joomsport_match_time',
                    
                );
        if(isset($played)){
            $mArray['meta_query'][] = 
                array(
                'key' => '_joomsport_match_played',
                'value' => $played
            );
        }
        
        
        if(isset($date_from)){
            $mArray['meta_query'][] = 
                array(
                'key' => '_joomsport_match_date',
                'value' => $date_from,
                'compare' => '>='    
            );
        }
        if(isset($date_exclude)){
            $mArray['meta_query'][] = 
                array(
                'key' => '_joomsport_match_date',
                'value' => $date_exclude,
                'compare' => '!='    
            );
        }
        if(isset($date_to)){
            $mArray['meta_query'][] = 
                array(
                'key' => '_joomsport_match_date',
                'value' => $date_to,
                'compare' => '<='    
            );
        }
        
        add_filter('posts_orderby',array('classJsportgetmatches', $orderfunc));
            
        $matches = new WP_Query($mArray);
        $matches = $matches->posts;
        
       
        
        remove_filter('posts_orderby',array('classJsportgetmatches', $orderfunc));
        
         $mArray['posts_per_page'] = -1;
         $mArray['offset'] = 0;


        $matches_count = new WP_Query($mArray);
        $matches_count = ($matches_count->post_count);

        if(isset($team_id) && $matches){
            $matches_id = array();
            
            foreach($matches as $m){
                $matches_id[] = $m->ID;
            }
            
            if(count($matches_id)){
                if(isset($place) && $place == '1'){
                    $selteam = array(
                    'key' => '_joomsport_home_team',
                    'value' => $team_id
                    );
                }elseif(isset($place) && $place == '2'){
                    $selteam = array(
                    'key' => '_joomsport_away_team',
                    'value' => $team_id
                    );
                }else{
                    $selteam = array('relation' => 'OR',
                        array(
                    'key' => '_joomsport_home_team',
                    'value' => $team_id
                    ),
                        
                    array(
                    'key' => '_joomsport_away_team',
                    'value' => $team_id
                    ) 
                    );
                }
                add_filter('posts_orderby',array('classJsportgetmatches', $orderfunc));
                $matches = new WP_Query(array(
                    'posts_per_page' => (isset($limit) && $limit != 0)?$limit:-1,
                    'offset'           => isset($offset)?$offset:0,
                    'post_type'        => 'joomsport_match',
                    'post_status'      => 'publish',
                    'order'     => 'DESC',

                    'post__in'          =>     $matches_id,
                    'meta_query' => array(
                        array('relation' => 'AND',
                        array('key'     => '_joomsport_seasonid'),
                        array('key'     => '_joomsport_match_date'),
                        array('key'     => '_joomsport_match_time')),
                        $selteam

                        )
                    )
                );
                remove_filter('posts_orderby',array('classJsportgetmatches', $orderfunc));
                $matches = $matches->posts;
                
                $matchesC = new WP_Query(array(
                    'posts_per_page' => -1,
                    'offset'           => 0,
                    'post_type'        => 'joomsport_match',
                    'post_status'      => 'publish',
                    'order'     => 'DESC',

                    'post__in'          =>     $matches_id,
                    'meta_query' => array(
                        array('relation' => 'AND',
                        array('key'     => '_joomsport_seasonid'),
                        array('key'     => '_joomsport_match_date'),
                        array('key'     => '_joomsport_match_time')),
                        $selteam

                        )
                    )
                );
                
                $matches_count = ($matchesC->post_count);
            }
        }


        $result_array['list'] = $matches;
        $result_array['count'] = $matches_count;

        return $result_array;
    }
    public static function  joomsport_ordermatchbydatetime($orderby) {
        global $wpdb;
        return str_replace($wpdb->prefix.'posts.post_date',$wpdb->prefix.'postmeta.meta_value,  mt1.meta_value,mt2.meta_value, '.$wpdb->prefix.'posts.post_date', $orderby);

   }
   public static function  joomsport_ordermatchbydatetimeDesc($orderby) {
        global $wpdb;
        return str_replace($wpdb->prefix.'posts.post_date',$wpdb->prefix.'postmeta.meta_value desc,  mt1.meta_value desc,mt2.meta_value desc, '.$wpdb->prefix.'posts.post_date', $orderby);

   }
}
