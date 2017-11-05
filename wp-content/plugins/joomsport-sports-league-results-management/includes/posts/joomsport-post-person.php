<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-person.php';

class JoomSportPostPerson {
    public function __construct() {

    }
    public static function init(){
        self::register_post_types();
    }
    public static function register_post_types(){
        add_action("admin_init", array("JoomSportPostPerson","admin_init"));
        add_action( 'edit_form_after_title',  array( 'JoomSportPostPerson','person_edit_form_after_title') );
        $slug = get_option( 'joomsportslug_joomsport_person', null );
        register_post_type( 'joomsport_person',
                apply_filters( 'joomsport_register_post_type_person',
                        array(
                                'labels'              => array(
                                                'name'               => __( 'Person', 'joomsport-sports-league-results-management' ),
                                                'singular_name'      => __( 'Person', 'joomsport-sports-league-results-management' ),
                                                'menu_name'          => _x( 'Persons', 'Admin menu name Players', 'joomsport-sports-league-results-management' ),
                                                'add_new'            => __( 'Add Person', 'joomsport-sports-league-results-management' ),
                                                'add_new_item'       => __( 'Add New Person', 'joomsport-sports-league-results-management' ),
                                                'edit'               => __( 'Edit', 'joomsport-sports-league-results-management' ),
                                                'edit_item'          => __( 'Edit Person', 'joomsport-sports-league-results-management' ),
                                                'new_item'           => __( 'New Person', 'joomsport-sports-league-results-management' ),
                                                'view'               => __( 'View Person', 'joomsport-sports-league-results-management' ),
                                                'view_item'          => __( 'View Person', 'joomsport-sports-league-results-management' ),
                                                'search_items'       => __( 'Search Person', 'joomsport-sports-league-results-management' ),
                                                'not_found'          => __( 'No Person found', 'joomsport-sports-league-results-management' ),
                                                'not_found_in_trash' => __( 'No Person found in trash', 'joomsport-sports-league-results-management' ),
                                                'parent'             => __( 'Parent Person', 'joomsport-sports-league-results-management' )
                                        ),
                                'description'         => __( 'This is where you can add new person.', 'joomsport-sports-league-results-management' ),
                                'public'              => true,
                                'show_ui'             => true,
                                'show_in_menu'        => 'joomsport',
                                'publicly_queryable'  => true,
                                'exclude_from_search' => false,
                                'hierarchical'        => false,
                                'query_var'           => true,
                                'supports'            => array( 'title' ),
                                'show_in_nav_menus'   => true,
                                'rewrite' => array(
                                    'slug' => $slug?$slug:'joomsport_person'
                                )
                                
                        )
                )
        );
    }
    public static function person_edit_form_after_title($post_type){
        global $post, $wp_meta_boxes;

        if($post_type->post_type == 'joomsport_person'){
            
            echo JoomSportMetaPerson::output($post_type);

        }
    

    }
    public static function admin_init(){
        add_meta_box('joomsport_person_personal_form_meta_box', __('Personal', 'joomsport-sports-league-results-management'), array('JoomSportMetaPerson','js_meta_personal'), 'joomsport_person', 'joomsportintab_person1', 'default');
        add_meta_box('joomsport_person_about_form_meta_box', __('About person', 'joomsport-sports-league-results-management'), array('JoomSportMetaPerson','js_meta_about'), 'joomsport_person', 'joomsportintab_person1', 'default');

        add_meta_box('joomsport_person_ef_form_meta_box', __('Extra fields', 'joomsport-sports-league-results-management'), array('JoomSportMetaPerson','js_meta_ef'), 'joomsport_person', 'joomsportintab_person1', 'default');

        
        
        add_action( 'save_post',      array( 'JoomSportMetaPerson', 'joomsport_person_save_metabox' ), 10, 2 );
    }
    
}    