<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

class JoomSportHelperSelectBox{
    public static function Simple($name, $lists, $value = 0, $options = '', $bulk = true){
        if($bulk === true){
            $bulk = __('Select', 'joomsport-sports-league-results-management');
        }
        $html = '';
        $html .= '<select name="'.$name.'" '.$options.'>';
        if($bulk){
            $html .= '<option value="0">'.esc_attr($bulk).'</option>';
        }
        if(count($lists)){
            foreach ($lists as $list) {
                $html .= '<option value="'.$list->id.'" '.($list->id == $value?' selected="true"':"").'>'.esc_attr($list->name).'</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }
    public static function addOption($id, $value){
        $tmp = new stdClass();
        $tmp->name = $value;
        $tmp->id = $id;
        return $tmp;
    }
    public static function Radio($name, $lists, $value = 0, $options = '',$classes = ''){

        $pro = 1;
        if($options == 'std'){
            $pro = 0;
        }
        $html = '';
        if(count($lists)){
            $intA = 0;
            $html .= '<p class="jsw_switch">';
            $id = str_replace('[', '_', $name);
            $id = str_replace(']', '_', $id);
            $id = 'jsuid_'.$id;
            foreach ($lists as $list) {
                
                $id_new = $id.$intA;
                $class='';
                $class = ($intA==(count($lists)-1))?" jsfw-last":"";
                if($pro){
                    if(isset($classes['lclasses'])){
                        $class .= $classes['lclasses'][$intA]?' jsfw-enable':' jsfw-disable';
                    }else{
                        $class .= ($intA?' jsfw-enable':' jsfw-disable');
                    }
                }else{
                    $class .= ($intA?' jsfw-enable-dsb':' jsfw-disable-dsb');
                    if($name == 'general[enbl_club]' || $name == 'general[unbl_venue]'){
                        $value = 1;
                    }
                }
                
                $html .= '<label for="'.$id_new.'" class="'.($intA?'':'jsfw-first').''.($list->id == $value?' selected':"").$class.'"><span>'.$list->name.'</span></label>';
                if($pro){
                    $html .= '<input type="radio" id="'.$id_new.'" name="'.$name.'" '.$options.' value="'.$list->id.'" '.($list->id == $value?' checked="true"':"").' />';
                }
                $intA++;
            }
            $html .= '</p>';
        }    
        
        
        return $html;
    }
    public static function Optgroup($name, $lists, $value = 0, $options = '', $bulk = true, $isnull = '0'){
        if($bulk === true){
            $bulk = __('Select', 'joomsport-sports-league-results-management');
        }
        $html = '';
        $html .= '<select name="'.$name.'" '.$options.'>';
        if($bulk){
            $html .= '<option value="'.$isnull.'">'.esc_attr($bulk).'</option>';
        }
        if(count($lists)){
            foreach ($lists as $key=>$list) {
                if(is_array($list)){
                    $html .= '<optgroup label="'.$key.'">';
                    foreach ($list as $sublist) {
                        $html .= '<option value="'.$sublist->id.'" '.($sublist->id == $value?' selected="true"':"").'>'.esc_attr($sublist->name).'</option>';
               
                    }
                    $html .= '</optgroup>';
                }else{
                    $html .= '<option value="'.$list->id.'" '.($list->id == $value?' selected="true"':"").'>'.esc_attr($list->name).'</option>';
                }    
                    
            }
        }
        $html .= '</select>';
        return $html;
    }
}