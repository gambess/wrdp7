<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportPaginationcms
{
    private $pagination = null;
    public function __construct()
    {
        $this->pagination = new stdClass();
    }

    public function getLimit()
    {
        $mainframe = JFactory::getApplication();

        return $mainframe->getCfg('list_limit');
    }
    public function getOffset()
    {
        return 0;
    }
}
