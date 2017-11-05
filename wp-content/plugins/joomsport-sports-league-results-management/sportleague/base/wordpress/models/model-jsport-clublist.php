<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportClublist
{
    public $row = null;
    public $lists = null;

    public function __construct()
    {
        global $jsDatabase;
        $this->row = $jsDatabase->select('SELECT t.*, t.c_name as name '
                .'FROM '.DB_TBL_CLUB.' as t '
                .' ORDER BY t.c_name, t.id');
    }
    public function getRow()
    {
        return $this->row;
    }
}
