<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class JoomSportStages_List_Table extends WP_List_Table {

    public function __construct() {

        parent::__construct( array(
                'singular' => __( 'Game stage', 'joomsport-sports-league-results-management' ), 
                'plural'   => __( 'Game stages', 'joomsport-sports-league-results-management' ),
                'ajax'     => false 

        ) );
        /** Process bulk action */
        $this->process_bulk_action();

    }
    public static function get_stages( $per_page = 5, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->joomsport_maps}";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
          $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
          $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
    public static function delete_stage( $id ) {
        global $wpdb;

        $wpdb->delete(
          "{$wpdb->joomsport_maps}",
          array( 'id' => $id ),
          array( '%d' )
        );
    }
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->joomsport_maps}";

        return $wpdb->get_var( $sql );
    }
    public function no_items() {
        echo __( 'No game stages available.', 'joomsport-sports-league-results-management' );
    }
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'joomsport_delete_gamestage' );

        $title = '<strong><a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-gamestages-form&id='.absint( $item['id'] )).'">' . $item['m_name'] . '</a></strong>';

        $actions = array(
          'delete' => sprintf( '<a href="?page=%s&action=%s&gamestage=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }
    
    function column_cb( $item ) {
        return sprintf(
          '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }
    function get_columns() {
        $columns = array(
          'cb'      => '<input type="checkbox" />',
          'name'    => __( 'Name', 'sp' )
        );

        return $columns;
    }
    public function get_sortable_columns() {
        $sortable_columns = array(
          'name' => array( 'name', true )
        );

        return $sortable_columns;
    }
    public function get_bulk_actions() {
        $actions = array(
          'bulk-delete' => 'Delete'
        );

        return $actions;
    }
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        

        $per_page     = $this->get_items_per_page( 'stages_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
          'total_items' => $total_items, //WE have to calculate the total number of items
          'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );


        $this->items = self::get_stages( $per_page, $current_page );
    }
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
          // In our file that handles the request, verify the nonce.
          $nonce = esc_attr( $_REQUEST['_wpnonce'] );

          if ( ! wp_verify_nonce( $nonce, 'joomsport_delete_gamestage' ) ) {
            die( 'Error' );
          }
          else {
            self::delete_stage( absint( $_GET['gamestage'] ) );
            wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-gamestages' ) );
            exit;
          }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
             || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

          $delete_ids = esc_sql( $_POST['bulk-delete'] );

          // loop over the array of record IDs and delete them
          foreach ( $delete_ids as $id ) {
            self::delete_stage( $id );

          }

          wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-gamestages' ) );
          exit;
        }
    }
    
}


class JoomSportStages_Plugin {

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
		?>
		<div class="wrap">
			<h2><?php echo __('Game stages', 'joomsport-sports-league-results-management');?>
                        <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-gamestages-form');?>"><?php echo __('Add new', 'joomsport-sports-league-results-management')?></a>
                        </h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->customers_obj->prepare_items();
								$this->customers_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
                    <script type="text/javascript" id="UR_initiator"> (function () { var iid = 'uriid_'+(new Date().getTime())+'_'+Math.floor((Math.random()*100)+1); if (!document._fpu_) document.getElementById('UR_initiator').setAttribute('id', iid); var bsa = document.createElement('script'); bsa.type = 'text/javascript'; bsa.async = true; bsa.src = '//beardev.useresponse.com/sdk/supportCenter.js?initid='+iid+'&wid=6'; (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(bsa); })(); </script>
		</div>
	<?php

	}

	/**
	 * Screen options
	 */
	public function screen_option() {
            if(isset($_POST['wp_screen_options']['option'])){
                update_user_meta(get_current_user_id(), 'stages_per_page', $_POST['wp_screen_options']['value']);



            }
		$option = 'per_page';
		$args   = array(
			'label'   => 'Stages',
			'default' => 5,
			'option'  => 'stages_per_page'
		);

		add_screen_option( $option, $args );

		$this->customers_obj = new JoomSportStages_List_Table();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


class JoomSportStagesNew_Plugin {
    public static function view(){

        global $wpdb;
        $table_name = $wpdb->joomsport_maps; 

        $message = '';
        $notice = '';

        // this is default $item which will be used for new records
        $default = array(
            'id' => 0,
            'm_name' => ''

        );
        $item = array();
        // here we are verifying does this request is post back and have correct nonce
        if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = self::joomsport_gamestages_validate($item);
            if ($item_valid === true) {
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'joomsport-sports-league-results-management');
                    } else {
                        $notice = __('There was an error while saving item', 'joomsport-sports-league-results-management');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    if ($result) {
                        $message = __('Item was successfully updated', 'joomsport-sports-league-results-management');
                    } else {
                        $notice = __('There was an error while updating item', 'joomsport-sports-league-results-management');
                    }
                }
                echo '<script> window.location="'.(esc_url(get_dashboard_url())).'admin.php?page=joomsport-page-gamestages"; </script> ';
                
            } else {
                // if $item_valid not true it contains error message(s)
                $notice = $item_valid;
            }
        }
        else {
            // if this is not post back we load item to edit or give new one to create
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'joomsport-sports-league-results-management');
                }
            }
        }

        // here we adding our custom meta box
        add_meta_box('joomsport_gamestage_form_meta_box', __('Details', 'joomsport-sports-league-results-management'), array('JoomSportStagesNew_Plugin','joomsport_gamestage_form_meta_box_handler'), 'joomsport-gamestages-form', 'normal', 'default');

        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php echo __('Game stage', 'joomsport-sports-league-results-management')?> <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-gamestages');?>"><?php echo __('back to list', 'joomsport-sports-league-results-management')?></a>
            </h2>

            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php endif;?>

            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
                <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content"  class="jsRemoveMB">
                            <?php /* And here we call our custom meta box */ ?>
                            <?php do_meta_boxes('joomsport-gamestages-form', 'normal', $item); ?>
                            <input type="submit" value="<?php echo __('Save & close', 'joomsport-sports-league-results-management')?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    public static function joomsport_gamestage_form_meta_box_handler($item)
    {
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="name"><?php echo __('Name', 'joomsport-sports-league-results-management')?></label>
            </th>
            <td>
                <input id="m_name" name="m_name" type="text" style="width: 95%" value="<?php echo esc_attr(isset($item['m_name'])?$item['m_name']:"")?>"
                       size="50" class="code"  required>
            </td>
        </tr>
        
        </tbody>
    </table>
    <?php
    }
    public static function joomsport_gamestages_validate($item)
    {
        $messages = array();

        if (empty($item['m_name'])) $messages[] = __('Name is required', 'joomsport-sports-league-results-management');
        //if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
        //if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'custom_table_example');
        //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
        //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
        //...

        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }
}