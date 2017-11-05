<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */


class JoomSportPostTypes {
    public function __construct() {
        add_action( 'init', array( __CLASS__, 'register_post_types' ), 0 );
        add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 0 );
        
    }
   

    public static function register_post_types(){
        if ( post_type_exists('joomsport_tournament') ) {
            return;
        }
        
        $custom_posts = array(
            "joomsport-post-season",
            "joomsport-post-team",
            "joomsport-post-match",
            "joomsport-post-player",
            "joomsport-post-person"
        );
        
        $custom_posts[] = ("joomsport-post-venue");
        
        foreach ($custom_posts as $cpost) {
            include_once JOOMSPORT_PATH_INCLUDES . 'posts' . DIRECTORY_SEPARATOR . $cpost . '.php';
            $className = str_replace('-', '', $cpost);
            $postObject = new $className();
            $postObject->init();
        }
        flush_rewrite_rules();

    }
    
    public static function register_taxonomies(){
        $custom_taxonomies = array(
            "joomsport-taxonomy-tournament",
            "joomsport-taxonomy-matchday",
            "joomsport-taxonomy-personcategory"
        );
        
        $custom_taxonomies[] = ("joomsport-taxonomy-club");
        
        foreach ($custom_taxonomies as $ctaxonomy) {
            include_once JOOMSPORT_PATH_INCLUDES . 'taxonomies' . DIRECTORY_SEPARATOR . $ctaxonomy . '.php';
            $className = str_replace('-', '', $ctaxonomy);
            $postObject = new $className();
            $postObject->init();
        }
        flush_rewrite_rules();
    }
 
}
new JoomSportPostTypes();

