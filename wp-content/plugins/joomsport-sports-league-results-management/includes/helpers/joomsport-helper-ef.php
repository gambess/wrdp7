<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class JoomSportHelperEF{
    public static function getEFList($type, $id , $season_related = 0){
        global $wpdb;
        $sql = "SELECT *"
                . " FROM {$wpdb->joomsport_ef}"
                . " WHERE type='".$type."' AND published = '1' AND season_related=".$season_related;
        return $wpdb->get_results($sql);
    }
    public static function getEFInput(&$ef, $value,$name='ef',$asarr = false){
        global $wpdb;
        $namearr = $asarr?$name.'[]':$name.'['.$ef->id.']';
        switch($ef->field_type){
            case '1': //radio
                    $is_field = array();
                    $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
                    $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
                    $ef->edit = JoomSportHelperSelectBox::Radio($namearr, $is_field,$value,' id="'.$name.'_'.$ef->id.'"');
                break;
            case '2': //textarea
                    $ef->edit = '';//wp_editor($value, 'ef_'.$ef->id,array("textarea_rows"=>3));
                break;
            case '3': //selectbox
                    $selval = $wpdb->get_results('SELECT id, sel_value as name FROM '.$wpdb->joomsport_ef_select.' WHERE fid='.absint($ef->id).' ORDER BY eordering', 'OBJECT') ;
                    $ef->edit = JoomSportHelperSelectBox::Simple($namearr, $selval,$value,' id="'.$name.'_'.$ef->id.'"',true);
                break;
            case '5': //persons
                    $cat = 0;
                    if($ef->options){
                        $opt = json_decode($ef->options, true);
                        if(isset($opt['personcategory']) && $opt['personcategory']){
                            $cat = $opt['personcategory'];
                        }
                    }
                    $postarr = array(
                            'post_type' => 'joomsport_person',
                            'post_status'      => 'publish',
                            'posts_per_page'   => -1,
                            'orderby' => 'title',
                            'order'=> 'ASC',
                            
                        );
                    if($cat){
                        $postarr['tax_query'] = array(
                            array(
                            'taxonomy' => 'joomsport_personcategory',
                            'field' => 'term_id',
                            'terms' => $cat)
                        );
                    }
                    
                    $persons = get_posts($postarr);
                    $lists = array();
                    
                    for($intA=0;$intA<count($persons);$intA++){
                        $tmp = new stdClass();
                        $tmp->id = $persons[$intA]->ID;
                        $tmp->name = $persons[$intA]->post_title;
                        $lists[] = $tmp;
                    }
                    $ef->edit = JoomSportHelperSelectBox::Simple($namearr, $lists,$value,' id="'.$name.'_'.$ef->id.'"',true);
                break;
            case '6':
                $ef->edit = '<input type="text" class="jsdatefield hasDatepickerr" value="'.esc_attr($value).'" id="'.$name.'_'.$ef->id.'" name="'.$namearr.'" />';
                break;
            default:
                $ef->edit = '<input type="text" value="'.esc_attr($value).'" id="'.$name.'_'.$ef->id.'" name="'.$namearr.'" />';
        }
    }
}