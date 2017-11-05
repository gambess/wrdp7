<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_MODELS.'model-jsport-event.php';

class classJsportEvent
{
    private $id = null;
    public $object = null;
    public $lists = null;

    public function __construct($id)
    {
        $this->id = $id;

        if (!$this->id) {
            die('ERROR! Event ID not DEFINED');
        }
        $this->loadObject();
    }

    private function loadObject()
    {
        $obj = new modelJsportEvent($this->id);
        $this->object = $obj->getRow();
    }

    public function getEventName()
    {
        return $this->object->e_name;
    }

    public function getEmblem($isblanked = true)
    {
        $html = jsHelperImages::getEmblemEvents($this->object->e_img);
        if(!$html && !$isblanked){
            $html = $this->getEventName();
        }
        return $html;
    }
}
