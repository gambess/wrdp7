<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_INCLUDES. 'classes'.DIRECTORY_SEPARATOR.'joomsport-class-match.php';
class JoomSportClassMatchRoundSingle extends JoomSportClassMatch{
    public $_mID = null;
    public function __construct($mID) {
        $this->_mID = $mID;
    }
    
    public function save(){
        
    }

}