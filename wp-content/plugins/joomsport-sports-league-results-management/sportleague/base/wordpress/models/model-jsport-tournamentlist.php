<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportTournamentlist
{
    public $row = null;
    public $lists = null;

    public function __construct()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->select('SELECT t.* '
                .'FROM '.DB_TBL_TOURNAMENT.' as t '
                . ' WHERE t.published="1"'
                .' ORDER BY t.name, t.id');
    }
    public function getRow()
    {
        return $this->row;
    }
}
