<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportMatchday
{
    public $matchday_id = null;
    public $lists = null;
    public $row = null;
    public $season = null;

    public function __construct($matchday_id)
    {
        $this->matchday_id = $matchday_id;

        if (!$this->matchday_id) {
            die('ERROR! Matchday ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = get_term_by('id',$this->matchday_id,'joomsport_matchday');
    }
    public function getObject()
    {
        return $this->row;
    }
}
