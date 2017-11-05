<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_MODELS.'model-jsport-seasonlist.php';
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-dlists.php';

class classJsportSeasonlist
{
    private $id = null;
    private $object = null;
    public $lists = null;
    public $tournid = null;

    public function __construct()
    {
        $this->tournid = classJsportRequest::get('filtr_tourn');
        
        $obj = new modelJsportSeasonlist($this->tournid);
        $this->object = $obj->getRow();

        $this->lists['options']['tourn'] = classJsportDlists::getSeasonsTournList($this->tournid);
    }

    public function getRow()
    {
        return $this->object;
    }

    public function canJoin($season)
    {
        $metadata = get_post_meta($season->ID,'_joomsport_season_sreg',true);
        if(!isset($metadata['s_reg']) || !$metadata['s_reg']){
            return false;
        }
        if(!$metadata['reg_start']){
            $metadata['reg_start'] = '0000-00-00 00:00:00';
        }
        if(!$metadata['reg_end']){
            $metadata['reg_end'] = '0000-00-00 00:00:00';
        }
        $reg_start = mktime(substr($metadata['reg_start'], 11, 2), substr($metadata['reg_start'], 14, 2), 0, substr($metadata['reg_start'], 5, 2), substr($metadata['reg_start'], 8, 2), substr($metadata['reg_start'], 0, 4));
        $reg_end = mktime(substr($metadata['reg_end'], 11, 2), substr($metadata['reg_end'], 14, 2), 0, substr($metadata['reg_end'], 5, 2), substr($metadata['reg_end'], 8, 2), substr($metadata['reg_end'], 0, 4));

        $part_count = $this->partCount($season);

        if ($metadata['s_reg'] && ($part_count < $metadata['s_participant'] || $metadata['s_participant'] == 0) && ($reg_start <= time() && (time() <= $reg_end || $metadata['reg_end'] == '0000-00-00 00:00:00'))) {
            return true;
        }

        return false;
    }
    public function partCount($season)
    {
        $metadata = get_post_meta($season->ID,'_joomsport_season_participiants',true);

        return count($metadata);
    }
}
