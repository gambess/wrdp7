<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-team.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-player.php';
class classJsportParticipant
{
    private $season_id = null;
    public $single = null;
    public function __construct($season_id, $m_single = null)
    {
        $this->season_id = $season_id;
        $obj = new classJsportSeason($this->season_id);
        if ($m_single != null && $season_id <= 0) {
            $this->single = $m_single;
        } else {
            $this->single = $obj->getSingle();
        }
    }

    public function getParticipants($group_id = null)
    {
        global $jsDatabase,$wpdb;
        $post_type = $this->single ? 'joomsport_player' :'joomsport_team';
        if($group_id){
            $group = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$post_id} AND id={$group_id} ORDER BY ordering"); 
            $partcipants = isset($group->group_partic)?  unserialize($group->group_partic):array();
        }else{
            $args = array(
                'posts_per_page' => -1,
                'offset'           => 0,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => $post_type,
                'post_status'      => 'publish',

            );
            $posts_array = get_posts( $args );
            $partcipants = array();
            for($intA=0;$intA<count($posts_array);$intA++){
                $partcipants[] = $posts_array[$intA]->ID;
            }
        }
        

        return $partcipants;
    }

    public function getParticipiantObj($id)
    {
        if ($id) {
            if ($this->single) {
                $obj = new classJsportPlayer($id, $this->season_id, false);
            } else {
                $obj = new classJsportTeam($id, $this->season_id, false);
            }
        } else {
            $obj = null;
        }

        return $obj;
    }
}
