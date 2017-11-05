<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomSportMetaTournament {
    
    public static function joomsport_tournament_edit_form_fields($term_obj){

    }
    public static function joomsport_tournament_add_form_fields($term_id){
        
    }
    public static function joomsport_tournament_save_form_fields($term_id){
        global $joomsportSettings;
        if(!isset($_POST['tag_ID']) || !$_POST['tag_ID']){
            $meta_value = $joomsportSettings->get('tournament_type');
            $term_metas = get_option("taxonomy_{$term_id}_metas");
            if (!is_array($term_metas)) {
                $term_metas = Array();
            }
            // Save the meta value
            $term_metas['t_single'] = $meta_value;
            update_option( "taxonomy_{$term_id}_metas", $term_metas );
        }
        
    }
    public static function tournament_type_columns( $taxonomies ) {
        $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'header_icon' => '',
//      'description' => __('Description'),
        't_single' => __('League type', 'joomsport-sports-league-results-management'),
        'posts' => __('Posts')
        );

        return $new_columns;    
    }

 
    public static function manage_joomsport_tournament_columns($out, $column_name, $tax_id) {
        //echo $theme_id;
        $t_single = get_option("taxonomy_{$tax_id}_metas");

        switch ($column_name) {
            case 't_single': 

                $out .= $t_single['t_single'] ? __('Single','joomsport-sports-league-results-management') : __('Team','joomsport-sports-league-results-management'); 
                break;

            default:
                break;
        }
        return $out;    
    }
}