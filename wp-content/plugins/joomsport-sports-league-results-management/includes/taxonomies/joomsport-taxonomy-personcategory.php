<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */


class JoomSportTaxonomyPersoncategory {
    public function __construct() {

    }
    public static function init(){
        self::register_taxonomy();
    }
    public static function register_taxonomy(){
        $labels = array(
                'name' => __( 'Person Categories', 'joomsport-sports-league-results-management' ),
                'singular_name' => __( 'Person Category', 'joomsport-sports-league-results-management' ),
                'all_items' => __( 'All', 'joomsport-sports-league-results-management' ),
                'edit_item' => __( 'Edit Person Category', 'joomsport-sports-league-results-management' ),
                'view_item' => __( 'View', 'joomsport-sports-league-results-management' ),
                'update_item' => __( 'Update', 'joomsport-sports-league-results-management' ),
                'add_new_item' => __( 'Add New', 'joomsport-sports-league-results-management' ),
                'new_item_name' => __( 'Name', 'joomsport-sports-league-results-management' ),
                'parent_item' => __( 'Parent', 'joomsport-sports-league-results-management' ),
                'parent_item_colon' => __( 'Parent:', 'joomsport-sports-league-results-management' ),
                'search_items' =>  __( 'Search', 'joomsport-sports-league-results-management' ),
                'not_found' => __( 'No results found.', 'joomsport-sports-league-results-management' ),
        );
        $args = array(
                'label' => __( 'Person Categories', 'joomsport-sports-league-results-management' ),
                'labels' => $labels,
                'public' => true,
                'show_ui'             => true,
                'show_in_menu'        => 'joomsport',
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
                'hierarchical' => false,
                
            
            
        );
        $object_types = apply_filters( 'joomsport_personcategory_object_types', array( 'joomsport_person' ) );
        
        register_taxonomy( 'joomsport_personcategory', $object_types, $args );
        
        
        
        $tournament_tax = new JoomSportTaxonomyDropPersoncategory('joomsport_personcategory', 'personcategory', array('joomsport_person'));
        add_action('add_meta_boxes', array( $tournament_tax, 'joomsport_custom_meta_box'));
        add_action( 'save_post', array( $tournament_tax, 'taxonomy_save_postdata') );
        
        /*require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-tournament.php';
        add_filter( 'manage_edit-joomsport_tournament_columns', array( 'JoomSportMetaTournament','tournament_type_columns') );
        add_action("manage_joomsport_tournament_custom_column", array( 'JoomSportMetaTournament','manage_joomsport_tournament_columns'), 10, 3);
        add_action('joomsport_tournament_edit_form_fields',array( 'JoomSportMetaTournament', 'joomsport_tournament_edit_form_fields'));
        add_action('joomsport_tournament_add_form_fields',array( 'JoomSportMetaTournament', 'joomsport_tournament_add_form_fields'));
        add_action('edited_joomsport_tournament', array( 'JoomSportMetaTournament', 'joomsport_tournament_save_form_fields'), 10, 2);
        add_action('created_joomsport_tournament', array( 'JoomSportMetaTournament', 'joomsport_tournament_save_form_fields'), 10, 2);
        */
        
    }
}    

// class ovveride taxonomy to dropdown

class JoomSportTaxonomyDropPersoncategory{
    public $pages = array();
    public $name = null;
    public $name_slug = null;
    
    public function __construct($name, $name_slug, $pages){
        $this->name = $name;
        $this->name_slug = $name_slug;
        $this->pages = $pages;
    }
    
    public function joomsport_custom_meta_box() {
        $jsconfig =  new JoomsportSettings();
        remove_meta_box( 'tagsdiv-'.$this->name, 'joomsport_person', 'side' );
        
        add_meta_box( 'tagsdiv-'.$this->name, __( 'Person Category', 'joomsport-sports-league-results-management' ), array( $this, 'drop_meta_box'), 'joomsport_person', 'side' );
        
    }
    
    public function drop_meta_box($post) {
        global $joomsportSettings;
        $taxonomy = get_taxonomy($this->name_slug);
        ?>
        <div class="tagsdiv" id="<?php echo $this->name_slug; ?>">
            <div class="jaxtag">
            <?php 
            wp_nonce_field( plugin_basename( __FILE__ ), $this->name_slug.'_noncename' );
            $type_IDs = wp_get_object_terms( $post->ID, $this->name, array('fields' => 'ids') );
            
            $current_tournament = !isset($type_IDs[0]) ? 0 : $type_IDs[0];
            
            if(get_bloginfo('version') < '4.5.0'){
                $tx = get_terms('joomsport_personcategory',array(
                    "hide_empty" => false
                ));
            }else{
                $tx = get_terms(array(
                    "taxonomy" => "joomsport_personcategory",
                    "hide_empty" => false,
                    
                ));
            }

            echo '<select name="joomsport_personcategory" id="joomsport_personcategory_inseas_id" class="postform" aria-required="true">';
                echo '<option value="-1">'.__('Select Category','joomsport-sports-league-results-management').'</option>';
                for($intA=0;$intA<count($tx);$intA++){
 
                        echo '<option value="'.$tx[$intA]->term_id.'" '.($tx[$intA]->term_id == $current_tournament?'selected':'').'>'.$tx[$intA]->name.'</option>';

                }

            echo '</select>';
            //echo '<select>'
            //wp_dropdown_categories('taxonomy='.$this->name.'&t_single=1&hide_empty=0&orderby=name&name='.$this->name.'&show_option_none=Select '.$this->name_slug.'&selected='.$current_tournament); ?>
            </div>
        </div>
        <?php
    }
     public  function taxonomy_save_postdata( $post_id ) {

      if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) 
          return;

      if(!isset($_POST[$this->name_slug.'_noncename']))
          return;    
      if ( !wp_verify_nonce( $_POST[$this->name_slug.'_noncename'], plugin_basename( __FILE__ ) ) )
          return;
      if ( 'joomsport_person' == $_POST['post_type'] ) 
      {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
      }
      else
      {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
      }

      $type_ID = $_POST[$this->name];

      $type = ( $type_ID > 0 ) ? get_term( $type_ID, $this->name )->slug : NULL;
      
      wp_set_object_terms(  $post_id , $type, $this->name );

    }
    
}