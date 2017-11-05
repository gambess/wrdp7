<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportPerson
{
    public $season_id = null;
    public $player_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id, $season_id = 0)
    {
        $this->season_id = $season_id;
        $this->player_id = $id;

        if (!$this->player_id) {
            die('ERROR! Person ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        /*$this->row = $jsDatabase->selectObject('SELECT p.*,c.country,c.ccode '
                .'FROM '.DB_TBL_PLAYERS.' as p'
                .' LEFT JOIN '.DB_TBL_COUNTRIES.' as c ON c.id=p.country_id'
                .' WHERE p.id = '.$this->player_id);*/
        $this->row = get_post($this->player_id);
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        global $jsDatabase;
        $metadata = get_post_meta($this->player_id,'_joomsport_person_personal',true);
        
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->player_id, '6', $this->season_id);
        if(isset($metadata['last_name']) && ($metadata['last_name'])){
            $tmparr = array(__('Last Name','joomsport-sports-league-results-management') => $metadata['last_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
        }
        if(isset($metadata['first_name']) && ($metadata['first_name'])){
            $tmparr = array(__('First Name','joomsport-sports-league-results-management') => $metadata['first_name']);
            $this->lists['ef'] = array_merge($tmparr, $this->lists['ef']);
            //$this->lists['ef'][__('First Name','joomsport-sports-league-results-management')] = $metadata['first_name'];
        }

        $this->getPhotos();
        $this->getDefaultImage();
        $this->getHeaderSelect();

        return $this->lists;
    }

    public function getDefaultImage()
    {
        global $jsDatabase;
        $this->lists['def_img'] = null;
        if (isset($this->lists['photos'][0])) {
            $this->lists['def_img'] = $this->lists['photos'][0];
        }
    }
    public function getPhotos()
    {
        global $jsConfig;
        $photos = get_post_meta($this->player_id,'vdw_gallery_id',true);

        $this->lists['photos'] = array();
        if ($photos && count($photos)) {
            foreach ($photos as $photo) {
                //$image = get_post($photo);
                $image_arr = wp_get_attachment_image_src($photo, 'joomsport-thmb-medium');
                if (($image_arr[0])) {
                    $this->lists['photos'][] = array("id" => $photo, "src" => $image_arr[0]);
                }
            }
        }
        
    }

    public function getHeaderSelect()
    {
        global $jsDatabase;
        /*$tourns = JoomSportHelperObjects::getPlayerSeasons($this->player_id);


        $javascript = " onchange='fSubmitwTab(this);'";

        
        
        $jqre = JoomSportHelperSelectBox::Optgroup('sid', $tourns,$this->season_id, 'class="selectpicker" '.$javascript);
        */
        $this->lists['tourn'] = '';//$jqre;
    }
    
    
}
