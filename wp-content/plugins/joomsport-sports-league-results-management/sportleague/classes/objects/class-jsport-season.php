<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-season.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-seasonlist.php';
require_once JOOMSPORT_PATH_ENV_CLASSES.'class-jsport-participant.php';
foreach (glob(__DIR__.DIRECTORY_SEPARATOR.'tournament_types/*.php') as $filename) {
    include $filename;
}

class classJsportSeason
{
    private $id = null;
    public $object = null;
    public $season = null;
    public $lists = null;
    public $modelObj = null;

    public function __construct($id = 0)
    {
        $this->id = $id;
        if (!$this->id) {
            $this->id = get_the_ID();
        }
        if (!$this->id) {
            die('ERROR! SEASON ID not DEFINED');
        }
        $this->loadObject($this->id);
        
    }

    private function loadObject($id)
    {
        $obj = $this->modelObj = new modelJsportSeason($id);
        $this->object = $obj->getRow();

        $this->lists = $obj->loadLists();
        if(!empty($this->object)){
            //$this->lists['optionsT']['title'] = $this->object->tsname;
        }
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getSingle()
    {
        return JoomSportHelperObjects::getTournamentType($this->id);
    }
    public function getTournType()
    {
        return 0; //(int)$this->object->tournament_type;
    }

    //

    public function getChild()
    {
        /*$args = array(
                'post_parent' => $this->id,
                'post_type'   => 'joomsport_season', 
                'numberposts' => -1,
                'post_status' => 'published' 
        );
        $children = get_children( $args );
        var_dump($children);*/
        $type = $this->getTournType();
        if ($type == 0) {
            $this->season = new classJsportTournMatches($this->object);
            //$this->season->calculateTable();
        } else {
            $this->season = new classJsportTournRace($this->object);
            //$this->season->calculateTable();
        }

        return $this->season;
    }

    public function getRow()
    {
        if(!empty($this->object)){
            //$obj = new classJsportSeason($this->id);
            $child = $this->getChild();
            $child->calculateTable();
            $this->getLists();
            //$this->lists['options']['title'] = $this->lists['optionsT']['title'];
            $this->lists['bonuses'] = $this->getSeasonBonuses();
            $this->setHeaderOptions();
            return $this;
        }else{
            JError::raiseError('404', 'Not found');
        }
        
        
    }

    public function getLists()
    {
        $this->lists['options'] = json_decode($this->object->season_options);
        $colors = $this->modelObj->getColors();
        
        $this->season->lists['tblcolors'] = $colors[0];
        $this->lists['legend'] = $colors[1];
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab

        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = __('Standings','joomsport-sports-league-results-management');
        $tabs[$intA]['body'] = 'table-group.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'tableS';

        //about
        $s_descr = get_post_meta($this->id,'_joomsport_season_rules',true);

        //rules
        if ($s_descr) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_rules';
            $tabs[$intA]['title'] = __('Rules','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = '';
            $tabs[$intA]['text'] = classJsportText::getFormatedText($s_descr);
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }

        return $tabs;
    }

    public function setHeaderOptions()
    {
        global $jsConfig;
        $this->lists['options']['calendar'] = $this->id;
        $seaslistObj = new classJsportSeasonlist();
        if ($seaslistObj->canJoin($this->object)) {
            $this->lists['options']['joinseason'] = $this->id;
        }
        if (!$this->getSingle() && $jsConfig->get('enbl_linktoplayerlist',1) == '1') {
            $this->lists['options']['playerlist'] = $this->id;
        }
        $this->lists['options']['print'] = '<a href="javascript:void(0);" onclick="componentPopup();"><span class="glyphicon glyphicon-print"></span></a>';
        //social
        if ($jsConfig->get('jsbp_season') == '1') {
            $this->lists['options']['social'] = true;
            //classJsportAddtag::addCustom('og:title', $this->object->tsname);
            $img = $this->object->tourn_logo;
            if (is_file(JOOMSPORT_PATH_IMAGES.$img)) {
                //classJsportAddtag::addCustom('og:image', JS_LIVE_URL_IMAGES.$img);
            }
            //classJsportAddtag::addCustom('og:description', $this->object->s_descr);
        }
    }
    public function getSeasonBonuses(){
        $obj = new classJsportParticipant($this->id);
        $participants = $obj->getParticipants();
        $html = '';
        for($intA = 0; $intA < count($participants); $intA++){
            $bonus_point = get_post_meta($participants[$intA],'_joomsport_team_bonuses_'.$this->id,true);
            if($bonus_point){
                $p = $obj->getParticipiantObj($participants[$intA]);
                $html .= "<div>" . $p->getName(false) . " - " . $bonus_point . "</div>";
            }
        }
        return $html;
    }
    
    public function isComplex(){
        return get_post_meta($this->id,'_joomsport_season_complex',true);
    }
    public function getSeasonChildrens(){
        $args = array(
                'post_parent' => $this->id,
                'post_type'   => 'joomsport_season', 
                'numberposts' => -1,
                'post_status' => 'published',
                'orderby' => 'menu_order title',
                'order'   => 'ASC',
        );
        $children = get_children( $args );
        return $children;
    }
    public function hideTable(){
        $mdays = get_terms(
                    array('taxonomy' => 'joomsport_matchday')
                );
        for($intA=0;$intA<count($mdays);$intA++){
            $metas = get_option("taxonomy_{$mdays[$intA]->term_id}_metas");
            $season_id = $metas['season_id'];
            $md_type = $metas['matchday_type'];
            if($this->id == $season_id && $md_type == '0'){
                return false;
            }
            
        }
        return true;
    }
}
