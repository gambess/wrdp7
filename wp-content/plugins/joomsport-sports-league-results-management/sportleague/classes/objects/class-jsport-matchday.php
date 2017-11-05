<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-matchday.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-season.php';
class classJsportMatchday
{
    private $matchday_id = null;
    private $object = null;
    public $view = null;
    public $lists = null;

    public function __construct($id = null)
    {
        $this->matchday_id = $id;
        if (!$this->matchday_id) {
            $name = get_query_var('joomsport_matchday');
            $term = get_term_by('slug',$name,'joomsport_matchday');
            $this->matchday_id = $term->term_id;
        }
        if (!$this->matchday_id) {
            die('ERROR! Matchday ID not DEFINED');
        }
        $model = new modelJsportMatchday($this->matchday_id);
        $this->object = $model->getObject();
        //$this->lists['options']['title'] = $this->object->name;
    }

    private function getMatchdayObject()
    {
        $metas = get_option("taxonomy_{$this->matchday_id}_metas");
        $season_id = $metas['season_id'];
        $seasonObj = new classJsportSeason($season_id);
        $single = $seasonObj->getSingle();

        switch ($metas['matchday_type']) {
            /*case 1:
                require_once JS_PATH_OBJECTS.'matchdays'.DIRECTORY_SEPARATOR.'class-jsport-knockout.php';
                $knockObj = new classJsportKnockout($this->object, $single);
                $this->view = 'knockout';

                return $knockObj;
                break;
            case 2:
                require_once JS_PATH_OBJECTS.'matchdays'.DIRECTORY_SEPARATOR.'class-jsport-knockout_de.php';
                $knockObj = new classJsportKnockoutDe($this->object, $single);
                $this->view = 'knockout';

                return $knockObj;
                break;*/
            default:

                $childObj = $seasonObj->getChild();
                $this->object = $childObj->getCalendar(array('matchday_id' => $this->matchday_id));
                $this->lists = $childObj->lists;
                $this->lists['pagination'] = null;
                $this->lists['enable_search'] = 0;
                $this->view = $childObj->getCalendarView();
                $this->getLists($seasonObj->object);

                return $this->object;
                break;
        }
    }

    public function getRow()
    {
        return $this->getMatchdayObject();
    }
    public function getView()
    {
        return $this->view;
    }
    public function getLists($seasonObj)
    {
        $this->lists['options'] = json_decode($seasonObj->season_options);
    }
}
