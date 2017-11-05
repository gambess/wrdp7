<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportEvent
{
    public $event_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id)
    {
        $this->event_id = $id;

        if (!$this->event_id) {
            die('ERROR! Event ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->selectObject('SELECT * '
                .'FROM '.DB_TBL_EVENTS.''
                .' WHERE id = '.$this->event_id);
    }
    public function getRow()
    {
        return $this->row;
    }
}
