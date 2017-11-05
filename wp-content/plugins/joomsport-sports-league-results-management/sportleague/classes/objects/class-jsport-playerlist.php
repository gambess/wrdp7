<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-getplayers.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-player.php';
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-dlists.php';
class classJsportPlayerlist
{
    public $season_id = null;
    public $lists = null;

    public function __construct($season_id = null)
    {
        $this->season_id = $season_id;
        if (!$this->season_id) {
            if(classJsportRequest::get('sid') != ''){
                $this->season_id = classJsportRequest::get('sid');
            }else{
                $this->season_id = get_the_ID();
            }
        }
        $this->loadObject();
        $this->lists['options']['title'] = __('Player List','joomsport-sports-league-results-management');
        $this->setHeaderOptions();
    }

    private function loadObject()
    {
        $options['season_id'] = $this->season_id;

        $link = classJsportLink::playerlist($this->season_id);
        if (classJsportRequest::get('sortf')) {
            $link .= '&sortf='.classJsportRequest::get('sortf');
            $link .= '&sortd='.classJsportRequest::get('sortd');
        }
        $pagination = new classJsportPagination($link);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();
        if (classJsportRequest::get('sortf')) {
            $options['ordering'] = classJsportRequest::get('sortf').' '.classJsportRequest::get('sortd');
        }

        $players = classJsportgetplayers::getPlayersFromTeam($options);
        $pagination->setPages($players['count']);
        $this->lists['pagination'] = $pagination;

        $players = $players['list'];
        $players_object = array();

        if ($players) {
            $count_players = count($players);
            $this->lists['ef_table'] = $ef = classJsportExtrafields::getExtraFieldListTable(0,false);
            for ($intC = 0; $intC < $count_players; ++$intC) {
                $row = $players[$intC];
                
                $obj = new classJsportPlayer($row->player_id, $this->season_id);
                $obj->lists['tblevents'] = $row;
                
                $players_object[$intC] = $obj->getRowSimple();
                
                for ($intB = 0; $intB < count($ef); ++$intB) {
                    $players_object[$intC]->{'ef_'.$ef[$intB]->id} = classJsportExtrafields::getExtraFieldValue($ef[$intB], $row->player_id, 0, $this->season_id);
                }
                
            }
            
        }
        $this->lists['players'] = $this->lists['players_Stat'] = $players_object;
        
        //events
        $this->lists['events_col'] = classJsportgetplayers::getPlayersEvents($this->season_id);
    }

    public function getRow()
    {
        return $this;
    }
    public function setHeaderOptions()
    {
        if ($this->season_id) {
            $this->lists['options']['standings'] = $this->season_id;
            $this->lists['options']['calendar'] = $this->season_id;
        }
        $this->lists['options']['tourn'] = classJsportDlists::getSeasonsPlayerList($this->season_id);
    }
}
