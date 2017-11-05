<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-getmatches.php';
class classJsportMatches
{
    public $params = array();
    private $available_params = array('season_id',
        'matchday_id',
        'team_id',
        'date_from',
        'date_to',
        'played',
        'ordering',
        'place',
        'limit',
        'offset',
            'ordering_dest',
        'date_exclude');
    public function __construct($params)
    {
        if (count($params)) {
            foreach ($params as $key => $value) {
                if (in_array($key, $this->available_params)) {
                    $this->params[$key] = $value;
                }
            }
        }
    }

    public function getMatchList($single = 0)
    {
        $matches = classJsportgetmatches::getMatches($this->params, $single);

        return $matches;
    }
}
