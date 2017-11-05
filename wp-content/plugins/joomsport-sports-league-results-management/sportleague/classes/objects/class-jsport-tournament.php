<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-tournament.php';
class classJsportTournament
{
    private $id = null;
    private $object = null;
    public $lists = null;

    public function __construct($id = null)
    {
        $this->id = $id;
        if (!$this->id) {
            $name = get_query_var('joomsport_tournament');
            $term = get_term_by('slug',$name,'joomsport_tournament');
            $this->id = $term->term_id;
        }
        if (!$this->id) {
            die('ERROR! TOURNAMENT ID not DEFINED');
        }

        $obj = new modelJsportTournament($this->id);
        $this->object = $obj->getRow();
        $this->lists = $obj->lists;

        //$title = isset($this->object->name) ? $this->object->name : '';
        //$this->lists['options']['title'] = $title;
    }

    public function getRow()
    {
        return $this->object;
    }
}
