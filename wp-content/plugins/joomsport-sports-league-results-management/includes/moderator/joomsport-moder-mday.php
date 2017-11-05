<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomSportModerMday{
    public static function showMdays(){
        $obj = new JoomSportMdayModer_Plugin();
        $obj->screen_option();
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'eview'){
            $obj->plugin_eview_page();

        }else{
            $obj->plugin_settings_page();
        }
        
    }
    
    
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class JoomSportMdayModer_List_Table extends WP_List_Table {

    public function __construct() {

        parent::__construct( array(
                'singular' => __( 'Matchday', 'joomsport-sports-league-results-management' ), 
                'plural'   => __( 'Matchdays', 'joomsport-sports-league-results-management' ),
                'ajax'     => false 

        ) );

        /** Process bulk action */
        $this->process_bulk_action();

    }

    public static function get_stages( $per_page = 5, $page_number = 1 ) {

        global $wpdb;
        $season_id = isset($_REQUEST['season_id'])?intval($_REQUEST['season_id']):0;
        $canAddMatches = JoomSportUserRights::canAddMatches();
        $my_posts = JoomSportUserRights::getUserPosts();
        //wp_term_taxonomy
        $sql = "SELECT t.term_id as id, t.name as e_name"
                . " FROM {$wpdb->term_taxonomy} as tt"
                . " JOIN {$wpdb->terms} as t ON t.term_id = tt.term_id"
                . " WHERE tt.taxonomy = 'joomsport_matchday'";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
          $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
          $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        if(!$season_id){
            $sql .= " LIMIT $per_page";

            $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        }
        
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        
        if($season_id){
            $seasres = array();
            for($intA = 0; $intA < count($result); $intA++){
                $metas = get_option("taxonomy_{$result[$intA]['id']}_metas");
                $md_season_id = $metas['season_id'];
                if($md_season_id == $season_id){
                    if($canAddMatches){
                        $seasres[] = $result[$intA];
                    }elseif(count($my_posts)){
                        
                        $metaquery[] = 
                        array(
                            'relation' => 'OR',
                                array(
                            'key' => '_joomsport_home_team',
                            'value' => $my_posts,
                            'compare' => 'IN'
                            ),

                            array(
                            'key' => '_joomsport_away_team',
                            'value' => $my_posts,
                                'compare' => 'IN'
                            ) 
                        ) ;
                        $matches = new WP_Query(array(
                            'post_type' => 'joomsport_match',
                            'posts_per_page'   => -1,
                            'author' => get_current_user_id(),
                            'post_status' => 'publish',
                            'tax_query' => array(
                                array(
                                'taxonomy' => 'joomsport_matchday',
                                'field' => 'term_id',
                                'terms' => $result[$intA]['id'])
                            ),
                            'meta_query' => $metaquery    
                        ));
                        if($matches->post_count){
                            $seasres[] = $result[$intA];
                        }
                    }
                    
                }
            }
            return $seasres;
        }elseif(!$canAddMatches && count($my_posts)){
             for($intA = 0; $intA < count($result); $intA++){
                $metas = get_option("taxonomy_{$result[$intA]['id']}_metas");
                $md_season_id = $metas['season_id'];
                $metaquery[] = 
                array(
                    'relation' => 'OR',
                        array(
                    'key' => '_joomsport_home_team',
                    'value' => $my_posts,
                    'compare' => 'IN'
                    ),

                    array(
                    'key' => '_joomsport_away_team',
                    'value' => $my_posts,
                        'compare' => 'IN'
                    ) 
                ) ;
                $matches = new WP_Query(array(
                    'post_type' => 'joomsport_match',
                    'posts_per_page'   => -1,
                    'author' => get_current_user_id(),
                    'post_status' => 'publish',
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'joomsport_matchday',
                        'field' => 'term_id',
                        'terms' => $result[$intA]['id'])
                    ),
                    'meta_query' => $metaquery    
                ));
                if($matches->post_count){
                    $seasres[] = $result[$intA];
                }
            }
            return $seasres;
        }
        
        
        

        return $result;
    }
    public static function delete_stage( $id ) {
        global $wpdb;

    }
    public static function record_count() {
        global $wpdb;
        $season_id = isset($_REQUEST['season_id'])?intval($_REQUEST['season_id']):0;
        
        $sql = "SELECT t.term_id as id, t.name as e_name"
                . " FROM {$wpdb->term_taxonomy} as tt"
                . " JOIN {$wpdb->terms} as t ON t.term_id = tt.term_id"
                . " WHERE tt.taxonomy = 'joomsport_matchday'";



        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        if($season_id){
            $seasres = array();
            for($intA = 0; $intA < count($result); $intA++){
                $metas = get_option("taxonomy_{$result[$intA]['id']}_metas");
                $md_season_id = $metas['season_id'];
                if($md_season_id == $season_id){
                    $seasres[] = $result[$intA];
                }
            }
            return count($seasres);
        }
        return count($result);
        
    }
    public function no_items() {
        echo __( 'No matchdays avaliable.', 'joomsport-sports-league-results-management' );
    }
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'joomsport_delete_event' );

        $title = '<strong><a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport_mday_moder&action=eview&id='.absint( $item['id'] ).'').'">' . $item['e_name'] . '</a></strong>';

        $actions = array();

        return $title . $this->row_actions( $actions );
    }
    
    function column_cb( $item ) {
        return '';
    }
    function get_columns() {
        $columns = array(
          'name'    => __( 'Name', 'joomsport-sports-league-results-management' ),
          'season_name'    => __( 'Season', 'joomsport-sports-league-results-management' ),  
        );

        return $columns;
    }
    function column_default($item, $column_name){
        switch($column_name){

            case 'season_name':
                $metas = get_option("taxonomy_{$item['id']}_metas");

                return get_the_title($metas['season_id']);

            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    public function get_sortable_columns() {
        $sortable_columns = array(
          'name' => array( 'name', true )
        );

        return $sortable_columns;
    }
    public function get_bulk_actions() {
        $actions = array(
        );

        return $actions;
    }
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        $season_id = isset($_REQUEST['season_id'])?intval($_REQUEST['season_id']):0;

        $per_page     = $this->get_items_per_page( 'jsevents_per_page', 20 );
        if($season_id){
            $per_page = 100;
        }
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
          'total_items' => $total_items, //WE have to calculate the total number of items
          'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );


        $this->items = self::get_stages( $per_page, $current_page );
    }
    
    
    
    public function process_bulk_action() {
        if ( 'eview' === $this->current_action() ) {
            

        }
    }
    
}


class JoomSportMdayModer_Plugin {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $customers_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		//add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
            
         echo '<div class="jslinktopro jscenterpage">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
	}
        
        /**
	 * Plugin settings page
	 */
	public function plugin_eview_page() {
            
         echo '<div class="jslinktopro jscenterpage">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
	}
        
	/**
	 * Screen options
	 */
	public function screen_option() {
            if(isset($_POST['wp_screen_options']['option'])){
                update_user_meta(get_current_user_id(), 'jsevents_per_page', $_POST['wp_screen_options']['value']);

            }
            $option = 'per_page';
            $args   = array(
                    'label'   => 'Matchdays',
                    'default' => 20,
                    'option'  => 'jsevents_per_page'
            );

            add_screen_option( $option, $args );

            $this->customers_obj = new JoomSportMdayModer_List_Table();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
