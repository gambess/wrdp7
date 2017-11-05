<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class JoomSportEvents_List_Table extends WP_List_Table {

    public function __construct() {

        parent::__construct( array(
                'singular' => __( 'Event', 'joomsport-sports-league-results-management' ), 
                'plural'   => __( 'Events stats', 'joomsport-sports-league-results-management' ),
                'ajax'     => false 

        ) );

        /** Process bulk action */
        $this->process_bulk_action();

    }

    public static function get_stages( $per_page = 5, $page_number = 1 ) {

        global $wpdb;
        
        
        $sql = "SELECT * FROM {$wpdb->joomsport_events}";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
          $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
          $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }else{
            $sql .= ' ORDER BY ordering';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
    public static function delete_stage( $id ) {
        global $wpdb;

        $wpdb->delete(
          "{$wpdb->joomsport_events}",
          array('id' => $id ),
          array( '%d' )
        );
    }
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->joomsport_events}";

        return $wpdb->get_var( $sql );
    }
    public function no_items() {
        echo __( 'No events available.', 'joomsport-sports-league-results-management' );
    }
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'joomsport_delete_event' );

        $title = '<strong><a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-events-form&id='.absint( $item['id'] )).'">' . $item['e_name'] . '</a></strong>';

        $actions = array(
          'delete' => sprintf( '<a href="?page=%s&action=%s&event=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
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
          'name'    => __( 'Name', 'joomsport-sports-league-results-management' ),
          'player_event'    => __( 'Event type', 'joomsport-sports-league-results-management' ),  
        );

        return $columns;
    }
    function column_default($item, $column_name){
        switch($column_name){

            case 'player_event':
                $is_field = array();
                $is_field[0] = __("Match", "joomsport-sports-league-results-management");
                $is_field[1] = __("Player", "joomsport-sports-league-results-management");
                $is_field[2] = __("Sum of events", "joomsport-sports-league-results-management");

                return $is_field[$item['player_event']];

            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    public function get_sortable_columns() {
        $sortable_columns = array(
          'name' => array( 'name', true ),
            'player_event' => array( 'player_event', true ),
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

        

        $per_page     = $this->get_items_per_page( 'jsevents_per_page', 5 );
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

          if ( ! wp_verify_nonce( $nonce, 'joomsport_delete_event' ) ) {
            die( 'Error' );
          }
          else {
            self::delete_stage( absint( $_GET['event'] ) );
            wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-events' ) );
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

          wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-events' ) );
          exit;
        }
    }
    
}


class JoomSportEvents_Plugin {

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
			<h2><?php echo __('Events stats', 'joomsport-sports-league-results-management');?>
                        <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-events-form');?>"><?php echo __('Add new', 'joomsport-sports-league-results-management')?></a>
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
                update_user_meta(get_current_user_id(), 'jsevents_per_page', $_POST['wp_screen_options']['value']);



            }
		$option = 'per_page';
		$args   = array(
			'label'   => 'Events stats',
			'default' => 5,
			'option'  => 'jsevents_per_page'
		);

		add_screen_option( $option, $args );

		$this->customers_obj = new JoomSportEvents_List_Table();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

class JoomSportEventsNew_Plugin {
    public static function view(){

        global $wpdb;
        $table_name = $wpdb->joomsport_events; 

        $message = '';
        $notice = '';

        // this is default $item which will be used for new records
        $default = array(
            'id' => 0,
            'e_name' => '',
            'e_img' => '',
            'player_event' => '0',
            'result_type' => '0',
            'sumev1' => 0,
            'sumev2' => 0,
            'ordering' => 0,
            'events_sum' => 0,
            'subevents' => '',
        );
        
        $item = array();
        // here we are verifying does this request is post back and have correct nonce
        if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            $lists = self::getListValues($item);
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = self::joomsport_events_validate($item);
            if(isset($_POST['subevents']) && count($_POST['subevents'])){
                $item['subevents'] = json_encode($_POST['subevents']);
            }
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
                echo '<script> window.location="'.(esc_url(get_dashboard_url())).'admin.php?page=joomsport-page-events"; </script> ';
                
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
            $lists = self::getListValues($item);
        }

        // here we adding our custom meta box
        add_meta_box('joomsport_event_form_meta_box', __('Details', 'joomsport-sports-league-results-management'), array('JoomSportEventsNew_Plugin','joomsport_event_form_meta_box_handler'), 'joomsport-events-form', 'normal', 'default');
        
        wp_enqueue_script('media-upload');
        wp_enqueue_script('wp-mediaelement');

        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php echo __('Event', 'joomsport-sports-league-results-management')?> <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-events');?>"><?php echo __('back to list', 'joomsport-sports-league-results-management')?></a>
            </h2>

            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php endif;?>
            <script>
            jQuery(function($){

  // Set all variables to be used in scope
  var frame,
      metaBox = $('#jseventcontainer'), // Your meta box id here
      addImgLink = metaBox.find('#jsEventImage'),
      delImgLink = metaBox.find( '.delete-jsev-img'),
      imgContainer = metaBox.find( '.jsev-img-container'),
      imgIdInput = metaBox.find( '.jsev-img-id' );
  
  // ADD IMAGE LINK
  addImgLink.on( 'click', function( event ){
    
    event.preventDefault();
    
    // If the media frame already exists, reopen it.
    if ( frame ) {
      frame.open();
      return;
    }
    
    // Create a new media frame
    frame = wp.media({
      title: 'Select or Upload Media Of Your Chosen Persuasion',
      button: {
        text: 'Use this media'
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    
    // When an image is selected in the media frame...
    frame.on( 'select', function() {
      
      // Get media attachment details from the frame state
      var attachment = frame.state().get('selection').first().toJSON();

      // Send the attachment URL to our custom image input field.
      imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

      // Send the attachment id to our hidden input
      imgIdInput.val( attachment.id );

      // Hide the add image link
      addImgLink.addClass( 'hidden' );

      // Unhide the remove image link
      delImgLink.removeClass( 'hidden' );
    });

    // Finally, open the modal on click
    frame.open();
  });
  
  
  // DELETE IMAGE LINK
  delImgLink.on( 'click', function( event ){

    event.preventDefault();

    // Clear out the preview image
    imgContainer.html( '' );

    // Un-hide the add image link
    addImgLink.removeClass( 'hidden' );

    // Hide the delete image link
    delImgLink.addClass( 'hidden' );

    // Delete the image id from the hidden input
    imgIdInput.val( '' );

  });

});
            </script>
            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
                <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content" class="jsRemoveMB">
                            <?php /* And here we call our custom meta box */ ?>
                            <?php do_meta_boxes('joomsport-events-form', 'normal', array($item,$lists)); ?>
                            <input type="submit" value="<?php echo __('Save & close', 'joomsport-sports-league-results-management')?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    public static function joomsport_event_form_meta_box_handler($item)
    {
        $lists = $item[1];
        $item = $item[0];
    ?>

    <div class="jsrespdiv12">
    <div class="jsBepanel">
        <div class="jsBEheader">
            <?php echo __('General', 'joomsport-sports-league-results-management'); ?>
        </div>
        <div class="jsBEsettings" id="jseventcontainer">		
		<table>
			<tr>
				<td width="200">
					<?php echo __('Event name', 'joomsport-sports-league-results-management'); ?>
				</td>
				<td>
					<input type="text" name="e_name" size="50" value="<?php echo htmlspecialchars($item['e_name'])?>" id="evname" maxlength="255" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			<tr>
				<td width="200" valign="middle">
					<?php echo __('Event type', 'joomsport-sports-league-results-management'); ?>
				</td>
				<td>
					<?php echo $lists['player_event'];?>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<table cellpadding="0" id="calctp" <?php echo ($item['player_event'] == 1) ? '' : "style='display:none;'";?>>
						<tr>
							<td width="202" valign="middle">
								<?php echo __('Calculate total as', 'joomsport-sports-league-results-management'); ?>
							</td>
							<td>
								<?php echo $lists['restype'];?>
							</td>
						</tr>
					</table>				
				</td>	
			</tr>
                        <tr>
				<td colspan="2">
					<table cellpadding="0" id="calctp_es" <?php echo ($item['player_event'] == 1) ? '' : "style='display:none;'";?>>
						<tr>
							<td width="202" valign="middle">
								<?php echo __('Sum of other events', 'joomsport-sports-league-results-management'); ?>
							</td>
							<td style="width:250px;">
								<?php echo $lists['events_sum'];?>
                                                            <div class="displ_subevents" <?php echo ($item['events_sum'] == 1) ? '' : "style='display:none;'";?>>
                                                                <?php echo $lists['subevents'];?>
                                                            </div>
							</td>
						</tr>
					</table>				
				</td>	
			</tr>
			
                        <tr>
				<td width="200" valign="middle">
					<?php echo __('Event image', 'joomsport-sports-league-results-management'); ?>
				</td>
				<td>
                                    <div>
                                        <div class="jsev-img-container">
                                            <?php
                                            if($item['e_img']){
                                                echo wp_get_attachment_image($item['e_img']);
                                            }
                                            ?>
                                        </div>
                                        <input type="hidden" name="e_img" class="jsev-img-id"  value="<?php echo intval($item['e_img']);?>"/>
                                        <?php if($item['e_img']){?>
                                        <a href="" class="delete-jsev-img"><?php echo __('Remove image', 'joomsport-sports-league-results-management');?></a>   
                                        <?php }?>
                                    </div>
                                    <?php if(!$item['e_img']){?>
                                    <button class="button" id="jsEventImage"><?php echo __('Add image', 'joomsport-sports-league-results-management'); ?></button>
                                    <?php }?>
                                </td>
			</tr>
			<tr>
                            <td width="200">
                                    <?php echo __('Ordering', 'joomsport-sports-league-results-management')?>
                            </td>
                            <td>
                                <input type="number" name="ordering" value="<?php echo $item['ordering']?>" />
                            </td>
                        </tr>
		</table>
            </div>
        </div>
    </div>
    <?php
    }
    public static function joomsport_events_validate($item)
    {
        $messages = array();

        if (empty($item['e_name'])) $messages[] = __('Name is required', 'joomsport-sports-league-results-management');
        //if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
        //if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'custom_table_example');
        //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
        //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
        //...

        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }
    public static function getListValues($item){
        global $wpdb;
        $lists = array();
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $lists['events_sum'] = JoomSportHelperSelectBox::Radio('events_sum', $is_field,$item['events_sum'],'onChange = "calcenblsumfun();"',false);

        $is_sumev = $wpdb->get_results('SELECT id, e_name as name FROM '.$wpdb->joomsport_events.' WHERE player_event="1" ORDER BY ordering', 'OBJECT') ;

        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Match", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Player", "joomsport-sports-league-results-management"));
        
        $lists['player_event'] = JoomSportHelperSelectBox::Radio('player_event', $is_field,$item['player_event'],'onChange = "calctpfun();"',false);
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Sum", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Average", "joomsport-sports-league-results-management"));
        $lists['restype'] = JoomSportHelperSelectBox::Radio('result_type', $is_field,$item['result_type'],'',false);
        
        $lists['sumev1'] = JoomSportHelperSelectBox::Simple('sumev1', $is_sumev,$item['sumev1'],'',true);
        $lists['sumev2'] = JoomSportHelperSelectBox::Simple('sumev2', $is_sumev,$item['sumev2'],'',true);
        

        $plEvents = $wpdb->get_results('SELECT id, e_name as name'
                . ' FROM '.$wpdb->joomsport_events.' '
                . ' WHERE player_event="1" '
                .($item['id']?' AND id != '.$item['id']:'')
                . ' ORDER BY ordering', 'OBJECT') ;

        $lists['subevents'] = '<select name="subevents[]"  class="jswf-chosen-select" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" multiple>';

        $subevents = !is_array($item['subevents'])?json_decode($item['subevents'],true):$item['subevents'];
        if(count($plEvents)){
            foreach ($plEvents as $tm) {
                $selected = '';
                if(is_array($subevents) && in_array($tm->id, $subevents)){
                    $selected = ' selected';
                }
                $lists['subevents'] .=  '<option value="'.$tm->id.'" '.$selected.'>'.$tm->name.'</option>';
            }
        }
        $lists['subevents'] .=  '</select>';
        
        
        
        
        return $lists;

    }
}