<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-participant.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-group.php';
require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-getmdays.php';

class classJsportTournMatches
{
    private $id = null;
    private $object = null;
    public $lists = null;
    public $pagination = null;
    const VIEW = 'calendar';

    public function __construct($object)
    {
        $this->object = $object;
        $this->id = $this->object->ID;
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

        $query = 'SELECT * FROM '.$jsDatabase->db->joomsport_season_table.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id
                .' ORDER BY ordering';
        $table = $jsDatabase->select($query);
        
        if (!$table) {
            classJsportPlugins::get('generateTableStanding', array('season_id' => $this->id));
            $query = 'SELECT * FROM '.$jsDatabase->db->joomsport_season_table.' '
                .' WHERE season_id = '.$this->id
                .' AND group_id = '.$group_id
                .' ORDER BY ordering';
            $table = $jsDatabase->select($query);
            //$table = $this->getTournColumnsVar($group_id);
        }
        $this->getExtraFieldsTable($table);
        
        if (isset($this->lists['columns']['curform_chk']) && $this->lists['columns']['curform_chk']) {
            $this->getTeamFormGraph($table);
        }

        return $table;
    }

    public function getTeamFormGraph(&$tbl)
    {
        global $jsDatabase;

        for ($intT = 0; $intT < count($tbl); ++$intT) {
            $tid = $tbl[$intT]->participant_id;
            
            $options = array('team_id' => $tid, 'season_id' => $this->id);

        $options['limit'] = 5;
        $options['played'] = '1';
        $options['ordering_dest'] = 'desc';
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();

        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->ID, false);
                $matches[] = $match->getRowSimple();
            }
        }
        

            $from_str = '';
            $matches = array_reverse($matches);
            for ($intA = 0; $intA < 5; ++$intA) {
                $from_str .= jsHelper::JsFormViewElement(isset($matches[$intA]) ? ($matches[$intA]) : null, $tid);
            }
            $tbl[$intT]->curform_chk = $from_str;
        }

        //return $from_str;
    }

    public function calculateTable($allcolumns = false, $group_id = 0)
    {
        global $jsDatabase;
        //get knockout
        $this->getKnock();
        //$this->getPlayoffs();
        //get matchdays group
        
        $show_table = false;
        
        if(get_bloginfo('version') < '4.5.0'){
            $tx = get_terms('joomsport_matchday',array(
                "hide_empty" => false
            ));
        }else{
            $tx = get_terms(array(
                "taxonomy" => "joomsport_matchday",
                "hide_empty" => false
            ));
        }

        for($intA=0;$intA<count($tx);$intA++){
            $term_meta = get_option( "taxonomy_".$tx[$intA]->term_id."_metas");

            if($term_meta['season_id'] == $this->id && $term_meta['matchday_type'] != 1){

                    $show_table = true;
                    break;
                
            }
        }

        /*$mdays_count = get_posts(array(
                    'post_type' => 'joomsport_match',
                    'post_status'      => 'publish',
                    'posts_per_page'   => -1,
                    'meta_query' => array(
                        array(
                        'key' => '_joomsport_seasonid',
                        'value' => $this->id),
                        
                        /*array(
                        'key' => '_joomsport_match_played',
                        'value' => '1')*/
                   /* ))
                );*/

        if ($show_table || (!isset($this->lists['knockout']) || !count($this->lists['knockout']))) {
            //get groups
            $groupsObj = new classJsportGroup($this->id);
            $groups = $groupsObj->getGroups();
            $this->lists['columns'] = $this->getTournColumns($allcolumns);
            $this->lists['groups'] = $groups;
            $columnsCell = array();
            //get participants
            if (count($groups)) {
                foreach ($groups as $group) {
                    if($group_id == 0 || $group_id == $group->id){
                        $columnsCell[$group->group_name] = $this->getTable($group->id);
                    }
                }
            } else {
                $columnsCell[] = $this->getTable(0);
            }
            $this->lists['columnsCell'] = $columnsCell;
        }
        //get season options
        //get variables for table view
        // multisort
        // save to db
    }

    public function getTournColumns($allcolumns)
    {
        global $jsDatabase, $jsConfig;
        $this->lists['available_options'] = $jsConfig->getStandingColumns();
        $this->lists['available_options'][]= array('emblem_chk' => array());
        $this->lists['available_options_short'] = json_decode($jsConfig->get('columnshort'),true);
        
        $lists = array();
        $listsss = get_post_meta($this->id,'_joomsport_season_standindgs',true);

        if($allcolumns){
            if($listsss && count($listsss)){
                foreach ($listsss as $key => $value) {
                    $lists[$key] = $value;
                }
            }
        }

        if($listsss && count($listsss)){
            foreach ($listsss as $key => $value) {
                if($value)
                $lists[$key] = $value;
            }
        }

        return $lists;
    }

    public function getKnock()
    {
        global $jsDatabase;
        
        if($this->id){
            $options = array();
            $options['season_id'] = $this->id;
            $options['mday_type'] = '1';
            $mdays = classJsportgetmdays::getMdays($options);
            $t_single = JoomSportHelperObjects::getCurrentTournamentType($this->id);
            $this->lists['knockout'] = array();
            wp_enqueue_style('jscssbracket',plugin_dir_url( __FILE__ ).'../../../assets/css/drawBracketBE.css');
        
            for ($intA = 0; $intA < count($mdays); ++$intA) {
                //if ($mdays[$intA]->t_type == 1) {
                require_once JOOMSPORT_SL_PATH. '/../includes/classes/matchday_types/joomsport-class-matchday-knockout.php';
                $knockObj = new JoomSportClassMatchdayKnockout($mdays[$intA]->id);
                $this->lists['knockout'][] = $knockObj->getView();
                    //$this->lists['knockout'][] = $knockObj->lists['knockout'];
                /*} elseif ($mdays[$intA]->t_type == 2) {
                    require_once JS_PATH_OBJECTS.'matchdays'.DIRECTORY_SEPARATOR.'class-jsport-knockout_de.php';
                    $knockObj = new classJsportKnockoutDe($mdays[$intA], $mdays[$intA]->t_single);
                    $this->lists['knockout'][] = $knockObj->lists['knockout'];
                }*/
            }
        }
    }

    public function getPartById($partId)
    {
        $obj = new classJsportParticipant($this->id);
        $participant = $obj->getParticipiantObj($partId);

        return $participant;
    }

    //calendar
    public function getCalendar($options = array())
    {
        global $jsConfig;

        $this->lists['enable_search'] = $jsConfig->get('enbl_calmatchsearch',1);
        if (classJsportRequest::get('tmpl') == 'component') {
            $this->lists['enable_search'] = 0;
        }
        if ($this->lists['enable_search']) {
            $this->lists['options']['tourn'] = '<a href="javascript:void(0);" id="aSearchFieldset">'.__('Search the matches','joomsport-sports-league-results-management').'</a>';
        }
        if(!isset($options['season_id']) && !is_array($options['season_id'])){
            $options['season_id'] = $this->id;
        }
        
        $filtersvar = classJsportRequest::get('filtersvar');

        if ($filtersvar) {
            classJsportSession::set('filtersvar_calendar_'.$this->id, json_encode($filtersvar));
        }
        $apply_filters = false;
        if (classJsportSession::get('filtersvar_calendar_'.$this->id)) {
            $filters = json_decode(classJsportSession::get('filtersvar_calendar_'.$this->id));

            $this->lists['filtersvar'] = $filters;
            if ($filters->mday) {
                $options['matchday_id'] = $filters->mday;
                $apply_filters = true;
            }
            if ($filters->partic) {
                $options['team_id'] = $filters->partic;
                $apply_filters = true;
            }
            if ($filters->date_from) {
                $options['date_from'] = $filters->date_from;
                $apply_filters = true;
            }
            if ($filters->date_to) {
                $options['date_to'] = $filters->date_to;
                $apply_filters = true;
            }
            if ($filters->place) {
                $options['place'] = $filters->place;
                $apply_filters = true;
            }
        }
        $this->lists['apply_filters'] = $apply_filters;

        $this->lists['filters'] = array();
        $this->lists['filters']['mday_list'] = classJsportgetmdays::getMdays($options);
        $partObj = new classJsportParticipant($this->id);
        $partic = $partObj->getParticipants();
        for ($intA = 0; $intA < count($partic); ++$intA) {
            $item = $partObj->getParticipiantObj($partic[$intA]);
            $this->lists['filters']['partic_list'][$partic[$intA]] = $item->getName(false);
        }
        $link = classJsportLink::calendar('', $this->id, true);
        
        // allready played matches
        $playedMatches = 0;

        if(count($options) == 1){
            $query = new WP_Query( 
                     array(
                        'posts_per_page' => -1,
                        'post_type'        => 'joomsport_match',
                        'post_status'      => 'publish',
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => '_joomsport_seasonid',
                                'value' => $options['season_id'],
                                'compare' => (is_array($options['season_id'])?'IN':'=')
                            ),
                            array(
                                'key' => '_joomsport_match_played',
                                'value' => '1'
                            )
                        )    
                        ) 
                     );

            $playedMatches = $query->found_posts;
        }
        //end
        
        $pagination = new classJsportPagination($link, $playedMatches);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();

        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList(JoomSportHelperObjects::getTournamentType($this->id));

        $pagination->setPages($rows['count']);
        $this->pagination = $pagination;
        $matches = array();
        //require_once JS_PATH_ENV_CLASSES . 'class-jsport-calc-player-list.php';
        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->ID, JSCONF_ENBL_MATCH_TOOLTIP);
                $match->getPlayerObj($match->lists['m_events_home']);
                $match->getPlayerObj($match->lists['m_events_away']);
                $matches[] = $match->getRow();
                //$obj = new classJsportCalcPlayerList($row->id);
            }
        }
        $type = 2;
        $this->lists['ef_table'] = $ef = classJsportExtrafields::getExtraFieldListTable($type);
        if (count($ef) && count($matches)) {
            for ($intA = 0; $intA < count($matches); ++$intA) {
                for ($intB = 0; $intB < count($ef); ++$intB) {
                    $matches[$intA]->{'ef_'.$ef[$intB]->id} = classJsportExtrafields::getExtraFieldValue($ef[$intB], $matches[$intA]->id, $type, $this->id);
                }
            }
        }
        return $matches;
    }

    public function getPlayoffs()
    {
        global $jsDatabase;

        $query = 'SELECT m.*,m.id as mid,m.team1_id as home, m.team2_id as away, md.m_name '
                .' FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m'
                .'  WHERE  m.m_id = md.id AND md.s_id = '.$this->id.''
                .' AND m.published = 1 AND md.is_playoff = 1'
                .' AND md.t_type = 0 '
                .' ORDER BY md.ordering,md.id,m.id';

        $rows = $jsDatabase->select($query);
        $matches = array();
        if ($rows) {
            foreach ($rows as $row) {
                $match = new classJsportMatch($row->id, false);
                $matches[] = $match->getRow();
                //$obj = new classJsportCalcPlayerList($row->id);
            }
        }
        
        
        
        $this->lists['playoffs'] = $matches;
    }

    public function getExtraFieldsTable(&$table)
    {
        $type = JoomSportHelperObjects::getTournamentType($this->id) ? 0 : 1;
        $this->lists['ef_table'] = $ef = classJsportExtrafields::getExtraFieldListTable($type);
        if (count($ef) && count($table)) {
            for ($intA = 0; $intA < count($table); ++$intA) {
                for ($intB = 0; $intB < count($ef); ++$intB) {
                    $table[$intA]->{'ef_'.$ef[$intB]->id} = classJsportExtrafields::getExtraFieldValue($ef[$intB], $table[$intA]->participant_id, $type, $this->id);
                }
            }
        }
    }

    public function getCalendarView()
    {
        return self::VIEW;
    }
}
