<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class classJsportPagination
{
    public $pages = 1;
    public $link = '';
    public $limit = 20;
    public $offset = 0;
    public $additvars = array();
    public $limit_array = array(5, 10, 20, 25, 50, 100, 0);
    private $setcurrent = 0;

    public function __construct($link, $setcurrent = 0)
    {
        $this->setcurrent = $setcurrent;

        // Get the pagination request variables
        $this->limit = 25;//$mainframe->getUserStateFromRequest('com_joomsport.jslimit', 'jslimit', $mainframe->getCfg('list_limit'), 'int');
        
        //$this->limit = $mainframe->getCfg('list_limit');

        if (classJsportRequest::get('jslimit') != null) {
            
            $_SESSION['jslimit'] = classJsportRequest::get('jslimit');
            
        }
        if(isset($_SESSION['jslimit'])){
            $this->limit = $_SESSION['jslimit'];
        }
        $this->link = $link;
    }
    public function setLimit()
    {
        return $this->limit;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function getOffset()
    {
        $this->offset = $this->getLimit() * $this->getCurrentPage();

        return $this->offset;
    }

    public function setPages($count)
    {
        $limit = $this->getLimit();
        if ($limit) {
            $this->pages = ceil($count / $limit);
        } else {
            $this->pages = 1;
        }
    }
    public function getCurrentPage()
    {

        $page = (int) classJsportRequest::get('pagejs');
        if(!$page && $this->setcurrent){
            $limit = $this->getLimit();
            $npage = ceil($this->setcurrent / $limit);
            return $npage ? $npage - 1 : 0;
        }
        $page = $page ? $page : 1;

        return $page - 1;
    }
    public function getLimitBox($val = '')
    {
        $kl = '<div class="jsdispad col-xs-12 col-sm-12 col-md-4 col-lg-4 text-right pull-right" style="min-width: 170px;"><label>'.__('Display','joomsport-sports-league-results-management').'</label>';
        $jas = 'onchange = "this.form.submit();"';
        foreach ($this->limit_array as $lim) {
            $limbox[] = JoomSportHelperSelectBox::addOption($lim, $lim ? $lim : __('All','joomsport-sports-league-results-management'));
        }
        $kl .= JoomSportHelperSelectBox::Simple('jslimit'.$val, $limbox,  $this->limit, 'class="pull-right" style="width:70px;" size="1" '.$jas, false);
        $kl .= '</div>';

        return $kl;
    }
    public function setAdditVar($name, $var)
    {
        $this->additvars[$name] = $var;
    }
}
