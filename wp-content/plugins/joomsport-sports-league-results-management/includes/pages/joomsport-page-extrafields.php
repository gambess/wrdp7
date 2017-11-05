<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class JoomSportExtraFields_List_Table extends WP_List_Table {

    public function __construct() {

        parent::__construct( array(
                'singular' => __( 'Extra field', 'joomsport-sports-league-results-management' ), 
                'plural'   => __( 'Extra fields', 'joomsport-sports-league-results-management' ),
                'ajax'     => false 

        ) );
        /** Process bulk action */
        $this->process_bulk_action();

    }
    public static function get_extrafields( $per_page = 5, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->joomsport_ef}";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
          $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
          $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
    public static function delete_extrafield( $id ) {
        global $wpdb;

        $wpdb->delete(
          "{$wpdb->joomsport_ef}",
          array( 'id' => $id ),
          array( '%d' )
        );
    }
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->joomsport_ef}";

        return $wpdb->get_var( $sql );
    }
    public function no_items() {
        echo __( 'No extra fields available.', 'joomsport-sports-league-results-management' );
    }
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'joomsport_delete_extrafield' );

        $title = '<strong><a href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-extrafields-form&id='.absint( $item['id'] )).'">' . $item['name'] . '</a></strong>';

        $actions = array(
          'delete' => sprintf( '<a href="?page=%s&action=%s&extrafield=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
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
          'type'    => __( 'Type', 'joomsport-sports-league-results-management' ),
          'field_type'    => __( 'Field Type', 'joomsport-sports-league-results-management' ),
          'published'    => __( 'Status', 'joomsport-sports-league-results-management' ),
        );

        return $columns;
    }
    function column_default($item, $column_name){
        switch($column_name){
            case 'field_type':
                $is_field = array();
                $is_field[0] = __("Text Field", "joomsport-sports-league-results-management");
                $is_field[1] = __("Radio Button", "joomsport-sports-league-results-management");
                $is_field[2] = __("Text Area", "joomsport-sports-league-results-management");
                $is_field[3] = __("Select Box", "joomsport-sports-league-results-management");
                $is_field[4] = __("Link", "joomsport-sports-league-results-management");
                $is_field[5] = __("Person", "joomsport-sports-league-results-management");
                $is_field[6] = __("Date", "joomsport-sports-league-results-management");
                
                return $is_field[$item['field_type']];
            case 'type':
                $is_field = array();
                $is_field[0] = __("Player", "joomsport-sports-league-results-management");
                $is_field[1] = __("Team", "joomsport-sports-league-results-management");
                $is_field[2] = __("Match", "joomsport-sports-league-results-management");
                $is_field[3] = __("Season", "joomsport-sports-league-results-management");
                $is_field[4] = __("Club", "joomsport-sports-league-results-management");
                $is_field[5] = __("Venue", "joomsport-sports-league-results-management");
                $is_field[6] = __("Person", "joomsport-sports-league-results-management");

                return $is_field[$item['type']];
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
            'field_type' => array( 'field_type', true ),
            'type' => array( 'type', true ),
            'published' => array( 'published', true ),
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

        

        $per_page     = $this->get_items_per_page( 'extrafields_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
          'total_items' => $total_items, //WE have to calculate the total number of items
          'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );


        $this->items = self::get_extrafields( $per_page, $current_page );
    }
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
          // In our file that handles the request, verify the nonce.
          $nonce = esc_attr( $_REQUEST['_wpnonce'] );

          if ( ! wp_verify_nonce( $nonce, 'joomsport_delete_extrafield' ) ) {
            die( 'Error' );
          }
          else {
            self::delete_extrafield( absint( $_GET['extrafield'] ) );
            wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-extrafields' ) );
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
            self::delete_extrafield( $id );

          }

          wp_redirect( esc_url(get_dashboard_url(). 'admin.php?page=joomsport-page-extrafields' ) );
          exit;
        }
    }
    
}


class JoomSportExtraField_Plugin {

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
			<h2><?php echo __('Extra Field', 'joomsport-sports-league-results-management');?>
                        <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-extrafields-form');?>"><?php echo __('Add new', 'joomsport-sports-league-results-management')?></a>
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
                update_user_meta(get_current_user_id(), 'extrafields_per_page', $_POST['wp_screen_options']['value']);



            }

		$option = 'per_page';
		$args   = array(
			'label'   => 'Extra fields',
			'default' => 5,
			'option'  => 'extrafields_per_page'
		);

		add_screen_option( $option, $args );

		$this->customers_obj = new JoomSportExtraFields_List_Table();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

class JoomSportExtraFieldsNew_Plugin {
    public static function view(){

        global $wpdb;
        $table_name = $wpdb->joomsport_ef; 

        $message = '';
        $notice = '';

        // this is default $item which will be used for new records
        $default = array(
            'id' => 0,
            'name' => '',
            'published' => '1',
            'type' => '0',
            'ordering' => '0',
            'e_table_view' => '0',
            'field_type' => '0',
            'reg_exist' => '0',
            'reg_require' => '0',
            'fdisplay' => '0',
            'season_related' => '0',
            'faccess' => '0',
            'display_playerlist' => '0',
            'options' => ''
        );

        $item = array();
        // here we are verifying does this request is post back and have correct nonce
        if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            // combine our default item with request params
            $item = shortcode_atts($default, $_REQUEST);
            
            // validate data, and if all ok save item to database
            // if id is zero insert otherwise update
            $item_valid = self::joomsport_extrafields_validate($item);
            if(isset($_POST['options']) && count($_POST['options'])){
                $item['options'] = json_encode($_POST['options']);
            }
            if ($item_valid === true) {
                if ($item['id'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id'] = $wpdb->insert_id;
                    if ($result) {
                        self::joomsport_extrafields_saveselect($item);
                        $message = __('Item was successfully saved', 'joomsport-sports-league-results-management');
                    } else {
                        $notice = __('There was an error while saving item', 'joomsport-sports-league-results-management');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                    self::joomsport_extrafields_saveselect($item);
                    $message = __('Item was successfully updated', 'joomsport-sports-league-results-management');
                    /*if ($result) {
                        
                        $message = __('Item was successfully updated', 'joomsport-sports-league-results-management');
                    } else {
                        //$notice = __('There was an error while updating item', 'joomsport-sports-league-results-management');
                    }*/
                }
                echo '<script> window.location="'.(esc_url(get_dashboard_url())).'admin.php?page=joomsport-page-extrafields"; </script> ';
                
                
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
        add_meta_box('joomsport_extrafield_form_meta_box', __('Details', 'joomsport-sports-league-results-management'), array('JoomSportExtraFieldsNew_Plugin','joomsport_extrafield_form_meta_box_handler'), 'joomsport-extrafield-form', 'normal', 'default');

        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php echo __('Extra field', 'joomsport-sports-league-results-management')?> <a class="add-new-h2"
                                        href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-extrafields');?>"><?php echo __('back to list', 'joomsport-sports-league-results-management')?></a>
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
                            <?php do_meta_boxes('joomsport-extrafield-form', 'normal', array($item, $lists)); ?>
                            <input type="submit" value="<?php echo __('Save & close', 'joomsport-sports-league-results-management')?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    public static function joomsport_extrafield_form_meta_box_handler($item)
    {
        $lists = $item[1];
        $item = $item[0];
    ?>
<div style="overflow: hidden;">
    <div class="jsrespdiv8">
    <div class="jsBepanel">
        <div class="jsBEheader">
            <?php echo __('General', 'joomsport-sports-league-results-management')?>
        </div>
        <div class="jsBEsettings">		
		<table>
			<tr>
				<td width="250">
                                    <?php echo __('Field name', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
					<input type="text" maxlength="255" size="60" name="name" id="fldname" value="<?php echo htmlspecialchars($item['name'])?>" onKeyPress="return disableEnterKey(event);" />
				</td>
			</tr>
			
			<tr>
				<td width="250">
                                    <?php echo __('Field type', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
					<?php echo $lists['field_type'];?>
				</td>
			</tr>
			<tr>
				<td width="250">
                                    <?php echo __('Assigned to', 'joomsport-sports-league-results-management')?>
				</td>
				<td>
					<?php echo $lists['is_type'];?>
				</td>
			</tr>
                        <?php
                        $stf = 'style="display:table-row;"';
                        if ($item['field_type'] != 5) {
                            $stf = 'style="display:none;"';
                        }
                        ?>
                        <tr class="jsw_personcat_ef" <?php echo $stf;?>>
                            <td width="250">
                                    <?php echo __('Person Category', 'joomsport-sports-league-results-management')?>
                            </td>
                            <td>
                                    <?php echo $lists['personcats'];?>
                            </td>
                            
                        </tr>
                        <?php
                        if ($item['type'] != 1) {
                            $stf = 'style="display:none;"';
                        }
                        ?>
                        <tr class="jsw_personroster_ef" <?php echo $stf;?>>
                            <td width="250">
                                    <?php echo __('Include into roster', 'joomsport-sports-league-results-management')?>
                            </td>
                            <td>
                                    <?php echo $lists['in_roster'];?>
                            </td>
                            
                        </tr>
                        
                        <?php
                        $stf = 'style="display:table-row;"';
                        if ($item['field_type'] != 6) {
                            $stf = 'style="display:none;"';
                        }
                        ?>
                        <tr class="jsw_dateage_ef" <?php echo $stf;?>>
                            <td width="250">
                                    <?php echo __('Display as', 'joomsport-sports-league-results-management')?>
                            </td>
                            <td>
                                    <?php echo $lists['dateage'];?>
                            </td>
                            
                        </tr>
			<?php
                        $stf = 'style="display:table-cell;"';
                        if ($item['type'] != 2) {
                            $stf = 'style="display:none;"';
                        }
                        ?>
			<tr>
				<td width="250" id="tbl_fv_11" <?php echo $stf;?>>
                                    <?php echo __('Display on calendar views', 'joomsport-sports-league-results-management')?>
				</td>
				<td id="tbl_fv_12" <?php echo $stf;?>>
                    <div class="controls"><fieldset class="radio btn-group"><?php echo $lists['t_view'];?></fieldset></div>

				</td>
			</tr>
			<?php
            $stf = 'style="visibility:visible;"';
            if ($item['type'] >= 2) {
                $stf = 'style="visibility:hidden;"';
            }
            ?>
			<tr>
				<td width="250" id="tbl_fv_1" <?php echo $stf;?>>
                                    <?php echo __('Display on standings views', 'joomsport-sports-league-results-management')?>
				</td>
				<td id="tbl_fv_2" <?php echo $stf;?>>
                    <div class="controls"><fieldset class="radio btn-group"><?php echo $lists['t_view'];?></fieldset></div>

				</td>
			</tr>
			<?php
            $stf = 'style="visibility:visible;"';
            if ($item['type'] >= 2) {
                $stf = 'style="visibility:hidden;"';
            }
            ?>
			<tr>
				<td width="250" id="tbl_seasr_1" <?php echo $stf;?>>
                                    <?php echo __('Assigned to the season', 'joomsport-sports-league-results-management')?>
                                </td>
				<td id="tbl_seasr_2" <?php echo $stf;?>>
                    <div class="controls"><fieldset class="radio btn-group"><?php echo $lists['season_related'];?></fieldset></div>
				</td>
			</tr>
                        <tr>
                            <?php
                            $stf = 'style="display:none;"';
                            if (!$item['type']) {
                                $stf = 'style="display:table-cell;"';
                            }
                            ?>
                            <td width="250" class="pllistdiv" <?php echo $stf;?>>
                                <?php echo __('Visible on player list layout', 'joomsport-sports-league-results-management')?>
                            </td>
                            <td class="pllistdiv" <?php echo $stf;?>>
                                <div class="controls"><fieldset class="radio btn-group"><?php echo $lists['display_playerlist'];?></fieldset></div>
                            </td>
			</tr>
                        
			
		</table>
		<br />
		<?php
        $st = 'style="display:none;"';
        if ($item['field_type'] == '3') {
            $st = 'style="display:block;"';
        }
        ?>
		<table id="seltable" <?php echo $st?>>
			<tbody>
			<?php
                        for ($i = 0;$i < count($lists['selval']);++$i) {
                            echo '<tr class="ui-state-default">';
                            echo '<td class="jsdadicon">
                                <i class="fa fa-bars" aria-hidden="true"></i>
                            </td>';
                            echo '<td class="jsdadicondel"><input type="hidden" name="adeslid[]" value="'.$lists['selval'][$i]->id.'" /><a href="javascript:void(0);" title="Remove" onClick="javascript:delJoomSportSelRow(this);"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                            echo "<td><input type='text' name='selnames[]' value='".htmlspecialchars($lists['selval'][$i]->name, ENT_QUOTES)."' /></td>";
                            
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                        <tfoot>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
                                
                            <th colspan="2"><input class="button" type="button" style="cursor:pointer;" value="<?php echo __('Add choice', 'joomsport-sports-league-results-management')?>" onclick="add_selval();" /></th>
                                <th><input style="margin:0px;" type="text" name="addsel" value="" id="addsel" /></th>
			</tr>
                        </tfoot>
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
                                <?php echo __('Visible for', 'joomsport-sports-league-results-management')?>
                        </td>
                        <td>
                                <?php echo $lists['faccess'];?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>    
    </div>
</div>


    <?php
    }
    public static function joomsport_extrafields_validate($item)
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
        $lists = array();
        
        if($item['options']){
            $item['options'] = json_decode($item['options'], true);
        }
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Player", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Team", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(2, __("Match", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(3, __("Season", "joomsport-sports-league-results-management"));
        //$is_field[] = JoomSportHelperSelectBox::addOption(4, __("Club", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(5, __("Venue", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(6, __("Person", "joomsport-sports-league-results-management"));
        
        $lists['is_type'] = JoomSportHelperSelectBox::Simple('type', $is_field,$item['type'],'onchange="tblview_hide();"',false);
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Text Field", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Radio Button", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(2, __("Text Area", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(3, __("Select Box", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(4, __("Link", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(5, __("Person", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(6, __("Date", "joomsport-sports-league-results-management"));
        
        $lists['field_type'] = JoomSportHelperSelectBox::Simple('field_type', $is_field,$item['field_type'],'onchange="shide();"',false);
        
        $tx = get_terms(array(
            "taxonomy" => "joomsport_personcategory",
            "hide_empty" => false,

        ));
        
        $current_personcat = 0;
        if(isset($item['options']['personcategory'])){
            $current_personcat = $item['options']['personcategory'];
        }
        $lists['personcats'] = '<select name="options[personcategory]" id="personcategory">';
            $lists['personcats'] .= '<option value="0">'.__('Select Category','joomsport-sports-league-results-management').'</option>';
            for($intA=0;$intA<count($tx);$intA++){

                    $lists['personcats'] .= '<option value="'.$tx[$intA]->term_id.'" '.($tx[$intA]->term_id == $current_personcat?'selected':'').'>'.$tx[$intA]->name.'</option>';

            }

        $lists['personcats'] .= '</select>';
        
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("All", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Registered only", "joomsport-sports-league-results-management"));
        $lists['faccess'] = JoomSportHelperSelectBox::Simple('faccess', $is_field,$item['faccess'],'',false);
        
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $lists['t_view'] = JoomSportHelperSelectBox::Radio('e_table_view', $is_field,$item['e_table_view'],'');
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $lists['season_related'] = JoomSportHelperSelectBox::Radio('season_related', $is_field,$item['season_related'],'');
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $lists['display_playerlist'] = JoomSportHelperSelectBox::Radio('display_playerlist', $is_field,$item['display_playerlist'],'');
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $lists['published'] = JoomSportHelperSelectBox::Radio('published', $is_field,$item['published'],'');
        
        $lists['selval'] = $wpdb->get_results('SELECT id, sel_value as name FROM '.$wpdb->joomsport_ef_select.' WHERE fid='.absint($item['id']).' ORDER BY eordering', 'OBJECT') ;
        
        $current_dateage = 0;
        if(isset($item['options']['dateage'])){
            $current_dateage = $item['options']['dateage'];
        }
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Date", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Age", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(2, __("Date(Age)", "joomsport-sports-league-results-management"));
        
        $lists['dateage'] = JoomSportHelperSelectBox::Radio('options[dateage]', $is_field,$current_dateage,'',array('lclasses'=>array(1,1,1)));
        
        $in_roster = 0;
        if(isset($item['options']['in_roster'])){
            $in_roster = $item['options']['in_roster'];
        }
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $lists['in_roster'] = JoomSportHelperSelectBox::Radio('options[in_roster]', $is_field,$in_roster,'');
        
        
        
        return $lists;
        
    }
    public static function joomsport_extrafields_saveselect($item){
        global $wpdb;
        $mj = 0;
        $mjarr = array();
        $eordering = 0;
        if (isset($_POST['selnames']) && count($_POST['selnames'])) {
            foreach ($_POST['selnames'] as $selname) {
                $selname = esc_sql(sanitize_text_field($selname));
                if ($_POST['adeslid'][$mj]) {
                    $wpdb->query('UPDATE '.$wpdb->joomsport_ef_select.' SET sel_value="'.esc_attr($selname).'", eordering='.$eordering.' WHERE id='.absint($_POST['adeslid'][$mj]));
                } else {
                    $wpdb->insert($wpdb->joomsport_ef_select, array("fid"=>$item['id'], "sel_value"=>esc_attr($selname), "eordering"=>$eordering),array( '%d', '%s', '%d' ));
                    $newid = $wpdb->insert_id;
                    //$wpdb->query('INSERT INTO #__bl_extra_select(fid,sel_value,eordering) VALUES('.$row->id.','.$selname.','.$eordering.')');
                }

                $mjarr[] = $_POST['adeslid'][$mj] ? intval($_POST['adeslid'][$mj]) : $newid;
                ++$mj;
                ++$eordering;
            }
        } else {
            $query = 'DELETE FROM '.$wpdb->joomsport_ef_select.' WHERE fid='.$item['id'];
            $wpdb->query($query);

        }

        $query = 'DELETE FROM '.$wpdb->joomsport_ef_select.'
		            WHERE fid='.$item['id'].' AND id NOT IN ('.(count($mjarr) ? implode(',', $mjarr) : "''").')';

        $wpdb->query($query);

    }
}