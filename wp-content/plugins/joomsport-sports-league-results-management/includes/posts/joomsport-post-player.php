<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

require_once JOOMSPORT_PATH_INCLUDES . 'meta-boxes' . DIRECTORY_SEPARATOR . 'joomsport-meta-player.php';

class JoomSportPostPlayer {
    public function __construct() {

    }
    public static function init(){
        self::register_post_types();
    }
    public static function register_post_types(){
        add_action("admin_init", array("JoomSportPostPlayer","admin_init"));
        add_action( 'edit_form_after_title',  array( 'JoomSportPostPlayer','player_edit_form_after_title') );
        add_action( 'admin_footer', array("JoomSportPostPlayer",'joomsport_player_action_javascript') );
        add_action( 'wp_ajax_player_seasonrelated', array("JoomSportPostPlayer",'joomsport_player_seasonrelated') );
        $slug = get_option( 'joomsportslug_joomsport_player', null );
        register_post_type( 'joomsport_player',
                apply_filters( 'joomsport_register_post_type_player',
                        array(
                                'labels'              => array(
                                                'name'               => __( 'Player', 'joomsport-sports-league-results-management' ),
                                                'singular_name'      => __( 'Player', 'joomsport-sports-league-results-management' ),
                                                'menu_name'          => _x( 'Players', 'Admin menu name Players', 'joomsport-sports-league-results-management' ),
                                                'add_new'            => __( 'Add Player', 'joomsport-sports-league-results-management' ),
                                                'add_new_item'       => __( 'Add New Player', 'joomsport-sports-league-results-management' ),
                                                'edit'               => __( 'Edit', 'joomsport-sports-league-results-management' ),
                                                'edit_item'          => __( 'Edit Player', 'joomsport-sports-league-results-management' ),
                                                'new_item'           => __( 'New Player', 'joomsport-sports-league-results-management' ),
                                                'view'               => __( 'View Player', 'joomsport-sports-league-results-management' ),
                                                'view_item'          => __( 'View Player', 'joomsport-sports-league-results-management' ),
                                                'search_items'       => __( 'Search Player', 'joomsport-sports-league-results-management' ),
                                                'not_found'          => __( 'No Player found', 'joomsport-sports-league-results-management' ),
                                                'not_found_in_trash' => __( 'No Player found in trash', 'joomsport-sports-league-results-management' ),
                                                'parent'             => __( 'Parent Player', 'joomsport-sports-league-results-management' )
                                        ),
                                'description'         => __( 'This is where you can add new player.', 'joomsport-sports-league-results-management' ),
                                'public'              => true,
                                'show_ui'             => true,
                                'show_in_menu'        => (current_user_can('manage_options')?'joomsport':null),
                                'publicly_queryable'  => true,
                                'exclude_from_search' => false,
                                'hierarchical'        => false,
                                'query_var'           => true,
                                'supports'            => array( 'title' ),
                                'show_in_nav_menus'   => true,
                                'capability_type' => 'jscp_player',
                                'capabilities' => array(
                                    'edit_post' => 'edit_jscp_player',
                                    'edit_posts' => 'edit_jscp_players',
                                    'edit_others_posts' => 'edit_others_jscp_player',
                                    'publish_posts' => 'publish_jscp_player',
                                    'read_post' => 'read_jscp_player',
                                    'delete_post' => 'delete_jscp_player'
                                ),
                                'map_meta_cap' => true,
                                'rewrite' => array(
                                    'slug' => $slug?$slug:'joomsport_player'
                                )
                        )
                )
        );
    }
    public static function player_edit_form_after_title($post_type){
        global $post, $wp_meta_boxes;

        if($post_type->post_type == 'joomsport_player'){
            
            echo JoomSportMetaPlayer::output($post_type);

        }
    

    }
    public static function admin_init(){
        $tournament_type = JoomSportHelperObjects::getCurrentTournamentType();
        add_meta_box('joomsport_player_personal_form_meta_box', __('Personal', 'joomsport-sports-league-results-management'), array('JoomSportMetaPlayer','js_meta_personal'), 'joomsport_player', 'joomsportintab_player1', 'default');
        add_meta_box('joomsport_player_about_form_meta_box', __('About player', 'joomsport-sports-league-results-management'), array('JoomSportMetaPlayer','js_meta_about'), 'joomsport_player', 'joomsportintab_player1', 'default');

        add_meta_box('joomsport_player_ef_form_meta_box', __('Extra fields', 'joomsport-sports-league-results-management'), array('JoomSportMetaPlayer','js_meta_ef'), 'joomsport_player', 'joomsportintab_player1', 'default');

        if($tournament_type == '1'){
            add_meta_box('joomsport_player_bonuses_form_meta_box', __('Bonuses', 'joomsport-sports-league-results-management'), array('JoomSportMetaPlayer','js_meta_bonuses'), 'joomsport_player', 'joomsportintab_player2', 'default');
        
        }else{
            add_meta_box('joomsport_player_teams_form_meta_box', __('Assigned teams', 'joomsport-sports-league-results-management'), array('JoomSportMetaPlayer','js_meta_teams'), 'joomsport_player', 'joomsportintab_player2', 'default');
        
        }

        add_meta_box('joomsport_player_ef_assigned_form_meta_box', __('Extra fields assigned to the season', 'joomsport-sports-league-results-management'), array('JoomSportMetaPlayer','js_meta_ef_assigned'), 'joomsport_player', 'joomsportintab_player2', 'default');

        
        add_action( 'save_post',      array( 'JoomSportMetaPlayer', 'joomsport_player_save_metabox' ), 10, 2 );
    }
    
    public static function joomsport_player_action_javascript(){
        ?>
        <script type="text/javascript" >
	jQuery(document).ready(function($) {
            jQuery('select[name="spb_season_id"]').on("change",function(){
                var data = {
			'action': 'player_seasonrelated',
			'season_id': jQuery('select[name="spb_season_id"]').val(),
                        'post_id':jQuery('#post_ID').val()
		};

		jQuery.post(ajaxurl, data, function(response) {
                    console.log(response);
                    var res = jQuery.parseJSON( response );
                    if(res.players){
                        jQuery('#js_player_teamsDIV').html(res.players);
                        jQuery("#stb_teams_id").chosen({disable_search_threshold: 10,width: "95%",disable_search:false});
                    }
                    if(res.bonuses){
                        jQuery('#js_player_bonusesDIV').html(res.bonuses);
                    }
                    if(res.efassigned){
                        jQuery('#js_player_efassignedDIV').html(res.efassigned);
                    }
                    //jQuery('#stb_players_id').trigger('liszt:updated');
                    
		});
            });
		
	});
	</script>
        <?php
    }
    public static function joomsport_player_seasonrelated(){
        
        $season_id = intval($_POST['season_id']);
        $post_id = intval($_POST['post_id']);
        $result = array(
            "players"=>__('No season selected','joomsport-sports-league-results-management'),
            "bonuses"=>__('No season selected','joomsport-sports-league-results-management'),
            "efassigned"=>__('No season selected','joomsport-sports-league-results-management'),
            );
        if($season_id && $post_id){
            $playersin = JoomSportHelperObjects::getPlayerTeams($season_id, $post_id);
            $posts_players = JoomSportHelperObjects::getParticipiants($season_id);

            if(count($posts_players)){
                $result['players'] = '<select name="teams_id[]" id="stb_teams_id" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" class="jswf-chosen-select" multiple>';
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
            $result['bonuses'] = '<table  class="jsminwdhtd"><tr><td>'.__('Bonus points','joomsport-sports-league-results-management').'</td><td><input type="text" name="js_bonuses" value="'.$bonus.'"></td></tr></table>';

            $metadata = get_post_meta($post_id,'_joomsport_player_ef_'.$season_id,true);
        
            $efields = JoomSportHelperEF::getEFList('0', 1, 1);

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