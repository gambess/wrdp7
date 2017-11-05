<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-season.php';


class JoomSportPostSeason {
    public function __construct() {

    }
    public static function init(){
        self::register_post_types();
    }
    public static function register_post_types(){
        add_action("admin_init", array("JoomSportPostSeason","admin_init"));
        add_action( 'edit_form_after_title',  array( 'JoomSportPostSeason','season_edit_form_after_title') );
        add_action( 'admin_footer', array("JoomSportPostSeason",'joomsport_season_action_javascript') );
        add_action( 'wp_ajax_season_parentseas', array("JoomSportPostSeason",'joomsport_season_parentseas') );
        add_action( 'wp_ajax_season_groupedit', array("JoomSportPostSeason",'joomsport_season_groupedit') );
        add_action( 'wp_ajax_season_genermodal', array("JoomSportPostSeason",'joomsport_season_genermodal') );
        add_action( 'wp_ajax_season_grouplist', array("JoomSportPostSeason",'joomsport_season_grouplist') );
        add_action( 'wp_ajax_season_groupdel', array("JoomSportPostSeason",'joomsport_season_groupdel') );
        add_action( 'wp_ajax_season_tournamentmodal', array("JoomSportPostSeason",'joomsport_season_tournamentmodal') );
        add_action( 'admin_print_scripts-post-new.php', array("JoomSportPostSeason",'joomsport_season_validate'), 11 );
        add_action( 'admin_print_scripts-edit-tags.php', array("JoomSportPostSeason",'joomsport_taxonomy_validate'), 11 );
        add_action( 'wp_ajax_joomsport_standings_shortcode', array("JoomSportPostSeason",'joomsport_standings_shortcode') );
        add_action( 'wp_ajax_joomsport_group_shortcode', array("JoomSportPostSeason",'joomsport_group_shortcode') );
        add_action( 'wp_ajax_joomsport_grouppart_shortcode', array("JoomSportPostSeason",'joomsport_grouppart_shortcode') );
        add_action( 'wp_ajax_joomsport_matches_shortcode', array("JoomSportPostSeason",'joomsport_matches_shortcode') );
        add_action( 'wp_ajax_joomsport_plstat_shortcode', array("JoomSportPostSeason",'joomsport_plstat_shortcode') );
        add_action( 'wp_ajax_joomsport_matchday_shortcode', array("JoomSportPostSeason",'joomsport_matchday_shortcode') );
        add_action( 'wp_ajax_joomsport_matchdaylist_shortcode', array("JoomSportPostSeason",'joomsport_matchdaylist_shortcode') );
        add_action( 'wp_ajax_joomsport_playerlist_shortcode', array("JoomSportPostSeason",'joomsport_playerlist_shortcode') );
        
        $slug = get_option( 'joomsportslug_joomsport_season', null );
    
        register_post_type( 'joomsport_season',
                apply_filters( 'joomsport_register_post_type_season',
                        array(
                                'labels'              => array(
                                                'name'               => __( 'Season', 'joomsport-sports-league-results-management' ),
                                                'singular_name'      => __( 'Season', 'joomsport-sports-league-results-management' ),
                                                'menu_name'          => _x( 'Seasons', 'Admin menu name Seasons', 'joomsport-sports-league-results-management' ),
                                                'add_new'            => __( 'Add Season', 'joomsport-sports-league-results-management' ),
                                                'add_new_item'       => __( 'Add New Season', 'joomsport-sports-league-results-management' ),
                                                'edit'               => __( 'Edit', 'joomsport-sports-league-results-management' ),
                                                'edit_item'          => __( 'Edit Season', 'joomsport-sports-league-results-management' ),
                                                'new_item'           => __( 'New Season', 'joomsport-sports-league-results-management' ),
                                                'view'               => __( 'View Season', 'joomsport-sports-league-results-management' ),
                                                'view_item'          => __( 'View Season', 'joomsport-sports-league-results-management' ),
                                                'search_items'       => __( 'Search Season', 'joomsport-sports-league-results-management' ),
                                                'not_found'          => __( 'No Season found', 'joomsport-sports-league-results-management' ),
                                                'not_found_in_trash' => __( 'No Season found in trash', 'joomsport-sports-league-results-management' ),
                                                'parent'             => __( 'Parent Season', 'joomsport-sports-league-results-management' )
                                        ),
                                'description'         => __( 'This is where you can add new season.', 'joomsport-sports-league-results-management' ),
                                'public'              => true,
                                'show_ui'             => true,
                                'show_in_menu'        => 'joomsport',
                                'publicly_queryable'  => true,
                                'exclude_from_search' => false,
                                'hierarchical'        => true,
                                'query_var'           => true,
                                'supports'            => array( 'title'),
                                'show_in_nav_menus'   => true,
                                'taxonomies'          => array("joomsport_tournament"),
                                'rewrite' => array(
                                    'slug' => $slug?$slug:'joomsport_season'
                                )
                        )
                )
        );
         
    }
    
    public static function season_edit_form_after_title($post_type){
        global $post, $wp_meta_boxes;

        if($post_type->post_type == 'joomsport_season'){
            
            echo JoomSportMetaSeason::output($post_type);

        }
    

    }
    public static function admin_init(){
        add_meta_box('joomsport_season_attr_form_meta_box', __('Attributes', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_attr'), 'joomsport_season', 'side', 'low');
        add_meta_box('joomsport_season_point_form_meta_box', __('Points', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_points'), 'joomsport_season', 'joomsportintab_season1', 'default');
        add_meta_box('joomsport_season_participiants_form_meta_box', __('Participiants', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_participiants'), 'joomsport_season', 'joomsportintab_season1', 'default');
        
        add_meta_box('joomsport_season_rules_form_meta_box', __('Season rules', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_rules'), 'joomsport_season', 'joomsportintab_season1', 'default');

        add_meta_box('joomsport_season_ef_form_meta_box', __('Extra fields', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_ef'), 'joomsport_season', 'joomsportintab_season1', 'default');

        add_meta_box('joomsport_season_stcolumns_form_meta_box', __('Standings columns', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_standingscolumn'), 'joomsport_season', 'joomsportintab_season2', 'default');
        add_meta_box('joomsport_season_highlight_form_meta_box', __('Highlight team places', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_highlight'), 'joomsport_season', 'joomsportintab_season2', 'default');
        add_meta_box('joomsport_season_ranking_form_meta_box', __('Ranking', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_rankcriteria'), 'joomsport_season', 'joomsportintab_season2', 'default');
        
        
        add_meta_box('joomsport_season_stages_form_meta_box', __('Game stages', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_stages'), 'joomsport_season', 'side', 'low');
        
        add_meta_box('joomsport_season_groups_form_meta_box', __('Groups', 'joomsport-sports-league-results-management'), array('JoomSportMetaSeason','js_meta_groups'), 'joomsport_season', 'joomsportintab_season4', 'default');
        
        
        
        add_action( 'save_post',      array( 'JoomSportMetaSeason', 'joomsport_season_save_metabox' ), 10, 2 );
    }
    
    public static function joomsport_season_action_javascript(){
        global $post;
        if(!isset($post->ID)){
            
            return '';
        }
        ?>
        <script type="text/javascript" >
	jQuery(document).ready(function($) {
            
            jQuery('#joomsport_tournament_inseas_id').on("change",function(){
                var data = {
			'action': 'season_parentseas',
			'tournament_id': jQuery('#joomsport_tournament_inseas_id').val(),
                        'post_id':jQuery('#post_ID').val()
		};

		jQuery.post(ajaxurl, data, function(response) {

                    jQuery('#js_seasparentDIV').html(response);
                    
		});
            });
            jQuery('body').on('click', '.jspopupGroups', function() {

                var data = {
			'action': 'season_genermodal',
			'group_id': jQuery(this).attr("attrid"),
                        'post_id':jQuery('#post_ID').val()
		};

		jQuery.post(ajaxurl, data, function(response) {

                    jQuery('#jsGroupsdialog').html(response);
                    jQuery("#js_group_part").chosen({disable_search_threshold: 10,width: "95%",disable_search:false});
                    jQuery( "#jsGroupsdialog" ).dialog({modal: true,height: 300,width:450,
                        buttons: {
                          Ok: function() {
                               var data = {
                               'action': 'season_groupedit',
                               'group_id': jQuery('#js_seas_groupid').val(),
                               'post_id':jQuery('#post_ID').val(),
                               'group_title':jQuery('#js_group_title').val(),
                               'group_part':jQuery('#js_group_part').val(),
                               'grroundtype':jQuery('#grroundtype').val(),
                               };

                               jQuery.post(ajaxurl, data, function(response) {

                                   var data = {
                                        'action': 'season_grouplist',
                                        'post_id':jQuery('#post_ID').val()
                                    };

                                    jQuery.post(ajaxurl, data, function(response) {
                                        jQuery('#jsGroupList').html(response);

                            });

                               }); 
                            jQuery( this ).dialog( "close" );
                          }
                        }
                        
                        
                    });
                    
		});
                
                
                
           });
           
           jQuery("body").on("click",".jsgroupsDEL",function(){
               var data = {
			'action': 'season_groupdel',
			'group_id': jQuery(this).attr("attrid"),
		};
                var cltr = jQuery(this).closest("tr");
		jQuery.post(ajaxurl, data, function(response) {
                    cltr.remove();
                });
           });
		
	});
	</script>
         
            <div style="display:none;" id="jsGroupsdialog" title="<?php echo __('Group','joomsport-sports-league-results-management');?>">
                <?php echo JoomSportHelperObjects::getGroupEdit(0, $post->ID);?>
            </div>
        
        <?php
    }
    public static function joomsport_season_parentseas(){
        $terms_id = intval($_POST['tournament_id']);
        $terms_id = $terms_id == -1 ?null:$terms_id;
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);
        echo JoomSportHelperObjects::wp_dropdown_posts($post,$terms_id);
        
        wp_die();
    }
    public static function joomsport_season_groupedit(){
        global $wpdb;
        $group_id = intval($_POST['group_id']);
        $post_id = intval($_POST['post_id']);
        $part = serialize($_POST["group_part"]);
        $groptions['grtype'] = ($_POST['grroundtype']);
        if($group_id){
            $wpdb->update($wpdb->joomsport_groups,array("group_name" => esc_attr(sanitize_text_field($_POST["group_title"])),"group_partic" => ($part),"s_id"=>$post_id,"options"=>serialize($groptions)),array("id" => $group_id),array("%s","%s","%d","%s"),array("%d"));
        
        }else{
            $wpdb->insert($wpdb->joomsport_groups,array("id" => 0,"group_name" => esc_attr(sanitize_text_field($_POST["group_title"])),"group_partic" => $part,"s_id"=>$post_id,"options"=>serialize($groptions)),array("%d","%s","%s","%d","%s"));
        }
        wp_die();
    }
    public static function joomsport_season_genermodal(){
        $group_id = intval($_POST['group_id']);
        $post_id = intval($_POST['post_id']);
        echo JoomSportHelperObjects::getGroupEdit($group_id, $post_id);
        wp_die();
    }
    public static function joomsport_season_grouplist(){
        global  $wpdb;

        $post_id = intval($_POST['post_id']);
                
        $groups = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$post_id} ORDER BY ordering"); 

        if (count($groups)) {
            $i = 0;
            foreach ($groups as $gr) {
                ?>
                <tr>
                    <td class="jsdadicon">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="jsgroupsDEL" attrid="<?php echo $gr->id?>">
                            <i class="fa fa-trash" aria-hidden="true" attrid="<?php echo $gr->id?>"></i>
                        </a>        
                        <input type="hidden" name="groupId[]" value="<?php echo $gr->id?>" />
                    </td>
                    <td></td>
                    <td>    
                        <a class="jspopupGroups" href="javascript:void(0);" attrid="<?php echo $gr->id?>">

                            <?php echo $gr->group_name?>
                        </a>
                    </td>    
                </tr>
                <?php
                ++$i;
            }
        }
               
        wp_die();
    }
    public static function joomsport_season_groupdel(){
        global  $wpdb;

        $group_id = intval($_POST['group_id']);
        if($group_id){
            $wpdb->query("DELETE  FROM {$wpdb->joomsport_groups} WHERE id = {$group_id}"); 
        }
        wp_die();
    }
    
    public static function joomsport_season_validate(){

        global $post_type;
        $post_for_check = array(
            "joomsport_season",
            "joomsport_match",
            "joomsport_team",
            "joomsport_player",
            );
        
        if( in_array($post_type, $post_for_check) )
        wp_enqueue_script( 'joomsport-season-admin-script', plugin_dir_url( __FILE__ ) . '../../assets/js/validate.js', array('jquery') );

    }
    public static function joomsport_taxonomy_validate(){

        
        wp_enqueue_script( 'joomsport-season-admin-script', plugin_dir_url( __FILE__ ) . '../../assets/js/validate.js', array('jquery') );

    }
    
    public static function joomsport_standings_shortcode(){
        ?>
        <div class="JSshrtPop">
            <div>
                <label><?php echo __("Select season", "joomsport-sports-league-results-management");?></label>
                <?php $results =  JoomSportHelperObjects::getSeasons(-1);?>
                <?php echo JoomSportHelperSelectBox::Optgroup('season_id', $results,'', ' id="jsshrtcodesid"');?>
            </div>
            <div id="jsstandgroup">

            </div>
            <div>
                <label><?php echo __("Display places", "joomsport-sports-league-results-management");?></label>
                <input type="number" name="places" id='jsshrtcplace' maxlength="2" size="3" value="0" min="0" />
            </div>
            <div id="jsstandpartic">

            </div>
            <div>
                <?php
                $jsconfig =  new JoomsportSettings();
                $available_options = $jsconfig->getStandingColumns();
                $available_options['emblem_chk']= array('label'=> __('Emblem', 'joomsport-sports-league-results-management'),'short'=>'');
        
                ?>
                <label><?php echo __("Choose columns", "joomsport-sports-league-results-management");?></label>
                <select name="jsshrtcolumns[]" id="jsshrtcolumns" class="jswf-chosen-select" data-placeholder="<?php echo __('Add item','joomsport-sports-league-results-management')?>" multiple="multiple">
                    <?php
                        foreach ($available_options as $key => $value) {
                           echo '<option value="'.$key.'">'.$value['label'].'</option>';             
                        }
                    ?>
                </select>
            </div>     
            <div>
                <input type="button" value="<?php echo __("Add shortcode","joomsport-sports-league-results-management");?>" class="button jsaddtblscode" />
            </div>
                <script>
                    jQuery(".jswf-chosen-select").chosen({disable_search_threshold: 10,width: "50%",disable_search:false});
                </script>   
        </div>        
        <?php
        wp_die();
    }
    public static function joomsport_group_shortcode(){
        global  $wpdb;

        $season_id = intval($_POST['season_id']);
        
        $return = array("groups" => '', "partic" => '');        
        $groups = $wpdb->get_results("SELECT id,group_name as name FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} ORDER BY ordering"); 
        if (count($groups)) {
            $i = 0;
            $return['groups'] .= '<label>'.__("Select group", "joomsport-sports-league-results-management").'</label>';
            $return['groups'] .= JoomSportHelperSelectBox::Simple('group_id', $groups,'',' id="jsshrtgroup_id"');
        }
        
        $partic = JoomSportHelperObjects::getParticipiants($season_id);

        if($partic && count($partic)){
            $particarray = array();
            foreach ($partic as $particA){
                
                $tmp = new stdClass();
                $tmp->name = $particA->post_title;
                $tmp->id = $particA->ID;
                $particarray[] = $tmp;
            }
            $return['partic'] .= '<label>'.__("Select participant", "joomsport-sports-league-results-management").'</label>';
            $return['partic'] .= JoomSportHelperSelectBox::Simple('partic_id', $particarray,'',' id="partic_id"');
        }
        echo json_encode($return);
        wp_die();
    }
    public static function joomsport_grouppart_shortcode(){
        global  $wpdb;

        $season_id = intval($_POST['season_id']);
        $group_id = intval($_POST['group_id']);
        if($group_id){
            $group = $wpdb->get_row("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$season_id} AND id={$group_id} ORDER BY ordering"); 
            $metadata = isset($group->group_partic)?  unserialize($group->group_partic):array();
            if($metadata && count($metadata)){
                $particarray = array();
                foreach ($metadata as $particA){

                    $tmp = new stdClass();
                    $tmp->name = get_the_title($particA);
                    $tmp->id = $particA;
                    $particarray[] = $tmp;
                }
                echo '<label>'.__("Select participant", "joomsport-sports-league-results-management").'</label>';
                echo JoomSportHelperSelectBox::Simple('partic_id', $particarray,'',' id="partic_id"');
            }
        }else{
            $partic = JoomSportHelperObjects::getParticipiants($season_id);

            if($partic && count($partic)){
                $particarray = array();
                foreach ($partic as $particA){

                    $tmp = new stdClass();
                    $tmp->name = $particA->post_title;
                    $tmp->id = $particA->ID;
                    $particarray[] = $tmp;
                }
                echo '<label>'.__("Select participant", "joomsport-sports-league-results-management").'</label>';
                echo JoomSportHelperSelectBox::Simple('partic_id', $particarray,'',' id="partic_id"');
            }
        }
        
        wp_die();
    }
    
    public static function joomsport_matches_shortcode(){
        $lists_radio = array();
        $lists_radio[] = JoomSportHelperSelectBox::addOption(0, __('No','joomsport-sports-league-results-management'));
        $lists_radio[] = JoomSportHelperSelectBox::addOption(1, __('Yes','joomsport-sports-league-results-management'));
        $lists_layout = array();
        $lists_layout[] = JoomSportHelperSelectBox::addOption(0, __('Horizontal','joomsport-sports-league-results-management'));
        $lists_layout[] = JoomSportHelperSelectBox::addOption(1, __('Vertical','joomsport-sports-league-results-management'));
        $lists_order = array();
        $lists_order[] = JoomSportHelperSelectBox::addOption(0, __('Asc','joomsport-sports-league-results-management'));
        $lists_order[] = JoomSportHelperSelectBox::addOption(1, __('Desc','joomsport-sports-league-results-management'));
        
        ?>
        <div class="JSshrtPop">
            <div>
                <label><?php echo __("Select season", "joomsport-sports-league-results-management");?></label>
                <?php $results =  JoomSportHelperObjects::getSeasons(-1);?>
                <?php echo JoomSportHelperSelectBox::Optgroup('season_id', $results,'', ' id="jsshrtcodesid"');?>
            </div>
            <div id="jsstandgroup">

            </div>

            <div id="jsstandpartic">

            </div>
            <div>
                <label><?php echo __("Matches quantity", "joomsport-sports-league-results-management");?></label>
                <input type="number" name="quantity" id='jsshrtcquantity' maxlength="2" size="3" value="0" min="0" />
            </div>
            <div>
                <label><?php echo __("Display matches", "joomsport-sports-league-results-management");?></label>
                <?php
                $lists = array();
                $lists[] = JoomSportHelperSelectBox::addOption(0, __('All','joomsport-sports-league-results-management'));
                $lists[] = JoomSportHelperSelectBox::addOption(1, __('Fixtures','joomsport-sports-league-results-management'));
                $lists[] = JoomSportHelperSelectBox::addOption(2, __('Played','joomsport-sports-league-results-management'));
                ?>
                <?php echo JoomSportHelperSelectBox::Simple('matchtype', $lists,'', ' id="jsshrtcodematchtype"');?>
            </div>
            
            <div>
                <label class="jsradiodivlabel"><?php echo __("Display emblems", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_embl', $lists_radio,'', ' id="display_embl"');?>
                </div>

            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Display venue", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_venue', $lists_radio,'', ' id="display_venue"');?>
                </div>    

            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Display season name", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_seasname', $lists_radio,'', ' id="display_seasname"');?>
                </div>    

            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Enable slider", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_slider', $lists_radio,'', ' id="display_slider"');?>
                </div>
            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Layout", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_layout', $lists_layout,'', ' id="display_layout"');?>
                </div>
            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Group by matchday", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_grbymd', $lists_radio,'', ' id="display_grbymd"');?>
                </div>
            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Ordering", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_order', $lists_order,'', ' id="display_order"');?>
                </div>
            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Date range", "joomsport-sports-league-results-management");?></label>
                
                <div class="jsradiodiv">
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo __("Past", "joomsport-sports-league-results-management");?></th>
                                <th><?php echo __("Today", "joomsport-sports-league-results-management");?></th>
                                <th><?php echo __("Future", "joomsport-sports-league-results-management");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input style="width:40px;" type="number" id="drange_past" name="drange_past" value=""></td>
                                <td style="text-align:center;"><input type="checkbox" id="drange_today" name="drange_today" value="1"></td>
                                <td><input style="width:40px;" type="number" id="drange_future" name="drange_future" value=""></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <input type="button" value="<?php echo __("Add shortcode","joomsport-sports-league-results-management");?>" class="button jsaddmatchesscode" />
            </div>
             
        </div>        
        <?php
        wp_die();
    }
    
    
    public static function joomsport_plstat_shortcode(){
        global $wpdb;
        $lists_radio = array();
        $lists_radio[] = JoomSportHelperSelectBox::addOption(0, __('No','joomsport-sports-league-results-management'));
        $lists_radio[] = JoomSportHelperSelectBox::addOption(1, __('Yes','joomsport-sports-league-results-management'));

        ?>
        <div class="JSshrtPop">
            <div>
                <label><?php echo __("Select event", "joomsport-sports-league-results-management");?></label>
                <?php $ev = $wpdb->get_results('SELECT id, e_name as name FROM '.$wpdb->joomsport_events.' WHERE player_event="1" ORDER BY ordering', 'OBJECT') ;?>
                <?php echo JoomSportHelperSelectBox::Simple('event_id', $ev,'', ' id="jsshrtcodeevid"');?>
            </div>
            <div>
                <label><?php echo __("Select season", "joomsport-sports-league-results-management");?></label>
                <?php $results =  JoomSportHelperObjects::getSeasons(-1);?>
                <?php echo JoomSportHelperSelectBox::Optgroup('season_id', $results,'', ' id="jsshrtcodesid"');?>
            </div>
            <div id="jsstandgroup">

            </div>

            <div id="jsstandpartic">

            </div>
            <div>
                <label><?php echo __("Quantity", "joomsport-sports-league-results-management");?></label>
                <input type="number" name="quantity" id='jsshrtcquantity' maxlength="2" size="3" value="0" min="0" />
            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Display photo", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_embl[]', $lists_radio,'', ' id="display_embl"');?>
                </div>  
            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Display team name", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('display_teamname[]', $lists_radio,'', ' id="display_teamname"');?>

                </div>  
            </div>

            <div>
                <input type="button" value="<?php echo __("Add shortcode","joomsport-sports-league-results-management");?>" class="button jsaddplayerscode" />
            </div>
             
        </div>        
        <?php
        wp_die();
    }
    public static function joomsport_matchday_shortcode(){
        global $wpdb;
        $lists_radio = array();
        $lists_radio[] = JoomSportHelperSelectBox::addOption(0, __('No','joomsport-sports-league-results-management'));
        $lists_radio[] = JoomSportHelperSelectBox::addOption(1, __('Yes','joomsport-sports-league-results-management'));

        ?>
        <div class="JSshrtPop">
            <div>
                <label><?php echo __("Select season", "joomsport-sports-league-results-management");?></label>
                <?php $results =  JoomSportHelperObjects::getSeasons(-1);?>
                <?php echo JoomSportHelperSelectBox::Optgroup('season_id', $results,'', ' id="jsshrtcodesidmd"');?>
            </div>
            <div id="jsmatchdayseason">

            </div>

            <div>
                <input type="button" value="<?php echo __("Add shortcode","joomsport-sports-league-results-management");?>" class="button jsaddmatchdaycode" />
            </div>
             
        </div>        
        <?php
        wp_die();
    }
    public static function joomsport_matchdaylist_shortcode(){
        global  $wpdb;

        $season_id = intval($_POST['season_id']);
        
        $return = array("mday" => '');        
        $mdays = array();
        if(get_bloginfo('version') < '4.5.0'){
            $tx = get_terms('joomsport_matchday',array(
                "hide_empty" => false
            ));
        }else{
            $tx = get_terms(array(
                "taxonomy" => "joomsport_matchday",
                "hide_empty" => false
            ));
        }

        for($intA=0;$intA<count($tx);$intA++){
            $term_meta = get_option( "taxonomy_".$tx[$intA]->term_id."_metas");

            if($term_meta['season_id'] == $season_id){

                    $tmp = new stdClass();
                    $tmp->id = $tx[$intA]->term_id;
                    $tmp->name = $tx[$intA]->name;
                    $mdays[] = $tmp;
                
            }
        }
        $return['mday'] .= '<label>'.__("Select matchday", "joomsport-sports-league-results-management").'</label>';
        $return['mday'] .= JoomSportHelperSelectBox::Simple('matchday_id', $mdays,'',' id="matchday_id"');
        
        echo json_encode($return);
        wp_die();
    }
    public static function joomsport_playerlist_shortcode(){
        global $wpdb;
        $lists_radio = array();
        $lists_radio[] = JoomSportHelperSelectBox::addOption(0, __('Statistic','joomsport-sports-league-results-management'));
        
        $lists_radio[] = JoomSportHelperSelectBox::addOption(1, __('Photos','joomsport-sports-league-results-management'));
        $query = "SELECT name, id"
                . " FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='0' AND field_type = 3"
                . " ORDER BY name";
        $adfSel = $wpdb->get_results($query);
        ?>
        <div class="JSshrtPop JSshrtPopPlayerList">
            
            <div>
                <label><?php echo __("Select season", "joomsport-sports-league-results-management");?></label>
                <?php $results =  JoomSportHelperObjects::getSeasons(0);?>
                <?php echo JoomSportHelperSelectBox::Optgroup('season_id', $results,'', ' id="jsshrtcodesid"');?>
            </div>


            <div id="jsstandpartic">

            </div>
            
            <div>
                <label class="jsradiodivlabel"><?php echo __("View", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php echo JoomSportHelperSelectBox::Radio('pview', $lists_radio,'', ' id="pview"',array('lclasses'=>array(1,1)));?>
                </div>  
            </div>
            <div>
                <label class="jsradiodivlabel"><?php echo __("Group players by (photos view)", "joomsport-sports-league-results-management");?></label>
                <div class="jsradiodiv">
                    <?php 
                    echo JoomSportHelperSelectBox::Simple('pgroup', $adfSel,'','',true);

                    ?>
                </div>  
            </div>

            <div>
                <input type="button" value="<?php echo __("Add shortcode","joomsport-sports-league-results-management");?>" class="button jsaddplayerlistcode" />
            </div>
             
        </div>        
        <?php
        wp_die();
    }
    public static function joomsport_season_tournamentmodal(){
        global $joomsportSettings;
        $is_container = $joomsportSettings->get('hierarchical_season', 0);
        $is_field_yn = array();
        $is_field_yn[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field_yn[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        
        ?>
            <table>    
                <tr>
                    <td>
                        <label><?php echo  __("League", "joomsport-sports-league-results-management");?> *</label>
                    </td>
                    <?php
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
                    echo '<td>';
                    echo '<select name="joomsport_tournament" id="joomsport_tournament_modal_id" class="postform" aria-required="true">';
                        echo '<option value="">'.__('Select league','joomsport-sports-league-results-management').'</option>';
                        for($intA=0;$intA<count($tx);$intA++){
                            $term_meta = get_option( "taxonomy_".$tx[$intA]->term_id."_metas");
                            
                            if($term_meta['t_single'] == $joomsportSettings->get('tournament_type')){
                                echo '<option value="'.$tx[$intA]->term_id.'">'.$tx[$intA]->name.'</option>';

                            }
                            
                        }

                    echo '</select>';
                    ?>
                    </td>
                </tr>
                <?php
                if($is_container){
                ?>
                <tr>
                    <td>
                        <label><?php echo  __("Season container", "joomsport-sports-league-results-management");?></label>
                    </td>
                    <td>
                    <div class="controls">
                        <fieldset class="radio btn-group">
                            <?php 
                            echo JoomSportHelperSelectBox::Radio('joomsport_season_container', $is_field_yn,0,'');
                            ?>
                        </fieldset>
                    </div>
                    </td>    
                </tr>
                <?php
                }else{
                    echo '<input type="hidden" name="joomsport_season_container" value="0" />';
                }
                ?>
            </table>    
        <?php
        wp_die();
    }

}    



add_action( 'wp_trash_post', 'function_to_run_on_post_trash' );

function function_to_run_on_post_trash( $post_id ){
    $childs = get_posts(
            array(
                'post_parent' => $post_id,
                'post_type' => 'joomsport_season'
            ) 
    );

    if(empty($childs))
        return;

    foreach($childs as $post){
        wp_trash_post($post->ID);
    }

}

/*
function JSSNrewrite_taxonomy_term_permalink($link, $post)
{
    if ($post->post_type != 'joomsport_season')
        return $link;

    return home_url() . '/' . $post->post_name . '-season/';
}
add_filter( 'post_type_link', 'JSSNrewrite_taxonomy_term_permalink', 10, 3);
function JSSN_rewrite_rules($wp_rewrite) {
  $new_rules = array(
      '(.+?)-season/?$' =>  'index.php?joomsport_season=$matches[1]' 
  );
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
  //var_dump($wp_rewrite);
  return $wp_rewrite;
}
add_action('generate_rewrite_rules', 'JSSN_rewrite_rules');
 * 
 */