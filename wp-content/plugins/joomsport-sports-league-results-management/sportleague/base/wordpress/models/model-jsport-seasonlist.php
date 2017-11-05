<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportSeasonlist
{
    public $row = null;
    public $lists = null;

    public function __construct($tournid = null)
    {
        global $jsDatabase;
         $args = array(
            'posts_per_page' => -1,
            'offset'           => 0,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_type'        => 'joomsport_season',
            'post_status'      => 'publish',
        );
         if($tournid){
            $args['tax_query'] = array(
                array(
                'taxonomy' => 'joomsport_tournament',
                'field' => 'term_id',
                'terms' => $tournid)
            );
         }
        $this->row = get_posts( $args );
        
    }
    public function getRow()
    {
        return $this->row;
    }
}
