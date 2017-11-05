<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class JoomSportBoxFields_List_Table extends WP_List_Table {

    public function __construct() {

        parent::__construct( array(
                'singular' => __( 'Box score record', 'joomsport-sports-league-results-management' ), 
                'plural'   => __( 'Box score stats', 'joomsport-sports-league-results-management' ),
                'ajax'     => false 

        ) );
        /** Process bulk action */
        $this->process_bulk_action();

    }
    public static function get_boxfields( $per_page = 5, $page_number = 1 ) {

        global $wpdb;
        $sql = "SELECT b1.* FROM {$wpdb->joomsport_box} as b1"
                . " LEFT JOIN {$wpdb->joomsport_box} as b2"
                . " ON b1.parent_id = b2.id OR (b1.parent_id = 0 AND b1.id = b2.id)";
            
        if ( ! empty( $_REQUEST['orderby'] ) ) {
          //$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
          $sqlway = ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
          $sql .= ' ORDER BY b2.name '.$sqlway.',b2.id,b1.parent_id, b1.name '.$sqlway.', b2.id, b1.id';
        }else{
            $sql .= ' ORDER BY b2.ordering,b2.id,b1.parent_id, b1.ordering, b2.id, b1.id';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
    public static function delete_boxfield( $id ) {
        global $wpdb;

        $wpdb->delete(
          "{$wpdb->joomsport_box}",
          array( 'id' => $id ),
          array( '%d' )
        );
    }
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->joomsport_box}";

        return $wpdb->get_var( $sql );
    }
    public function no_items() {
        echo __( 'No box fields available.', 'joomsport-sports-league-results-management' );
    }
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'joomsport_delete_boxfield' );

        $title = '<strong><a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-boxfields-form&id='.absint( $item['id'] )).'">'.($item['parent_id']?'- ':'') . $item['name'] . '</a></strong>';

        $actions = array(
          'delete' => sprintf( '<a href="?page=%s&action=%s&boxfield=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
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
          //'type'    => __( 'Type', 'joomsport-sports-league-results-management' ),
          //'field_type'    => __( 'Field Type', 'joomsport-sports-league-results-management' ),
          'published'    => __( 'Status', 'joomsport-sports-league-results-management' ),
        );

        return $columns;
    }
    function column_default($item, $column_name){
        switch($column_name){
            /*case 'field_type':
                $is_field = array();
                $is_field[0] = __("Text Field", "joomsport-sports-league-results-management");
                $is_field[1] = __("Radio Button", "joomsport-sports-league-results-management");
                $is_field[2] = __("Text Area", "joomsport-sports-league-results-management");
                $is_field[3] = __("Select Box", "joomsport-sports-league-results-management");
                $is_field[4] = __("Link", "joomsport-sports-league-results-management");
                
                return $is_field[$item['field_type']];
            case 'type':
                $is_field = array();
                $is_field[0] = __("Player", "joomsport-sports-league-results-management");
                $is_field[1] = __("Team", "joomsport-sports-league-results-management");
                $is_field[2] = __("Match", "joomsport-sports-league-results-management");
                $is_field[3] = __("Season", "joomsport-sports-league-results-management");
                $is_field[4] = __("Club", "joomsport-sports-league-results-management");
                $is_field[5] = __("Venue", "joomsport-sports-league-results-management");

                return $is_field[$item['type']];*/
            case 'published':
                $is_field = array();
                $is_field[0] = __("Unpublished", "joomsport-sports-league-results-management");
                $is_field[1] = __("Published", "joomsport-sports-league-results-management");

                return $is_field[$item['published']];   
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    public function get_sortable_columns() {
        $sortable_columns = array(
          'name' => array( 'name', true ),
            //'field_type' => array( 'field_type', true ),
            //'type' => array( 'type', true ),
            //'published' => array( 'published', true ),
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

        

        $per_page     = $this->get_items_per_page( 'boxfields_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
          'total_items' => $total_items, //WE have to calculate the total number of items
          'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );


        $this->items = self::get_boxfields( $per_page, $current_page );
    }
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
          // In our file that handles the request, verify the nonce.
          $nonce = esc_attr( $_REQUEST['_wpnonce'] );

          if ( ! wp_verify_nonce( $nonce, 'joomsport_delete_boxfield' ) ) {
            die( 'Error' );
          }
          else {
            self::delete_boxfield( absint( $_GET['boxfield'] ) );
            wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-boxfields' ) );
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
            self::delete_boxfield( $id );

          }

          wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-boxfields' ) );
          exit;
        }
    }
    
}


class JoomSportBoxField_Plugin {

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
			<h2><?php echo __('Box score stats', 'joomsport-sports-league-results-management');?>
                        <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-boxfields-form');?>"><?php echo __('Add new', 'joomsport-sports-league-results-management')?></a>
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
                update_user_meta(get_current_user_id(), 'boxfields_per_page', $_POST['wp_screen_options']['value']);



            }

		$option = 'per_page';
		$args   = array(
			'label'   => 'Box score records',
			'default' => 5,
			'option'  => 'boxfields_per_page'
		);

		add_screen_option( $option, $args );

		$this->customers_obj = new JoomSportBoxFields_List_Table();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


class JoomSportBoxFieldsNew_Plugin {
    public static function view(){

        global $wpdb;
        $table_name = $wpdb->joomsport_box; 

        $message = '';
        $notice = '';

        // this is default $item which will be used for new records
        $default = array(
            'id' => 0,
            'name' => '',
            'published' => '1',
            'complex' => '0',
            'ordering' => '0',
            'parent_id' => '0',
            'ftype' => '0',
            'options' => '',
            'displayonfe' => '1'
        );

        $item = array();
        // here we are verifying does this request is post back and have correct nonce
        if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = self::joomsport_boxfields_validate($item);
            if ($item_valid === true) {
                $item['options'] = json_encode($item['options']);
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        self::joomsport_boxfields_saveselect($item);
                        $message = __('Item was successfully saved', 'joomsport-sports-league-results-management');
                    } else {
                        $notice = __('There was an error while saving item', 'joomsport-sports-league-results-management');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    self::joomsport_boxfields_saveselect($item);
                    $message = __('Item was successfully updated', 'joomsport-sports-league-results-management');
                    /*if ($result) {
                        
                        $message = __('Item was successfully updated', 'joomsport-sports-league-results-management');
                    } else {
                        //$notice = __('There was an error while updating item', 'joomsport-sports-league-results-management');
                    }*/
                }
                echo '<script> window.location="'.(esc_url(get_dashboard_url())).'admin.php?page=joomsport-page-boxfields"; </script> ';
                
                
            } else {
                // if $item_valid not true it contains error message(s)
                $notice = $item_valid;
            }
            $lists = self::getListValues($item);
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
        add_meta_box('joomsport_boxfield_form_meta_box', __('Details', 'joomsport-sports-league-results-management'), array('JoomSportBoxFieldsNew_Plugin','joomsport_boxfield_form_meta_box_handler'), 'joomsport-boxfield-form', 'normal', 'default');

        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php echo __('Box score record', 'joomsport-sports-league-results-management')?> <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-boxfields');?>"><?php echo __('back to list', 'joomsport-sports-league-results-management')?></a>
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
                        <div id="post-body-content" class="jsRemoveMB">
                            <?php /* And here we call our custom meta box */ ?>
                            <?php do_meta_boxes('joomsport-boxfield-form', 'normal', array($item, $lists)); ?>
                            <input type="submit" value="<?php echo __('Save & close', 'joomsport-sports-league-results-management')?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    public static function joomsport_boxfield_form_meta_box_handler($item)
    {
        $lists = $item[1];
        $item = $item[0];
    ?>
<div >
    <div class="jsrespdiv8">
    <div class="jsBepanel">
        <div class="jsBEheader">
            <?php echo __('General', 'joomsport-sports-league-results-management')?>
        </div>
        <div class="jsBEsettings">
            <script>
                function boxfield_hide(){
                    if(jQuery('input[name="complex"]:checked').val() == '1'){
                        jQuery('.jshideforcomposite').hide();
                    }else{
                        jQuery('.jshideforcomposite').show();
                    }    
                }
                function boxfield_type_hide(){
                    if(jQuery('input[name="ftype"]:checked').val() == '1'){
                        jQuery('.jshideforboxtype').show();
                    }else{
                        jQuery('.jshideforboxtype').hide();
                    }    
                }
                
                jQuery( document ).ready(function() {
                    boxfield_hide();
                    boxfield_type_hide();
                });
            </script>    
		<table>
			<tr>
				<td width="250">
                                    <?php echo __('Record name', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="name" id="fldname" value="<?php echo htmlspecialchars($item['name'])?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			
			<tr>
				<td width="250">
                                    <?php echo __('Composite', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
                                    <?php ?>
                                    <?php  echo 'Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only';  ?>
				</td>
			</tr>
			<tr  class="jshideforcomposite">
				<td width="250">
                                    <?php echo __('Parent', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
                                    <?php ?>
                                    <?php  echo 'Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only';  ?>
				</td>
			</tr>
			<tr  class="jshideforcomposite">
				<td width="250">
                                    <?php echo __('Type', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
                                    
				    <?php echo $lists['ftype'];?>
                                    
				</td>
			</tr>
                        <tr class="jshideforcomposite jshideforboxtype">
				<td width="250">
                                    <?php echo __('Fields', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
                                    <?php echo $lists['depend1'];?>
                                    <?php echo $lists['calc'];?>
                                    <?php echo $lists['depend2'];?>
				</td>
			</tr>
                        <?php if($lists['extraf']){?>
			<tr class="jshideforcomposite">
				<td width="250">
                                    <?php echo __('Connected to', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
                                    <?php ?>
                                    <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>';  ?>
					
				</td>
			</tr>
                        <?php } ?>
		</table>
		
            </div>
        </div>
    </div>
    <div class="jsrespdiv4 jsrespmarginleft2">
        <div class="jsBepanel">
            <div class="jsBEheader">
                <?php echo __('Publishing', 'joomsport-sports-league-results-management')?>
            </div>
            <div class="jsBEsettings">
                <table>
                    <tr>
                        <td width="250">
                                <?php echo __('Published', 'joomsport-sports-league-results-management')?>
                        </td>
                        <td>
                            <div class="controls"><fieldset class="radio btn-group"><?php echo $lists['published'];?></fieldset></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="250">
                                <?php echo __('Visible on front-end', 'joomsport-sports-league-results-management')?>
                        </td>
                        <td>
                                <?php echo $lists['displayonfe'];?>
                        </td>
                    </tr>
                    <tr>
                        <td width="250">
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
    <div class="clear"></div>
</div>


    <?php
    }
    public static function joomsport_boxfields_validate($item)
    {
        $messages = array();

        if (empty($item['name'])) $messages[] = __('Name is required', 'joomsport-sports-league-results-management');
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
        $jsconfig =  new JoomsportSettings();
        $lists = array();
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        
        $stdoptions = 'onchange="boxfield_hide();"';
         $stdoptions = "std"; 
        
        $lists['composite'] = JoomSportHelperSelectBox::Radio('complex', $is_field,$item['complex'],$stdoptions,false);
        $lists['displayonfe'] = JoomSportHelperSelectBox::Radio('displayonfe', $is_field,$item['displayonfe'],'',false);
        $lists['published'] = JoomSportHelperSelectBox::Radio('published', $is_field,$item['published'],'');
        
        $parentBox = $wpdb->get_results('SELECT id, name FROM '.$wpdb->joomsport_box.' WHERE complex="1" ORDER BY ordering', 'OBJECT') ;
        $lists['parent'] = JoomSportHelperSelectBox::Simple('parent_id', $parentBox,$item['parent_id'],'',__("None", "joomsport-sports-league-results-management"));
        
        
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Sum", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Countable", "joomsport-sports-league-results-management"));
        
        
        
        $lists['ftype'] = JoomSportHelperSelectBox::Radio('ftype', $is_field,$item['ftype'],'onchange="boxfield_type_hide();"',false);
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, '/');
        $is_field[] = JoomSportHelperSelectBox::addOption(1, "*");
        $is_field[] = JoomSportHelperSelectBox::addOption(2, "+");
        $is_field[] = JoomSportHelperSelectBox::addOption(3, "-");
        $is_field[] = JoomSportHelperSelectBox::addOption(4, "'/'");
        
        $options = json_decode($item['options'],true);
        $lists['calc'] = JoomSportHelperSelectBox::Simple('options[calc]', $is_field,(isset($options['calc'])?$options['calc']:0),'',false);
        
        $simpleBox = $wpdb->get_results('SELECT id, name FROM '.$wpdb->joomsport_box.' WHERE complex="0" ORDER BY ordering,name', 'OBJECT') ;
        $lists['depend1'] = JoomSportHelperSelectBox::Simple('options[depend1]', $simpleBox,(isset($options['depend1'])?$options['depend1']:0),'',false);
        $lists['depend2'] = JoomSportHelperSelectBox::Simple('options[depend2]', $simpleBox,(isset($options['depend2'])?$options['depend2']:0),'',false);
        
        $lists['extraf'] = '';
        $efbox = (int) $jsconfig->get('boxExtraField','0');
        if($efbox){
            $simpleBox = $wpdb->get_results('SELECT id, sel_value as name FROM '.$wpdb->joomsport_ef_select.' WHERE fid="'.$efbox.'" ORDER BY eordering,sel_value', 'OBJECT') ;
            if(count($simpleBox)){
                //$lists['extraf'] = JoomSportHelperSelectBox::Simple('options[extraVals][]', $simpleBox,(isset($options['extraVals'])?$options['extraVals']:0),'class="jswf-chosen-select" multiple',false);
                $lists['extraf'] = '<select name="options[extraVals][]" class="jswf-chosen-select" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" multiple>';
                foreach ($simpleBox as $tm) {
                    $selected = '';
                    if(isset($options['extraVals']) && in_array($tm->id, $options['extraVals'])){
                        $selected = ' selected';
                    }
                    $lists['extraf'] .=  '<option value="'.$tm->id.'" '.$selected.'>'.$tm->name.'</option>';
                }
                $lists['extraf'] .=  '</select>';
            }
        }
        
        
        return $lists;
        
    }
    public static function joomsport_boxfields_saveselect($item){
        global $wpdb;
        if($item['complex'] != 1){
            $tblCOl = 'boxfield_'.$item['id'];
            $is_col = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->joomsport_box_match} LIKE '".$tblCOl."'");

            if (empty($is_col)) {
                $wpdb->query('ALTER TABLE '.$wpdb->joomsport_box_match.' ADD `'.$tblCOl."` FLOAT NULL DEFAULT NULL");
            }
        }
    }
}
