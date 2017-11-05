<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomSportMetaTeam {
    public static function output( $post ) {
        global $post, $thepostid, $wp_meta_boxes;
        
        
        $thepostid = $post->ID;

        require_once JOOMSPORT_PATH_HELPERS . 'tabs.php';
        $etabs = new esTabs();
        wp_nonce_field( 'joomsport_team_savemetaboxes', 'joomsport_team_nonce' );
        ?>
        <div id="joomsportContainerBE">
            <div class="jsBEsettings" style="padding:0px;">
                <!-- <tab box> -->
                <ul class="tab-box">
                    <?php
                    echo $etabs->newTab(__('Main','joomsport-sports-league-results-management'), 'main_conf', '', 'vis');

                    echo $etabs->newTab(__('Season related settings','joomsport-sports-league-results-management'), 'col_conf', '');
                    ?>
                </ul>	
                <div style="clear:both"></div>
            </div>
            <div id="main_conf_div" class="tabdiv">
                <div>
                    <div>
                        <?php
                        do_meta_boxes(get_current_screen(), 'joomsportintab_team1', $post);
                        unset($wp_meta_boxes[get_post_type($post)]['joomsportintab_team1']);
                        ?>

                    </div>    
                </div>
            </div>   
            <div id="col_conf_div" class="tabdiv visuallyhidden">
                <div style="margin-bottom: 25px;margin-left:10px;">
                    <?php
                    $results = JoomSportHelperObjects::getParticipiantSeasons($thepostid);
                    echo __('Select Season', 'joomsport-sports-league-results-management').'&nbsp;&nbsp;';
                    if(!empty($results)){
                        echo JoomSportHelperSelectBox::Optgroup('stb_season_id', $results, '');
                    }else{
                        echo '<div style="color:red;">'.__('Participant is not assigned to any season. Open Main tab and use Assign to season field.', 'joomsport-sports-league-results-management').'</div>';
                    }
                    
                    ?>
                </div>
                <div>
                    <?php
                    do_meta_boxes(get_current_screen(), 'joomsportintab_team2', $post);
                    unset($wp_meta_boxes[get_post_type($post)]['joomsportintab_team2']);
                    ?>
                </div>    
            </div>
        </div>

        <?php
    }
        
        
    public static function js_meta_personal($post){

        $metadata = get_post_meta($post->ID,'_joomsport_team_personal',true);

        ?>
        <table>
            <tr>
                <td>
                    <?php echo __('First name', 'joomsport-sports-league-results-management');?>
                </td>
                <td>
                    <input type="text" name="personal[first_name]" value="<?php echo isset($metadata['first_name'])?esc_attr($metadata['first_name']):""?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo __('Last name', 'joomsport-sports-league-results-management');?>
                </td>
                <td>
                    <input type="text" name="personal[last_name]" value="<?php echo isset($metadata['last_name'])?esc_attr($metadata['last_name']):""?>" />
                </td>
            </tr>
        </table> 
        <?php
    }
    public static function js_meta_about($post){

        $metadata = get_post_meta($post->ID,'_joomsport_team_about',true);
        echo wp_editor($metadata, 'about',array("textarea_rows"=>3));


    }

    public static function js_meta_ef($post){

        $metadata = get_post_meta($post->ID,'_joomsport_team_ef',true);
        
        $efields = JoomSportHelperEF::getEFList('1', 0);

        if(count($efields)){
            echo '<div class="jsminwdhtd jstable">';
            foreach ($efields as $ef) {

                JoomSportHelperEF::getEFInput($ef, isset($metadata[$ef->id])?$metadata[$ef->id]:null);
                //var_dump($ef);
                ?>
                
                <div class="jstable-row">
                    <div class="jstable-cell"><?php echo $ef->name?></div>
                    <div class="jstable-cell">
                        <?php 
                        if($ef->field_type == '2'){
                            wp_editor(isset($metadata[$ef->id])?$metadata[$ef->id]:'', 'ef_'.$ef->id,array("textarea_rows"=>3));
                            echo '<input type="hidden" name="ef['.$ef->id.']" value="ef_'.$ef->id.'" />';
                            
                        }else{
                            echo $ef->edit;
                        }
                        ?>
                    </div>    
                        
                </div>    
                <?php
            }
            echo '</div>';
        }else{
            $link = get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-extrafields');
             printf( __( 'There are no extra fields assigned to this section. Create new one on %s Extra fields list %s', 'joomsport-sports-league-results-management' ), '<a href="'.$link.'">','</a>' );

        }

    }

    public static function js_meta_seasons($post){
        $seasons_chk = JoomSportHelperObjects::getParticipiantSeasons($post->ID);
        $arr_chk = array();
        for($intB=0; $intB < count($seasons_chk); $intB++){
            foreach ($seasons_chk as $key => $value) {
                for($intA = 0; $intA < count($value); $intA++){
                    $arr_chk[] = $value[$intA]->id;
                }    
            }
        }
        $posts_array = JoomSportHelperObjects::getSeasons(0,false);
        if(JoomSportUserRights::isAdmin()){
            if(count($posts_array)){
                echo '<select name="seasons[]" class="jswf-chosen-select" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" multiple>';
                foreach ($posts_array as $key => $value) {
                    for($intA = 0; $intA < count($value); $intA++){
                        $tm = $value[$intA];
                        $selected = '';
                        if(in_array($tm->id, $arr_chk)){
                            $selected = ' selected';
                        }
                        echo '<option value="'.$tm->id.'" '.$selected.'>'.$key .' '.$tm->name.'</option>';
                    }

                }
                echo '</select>';
            }
        }else{
            if(count($posts_array)){
                foreach ($posts_array as $key => $value) {
                    for($intA = 0; $intA < count($value); $intA++){
                        $tm = $value[$intA];
                        $selected = '';
                        if(in_array($tm->id, $arr_chk)){
                            echo $key .' '.$tm->name.'<br />';
                        }
                       
                    }

                }
            }
            $season_to_reg = JoomSportUserRights::canJoinSeasons($post->ID);
            if(count($season_to_reg)){
                echo '<select name="seasons[]" class="jswf-chosen-select" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" multiple>';
                foreach ($season_to_reg as $key => $value) {
                    for($intA = 0; $intA < count($value); $intA++){
                        $tm = $value[$intA];
                        $selected = '';
                        
                        echo '<option value="'.$tm->id.'">'.$key .' '.$tm->name.'</option>';
                    }

                }
                echo '</select>';
            }
        }
    }
    public static function js_meta_venue($post){
        $metadata = get_post_meta($post->ID,'_joomsport_team_venue',true);
        $venues = get_posts(array(
                    'post_type' => 'joomsport_venue',
                    'post_status'      => 'publish',
                    'posts_per_page'   => -1,
                    )
                );
        $lists = array();
        
        for($intA=0;$intA<count($venues);$intA++){
            $tmp = new stdClass();
            $tmp->id = $venues[$intA]->ID;
            $tmp->name = $venues[$intA]->post_title;
            $lists[] = $tmp;
        }
        if(count($lists)){
            echo JoomSportHelperSelectBox::Simple('venue_id', $lists,$metadata);
        }else{
            $link = get_admin_url(get_current_blog_id(), 'edit.php?post_type=joomsport_venue');
            printf( __( "There are no venues created yet. Create it in %s Venue menu %s.", 'joomsport-sports-league-results-management' ), '<a href="'.$link.'">','</a>' );

        }
    }
    public static function js_meta_players($post){
        ?>
            <div id="js_team_playersDIV">
                <?php echo __('No season selected','joomsport-sports-league-results-management');?>
            </div>
        <?php
    }
    public static function js_meta_bonuses($post){
        ?>
            <div id="js_team_bonusesDIV">
                <?php echo __('No season selected','joomsport-sports-league-results-management');?>
            </div>
        <?php
    }
    public static function js_meta_ef_assigned($post){
        ?>
            <div id="js_team_efassignedDIV">
                <?php echo __('No season selected','joomsport-sports-league-results-management');?>
            </div>
        <?php
    }

    public static function joomsport_team_save_metabox($post_id, $post){
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['joomsport_team_nonce'] ) ? $_POST['joomsport_team_nonce'] : '';
        $nonce_action = 'joomsport_team_savemetaboxes';
 
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        
        if('joomsport_team' == $_POST['post_type'] ){
            self::saveMetaPersonal($post_id);
            self::saveMetaAbout($post_id);

            self::saveMetaEF($post_id);

            self::saveMetaPlayers($post_id);
            self::saveMetaBonuses($post_id);
            self::saveMetaEFAssigned($post_id);
            self::saveMetaVenue($post_id);
            self::saveMetaSeasons($post_id);
        }
    }
    
    private static function saveMetaPersonal($post_id){
        $meta_array = array();
        $meta_array = isset($_POST['personal'])?  ($_POST['personal']):'';
        if($meta_array){
        $meta_array = array_map( 'sanitize_text_field', wp_unslash( $_POST['personal'] ) );
        }
        update_post_meta($post_id, '_joomsport_team_personal', $meta_array);
    }
    private static function saveMetaAbout($post_id){
        $meta_data = isset($_POST['about'])?  ($_POST['about']):'';
        update_post_meta($post_id, '_joomsport_team_about', $meta_data);
    }

    private static function saveMetaEF($post_id){
        $meta_array = array();
        if(isset($_POST['ef']) && count($_POST['ef'])){
            foreach ($_POST['ef'] as $key => $value){
                if(isset($_POST['ef_'.$key])){
                    $meta_array[$key] = sanitize_text_field($_POST['ef_'.$key]);
                }else{
                    $meta_array[$key] = sanitize_text_field($value);
                }
            }
        }
        //$meta_data = serialize($meta_array);
        update_post_meta($post_id, '_joomsport_team_ef', $meta_array);
    }

    private static function saveMetaPlayers($post_id){
        $season_id = isset($_POST['stb_season_id'])?  intval($_POST['stb_season_id']):0;
        if($season_id){
            $players_id = isset($_POST['players_id'])?  ($_POST['players_id']):array();
            $players_id = array_map( 'sanitize_text_field', $players_id ) ;
            update_post_meta($post_id, '_joomsport_team_players_'.$season_id, $players_id);
        }
    }
    private static function saveMetaBonuses($post_id){
        $season_id = isset($_POST['stb_season_id'])?  intval($_POST['stb_season_id']):0;
        if($season_id){
            $bonuses = isset($_POST['js_bonuses'])?  floatval($_POST['js_bonuses']):'0';
            $old_bonuses = get_post_meta($post_id, '_joomsport_team_bonuses_'.$season_id, true);
            update_post_meta($post_id, '_joomsport_team_bonuses_'.$season_id, $bonuses);
            if($bonuses != $old_bonuses){
                do_action('joomsport_update_standings',$season_id);
            }
        }
    }
    private static function saveMetaEFAssigned($post_id){
        $season_id = isset($_POST['stb_season_id'])?  intval($_POST['stb_season_id']):0;
        $meta_array = array();
        if($season_id){
            if(isset($_POST['efs']) && count($_POST['efs'])){
                foreach ($_POST['efs'] as $key => $value){
                    if(isset($_POST['efs_'.$key])){
                        $meta_array[$key] = sanitize_text_field($_POST['efs_'.$key]);
                    }else{
                        $meta_array[$key] = $value;
                    }
                }
            }
            //$meta_data = serialize($meta_array);
            update_post_meta($post_id, '_joomsport_team_ef_'.$season_id, $meta_array);
            do_action('joomsport_update_playerlist', $season_id);
        }
        
        
    }
    private static function saveMetaVenue($post_id){
        $venue_id = isset($_POST['venue_id'])?  intval($_POST['venue_id']):0;
        update_post_meta($post_id, '_joomsport_team_venue', $venue_id);
    }
    
    private static function saveMetaSeasons($post_id){
        $seasons = isset($_POST['seasons'])?  ($_POST['seasons']):0;
        if(JoomSportUserRights::isAdmin()){
            
            $metadata = get_post_meta($post_id,'_joomsport_season_participiants',true);
            $teamsin = JoomSportHelperObjects::getParticipiantSeasons($post_id);


            for($intB=0; $intB < count($teamsin); $intB++){
                foreach ($teamsin as $key => $value) {
                    for($intA = 0; $intA < count($value); $intA++){
                        $metadata = get_post_meta($value[$intA]->id,'_joomsport_season_participiants',true);
                        if(!$seasons || !in_array($value[$intA]->id, $seasons)){
                            $metadata = array_diff($metadata, array($post_id));
                            update_post_meta($value[$intA]->id, '_joomsport_season_participiants', $metadata);
                            do_action('joomsport_update_standings',$value[$intA]->id);
                            do_action('joomsport_update_playerlist',$value[$intA]->id);
                        }
                    }    
                }
            }

            if($seasons && count($seasons)){
                foreach ($seasons as $seasonID) {
                    $seasonID = intval($seasonID);
                    $metadata = get_post_meta($seasonID,'_joomsport_season_participiants',true);
                    if(!$metadata ||  ($metadata && !in_array($post_id, $metadata))){
                        $metadata[] = $post_id;
                        update_post_meta($seasonID, '_joomsport_season_participiants', $metadata);
                        do_action('joomsport_update_standings',$seasonID);
                        do_action('joomsport_update_playerlist',$seasonID);
                    }



                }
            }
        }else{
            if($seasons && count($seasons)){
                foreach ($seasons as $seasonID) {
                    $seasonID = intval($seasonID);
                    $metadata = get_post_meta($seasonID,'_joomsport_season_participiants',true);
                    if(!$metadata ||  ($metadata && !in_array($post_id, $metadata))){
                        $metadata[] = $post_id;
                        update_post_meta($seasonID, '_joomsport_season_participiants', $metadata);
                        do_action('joomsport_update_standings',$seasonID);
                        do_action('joomsport_update_playerlist',$seasonID);
                    }

                }
            }
        }

    }
}