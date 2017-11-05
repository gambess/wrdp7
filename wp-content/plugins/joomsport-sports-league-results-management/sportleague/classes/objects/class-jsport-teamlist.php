<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-getteams.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-team.php';
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-dlists.php';
class classJsportTeamlist
{
    private $season_id = null;
    public $lists = null;

    public function __construct($season_id = null)
    {
        $this->season_id = $season_id;
        if (!$this->season_id) {
            $this->season_id = classJsportRequest::get('sid');
        }
        $this->loadObject();
        $this->lists['options']['title'] = __('Team List','joomsport-sports-league-results-management');
        $this->lists['options']['tourn'] = classJsportDlists::getSeasonsTeamList($this->season_id);
    }

    private function loadObject()
    {
        $options['season_id'] = $this->season_id;
        $link = classJsportLink::teamlist($this->season_id);
        $pagination = new classJsportPagination($link);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();

        $teams = classJsportgetteams::getTeams($options);
        $pagination->setPages($teams['count']);
        $this->lists['pagination'] = $pagination;
        $teams = $teams['list'];
        $players_object = array();

        if ($teams) {
            $count_teams = count($teams);
            for ($intC = 0; $intC < $count_teams; ++$intC) {
                $row = $teams[$intC];
                $obj = new classJsportTeam($row->id, $this->season_id);

                $players_object[] = $obj->getRow();
            }
        }
        $this->lists['teamsObj'] = $players_object;
    }

    public function getRow()
    {
        return $this;
    }
}
