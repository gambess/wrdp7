<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportTournament
{
    public $row = null;
    public $lists = null;

    public function __construct($id)
    {
        global $jsDatabase;
        $this->row = get_term_by('id',$id,'joomsport_tournament');
        $this->lists['slist'] = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => 'joomsport_season',
            'tax_query' => array(
                array(
                'taxonomy' => 'joomsport_tournament',
                'field' => 'term_id',
                'terms' => $id)
            ))
        );;
    }
    public function getRow()
    {
        return $this->row;
    }
}
