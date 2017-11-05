<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-team.php';
require_once JOOMSPORT_PATH_CLASSES.'class-jsport-matches.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-match.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-club.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-venue.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-person.php';
require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-getplayers.php';

class classJsportTeam
{
    private $id = null;
    public $season_id = null;
    public $object = null;
    public $lists = null;
    public $model = null;
    public $matches_latest = 5;
    public $matches_next = 5;
    public $ef_type = null;
    public $ef_sort_id = null;

    public function __construct($id = 0, $season_id = null, $loadLists = true)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = (int) classJsportRequest::get('tid');
            $this->id = get_the_ID();
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        
        if (!$this->id) {
            die('ERROR! Team ID not DEFINED');
        }

        $this->loadObject($loadLists);
    }

    private function loadObject($loadLists)
    {
        $obj = $this->model = new modelJsportTeam($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($loadLists) {
            $this->lists = $obj->loadLists();
            $this->setHeaderOptions();
        }
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getName($linkable = false, $itemid = 0)
    {
        global $jsConfig;
        $pp = get_post($this->id);
        if(empty($pp)){
            return '';
        }
        if ($pp->post_status == 'publish' && get_post_status($this->id) != 'private') {
            
            if (!$linkable || ($jsConfig->get('enbl_teamlinks',1) == '0' && (!in_array($this->id, $jsConfig->get('yteams',array())) || $jsConfig->get('enbl_teamhgllinks') != '1'))) {

                return get_the_title($this->id);

            }
            $html = '';
            if ($this->id > 0 && $this->id) {
                $html = classJsportLink::team(get_the_title($this->id), $this->id, $this->season_id, false, $itemid);
            }

            return $html;
        }else{
            return get_the_title($this->id);
        }    
    }

    public function getDefaultPhoto()
    {

        if ($this->lists['def_img']) {
            return $this->lists['def_img'];
        }

        return JOOMSPORT_LIVE_URL_IMAGES_DEF.JSCONF_TEAM_DEFAULT_IMG;
    }
    public function getEmblem($linkable = true, $type = 0, $class = 'emblInline', $width = 0, $itemid = 0)
    {
        global $jsConfig;
        $pp = get_post($this->id);
        if (empty($pp) || $pp->post_status != 'publish' || get_post_status($this->id) == 'private') {
            $linkable = false;
        }
        $html = '';
        if (has_post_thumbnail( $this->id ) ){
            
            //$image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->id ), 'single-post-thumbnail' );
            $image= wp_get_attachment_image_src(get_post_thumbnail_id( $this->id ), array($jsConfig->get('teamlogo_height',40),'auto'));

            $html = $image[0];
        }
        $html = jsHelperImages::getEmblem($html, 1, $class, $width);
        if ($linkable && $jsConfig->get('enbl_teamlogolinks',1) == '1') {
            $html = classJsportLink::team($html, $this->id, $this->season_id, '', $itemid);
        }

        return $html;
    }
    public function getRow()
    {
        return $this;
    }
    public function getTabs()
    {
        global $jsConfig;
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = __('Team','joomsport-sports-league-results-management');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'flag';
        if($jsConfig->get('enbl_club')){
            $this->getClub();
        }
        
        if($jsConfig->get('unbl_venue',1)){
            $this->getVenue();
        }
        //matches
        $this->getMatches();
        if (count($this->lists['matches'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_matches';
            $tabs[$intA]['title'] = __('Matches','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = '';
            $this->lists['pagination'] = $this->lists['match_pagination'];
            $tabs[$intA]['text'] = jsHelper::getMatches($this->lists['matches'], $this->lists, false);
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }

        $this->getPlayers();
        //roster
        $show_rostertab = $jsConfig->get('show_rostertab','1');
        if($show_rostertab){
            if (count($this->lists['players'])) {
                ++$intA;
                $tabs[$intA]['id'] = 'stab_players';
                $tabs[$intA]['title'] = __('Roster','joomsport-sports-league-results-management');
                $tabs[$intA]['body'] = 'player-list-photo.php';
                $tabs[$intA]['text'] = '';
                $tabs[$intA]['class'] = '';
                $tabs[$intA]['ico'] = 'users';
            }
        }
        //players
        $show_playertab = $jsConfig->get('show_playertab');
        $show_playerstattab = $jsConfig->get('show_playerstattab','1');
        if($show_playerstattab){
            if (count($this->lists['players']) || ($show_playertab == '1' && !count($this->lists['players']))) {
                ++$intA;
                $tabs[$intA]['id'] = 'stab_players_stats';
                $tabs[$intA]['title'] = __('Players Stats','joomsport-sports-league-results-management');
                $tabs[$intA]['body'] = 'player-list.php';
                $tabs[$intA]['text'] = '';
                $tabs[$intA]['class'] = '';
                $tabs[$intA]['ico'] = 'chart';
            }
        }
        
        //box score
        $this->getBoxScoreList();
        if (isset($this->lists['boxscore_home']) && ($this->lists['boxscore_home'] != '')) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_boxscore';
            $tabs[$intA]['title'] = __('Box Score','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = $this->lists['boxscore_home'];
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'boxscore';
        }
        if ($this->_displayOverviewTab() && count($this->lists['matches'])) {
            $obj = new modelJsportTeam($this->id, $this->season_id);
            $this->lists['curposition'] = $obj->getCurrentPosition();
            $this->getLatestMatches();
            $this->getNextMatches();
            ++$intA;
            $tabs[$intA]['id'] = 'stab_overview';
            $tabs[$intA]['title'] = __('Overview','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = 'team-overview.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'chart';
        }

        //photos
        if (count($this->lists['photos']) > 1) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_photos';
            $tabs[$intA]['title'] = __('Photos','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = 'gallery.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'photos';
        }

        return $tabs;
    }

    public function getMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);

        $link = classJsportLink::team('', $this->id, $this->season_id, true);
        $pagination = new classJsportPagination($link);
        $options['limit'] = $pagination->getLimit();
        $options['offset'] = $pagination->getOffset();
        $pagination->setAdditVar('jscurtab', 'stab_matches');
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();
        $pagination->setPages($rows['count']);
        $this->lists['match_pagination'] = $pagination;
        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->ID, false);
                $matches[] = $match->getRowSimple();
            }
        }
        $this->lists['matches'] = $matches;
    }

    public function getPlayers($options = array())
    {
        global $jsConfig, $jsDatabase;
        
        $attr = array('team_id' => $this->id, 'season_id' => $this->season_id);
        
        $plorder = $jsConfig->get('pllist_order');
        if($plorder){
            $plorder = explode('_', $plorder);
            if(isset($plorder[1])){
                if($plorder[1] == '2'){
                    $attr['ordering'] = 'eventid_'.intval($plorder[0]).' desc';
                }elseif($plorder[1] == '1'){
                    $ordering_ef = intval($plorder[0]);
                    if(isset($ordering_ef) && $ordering_ef){
                        $sql = "SELECT field_type"
                            . " FROM {$jsDatabase->db->joomsport_ef} WHERE id={$ordering_ef}";
                        $ef_type = $jsDatabase->selectValue($sql);
                        $this->ef_sort_id = $ordering_ef;
                    }
                    if($ef_type == 3){
                        $sql = "SELECT id as name, eordering as value"
                            . " FROM {$jsDatabase->db->joomsport_ef_select} WHERE fid={$ordering_ef}";
                        $ef_ordering = $jsDatabase->select($sql);
                        $ef_ordering_assocc = array();
                        for($intZ=0;$intZ<count($ef_ordering);$intZ++){
                            $ef_ordering_assocc[$ef_ordering[$intZ]->name] = $ef_ordering[$intZ]->value;
                        }
                        
                    }
                    
                }
            }
        }else{
            $attr['ordering'] = 'p.post_title';
        }
        
        $players = classJsportgetplayers::getPlayersFromTeam($attr);

        $players_object_gr = array();
        $players = $players['list'];
        
        
        $groupBySelect = $jsConfig->get('set_teampgplayertab_groupby',0);
        if(isset($options['groupBySelect'])){
            $groupBySelect = $options['groupBySelect'];
        }
        $playerPhotoTab = $jsConfig->get('show_rostertab',1);
        if(isset($options['playerPhotoTab'])){
            $playerPhotoTab = $options['playerPhotoTab'];
        }
        $playerNumber = $this->lists['playerfieldnumber'] = $jsConfig->get('set_playerfieldnumber',0);
        if($playerNumber){
            $query = 'SELECT ef.*'
                .' FROM '.$jsDatabase->db->joomsport_ef.' as ef '
                ." WHERE ef.id=".intval($playerNumber);

            $efPlayerNumber = $jsDatabase->selectObject($query);
        }
        $playerCard = $this->lists['playercardef'] = $jsConfig->get('set_playercardef',0);
        if($playerCard){
            $query = 'SELECT ef.*'
                .' FROM '.$jsDatabase->db->joomsport_ef.' as ef '
                ." WHERE ef.id=".intval($playerCard);

            $efplayerCard = $jsDatabase->selectObject($query);
        }
        $statplyers = array();
            $playerGroups = array('0');
        if ($players) {
            
            if($groupBySelect && $playerPhotoTab){
                $query = "SELECT sel_value FROM {$jsDatabase->db->joomsport_ef_select} WHERE fid={$groupBySelect} ORDER BY eordering";
                $efgroup = $jsDatabase->selectColumn($query);
                if(count($efgroup)){
                    $playerGroups = array_merge($playerGroups,$efgroup);
                }
            }
            foreach ($playerGroups as $value) {
                $players_object_gr[$value] = array();
            }
            
            $count_players = count($players);
            $this->lists['ef_table'] = $ef = classJsportExtrafields::getExtraFieldListTable(0, false);
            for ($intC = 0; $intC < $count_players; ++$intC) {
                $row = $players[$intC];
                if($row->player_id){
                    $uGroup = '0';
                    $obj = new classJsportPlayer($row->player_id, $this->season_id);
                    $obj->lists['tblevents'] = $row;
                    $players_object = array();
                    $players_object = $obj->getRowSimple();
                    if ($jsConfig->get('played_matches')) {
                        $players_object->played_matches = classJsportgetplayers::getPlayersPlayedMatches($row->player_id, $this->id, $this->season_id);
                    }
                    for ($intB = 0; $intB < count($ef); ++$intB) {
                        $players_object->{'ef_'.$ef[$intB]->id} = classJsportExtrafields::getExtraFieldValue($ef[$intB], $row->player_id, 0, $this->season_id);
                    
                        if(isset($ef_type) && $ef_type == 3){
                            $orderValue = -1;
                            $meta = get_post_meta($row->player_id,'_joomsport_player_ef',true);
                            $meta_s = get_post_meta($row->player_id,'_joomsport_player_ef_'.$this->season_id,true);
                            if(isset($meta[$ef[$intB]->id])){
                                if(isset($ef_ordering_assocc[$meta[$ef[$intB]->id]])){
                                    $orderValue = $ef_ordering_assocc[$meta[$ef[$intB]->id]];
                                }
                            }elseif(isset($meta_s[$ef[$intB]->id])){
                                if(isset($ef_ordering_assocc[$meta_s[$ef[$intB]->id]])){
                                    $orderValue = $ef_ordering_assocc[$meta_s[$ef[$intB]->id]];
                                }
                            }
                            $players_object->{'ef0_'.$ef[$intB]->id} = $orderValue;
                    
                        }
                        if($groupBySelect && $playerPhotoTab){
                            if($ef[$intB]->id == $groupBySelect){
                                if($players_object->{'ef_'.$ef[$intB]->id}){
                                    $uGroup = $players_object->{'ef_'.$ef[$intB]->id};
                                }
                            }
                        }
                    }
                    if(isset($efPlayerNumber) && isset($efPlayerNumber->id)){
                        $players_object->{'ef_'.$efPlayerNumber->id} = classJsportExtrafields::getExtraFieldValue($efPlayerNumber, $row->player_id, 0, $this->season_id);
                    
                    }
                    if(isset($efplayerCard) && isset($efplayerCard->id)){
                        $players_object->{'ef_'.$efplayerCard->id} = classJsportExtrafields::getExtraFieldValue($efplayerCard, $row->player_id, 0, $this->season_id);
                    
                    }
                    
                    $statplyers[] = $players_object;
                    $players_object_gr[$uGroup][] = $players_object;
                }
            }
            
            if(isset($ef_type)){
                
                $this->ef_type = $ef_type;
                if(count($players_object_gr)){
                    foreach ($players_object_gr as $uGrKey => $uGrVal) {
                        usort($players_object_gr[$uGrKey], array($this,'sortPlayers'));
                    }
                }
                if(count($statplyers)){
                    usort($statplyers, array($this,'sortPlayers'));
                }
                
            }
        }
        
        
        //staff list
        $this->lists['team_staff'] = array();
        $sql = "SELECT *"
                . " FROM {$jsDatabase->db->joomsport_ef}"
                . " WHERE type='1' AND published = '1' AND field_type='5'"
                .(classJsportUser::getUserId() ? '' : " AND faccess='0'")
                ." ORDER BY ordering";
        $coaches = $jsDatabase->select($sql);
        
        
        for($intA=0;$intA<count($coaches);$intA++){
            $options = $coaches[$intA]->options;
            if($options){
                $person_id = 0;
                $options_decode = json_decode($options, true);
                if(isset($options_decode["in_roster"])){
                    if($coaches[$intA]->season_related){
                        $efArr = get_post_meta($this->id,'_joomsport_team_ef_'.$this->season_id,true);
                    }else{
                        $efArr = get_post_meta($this->id,'_joomsport_team_ef',true);
                    }
                    if(isset($efArr[$coaches[$intA]->id])){
                        $person_id = $efArr[$coaches[$intA]->id];
                    }
                }
                if($person_id){
                    $obj = new classJsportPerson($person_id, $this->season_id);
                    $this->lists['team_staff'][] = array("name"=>$coaches[$intA]->name, "obj"=>$obj);
                }
            }
        }
        
        
        if ($jsConfig->get('played_matches')) {
            $this->lists['played_matches_col'] = __('Match played','joomsport-sports-league-results-management');
        }
        
        $this->lists['players'] = $players_object_gr;
        
        
        
        $this->lists['players_Stat'] = $statplyers;
        
        

        //events
        
        if($this->season_id){
            $this->lists['events_col'] = classJsportgetplayers::getPlayersEvents($this->season_id);
        }else{
            $seasons = JoomSportHelperObjects::getParticipiantSeasons($this->id);
            $seasons_arr = array();
            if(count($seasons)){
                foreach($seasons as $seas){
                    
                    for($intA=0;$intA<count($seas);$intA++){
                        
                        $seasons_arr[] = $seas[$intA]->id;
                    }
                }
            }
            if(!count($seasons_arr)){
                $seasons_arr = 0;
            }
            $this->lists['events_col'] = classJsportgetplayers::getPlayersEvents($seasons_arr);
        }
    }
    public function sortPlayers($a,$b){
        if($this->ef_type == '3'){
            if ($a->{'ef0_'.$this->ef_sort_id} == $b->{'ef0_'.$this->ef_sort_id}) {
                return 0;
            }
            return ($a->{'ef0_'.$this->ef_sort_id} < $b->{'ef0_'.$this->ef_sort_id}) ? -1 : 1;
        }else{
            if ($a->{'ef_'.$this->ef_sort_id} == $b->{'ef_'.$this->ef_sort_id}) {
                return 0;
            }
            return ($a->{'ef_'.$this->ef_sort_id} < $b->{'ef_'.$this->ef_sort_id}) ? -1 : 1;
        }
        
        
    }
    public function getDescription()
    {
        $t_descr = get_post_meta($this->id,'_joomsport_team_about',true);
        return classJsportText::getFormatedText($t_descr);
    }

    private function _displayOverviewTab()
    {
        global $jsConfig;

        return $jsConfig->get('tlb_position') || $jsConfig->get('tlb_form') || $jsConfig->get('tlb_latest') || $jsConfig->get('tlb_next');
    }
    public function getLatestMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);
        $options['ordering_dest'] = 'desc';
        $options['limit'] = $this->matches_latest;
        $options['played'] = '1';

        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();

        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->ID, false);
                $matches[] = $match->getRowSimple();
            }
        }

        $this->lists['matches_latest'] = $matches;
    }
    public function getNextMatches()
    {
        $options = array('team_id' => $this->id, 'season_id' => $this->season_id);

        $options['limit'] = $this->matches_next;
        $options['played'] = '0';
        $obj = new classJsportMatches($options);
        $rows = $obj->getMatchList();

        $matches = array();

        if ($rows['list']) {
            foreach ($rows['list'] as $row) {
                $match = new classJsportMatch($row->ID, false);
                $matches[] = $match->getRowSimple();
            }
        }
        $this->lists['matches_next'] = $matches;
    }
    public function setHeaderOptions()
    {
        global $jsConfig;
        if ($this->season_id > 0) {
            $this->lists['options']['calendar'] = $this->season_id;
            $this->lists['options']['standings'] = $this->season_id;
            if ($this->lists['enbl_join']) {
                $this->lists['options']['jointeam']['seasonid'] = $this->season_id;
                $this->lists['options']['jointeam']['teamid'] = $this->id;
            }
        }
        $this->lists['options']['tourn'] = $this->lists['tourn'];
        $img = $this->getEmblem(false);
        //social
        if ($jsConfig->get('jsbp_team') == '1') {
            $this->lists['options']['social'] = true;
            //classJsportAddtag::addCustom('og:title', $this->getName(false));

            if ($img) {
                //classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$this->object->t_emblem);
            }
            //classJsportAddtag::addCustom('og:description', $this->getDescription());
        }
        $imgtitle = '';
        if ($img) {
            $imgtitle = $img.'&nbsp;';
        }
        $this->lists['options']['title'] = $imgtitle.$this->getName(false);
    }
    public function getYourTeam()
    {
        global $jsConfig;

        return (in_array($this->id, $jsConfig->get('yteams',array())) && $jsConfig->get('highlight_team')) ? $jsConfig->get('yteam_color') : '';
    }
    public function getClub($linkable = true){
        $term_list = wp_get_post_terms($this->id, 'joomsport_club', array("fields" => "all"));

        if ($term_list && count($term_list)) {
            $club = new classJsportClub($term_list[0]->term_id);

            $this->lists['ef'][__('Club','joomsport-sports-league-results-management')] =  $club->getName($linkable);

        }
        return false;
    }
    public function getVenue($linkable = true){
        $tVenue = get_post_meta($this->id,'_joomsport_team_venue',true);
        if ($tVenue) {
            $venue = new classJsportVenue($tVenue);
            if(isset($venue->object->post_status) && $venue->object->post_status != 'trash'){
            $this->lists['ef'][__('Venue','joomsport-sports-league-results-management')] = $venue->getName($linkable);
            }
        }
        return false;
    }
    public function getBoxScoreList(){
        $this->lists['boxscore_home'] = $this->model->getBoxScore();
        
    }
}
