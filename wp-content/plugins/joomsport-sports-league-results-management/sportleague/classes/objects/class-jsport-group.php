<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
class classJsportGroup
{
    private $season_id = null;

    public function __construct($id)
    {
        $this->season_id = intval($id);
    }

    public function getGroups()
    {
        global $jsDatabase;
        // check if groups enabled
        // get groups
        $query = 'SELECT id,group_name '
                .' FROM '.$jsDatabase->db->joomsport_groups
                .' WHERE s_id = '.$this->season_id
                .' ORDER BY ordering,id';
        $groups = $jsDatabase->select($query);

        // return array

        return $groups;
    }
}
