<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once __DIR__.DIRECTORY_SEPARATOR.'matchday_types'.DIRECTORY_SEPARATOR.'joomsport-class-matchday-round.php';

class JoomSportClassMatchday{
    public static function getViewEdit($mdID){
        $obj = self::getMdayType($mdID);
        return $obj->getViewEdit();
    }
    public static function save($mdID){
        $obj = self::getMdayType($mdID);
        $obj->save();
        $metas = get_option("taxonomy_{$mdID}_metas");
        $season_id = $metas['season_id'];
        do_action('joomsport_update_standings',$season_id);
        do_action('joomsport_update_playerlist',$season_id);
    }
    public static function saveMatch($mdID){
        $obj = self::getMdayType($mdID);
        $obj->saveMatch();
        $metas = get_option("taxonomy_{$mdID}_metas");
        $season_id = $metas['season_id'];
        do_action('joomsport_update_standings',$season_id);
        do_action('joomsport_update_playerlist',$season_id);
    }
    public static function getMdayType($mdID){
        $metas = get_option("taxonomy_{$mdID}_metas");
        switch ($metas['matchday_type']){
            case '1':
                
                break;
            default:
                $obj = new JoomSportClassMatchdayRound($mdID);
                break;
        }
        return $obj;
        
    }
}