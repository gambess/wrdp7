<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportClub
{
    public $club_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id)
    {
        $this->club_id = $id;

        if (!$this->club_id) {
            die('ERROR! Club ID not DEFINED');
        }
        $this->loadObject();
    }
    private function loadObject()
    {
        global $jsDatabase;
        $this->row = get_post($this->club_id);
    }
    public function getRow()
    {
        //$this->loadLists();
        return $this->row;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->club_id, '4', 0);
        $this->getPhotos();
        $this->getDefaultImage();
        $this->getTeams();

        return $this->lists;
    }

    public function getDefaultImage()
    {
        global $jsDatabase;
        $this->lists['def_img'] = null;
        
    }
    public function getPhotos()
    {
        global $jsDatabase;
        
        $this->lists['photos'] = array();
        
    }

    public function getTeams()
    {
        global $jsDatabase;
        
        $this->lists['teams'] = get_posts(array(
                'posts_per_page' => -1,
                'offset'           => 0,
                'post_type'        => 'joomsport_team',
                'post_status'      => 'publish',
                'tax_query'        =>  array(
                        array(
                        'taxonomy' => 'joomsport_club',
                        'field' => 'term_id',
                        'terms' => $this->club_id)
                    )
                ));
    }
}
