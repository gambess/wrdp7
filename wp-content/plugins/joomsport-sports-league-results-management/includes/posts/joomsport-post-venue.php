<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-venue.php';

class JoomSportPostVenue {
    public function __construct() {

    }
    public static function init(){
        self::register_post_types();
    }
    public static function register_post_types(){
        $jsconfig =  new JoomsportSettings();
        add_action("admin_init", array("JoomSportPostVenue","admin_init"));
        add_action( 'edit_form_after_title',  array( 'JoomSportPostVenue','venue_edit_form_after_title') );
        $showui = $jsconfig->get('unbl_venue',true)?true:false;
        $slug = get_option( 'joomsportslug_joomsport_venue', null );
        register_post_type( 'joomsport_venue',
                apply_filters( 'joomsport_register_post_type_venue',
                        array(
                                'labels'              => array(
                                                'name'               => __( 'Venue', 'joomsport-sports-league-results-management' ),
                                                'singular_name'      => __( 'Venue', 'joomsport-sports-league-results-management' ),
                                                'menu_name'          => _x( 'Venues', 'Admin menu name Venues', 'joomsport-sports-league-results-management' ),
                                                'add_new'            => __( 'Add Venue', 'joomsport-sports-league-results-management' ),
                                                'add_new_item'       => __( 'Add New Venue', 'joomsport-sports-league-results-management' ),
                                                'edit'               => __( 'Edit', 'joomsport-sports-league-results-management' ),
                                                'edit_item'          => __( 'Edit Venue', 'joomsport-sports-league-results-management' ),
                                                'new_item'           => __( 'New Venue', 'joomsport-sports-league-results-management' ),
                                                'view'               => __( 'View Venue', 'joomsport-sports-league-results-management' ),
                                                'view_item'          => __( 'View Venue', 'joomsport-sports-league-results-management' ),
                                                'search_items'       => __( 'Search Venue', 'joomsport-sports-league-results-management' ),
                                                'not_found'          => __( 'No Venue found', 'joomsport-sports-league-results-management' ),
                                                'not_found_in_trash' => __( 'No Venue found in trash', 'joomsport-sports-league-results-management' ),
                                                'parent'             => __( 'Parent Venue', 'joomsport-sports-league-results-management' )
                                        ),
                                'description'         => __( 'This is where you can add new venue.', 'joomsport-sports-league-results-management' ),
                                'public'              => true,
                                'show_ui'             => $showui,
                                'show_in_menu'        => 'joomsport',
                                'publicly_queryable'  => true,
                                'exclude_from_search' => false,
                                'hierarchical'        => false,
                                'query_var'           => true,
                                'supports'            => array( 'title' ),
                                'show_in_nav_menus'   => true,
                                'rewrite' => array(
                                    'slug' => $slug?$slug:'joomsport_venue'
                                )
                        )
                )
        );
    }
    public static function venue_edit_form_after_title($post_type){
        global $post, $wp_meta_boxes;

        if($post_type->post_type == 'joomsport_venue'){
            
            echo JoomSportMetaVenue::output($post_type);

        }
    

    }
    public static function admin_init(){
        add_meta_box('joomsport_venue_general_form_meta_box', __('General', 'joomsport-sports-league-results-management'), array('JoomSportMetaVenue','js_meta_personal'), 'joomsport_venue', 'joomsportintab_venue1', 'default');
        add_meta_box('joomsport_venue_about_form_meta_box', __('Venue description', 'joomsport-sports-league-results-management'), array('JoomSportMetaVenue','js_meta_about'), 'joomsport_venue', 'joomsportintab_venue1', 'default');
        
        add_meta_box('joomsport_venue_ef_form_meta_box', __('Extra fields', 'joomsport-sports-league-results-management'), array('JoomSportMetaVenue','js_meta_ef'), 'joomsport_venue', 'joomsportintab_venue1', 'default');
        
        
        add_action( 'save_post',      array( 'JoomSportMetaVenue', 'joomsport_venue_save_metabox' ), 10, 2 );
    }
}    
