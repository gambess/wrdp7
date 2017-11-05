<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-player.php';
require_once JOOMSPORT_PATH_MODELS.'model-jsport-season.php';
class modelJsportMatch
{
    public $match_id = null;
    public $lists = null;
    public $row = null;
    public $season = null;

    public function __construct($match_id)
    {
        $this->match_id = $match_id;

        if (!$this->match_id) {
            die('ERROR! Match ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = get_post($this->match_id);
    }
    public function getSeasonID()
    {
        global $jsDatabase;

        $season_id = JoomSportHelperObjects::getMatchSeason($this->match_id);

        return $season_id;
    }

    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->match_id, '2', 0);
        $this->getPhotos();
        $this->getPlayerEvents();
        $this->getTeamEvents();
        $this->getLineUps();
        $this->getMaps();
    }

    public function getPhotos()
    {
        $photos = get_post_meta($this->match_id,'vdw_gallery_id',true);
        $this->lists['photos'] = array();
        if ($photos && count($photos)) {
            foreach ($photos as $photo) {
                $image_arr = wp_get_attachment_image_src($photo, 'joomsport-thmb-medium');
                if (($image_arr[0])) {
                    $this->lists['photos'][] = array("id" => $photo, "src" => $image_arr[0]);
                }
            }
        }
        
    }

    public function getPlayerEvents()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        if($season_id){
            $home_team = get_post_meta( $this->match_id, '_joomsport_home_team', true );
            $away_team = get_post_meta( $this->match_id, '_joomsport_away_team', true );
            $sObj = new modelJsportSeason($season_id);
            $single = $sObj->getSingle();

            $query = 'SELECT me.*,ev.*'
                            .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                            .' '.DB_TBL_EVENTS.' as ev'

                            ." WHERE ev.player_event = '1' AND me.e_id = ev.id"
                            .' AND me.match_id = '.$this->match_id.' AND '.($single ? 'me.player_id='.intval($home_team) : 'me.t_id='.intval($home_team))
                            .' ORDER BY CAST(me.minutes as DECIMAL(10,5)),me.eordering';

            $this->lists['m_events_home'] = $jsDatabase->select($query);

            $query = 'SELECT me.*,ev.*'
                            .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                            .' '.DB_TBL_EVENTS.' as ev'

                            ." WHERE ev.player_event = '1' AND me.e_id = ev.id"
                            .' AND me.match_id = '.$this->match_id.' AND '.($single ? 'me.player_id='.intval($away_team) : 'me.t_id='.intval($away_team))
                            .' ORDER BY CAST(me.minutes as DECIMAL(10,5)),me.eordering';

            $this->lists['m_events_away'] = $jsDatabase->select($query);
            
            $query = 'SELECT me.*,ev.*'
                            .' FROM '.DB_TBL_MATCH_EVENTS.' as me,'
                            .' '.DB_TBL_EVENTS.' as ev'

                            ." WHERE ev.player_event = '1' AND me.e_id = ev.id"
                            .' AND me.match_id = '.$this->match_id
                            .' AND me.minutes != "" AND me.minutes!= "0"'
                            .' ORDER BY CAST(me.minutes as DECIMAL(10,5)),me.eordering';

            $this->lists['m_events_all'] = $jsDatabase->select($query);
            $this->lists['m_events_display'] = 1;
            if(count($this->lists['m_events_all']) == count($this->lists['m_events_home'])+count($this->lists['m_events_away'])){
                $this->lists['m_events_display'] = 0;
            }
            
        }
    }
    public function getTeamEvents()
    {
        global $jsDatabase, $wpdb;
        $season_id = $this->getSeasonID();
        if($season_id){
            $sObj = new modelJsportSeason($season_id);
            $single = $sObj->getSingle();
            $team_events_list = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_events} WHERE player_event='0' ORDER BY ordering");
            $this->lists['team_events'] = '';
            $team_events = get_post_meta($this->match_id,'_joomsport_matchevents',true);

            for($intA=0;$intA<count($team_events_list);$intA++){
                if(isset($team_events[$team_events_list[$intA]->id])){
                    $this->lists['team_events'][$team_events_list[$intA]->id] = $team_events[$team_events_list[$intA]->id];
                }
            }
        }

    }
    public function getLineUps()
    {
        global $jsDatabase,$jsConfig;
        $orderField = 0;
        $pllist_order_se = $jsConfig->get('pllist_order_se');
        if($pllist_order_se){
            $pllist_order_se = explode('_',$pllist_order_se);
            if(isset($pllist_order_se[1]) && $pllist_order_se[1] == '1'){
                $orderField = intval($pllist_order_se[0]);
                $query = "SELECT id as name, eordering as value FROM {$jsDatabase->db->joomsport_ef_select} WHERE fid={$orderField}";
                $eforder = $jsDatabase->selectKeyPair($query);
            }
        }
        
        $firstCol = $jsConfig->get('jsmatch_squad_firstcol');
        if($firstCol){
            $firstColObj = $jsDatabase->selectObject("SELECT * FROM {$jsDatabase->db->joomsport_ef} WHERE id={$firstCol}");
        }
        $lastCol = $jsConfig->get('jsmatch_squad_lastcol');
        if($lastCol){
            $lastColObj = $jsDatabase->selectObject("SELECT * FROM {$jsDatabase->db->joomsport_ef} WHERE id={$lastCol}");
        }
        $season_id = $this->getSeasonID();
        $home_team = get_post_meta( $this->match_id, '_joomsport_home_team', true );
        $away_team = get_post_meta( $this->match_id, '_joomsport_away_team', true );
        $query = "SELECT s.* "
                . "FROM {$jsDatabase->db->joomsport_squad} as s"
                . " JOIN {$jsDatabase->db->posts} as p ON p.ID = s.player_id AND p.post_type='joomsport_player'"
                //. ($orderField?" LEFT JOIN {$jsDatabase->db->joomsport_ef_select} as ef ON ef.fid={$orderField}":'')
                . " WHERE squad_type = 1 AND match_id={$this->match_id} AND  team_id={$home_team}"
                . " GROUP BY s.player_id"
                . " ORDER BY p.post_title";
        $this->lists['squard1'] = $jsDatabase->select($query);        
       // $this->lists['squard1'] = $jsDatabase->select("SELECT * FROM {$jsDatabase->db->joomsport_squad} WHERE squad_type = 1 AND match_id={$this->match_id} AND  team_id={$home_team}");

        for($intA=0;$intA<count($this->lists['squard1']);$intA++){
            if(isset($firstColObj->id)){
                $this->lists['squard1'][$intA]->efFirst = classJsportExtrafields::getExtraFieldValue($firstColObj, $this->lists['squard1'][$intA]->player_id, 0, $season_id);
            }
            if(isset($lastColObj->id)){
                $this->lists['squard1'][$intA]->efLast = classJsportExtrafields::getExtraFieldValue($lastColObj, $this->lists['squard1'][$intA]->player_id, 0, $season_id);
            }
            $metadata = get_post_meta($this->lists['squard1'][$intA]->player_id,'_joomsport_player_ef',true);
            $this->lists['squard1'][$intA]->efValueOrder = -1;

            if(isset($metadata[$orderField])){
                if(isset($eforder[$metadata[$orderField]])){
                    $this->lists['squard1'][$intA]->efValueOrder = $eforder[$metadata[$orderField]];
                }
            }
        }
        if($orderField){
            usort($this->lists['squard1'], array($this,'msortPlayers'));
        }
        $query = "SELECT s.* "
                . "FROM {$jsDatabase->db->joomsport_squad} as s"
                . " JOIN {$jsDatabase->db->posts} as p ON p.ID = s.player_id AND p.post_type='joomsport_player'"
                //. ($orderField?" LEFT JOIN {$jsDatabase->db->joomsport_ef_select} as ef ON ef.fid={$orderField}":'')
                . " WHERE squad_type = 1 AND match_id={$this->match_id} AND  team_id={$away_team}"
                . " GROUP BY s.player_id"
                . " ORDER BY p.post_title";
        $this->lists['squard2'] = $jsDatabase->select($query);   
        //$this->lists['squard2'] = $jsDatabase->select("SELECT * FROM {$jsDatabase->db->joomsport_squad} WHERE squad_type = 1 AND match_id={$this->match_id} AND  team_id={$away_team}");
        for($intA=0;$intA<count($this->lists['squard2']);$intA++){
            if(isset($firstColObj->id)){
                $this->lists['squard2'][$intA]->efFirst = classJsportExtrafields::getExtraFieldValue($firstColObj, $this->lists['squard2'][$intA]->player_id, 0, $season_id);
            }
            if(isset($lastColObj->id)){
                $this->lists['squard2'][$intA]->efLast = classJsportExtrafields::getExtraFieldValue($lastColObj, $this->lists['squard2'][$intA]->player_id, 0, $season_id);
            }
            $metadata = get_post_meta($this->lists['squard2'][$intA]->player_id,'_joomsport_player_ef',true);
            $this->lists['squard2'][$intA]->efValueOrder = -1;

            if(isset($metadata[$orderField])){
                if(isset($eforder[$metadata[$orderField]])){
                    $this->lists['squard2'][$intA]->efValueOrder = $eforder[$metadata[$orderField]];
                }
            }
        }
        if($orderField){
            usort($this->lists['squard2'], array($this,'msortPlayers'));
        }
        $this->lists['squard1_res'] = $jsDatabase->select("SELECT *,group_concat(`minutes` separator ',') as `minarray`,group_concat(`player_subs` separator ',') as `player_subsarray` FROM {$jsDatabase->db->joomsport_squad} WHERE squad_type = 2 AND match_id={$this->match_id} AND  team_id={$home_team} GROUP BY player_id ORDER BY minutes");
        for($intA=0;$intA<count($this->lists['squard1_res']);$intA++){
            if(isset($firstColObj->id)){
                $this->lists['squard1_res'][$intA]->efFirst = classJsportExtrafields::getExtraFieldValue($firstColObj, $this->lists['squard1_res'][$intA]->player_id, 0, $season_id);
            }
            if(isset($lastColObj->id)){
                $this->lists['squard1_res'][$intA]->efLast = classJsportExtrafields::getExtraFieldValue($lastColObj, $this->lists['squard1_res'][$intA]->player_id, 0, $season_id);
            }
        }
        $this->lists['squard2_res'] = $jsDatabase->select("SELECT *,group_concat(`minutes` separator ',') as `minarray`,group_concat(`player_subs` separator ',') as `player_subsarray` FROM {$jsDatabase->db->joomsport_squad} WHERE squad_type = 2 AND match_id={$this->match_id} AND  team_id={$away_team} GROUP BY player_id ORDER BY minutes");
        for($intA=0;$intA<count($this->lists['squard2_res']);$intA++){
            if(isset($firstColObj->id)){
                $this->lists['squard2_res'][$intA]->efFirst = classJsportExtrafields::getExtraFieldValue($firstColObj, $this->lists['squard2_res'][$intA]->player_id, 0, $season_id);
            }
            if(isset($lastColObj->id)){
                $this->lists['squard2_res'][$intA]->efLast = classJsportExtrafields::getExtraFieldValue($lastColObj, $this->lists['squard2_res'][$intA]->player_id, 0, $season_id);
            }
        }
    }

    public function getMaps()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        $this->lists['maps'] = get_post_meta($this->match_id, '_joomsport_match_maps',true);

    }
    public function getSeasonOptions()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();
        
        $seasPost = get_post($season_id);

        return $seasPost;
    }

    public function getCustomMatch()
    {
        global $jsDatabase;
        $season_id = $this->getSeasonID();

        $query = 'SELECT *'
                .' FROM '.$jsDatabase->db->joomsport_match_statuses;

        return $jsDatabase->selectObject($query);
    }
    
    public function getBoxScore($home = true){
        global $jsDatabase,$jsConfig;

        if($jsConfig->get('partdisplay_awayfirst',0) == 1){
            $away_team = (int) get_post_meta( $this->match_id, '_joomsport_home_team', true );
            $home_team = (int) get_post_meta( $this->match_id, '_joomsport_away_team', true );
           
        }else{
            $home_team = (int) get_post_meta( $this->match_id, '_joomsport_home_team', true );
            $away_team = (int) get_post_meta( $this->match_id, '_joomsport_away_team', true );
        }    
        $team_id = $home?$home_team:$away_team; 
        $query = "SELECT * FROM ".DB_TBL_BOX_FIELDS.""
                . " WHERE complex=0 AND published=1 AND displayonfe=1";
        $boxf = $jsDatabase->select($query);

        $checkfornull = '';
        for($intA=0;$intA<count($boxf);$intA++){
            if($checkfornull){ $checkfornull .= ' OR ';}
            
            if($boxf[$intA]->ftype == '1'){
                $options = json_decode($boxf[$intA]->options,true);
                if($options['depend1'] && $options['depend2']){
                    $checkfornull .= ' ( boxfield_'.$options['depend1'].' IS NOT NULL ';
                    $checkfornull .= ' AND boxfield_'.$options['depend2'].' IS NOT NULL ) ';
                }
            }else{
                $checkfornull .= ' boxfield_'.$boxf[$intA]->id.' IS NOT NULL';
            }
            
            
            
        }
        if($checkfornull){
            $query = "SELECT player_id FROM ".DB_TBL_BOX_MATCH
                    ." WHERE match_id={$this->match_id} AND team_id = {$team_id}"
                    . " AND (".$checkfornull.")";
            $players = $jsDatabase->selectColumn($query);
            $html = '';
            if(count($players)){
                $html = $this->getBoxHtml($team_id, $players);
            }
            return $html;
        }
        return null;
    }
    
    public function getBoxHtml($home_team, $playersNotNull){
        global $jsConfig,$wpdb;
        $season_id = $this->getSeasonID();
        $efbox = (int) $jsConfig->get('boxExtraField','0');
        
        $html = '';
        $totalSQL = '';
        $bfields = $wpdb->get_results('SHOW COLUMNS FROM '.$wpdb->joomsport_box_match.' LIKE  "boxfield_%"');
        
        for($intA=0;$intA<count($bfields);$intA++){
            $totalSQL .= 'SUM('.$bfields[$intA]->Field .') as '.$bfields[$intA]->Field.',';
        }
        if(!$totalSQL){
            $totalSQL = '*';
        }else{
            $totalSQL .= '1';
        }                
        
        $parentB = array();
        $complexBox = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_box.' WHERE parent_id="0" AND published="1"  AND displayonfe="1" ORDER BY ordering,name', 'OBJECT') ;
        for($intA=0;$intA<count($complexBox); $intA++){
            $complexBox[$intA]->extras = array();
            $childBox = array();
            if($complexBox[$intA]->complex == '1'){
                $childBox = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_box.' WHERE parent_id="'.$complexBox[$intA]->id.'" AND published="1" AND displayonfe="1" ORDER BY ordering,name', 'OBJECT') ;
                for($intB=0;$intB<count($childBox); $intB++){
                    $options = json_decode($childBox[$intB]->options,true);
                    $extras = isset($options['extraVals'])?$options['extraVals']:array();
                    $childBox[$intB]->extras = $extras;
                    if(count($extras)){
                        foreach($extras as $extr){
                            array_push($complexBox[$intA]->extras, $extr);
                        }
                    }
                }
            }else{
                $options = json_decode($complexBox[$intA]->options,true);
                $extras = isset($options['extraVals'])?$options['extraVals']:array();
                $complexBox[$intA]->extras =  $extras;
            }
            $parentB[$intA]['object'] = $complexBox[$intA];
            $parentB[$intA]['childs'] = $childBox;
        }
        
        $th1 = '';
        $th2 = '';
        
        if($efbox){
            $simpleBox = $wpdb->get_results('SELECT id, sel_value as name FROM '.$wpdb->joomsport_ef_select.' WHERE fid="'.$efbox.'" ORDER BY eordering,sel_value', 'OBJECT') ;
            for($intS=0;$intS<count($simpleBox);$intS++){    
                $players = JoomSportHelperObjects::getPlayersByEF($home_team, $season_id, $efbox, $simpleBox[$intS]->id);
                //$html .= $simpleBox[$intS]->name;
                $th1=$th2='';
                $boxtd = array();
                for($intA=0;$intA<count($parentB);$intA++){
                    $box = $parentB[$intA];
                    $intChld = 0;
                    
                    for($intB=0;$intB<count($box['childs']); $intB++){
                        if(!count($box['childs'][$intB]->extras) || in_array($simpleBox[$intS]->id, $box['childs'][$intB]->extras)){
                            $intChld++;
                            $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                            $boxtd[] =  $box['childs'][$intB]->id;
                            
                        }
                    }

                    if(!count($box['object']->extras) || in_array($simpleBox[$intS]->id, $box['object']->extras)){

                        if($intChld){
                            $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                        }else{
                            $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                            $boxtd[] =  $box['object']->id;
                        }
                    }elseif($intChld){
                        $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                    }
                }
                $html_head = $html_body = '';
                $html_head .= '<div class="table-responsive">
                    <table class="jsBoxStatDIvFE">
                                <thead>
                                    <tr>
                                        <th rowspan="2">'.$simpleBox[$intS]->name.'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                $playersIN = array();
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        if(in_array($players[$intPP], $playersNotNull)){
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            
                                            $player = new classJsportPlayer($players[$intPP],$season_id);
                                            $html_body .= $player->getName(true);
                                            $html_body .= '</td>';
                                            $player_stat = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_box_match} WHERE match_id={$this->match_id} AND team_id={$home_team} AND player_id={$player->object->ID}");

                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                                            }
                                            $playersIN[] = $players[$intPP];
                                            $html_body .= '</tr>';
                                        }
                                    }
                            if($html_body){
                                $html .= $html_head.$html_body.'</tbody>';
                            }        
                    
                    if(count($playersIN) && $html_body){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= __('Total','joomsport-sports-league-results-management');
                        $html .= '</td>';
                        $player_stat = $wpdb->get_row("SELECT ".$totalSQL." FROM {$wpdb->joomsport_box_match} WHERE match_id={$this->match_id} AND team_id={$home_team} AND player_id IN (".  implode(',', $playersIN).")");
                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                            
                            $html .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                        }

                        $html .= '</tr>';
                        $html .= '</tfoot>';
                    }
                    if($html_body){
                        $html .=  '</table></div>';
                    }
            }
        }else{
            $th1=$th2='';
            $boxtd = array();
            $players = get_post_meta($home_team,'_joomsport_team_players_'.$season_id,true);
            $players = JoomSportHelperObjects::cleanJSArray($players);
            for($intA=0;$intA<count($parentB);$intA++){
                $box = $parentB[$intA];
                $intChld = 0;
                for($intB=0;$intB<count($box['childs']); $intB++){
                    $intChld++;
                    $th2 .= "<th>".$box['childs'][$intB]->name."</th>";
                    $boxtd[] =  $box['childs'][$intB]->id;
                    
                }

                if($intChld){
                    $th1 .= '<th colspan="'.$intChld.'">'.$box['object']->name.'</th>';
                }else{
                    $th1 .= '<th rowspan="2">'.$box['object']->name.'</th>';
                    $boxtd[] =  $box['object']->id;
                }
                
            }
            $html_head = $html_body = '';
            $html_head .= '<div class="table-responsive"><table class="jsBoxStatDIvFE">
                                <thead>
                                    <tr>
                                        <th rowspan="2">'.__('Player', 'joomsport-sports-league-results-management').'</th>'
                                        .$th1.
                                    '</tr>
                                    <tr>'
                                        .$th2.
                                    '</tr>
                                </thead>
                                <tbody>';
                                    $playersIN = array();
                                    for($intPP=0;$intPP<count($players);$intPP++){
                                        if(in_array($players[$intPP], $playersNotNull)){
                                        
                                            $html_body .= '<tr>';
                                            $html_body .= '<td>';
                                            $player = new classJsportPlayer($players[$intPP],$season_id);
                                            $html_body .= $player->getName(true);
                                            $html_body .= '</td>';
                                            $player_stat = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_box_match} WHERE match_id={$this->match_id} AND team_id={$home_team} AND player_id={$player->object->ID}");

                                            for($intBox=0;$intBox<count($boxtd);$intBox++){
                                                $html_body .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                                            }

                                            $playersIN[] = $players[$intPP];
                                            $html_body .= '</tr>';
                                        }
                                    }
                    if($html_body){
                        $html .=  $html_head.$html_body.'</tbody>';
                    }
                    
                    if(count($playersIN) && $html){
                        $html .= '<tfoot>';
                        $html .= '<tr>';
                        $html .= '<td>';
                        $html .= __('Total','joomsport-sports-league-results-management');
                        $html .= '</td>';
                        $player_stat = $wpdb->get_row("SELECT ".$totalSQL." FROM {$wpdb->joomsport_box_match} WHERE match_id={$this->match_id} AND team_id={$home_team}");

                        for($intBox=0;$intBox<count($boxtd);$intBox++){
                            $html .= '<td>'.(jsHelper::getBoxValue($boxtd[$intBox], $player_stat)).'</td>';
                        }

                        $html .= '</tr>';
                        $html .= '</tfoot>';
                    }
                    if($html){
                        $html .=  '</table></div>';
                    }
        }
        return $html;
        
    }
    public function msortPlayers($a,$b){
        
        if ($a->efValueOrder == $b->efValueOrder) {
            return 0;
        }
        return ($a->efValueOrder < $b->efValueOrder) ? -1 : 1;

    }
}
