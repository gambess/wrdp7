<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportSeason
{
    public $season_id = null;
    public $lists = null;
    public $object = null;

    public function __construct($id)
    {
        $this->season_id = $id;

        if (!$this->season_id) {
            die('ERROR! SEASON ID not DEFINED');
        }
        global $jsDatabase;
        $this->object = get_post(intval($this->season_id));

    }
    public function getRow()
    {
        return $this->object;
    }
    public function getName()
    {
        $name = '';
        $term_list = wp_get_post_terms($this->season_id, 'joomsport_tournament', array("fields" => "all"));
        if(count($term_list)){
            $name .= esc_attr($term_list[0]->name).' ';
        }
        $name .= get_the_title($this->season_id);
        return $name;
    }
    public function loadLists()
    {
        $this->lists['ef'] = classJsportExtrafields::getExtraFieldList($this->season_id, '3', $this->season_id);

        return $this->lists;
    }
    public function getType()
    {
        //return $this->object->tournament_type;
        return 0;
    }

    public function getSingle()
    {
        if($this->season_id > 0){
            return JoomSportHelperObjects::getTournamentType($this->season_id);
        }
    }

    public function getColors()
    {
        global $jsDatabase;
        
        $colors = get_post_meta($this->season_id,'_joomsport_season_colors',true);

        $color_mass = array();
        $legend_mass = array();
        if($colors){
            for ($j = 0;$j < count($colors);++$j) {
                $tmp_pl = $colors[$j]['places'];
                $color_mass[intval($colors[$j]['places'])] = $colors[$j]['color_field'];
                $tmp_arr = explode(',', $tmp_pl);
                $tmp_arr2 = explode('-', $tmp_pl);
                if (count($tmp_arr) > 1) {
                    foreach ($tmp_arr as $arr) {
                        if (intval($arr)) {
                            $color_mass[intval($arr)] = $colors[$j]['color_field'];
                        }
                    }
                }
                if (count($tmp_arr2) > 1) {
                    for ($zzz = $tmp_arr2[0];$zzz < $tmp_arr2[1] + 1;++$zzz) {
                        $color_mass[$zzz] = $colors[$j]['color_field'];
                    }
                }
                if(isset($colors[$j]['legend'])){
                    $legend_mass[] = array("color" => $colors[$j]['color_field'], "legend" => $colors[$j]['legend']);
                }
                
            }
        }

        return array($color_mass,$legend_mass);
    }
}
