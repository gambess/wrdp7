<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-participant.php';

class classJsportTournRace
{
    private $id = null;
    private $object = null;
    public $lists = null;
    public $VIEW = 'race_calendar';

    public function __construct($object)
    {
        $this->object = $object;
        $this->id = $this->object->s_id;
    }

    private function loadObject()
    {
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getTable($group_id)
    {
        global $jsDatabase;
        $query = 'SELECT * FROM '.DB_TBL_SEASON_TABLE.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id
                .' ORDER BY ordering';
        $table = $jsDatabase->select($query);
        if (!$table) {
            $table = $this->getTournColumnsVar($group_id);
        }

        return $table;
    }

    public function calculateTable()
    {
        global $jsDatabase;
        //get groups

        $this->lists['columns'] = $this->getTournColumns();

        $columnsCell = array();
        //get participants

        $columnsCell[] = $this->getTable(0);

        $this->lists['columnsCell'] = $columnsCell;
        //get season options
        //get variables for table view
        // multisort
        // save to db
    }

    public function getTournColumns()
    {
        global $jsDatabase;
        $this->lists['available_options'] = array(
            'point_chk' => __('Points','joomsport-sports-league-results-management'),
            );

        return $this->lists['available_options'];
    }

    public function getTournColumnsVar($group_id)
    {
        global $jsDatabase;
        $obj = new classJsportParticipant($this->id);
        $participants = $obj->getParticipants($group_id);

        $array = array();
        $intA = 0;
        if (count($participants)) {
            foreach ($participants as $participant) {
                $query = 'SELECT SUM(points) as rc '
                            .' FROM '.DB_TBL_MATCHDAY.' as md '
                            .' JOIN '.DB_TBL_ROUNDS.' as r '
                            .' ON r.md_id = md.id AND md.s_id = '.$this->id
                            .' JOIN '.DB_TBL_ROUNDS_PARTICIPIANTS.' as p'
                            .' ON p.round_id = r.id'
                            ." WHERE r.round_status = '2' AND p.participiant_id = {$participant->id}";

                $pts = $jsDatabase->selectValue($query);
                //$array[$intA] = new stdClass();
                $array[$intA]['g_id'] = 0;
                $array[$intA]['id'] = $participant->id;

                $partObj = $obj->getParticipiantObj($participant->id);
                $array[$intA]['name'] = $partObj->getName();
                //$array[$intA]['yteam'] = $teams_your?$teams_your_color:'';
                $array[$intA]['point_chk'] = $pts + $participant->bonus_point;

                ++$intA;
            }

            $array = $this->sortTable($array);
            //$this->saveToDB($array, $group_id);
            //$array = $this->getTable($group_id);
        }

        return $array;
    }

    public function sortTable($array)
    {
        $sort_arr = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $sort_arr[$key][$uniqid] = $value;
            }
        }
        array_multisort($sort_arr['point_chk'], SORT_DESC, $array);
        $new_array = array();
        for ($intA = 0; $intA < count($array); ++$intA) {
            $new_array[$intA] = new stdClass();
            $new_array[$intA]->g_id = 0;
            $new_array[$intA]->id = $array[$intA]['id'];

            $new_array[$intA]->id = $array[$intA]['name'];

            $new_array[$intA]->id = $array[$intA]['point_chk'];
            $json = array('point_chk' => $array[$intA]['point_chk'], 'id' => $array[$intA]['id']);
            $new_array[$intA]->options = json_encode($json);
        }

        return $new_array;
    }

    public function getPartById($partId)
    {
        $obj = new classJsportParticipant($this->id);
        $participant = $obj->getParticipiantObj($partId);

        return $participant;
    }

    public function getCalendar($options)
    {
        global $jsDatabase;

        if (isset($options['matchday_id']) && intval($options['matchday_id'])) {
            $query = ' SELECT * FROM '.DB_TBL_ROUNDS_EXTRACOL
                .' WHERE round_id = '.intval($options['matchday_id'])
                .' ORDER BY ordering';

            $this->lists['extracol'] = $jsDatabase->select($query);

            $query = 'Select * FROM '.DB_TBL_ROUNDS.' WHERE md_id = '.intval($options['matchday_id']);
            $query .= ' ORDER BY ordering desc';

            $rounds = $jsDatabase->select($query);
            $obj = new classJsportParticipant($this->id);
            for ($intA = 0; $intA < count($rounds); ++$intA) {
                if ($obj->single) {
                    $query = "Select r.*, CONCAT(t.first_name,' ',t.last_name) as t_name,t.id as t_id"
                            .' FROM '.DB_TBL_ROUNDS_PARTICIPIANTS.' as r'
                            .' JOIN '.DB_TBL_PLAYERS.' as t'
                            .' ON r.participiant_id = t.id'
                        .' WHERE r.round_id = '.intval($rounds[$intA]->id)
                        .' ORDER BY r.rank asc';
                } else {
                    $query = 'Select r.*, t.t_name,t.id as t_id'
                            .' FROM '.DB_TBL_ROUNDS_PARTICIPIANTS.' as r'
                            .' JOIN '.DB_TBL_TEAMS.' as t'
                            .' ON r.participiant_id = t.id'
                        .' WHERE r.round_id = '.intval($rounds[$intA]->id)
                        .' ORDER BY r.rank asc';
                }

                $rounds[$intA]->res = $jsDatabase->select($query);
            }
            $this->VIEW = 'race_matchday';

            return $rounds;
        } else {
            $query = 'SELECT * FROM '.DB_TBL_MATCHDAY
                    .' WHERE s_id = '.$this->id
                    .' ORDER BY start_date desc, id';

            $mdays = $jsDatabase->select($query);

            return $mdays;
        }
    }
    public function getCalendarView()
    {
        return $this->VIEW;
    }
}
