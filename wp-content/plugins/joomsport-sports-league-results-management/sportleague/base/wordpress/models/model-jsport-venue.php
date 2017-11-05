<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportVenue
{
    public $season_id = null;
    public $venue_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id, $season_id = 0)
    {
        if (!$id) {
            $this->season_id = (int) classJsportRequest::get('sid');
            $this->venue_id = (int) classJsportRequest::get('id');
        } else {
            $this->season_id = $season_id;
            $this->venue_id = $id;
        }
        if (!$this->venue_id) {
            die('ERROR! Venue ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = get_post(intval($this->venue_id));
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->venue_id, '5', 0);
        $vpersonal = get_post_meta($this->venue_id,'_joomsport_venue_personal',true);
        if(isset($vpersonal['venue_addr']) && $vpersonal['venue_addr']){
            $this->lists['ef'][__('Address','joomsport-sports-league-results-management')] = $vpersonal['venue_addr'];
        }
        $this->getPhotos();
        $this->getDefaultImage();

        $this->lists['options']['title'] = $this->row->post_title;

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
        $photos = get_post_meta($this->venue_id,'vdw_gallery_id',true);

        $this->lists['photos'] = array();
        if ($photos && count($photos)) {
            foreach ($photos as $photo) {
                $image = get_post($photo);

                if (($image->guid)) {
                    $this->lists['photos'][] = $image->guid;
                }
            }
        }
        
    }
}
