<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

class JoomSportTaxonomyTournament {
    public function __construct() {

    }
    public static function init(){
        self::register_taxonomy();

    }

    public static function register_taxonomy(){

        $labels = array(
                'name' => __( 'Leagues', 'joomsport-sports-league-results-management' ),
                'singular_name' => __( 'League', 'joomsport-sports-league-results-management' ),
                'all_items' => __( 'All', 'joomsport-sports-league-results-management' ),
                'edit_item' => __( 'Edit League', 'joomsport-sports-league-results-management' ),
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
                'label' => __( 'Leagues', 'joomsport-sports-league-results-management' ),
                'labels' => $labels,
                'public'            =>  true,
                'publicly_queryable'=>  true,
                'show_ui'           =>  true, 
                'query_var'         =>  true,
                'show_in_menu'        => 'joomsport',
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
                'hierarchical' => false,
                'exclude_from_search' => true,
                "singular_label" => "joomsport_tournament",
                'rewrite' => array('slug' => 'joomsport_tournament', 'with_front'    => false),
        );
        $object_types = apply_filters( 'joomsport_tournament_object_types', array( 'joomsport_season' ) );
        register_taxonomy( 'joomsport_tournament', $object_types, $args );
        foreach ( $object_types as $object_type ):
                register_taxonomy_for_object_type( 'joomsport_tournament', $object_type );
        endforeach;

        $tournament_tax = new JoomSportTaxonomyDrop('joomsport_tournament', __( 'League', 'joomsport-sports-league-results-management' ), array('joomsport_season'));
        add_action('add_meta_boxes', array( $tournament_tax, 'joomsport_custom_meta_box'));
        add_action( 'save_post', array( $tournament_tax, 'taxonomy_save_postdata') );
        
        require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-tournament.php';
        add_filter( 'manage_edit-joomsport_tournament_columns', array( 'JoomSportMetaTournament','tournament_type_columns') );
        add_action("manage_joomsport_tournament_custom_column", array( 'JoomSportMetaTournament','manage_joomsport_tournament_columns'), 10, 3);
        add_action('joomsport_tournament_edit_form_fields',array( 'JoomSportMetaTournament', 'joomsport_tournament_edit_form_fields'));
        add_action('joomsport_tournament_add_form_fields',array( 'JoomSportMetaTournament', 'joomsport_tournament_add_form_fields'));
        add_action('edited_joomsport_tournament', array( 'JoomSportMetaTournament', 'joomsport_tournament_save_form_fields'), 10, 2);
        add_action('created_joomsport_tournament', array( 'JoomSportMetaTournament', 'joomsport_tournament_save_form_fields'), 10, 2);

        
    }
}    

// class ovveride taxonomy to dropdown

class JoomSportTaxonomyDrop{
    public $pages = array();
    public $name = null;
    public $name_slug = null;
    
    public function __construct($name, $name_slug, $pages){
        $this->name = $name;
        $this->name_slug = $name_slug;
        $this->pages = $pages;
    }
    
    public function joomsport_custom_meta_box() {

        remove_meta_box( 'tagsdiv-'.$this->name, 'joomsport_season', 'side' );

        add_meta_box( 'tagsdiv-'.$this->name, $this->name_slug.' *', array( $this, 'drop_meta_box'), 'joomsport_season', 'side', 'default' );

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
            
            if(!$current_tournament && isset($_GET['tid']) && intval($_GET['tid'])){
                $current_tournament = intval($_GET['tid']);
            }
            
            if(get_bloginfo('version') < '4.5.0'){
                $tx = get_terms('joomsport_tournament',array(
                    "hide_empty" => false
                ));
            }else{
                $tx = get_terms(array(
                    "taxonomy" => "joomsport_tournament",
                    "hide_empty" => false,

                ));
            }

           /* echo '<select name="joomsport_tournament" id="joomsport_tournament_inseas_id" class="postform" aria-required="true">';
                echo '<option value="-1">'.__('Select Tournament','joomsport-sports-league-results-management').'</option>';
                for($intA=0;$intA<count($tx);$intA++){
                    $term_meta = get_option( "taxonomy_".$tx[$intA]->term_id."_metas");
                    if($current_tournament){
                        $term_current = get_option( "taxonomy_".$current_tournament."_metas");
                        if($term_meta['t_single'] == $term_current['t_single']){
                            echo '<option value="'.$tx[$intA]->term_id.'" '.($tx[$intA]->term_id == $current_tournament?'selected':'').'>'.$tx[$intA]->name.'</option>';

                        }
                    }else{
                        if($term_meta['t_single'] == $joomsportSettings->get('tournament_type')){
                            echo '<option value="'.$tx[$intA]->term_id.'" '.($tx[$intA]->term_id == $current_tournament?'selected':'').'>'.$tx[$intA]->name.'</option>';

                        }
                    }
                }

            echo '</select>';*/
            $ctx = get_term($current_tournament, 'joomsport_tournament');
            echo $ctx->name;
            echo '<input type="hidden" name="joomsport_tournament" value="'.$current_tournament.'" />';
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

      if ( 'joomsport_season' == $_POST['post_type'] ) 
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
/*
function JSTRNrewrite_taxonomy_term_permalink($termlink, $term, $taxonomy) {
  if ('joomsport_tournament' == $taxonomy) {
    return home_url() . '/' . $term->slug . '-tournament/';
  }

  return $termlink;
}
add_filter( 'term_link', 'JSTRNrewrite_taxonomy_term_permalink', 10, 3);
function JSTRN_rewrite_rules($wp_rewrite) {
  $new_rules = array(
      '(.+?)-tournament/?$' =>  'index.php?joomsport_tournament=$matches[1]' 
  );
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
  //var_dump($wp_rewrite);
  return $wp_rewrite;
}
add_action('generate_rewrite_rules', 'JSTRN_rewrite_rules');
 * 
 */