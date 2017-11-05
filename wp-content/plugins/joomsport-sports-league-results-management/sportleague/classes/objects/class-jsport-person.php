<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-person.php';

class classJsportPerson
{
    private $id = null;
    public $season_id = null;
    public $object = null;
    public $lists = null;
    public $model = null;

    public function __construct($id = 0, $season_id = null, $loadLists = true)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->id = get_the_ID();
        } else {
            $this->season_id = $season_id;
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Person ID not DEFINED');
        }
        $this->loadObject($loadLists);
    }

    private function loadObject($loadLists)
    {
        $obj = $this->model = new modelJsportPerson($this->id, $this->season_id);
        $this->object = $obj->getRow();
        if ($loadLists) {
            $this->lists = $obj->loadLists();
            //$this->lists['options']['tourn'] = $this->lists['tourn'];
        }
        $this->lists['options']['title'] = $this->getName(false);
    }

    public function getName($linkable = false, $itemid = 0)
    {
        global $jsConfig;
        $metadata = get_post_meta($this->id,'_joomsport_person_personal',true);
        if(isset($metadata['first_name']) && isset($metadata['last_name'])){
            $pname = $metadata['first_name'] .' '.$metadata['last_name'];
        }

        if(!isset($pname) || !trim($pname)){
            $pname = get_the_title($this->id);
        }
        $pp = get_post($this->id);
        if(empty($pp)){
            return '';
        }
        if ($pp->post_status != 'publish' || get_post_status($this->id) == 'private') {
            $linkable = false;
        }
        if (!$linkable || $jsConfig->get('enbl_playerlinks',1) == '0') {
            return $pname;
        }
        $html = '';
        if ($this->id > 0 && $pname) {
            $html = classJsportLink::person($pname, $this->id, $this->season_id,false, $itemid);
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        return $this->lists['def_img'];
    }
    public function getEmblem($linkable = true, $type = 0, $class = 'emblInline', $width = 0, $light = true, $itemid = 0)
    {
        global $jsConfig;
        $html = '';
        $pp = get_post($this->id);
        if (empty($pp) || $pp->post_status != 'publish' || get_post_status($this->id) == 'private') {
            $linkable = false;
        }
        if (!isset($this->lists['def_img']) && $type != 10) {
            $this->loadObject(true);
        }
        if($type == 10){
            $html = jsHelperImages::getEmblemBig($this->lists['def_img'], 10, $class, $width, $light);
        }else{
            $html = jsHelperImages::getEmblem($this->lists['def_img'], 0, $class, $width, $light);
        }
        
        if ($linkable && $jsConfig->get('enbl_playerlogolinks',1) == '1') {
            $html = classJsportLink::player($html, $this->id, $this->season_id, $itemid, $linkable);
        }

        return $html;
    }

    public function getRow()
    {
        $this->setHeaderOptions();

        return $this;
    }
    public function getRowSimple()
    {
        return $this;
    }

    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = __('Person','joomsport-sports-league-results-management');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'users';
        
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
    public function getDescription()
    {
        $about = get_post_meta($this->id,'_joomsport_person_about',true);
        return classJsportText::getFormatedText($about);
    }
    public function setHeaderOptions()
    {
        
    }
}
