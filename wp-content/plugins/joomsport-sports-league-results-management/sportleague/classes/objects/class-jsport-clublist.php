<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_MODELS.'model-jsport-clublist.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-club.php';
class classJsportClublist
{
    private $object = null;
    public $lists = null;

    public function __construct()
    {
        $obj = new modelJsportClublist();
        $object = $obj->getRow();
        for ($intA = 0; $intA < count($object); ++$intA) {
            $this->object[] = new classJsportClub($object[$intA]->id);
        }
        $this->lists['options']['title'] = __('Club list','joomsport-sports-league-results-management');
    }

    public function getRow()
    {
        return $this->object;
    }
}
