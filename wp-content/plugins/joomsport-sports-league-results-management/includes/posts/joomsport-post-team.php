<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-team.php';

class JoomSportPostTeam {
    public function __construct() {

    }
    public static function init(){
        self::register_post_types();
    }
    public static function register_post_types(){
        add_action("admin_init", array("JoomSportPostTeam","admin_init"));
        add_action( 'edit_form_after_title',  array( 'JoomSportPostTeam','team_edit_form_after_title') );
        add_action( 'admin_footer', array("JoomSportPostTeam",'joomsport_team_action_javascript') );
        add_action( 'wp_ajax_team_seasonrelated', array("JoomSportPostTeam",'joomsport_team_seasonrelated') );
        add_filter( 'post_type_labels_joomsport_team', array( 'JoomSportPostTeam','team_replace_default_featured_image_meta_box'), 10,1 );
        $slug = get_option( 'joomsportslug_joomsport_team', null );
        register_post_type( 'joomsport_team',
                apply_filters( 'joomsport_register_post_type_team',
                        array(
                                'labels'              => array(
                                                'name'               => __( 'Team', 'joomsport-sports-league-results-management' ),
                                                'singular_name'      => __( 'Team', 'joomsport-sports-league-results-management' ),
                                                'menu_name'          => _x( 'Teams', 'Admin menu name Teams', 'joomsport-sports-league-results-management' ),
                                                'add_new'            => __( 'Add Team', 'joomsport-sports-league-results-management' ),
                                                'add_new_item'       => __( 'Add New Team', 'joomsport-sports-league-results-management' ),
                                                'edit'               => __( 'Edit', 'joomsport-sports-league-results-management' ),
                                                'edit_item'          => __( 'Edit Team', 'joomsport-sports-league-results-management' ),
                                                'new_item'           => __( 'New Team', 'joomsport-sports-league-results-management' ),
                                                'view'               => __( 'View Team', 'joomsport-sports-league-results-management' ),
                                                'view_item'          => __( 'View Team', 'joomsport-sports-league-results-management' ),
                                                'search_items'       => __( 'Search Team', 'joomsport-sports-league-results-management' ),
                                                'not_found'          => __( 'No Team found', 'joomsport-sports-league-results-management' ),
                                                'not_found_in_trash' => __( 'No Team found in trash', 'joomsport-sports-league-results-management' ),
                                                'parent'             => __( 'Parent Team', 'joomsport-sports-league-results-management' )
                                        ),
                                'description'         => __( 'This is where you can add new team.', 'joomsport-sports-league-results-management' ),
                                'public'              => true,
                                'show_ui'             => true,
                                'show_in_menu'        => (current_user_can('manage_options')?'joomsport':null),
                                'publicly_queryable'  => true,
                                'exclude_from_search' => false,
                                'hierarchical'        => false,
                                'query_var'           => true,
                                'supports'            => array( 'title','thumbnail' ),
                                'show_in_nav_menus'   => true,
                                'capability_type' => 'jscp_team',
                                'capabilities' => array(
                                    'edit_post' => 'edit_jscp_team',
                                    'edit_posts' => 'edit_jscp_teams',
                                    'edit_others_posts' => 'edit_others_jscp_team',
                                    'publish_posts' => 'publish_jscp_team',
                                    'read_post' => 'read_jscp_team',
                                    'delete_post' => 'delete_jscp_team'
                                ),
                                'map_meta_cap' => true,
                                'rewrite' => array(
                                    'slug' => $slug?$slug:'joomsport_team'
                                )
                        )
                )
        );
    }
    public static function team_edit_form_after_title($post_type){
        global $post, $wp_meta_boxes;

        if($post_type->post_type == 'joomsport_team'){
            
            echo JoomSportMetaTeam::output($post_type);

        }
    

    }
    public static function team_replace_default_featured_image_meta_box($labels ) {

	$labels->featured_image 	= __('Logo', 'joomsport-sports-league-results-management');
	$labels->set_featured_image 	= __('Set logo', 'joomsport-sports-league-results-management');
	$labels->remove_featured_image 	= __('Remove logo', 'joomsport-sports-league-results-management');
	$labels->use_featured_image 	= __('Use as logo', 'joomsport-sports-league-results-management');

	return $labels;

    } 
    public static function admin_init(){
        //add_meta_box('joomsport_team_personal_form_meta_box', __('Personal', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_personal'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_about_form_meta_box', __('About team', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_about'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_seasons_form_meta_box', __('Assign to season', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_seasons'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_ef_form_meta_box', __('Extra fields', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_ef'), 'joomsport_team', 'joomsportintab_team1', 'default');
        add_meta_box('joomsport_team_players_form_meta_box', __('Players', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_players'), 'joomsport_team', 'joomsportintab_team2', 'default');
        add_meta_box('joomsport_team_bonuses_form_meta_box', __('Bonuses', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_bonuses'), 'joomsport_team', 'joomsportintab_team2', 'default');

        add_meta_box('joomsport_team_ef_assigned_form_meta_box', __('Extra fields assigned to the season', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_ef_assigned'), 'joomsport_team', 'joomsportintab_team2', 'default');

        add_meta_box('joomsport_team_venue_form_meta_box', __('Home venue', 'joomsport-sports-league-results-management'), array('JoomSportMetaTeam','js_meta_venue'), 'joomsport_team', 'side', 'default');
        
        
        add_action( 'save_post',      array( 'JoomSportMetaTeam', 'joomsport_team_save_metabox' ), 10, 2 );
    }
    public static function joomsport_team_action_javascript(){
        ?>
        <script type="text/javascript" >
	jQuery(document).ready(function($) {
            jQuery('select[name="stb_season_id"]').on("change",function(){
                var data = {
			'action': 'team_seasonrelated',
			'season_id': jQuery('select[name="stb_season_id"]').val(),
                        'post_id':jQuery('#post_ID').val()
		};

		jQuery.post(ajaxurl, data, function(response) {

                    var res = jQuery.parseJSON( response );
                    if(res.players){
                        jQuery('#js_team_playersDIV').html(res.players);
                        jQuery("#stb_players_id").chosen({disable_search_threshold: 10,width: "95%",disable_search:false});
                    }
                    if(res.bonuses){
                        jQuery('#js_team_bonusesDIV').html(res.bonuses);
                    }
                    if(res.efassigned){
                        jQuery('#js_team_efassignedDIV').html(res.efassigned);
                    }
                    //jQuery('#stb_players_id').trigger('liszt:updated');
                    
		});
            });
		
	});
	</script>
        <?php
    }
    public static function joomsport_team_seasonrelated(){
        
        $season_id = intval($_POST['season_id']);
        $post_id = intval($_POST['post_id']);
        $result = array(
            "players"=>__('No season selected','joomsport-sports-league-results-management'),
            "bonuses"=>__('No season selected','joomsport-sports-league-results-management'),
            "efassigned"=>__('No season selected','joomsport-sports-league-results-management'),
            );
        if($season_id && $post_id){
            $playersin = get_post_meta($post_id,'_joomsport_team_players_'.$season_id,true);
            $args = array(
                'posts_per_page' => -1,
                'offset'           => 0,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'joomsport_player',
                'post_status'      => 'publish',
            );
            $posts_players = get_posts( $args );
            if(count($posts_players)){
                $result['players'] = '<select name="players_id[]" id="stb_players_id" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" class="jswf-chosen-select" multiple>';
                foreach ($posts_players as $tm) {
                    $selected = '';
                    if(is_array($playersin) && in_array($tm->ID, $playersin)){
                        $selected = ' selected';
                    }
                    $result['players'] .= '<option value="'.$tm->ID.'" '.$selected.'>'.$tm->post_title.'</option>';
                }
                $result['players'] .= '</select>';
            }
            $bonus = get_post_meta($post_id,'_joomsport_team_bonuses_'.$season_id,true);
            $result['bonuses'] = '<table class="jsminwdhtd"><tr><td>'.__('Bonus points','joomsport-sports-league-results-management').'</td><td><input type="text" name="js_bonuses" value="'.$bonus.'"></td></tr></table>';
            
            $metadata = get_post_meta($post_id,'_joomsport_team_ef_'.$season_id,true);

            $efields = JoomSportHelperEF::getEFList('1', 1, 1);

            if(count($efields)){
                $html_ef = '<div class="jsminwdhtd jstable">';
                foreach ($efields as $ef) {

                    JoomSportHelperEF::getEFInput($ef, isset($metadata[$ef->id])?$metadata[$ef->id]:null,'efs');
                    //var_dump($ef);
                    
                    $html_ef .= '<div class="jstable-row">';
                        $html_ef .= '<div class="jstable-cell">'.$ef->name.'</div>';
                        $html_ef .= '<div class="jstable-cell">';

                            if($ef->field_type == '2'){
                                ob_start();
                                wp_editor(isset($metadata[$ef->id])?$metadata[$ef->id]:'', 'efs_'.$ef->id,array("textarea_rows"=>3));
                                //$html_ef .= '<textarea id="efs_'.$ef->id.'"></textarea>';
                                $html_ef .= ob_get_contents();
                                ob_end_clean();
                                $html_ef .=  '<input type="hidden" name="efs['.$ef->id.']" value="efs_'.$ef->id.'" />';
                                $html_ef .= '<script>tinymce.execCommand( \'mceAddEditor\', true, jQuery("#efs_'.$ef->id.'").attr("id") );quicktags({id : jQuery("#efs_'.$ef->id.'").attr("id")});</script>';
                            }else{
                                $html_ef .=  $ef->edit;
                            }
                           
                        $html_ef .= '</div>';    

                    $html_ef .= '</div>';

                }
                $html_ef .= '</div>';
                $result['efassigned'] = $html_ef;
            }else{
                $result['efassigned'] = 'There are no extra fields assigned to this section.';
            }

        }
        
        
        echo json_encode($result);
        
        wp_die();
    }
}    
