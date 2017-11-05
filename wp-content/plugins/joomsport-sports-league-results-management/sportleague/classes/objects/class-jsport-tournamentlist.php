<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-tournamentlist.php';

class classJsportTournamentlist
{
    private $object = null;
    public $lists = null;

    public function __construct()
    {
        $obj = new modelJsportTournamentlist();
        $this->object = $obj->getRow();
        $this->lists['options']['title'] = __('League list','joomsport-sports-league-results-management');
    }

    public function getRow()
    {
        return $this->object;
    }
}
