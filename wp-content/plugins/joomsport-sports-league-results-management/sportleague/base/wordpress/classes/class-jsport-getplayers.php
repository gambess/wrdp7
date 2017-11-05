<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-event.php';
class classJsportgetplayers
{
    public static function getPlayersFromTeam($options, $player_id = null)
    {
        global $jsDatabase, $wpdb;
        $result_array = array();
        if ($options) {
            extract($options);
        }

        if (!isset($ordering) || !$ordering || $ordering == ' ') {
            $ordering = 'id';
        }

        $stdoptions = '';
         $stdoptions = "std"; 

        $query = "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '".$jsDatabase->db->joomsport_playerlist."'
                AND table_schema = '".DB_NAME."'
            AND column_name LIKE 'eventid%'";
        $cols = $jsDatabase->select($query);

        $sql_select = '';
        for ($intQ = 0; $intQ < count($cols); ++$intQ) {
            $res_type = 0;
            $eventid = (int) str_replace('eventid_', '', $cols[$intQ]->COLUMN_NAME);
            if($eventid){
                $query = 'SELECT result_type FROM '.$jsDatabase->db->joomsport_events." WHERE id={$eventid}";
                $res_type = $jsDatabase->selectValue($query);
            }
            if($res_type == 1){
                $sql_select .= ',COALESCE(AVG('.$cols[$intQ]->COLUMN_NAME.'),0) as '.$cols[$intQ]->COLUMN_NAME;
            }else{
                $sql_select .= ',COALESCE(SUM('.$cols[$intQ]->COLUMN_NAME.'),0) as '.$cols[$intQ]->COLUMN_NAME;
            }
            
        }

        if ((isset($season_id) && $season_id)) {
            //$season = new modelJsportSeason($season_id);
            $season_array = array();
            $seasonObj = new classJsportSeason($season_id);
            if($seasonObj->isComplex() == '1'){
                $childrens = $seasonObj->getSeasonChildrens();
                if(count($childrens)){
                    foreach($childrens as $ch){
                        array_push($season_array, $ch->ID);
                    }
                }
            } 
            
            $single = $seasonObj->getSingle();
            if ($single == 1) {
                $query = 'SELECT pl.*, pl.player_id as id'
                    .$sql_select
                    .' FROM '.DB_TBL_PLAYER_LIST.' as pl'
                        .' JOIN '.$wpdb->prefix.'posts as p ON p.ID = pl.player_id AND p.post_status = "publish"'
                    .' WHERE 1 = 1 AND pl.player_id IS NOT NULL'
                    .(isset($player_id) && $player_id ? ' AND pl.player_id = '.$player_id : '');
                if(isset($season_id) && count($season_array)){
                    $query .= ' AND pl.season_id IN ('.implode(',', $season_array).') ';
                }else if(isset($season_id) && $season_id){
                    $query .= ' AND pl.season_id = '.$season_id;
                
                }
                
                $query .= ' GROUP BY pl.player_id'
                    .' ORDER BY pl.'.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');

                $query_count = 'SELECT COUNT(pl.id)'
                    .' FROM '.DB_TBL_PLAYER_LIST.' as pl'
                        .' JOIN '.$wpdb->prefix.'posts as p ON p.ID = pl.player_id AND p.post_status = "publish"'
                    .' WHERE 1 = 1 AND pl.player_id IS NOT NULL'
                    .(isset($player_id) && $player_id ? ' AND pl.player_id = '.$player_id : '');
                if(isset($season_id) && count($season_array)){
                    $query_count .= ' AND pl.season_id IN ('.implode(',', $season_array).') ';
                }else if(isset($season_id) && $season_id){
                    $query_count .= ' AND pl.season_id = '.$season_id;
                
                }
                    $query_count .=' GROUP BY pl.player_id';
            } else {
                if($stdoptions != 'std'){
                    $sql_select = '';
                }
               $query = "SELECT l.* ".$sql_select." FROM ".DB_TBL_PLAYER_LIST." as l"
                       .' JOIN '.$wpdb->prefix.'posts as p ON p.ID = l.player_id AND p.post_status = "publish"'
                        . " WHERE 1=1 AND l.player_id IS NOT NULL"
                        .(isset($team_id) && $team_id ? ' AND l.team_id = '.$team_id : '');
                if(isset($season_id) && count($season_array)){
                    $query .= ' AND l.season_id IN ('.implode(',', $season_array).') ';
                }else if(isset($season_id) && $season_id){
                    $query .= ' AND l.season_id = '.$season_id;
                
                }

                $query .= (isset($player_id) && $player_id ? ' AND l.player_id = '.$player_id : '');
                if($stdoptions == 'std'){
                    $query .= ' GROUP BY l.player_id';
                }
                $query .= ' ORDER BY '.$ordering
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');
                
                

                $query_count = "SELECT (l.id) FROM ".DB_TBL_PLAYER_LIST." as l"
                       .' JOIN '.$wpdb->prefix.'posts as p ON p.ID = l.player_id AND p.post_status = "publish"'
                        . " WHERE 1=1 AND l.player_id IS NOT NULL"
                        .(isset($team_id) && $team_id ? ' AND l.team_id = '.$team_id : '');
                if(isset($season_id) && count($season_array)){
                    $query_count .= ' AND l.season_id IN ('.implode(',', $season_array).') ';
                }else if(isset($season_id) && $season_id){
                    $query_count .= ' AND l.season_id = '.$season_id;
                
                }

                $query_count .= (isset($player_id) && $player_id ? ' AND l.player_id = '.$player_id : '');
                if($stdoptions == 'std'){
                    $query_count .= ' GROUP BY l.player_id';
                }
            }
        } else {
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
            
            
            $query = "SELECT l.*"
                    .$sql_select
                    . " FROM ".DB_TBL_PLAYER_LIST." as l"
                    .' JOIN '.$wpdb->prefix.'posts as p ON p.ID = l.player_id AND p.post_status = "publish"'    
                        . " WHERE 1=1 AND l.player_id IS NOT NULL "
                        .(isset($team_id) ? ' AND l.team_id = '.$team_id : '')
                        .(isset($player_id) && $player_id ? ' AND l.player_id = '.$player_id : '')
                        .(count($seasonsArray)?' AND l.season_id IN ('.implode(',',$seasonsArray).')':' AND l.season_id=-1')
                    .((isset($groupby) && !$groupby)?' GROUP BY l.season_id,l.team_id':' GROUP BY l.player_id')   
                    .' ORDER BY l.player_id'
                    .(isset($limit) && $limit ? " LIMIT {$limit}" : '')
                    .(isset($limit) && $limit && isset($offset) ? " OFFSET {$offset}" : '');
                    
            $query_count = "SELECT (l.id)"
                    .$sql_select
                    . " FROM ".DB_TBL_PLAYER_LIST." as l"
                    .' JOIN '.$wpdb->prefix.'posts as p ON p.ID = l.player_id AND p.post_status = "publish"'    
                        . " WHERE 1=1 AND l.player_id IS NOT NULL "
                        .(isset($team_id) ? ' AND l.team_id = '.$team_id : '')
                        .(isset($player_id) && $player_id ? ' AND l.player_id = '.$player_id : '')
                .(count($seasonsArray)?' AND l.season_id IN ('.implode(',',$seasonsArray).')':' AND l.season_id=-1')
                    .((isset($groupby) && !$groupby)?' GROUP BY l.season_id,l.team_id':' GROUP BY l.player_id')    
                    .' ORDER BY l.player_id';
        }

        $players = $jsDatabase->select($query);

        $players_count = count($jsDatabase->selectColumn($query_count));

        $result_array['list'] = $players;
        $result_array['count'] = $players_count;

        return $result_array;
    }

    public static function getPlayersEvents($season_id = 0)
    {
        global $jsDatabase;

        $events = $jsDatabase->select('SELECT DISTINCT(ev.id), ev.e_name as name'
                . ' FROM '.$jsDatabase->db->joomsport_events.' as ev'
                . ' JOIN '.$jsDatabase->db->joomsport_match_events.' as mev ON ev.id=mev.e_id'
                . ' WHERE ev.player_event="1"'
                . ($season_id?(is_array($season_id)?' AND mev.season_id IN ('.implode(',',$season_id).')':' AND mev.season_id='.$season_id):'')
                . ' ORDER BY ev.ordering', 'OBJECT') ;


        $events_array = array();
        for ($intA = 0; $intA < count($events); ++$intA) {

            $objEvent = new classJsportEvent($events[$intA]->id);
            $events_array['eventid_'.$events[$intA]->id] = $objEvent;
        }

        return $events_array;
    }
    public static function getPlayersPlayedMatches($player_id, $team_id = 0, $season_id = 0)
    {
        global $jsDatabase;
        $query = 'SELECT SUM(played)'
                .' FROM '.DB_TBL_PLAYER_LIST
                ." WHERE player_id = {$player_id}"
                .($team_id ? ' AND team_id = '.$team_id : '')
                .($season_id ? ' AND season_id = '.$season_id : '');

        return (int) $jsDatabase->selectValue($query);
    }
}
