<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */

class JoomSportClassMatchdayRound{
    private $_mdID = null;
    private $_seasonID = null;
    public function __construct($mdID) {
        $this->_mdID = $mdID;
        $metas = get_option("taxonomy_{$mdID}_metas");
        $this->_seasonID = $metas['season_id'];
    }
    public function getViewEdit(){
        global $wpdb;
        $jsconfig =  new JoomsportSettings();
        $canAddMatches = JoomSportUserRights::canAddMatches();
        add_filter('posts_orderby','joomsport_ordermatchbydatetime');
        
        $metaquery =  array(
                array(
                    'key'     => '_joomsport_match_date',
                ),
                array(
                    'key'     => '_joomsport_match_time',
                    
                )
            ) ;
        
        if(!current_user_can('manage_options')){
            $teamsToModer = JoomSportUserRights::getTeamsArray();
            $metaquery[] = 
                array(
                    'relation' => 'OR',
                        array(
                    'key' => '_joomsport_home_team',
                    'value' => $teamsToModer,
                    'compare' => 'IN'
                    ),

                    array(
                    'key' => '_joomsport_away_team',
                    'value' => $teamsToModer,
                        'compare' => 'IN'
                    ) 
                ) ;
        }
        
        $matches = new WP_Query(array(
            'post_type' => 'joomsport_match',
            'posts_per_page'   => -1,
            'orderby' => 'id',
            'order'=>'ASC',
            'tax_query' => array(
                array(
                'taxonomy' => 'joomsport_matchday',
                'field' => 'term_id',
                'terms' => $this->_mdID)
            ),
            'meta_query' => $metaquery    
        ));
        remove_filter('posts_orderby','joomsport_ordermatchbydatetime');
        ob_start();
        $participiants = JoomSportHelperObjects::getParticipiants($this->_seasonID);
        $groups = $wpdb->get_results("SELECT id, group_name as name FROM {$wpdb->joomsport_groups} WHERE s_id = {$this->_seasonID} ORDER BY ordering"); 
        $season_options = get_post_meta($this->_seasonID,'_joomsport_season_point',true);
        
        $is_field_extra = array();
        $is_field_extra[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field_extra[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $enabla_extra = (isset($season_options['s_enbl_extra']) && $season_options['s_enbl_extra']) ? 1:0;
        
        $mstatuses = $wpdb->get_results('SELECT id,stName as name FROM '.$wpdb->joomsport_match_statuses.' ORDER BY ordering');
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Fixtures", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Played", "joomsport-sports-league-results-management"));
        if(count($mstatuses)){
            $is_field = array_merge($is_field,$mstatuses);
        }
        $venues = get_posts(array(
            'post_type' => 'joomsport_venue',
            'post_status'      => 'publish',
            'posts_per_page'   => -1,
            'orderby' => 'title',
            'order'=> 'ASC',
            )
        );
        $lists = array();

        for($intA=0;$intA<count($venues);$intA++){
            $tmp = new stdClass();
            $tmp->id = $venues[$intA]->ID;
            $tmp->name = $venues[$intA]->post_title;
            $lists[] = $tmp;
        }
        ?>

        <div class="jsOverXdiv">
            <table class="mglTable" id="mglMatchDay">
                <thead>
                    <tr>
                        <th style="width:30px;">
                            #
                        </th>
                        <?php
                        if(count($groups)){
                            echo '<th>';
                            echo __("Group","joomsport-sports-league-results-management");
                            echo '</th>';
                        }
                        ?>
                        <th>
                            <?php echo __('Home', 'joomsport-sports-league-results-management');?>
                        </th>
                        <th>
                            <?php echo __('Score', 'joomsport-sports-league-results-management');?>
                        </th>
                        <th>
                            <?php echo __('Away', 'joomsport-sports-league-results-management');?>
                        </th>
                        <?php
                        if($enabla_extra && $jsconfig->get('mdf_et')){
                           echo '<th>'.__('ET', 'joomsport-sports-league-results-management').'</th>'; 
                        }
                        ?>
                        <?php
                        if($jsconfig->get('mdf_played',1)){
                           echo '<th>'.__('Status', 'joomsport-sports-league-results-management').'</th>'; 
                        }
                        ?>
                        <?php
                        if($jsconfig->get('mdf_date',1)){
                           echo '<th>'.__('Date', 'joomsport-sports-league-results-management').'</th>'; 
                        }
                        ?>
                        <?php
                        if($jsconfig->get('mdf_time',1)){
                           echo '<th>'.__('Time', 'joomsport-sports-league-results-management').'</th>'; 
                        }
                        ?>
                        <?php
                        
                        if($jsconfig->get('mdf_venue')){
                           echo '<th>'.__('Venue', 'joomsport-sports-league-results-management').'</th>'; 
                        }
                        ?>
                        <?php
                        
                        $efields = JoomSportHelperEF::getEFList('2', 0);
                        for($intA=0; $intA < count($efields); $intA ++){
                            $ef = $efields[$intA];
                            if($jsconfig->get('extra_'.$ef->id)){
                                echo '<th>'.$ef->name.'</th>'; 
                            }
                        }
                        
                        ?>
                        <th>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    for($intA = 0; $intA < count($matches->posts); $intA ++){
                        //var_dump($matches->posts[$intA]);
                        //continue;
                        $match = $matches->posts[$intA];
                        $home_team = get_post_meta( $match->ID, '_joomsport_home_team', true );
                        $away_team = get_post_meta( $match->ID, '_joomsport_away_team', true );
                        $home_score = get_post_meta( $match->ID, '_joomsport_home_score', true );
                        $away_score = get_post_meta( $match->ID, '_joomsport_away_score', true );
                        $m_played = get_post_meta( $match->ID, '_joomsport_match_played', true );
                        $m_date = get_post_meta( $match->ID, '_joomsport_match_date', true );
                        $m_time = get_post_meta( $match->ID, '_joomsport_match_time', true );
                        $venue_id = (int) get_post_meta( $match->ID, '_joomsport_match_venue', true );
                        $group_id = (int) get_post_meta( $match->ID, '_joomsport_groupID', true );
                        
                        
                        $jmscore = get_post_meta($match->ID, '_joomsport_match_jmscore',true);
                        $metadataEF = get_post_meta($match->ID,'_joomsport_match_ef',true);

                        ?>
                        <tr>
                            <td>
                                <?php
                                if(current_user_can('delete_jscp_match', $match->ID)){
                                ?>
                                <a href="javascript:void(0);" onclick="javascript:(this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode));"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                <?php
                                }
                                ?>
                                <input type="hidden" name="match_id[]" value="<?php echo $match->ID;?>">
                            </td>
                            <?php
                            if(count($groups)){
                                echo '<td>';
                                echo JoomSportHelperSelectBox::Simple('group_id[]', $groups,$group_id,'',true);
                                echo '</td>';
                            }
                            ?>
                            <td><?php echo get_the_title($home_team);?><input type="hidden" name="home_team[]" value="<?php echo $home_team;?>"></td>
                            <td nowrap="nowrap"><input type="number" name="home_score[]" class="mglScore jsNumberNotNegative" value="<?php echo $home_score;?>">:<input type="number" name="away_score[]" class="mglScore jsNumberNotNegative" value="<?php echo $away_score;?>"></td>

                            <td><?php echo get_the_title($away_team);?><input type="hidden" name="away_team[]" value="<?php echo $away_team;?>"></td>
                            
                            <?php
                            if ($enabla_extra && $jsconfig->get('mdf_et')) {
                                echo '<td class="col-extra-time" nowrap="nowrap">';
                                echo JoomSportHelperSelectBox::Simple('extra_time[]', $is_field_extra,isset($jmscore['is_extra'])?$jmscore['is_extra']:'','',false);
                                echo '</td>';
                            }
                            if ($jsconfig->get('mdf_played',1)) {
                                echo '<td>';
                                echo JoomSportHelperSelectBox::Simple('m_played[]', $is_field,$m_played,'',false);
                                echo '</td>';
                            }
                            if ($jsconfig->get('mdf_date',1)) {
                                echo '<td>';
                                echo '<input type="text" class="jsdatefield" name="m_date[]" maxlength="10" size="12" value="'.$m_date.'" />';

                                echo '</td>';
                            }
                            if ($jsconfig->get('mdf_time',1)) {
                                echo '<td><input type="time"  name="m_time[]" maxlength="5" size="12" value="'.$m_time.'" />';
                            }
                            
                            if ($jsconfig->get('mdf_venue')) {
                                echo '<td>'. JoomSportHelperSelectBox::Simple('venue_id[]', $lists,$venue_id).'</td>';
                            }

                            for($intE=0; $intE < count($efields); $intE ++){
                                $ef = $efields[$intE];
                                if($jsconfig->get('extra_'.$ef->id)){
                                    
                                    JoomSportHelperEF::getEFInput($ef, isset($metadataEF[$ef->id])?$metadataEF[$ef->id]:null,'ef_'.$ef->id,true);

                                    if($ef->field_type == '2'){
                                    }else{
                                        echo '<td>'. $ef->edit.'</td>';
                                    }
                                }
                            }
                            
                            ?>
                            
                            <td>
                                <?php if(JoomSportUserRights::isAdmin() || $jsconfig->get('moder_edit_matches_reg', 0)){?>
                                <a href="post.php?post=<?php echo $match->ID;?>&action=edit"><input type="button" class="button" value="<?php echo __('Details', 'joomsport-sports-league-results-management');?>"></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php    
                    }
                    ?>
                </tbody>
                <?php 
                if(current_user_can('edit_jscp_matchs') && $canAddMatches){
                ?>
                <tfoot>
                    <tr>
                        <td>

                        </td>
                        <?php
                        if(count($groups)){
                            echo '<td>';
                            echo JoomSportHelperSelectBox::Simple('js_groupid_add', $groups,0,' id="js_groupid_add"',true);
                            echo '</td>';
                        }
                        ?>
                        <td>
                            <select name="set_home_team"  id="set_home_team">
                                <option value="0"><?php echo __('Select participiant', 'joomsport-sports-league-results-management');?></option>
                                <?php
                                if(count($participiants)){
                                    foreach ($participiants as $part) {
                                        echo '<option value="'.$part->ID.'">'.$part->post_title.'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td nowrap="nowrap">
                            <input type="number" name="set_score_home" id="set_score_home" class="mglScore jsNumberNotNegative" />:<input type="number" name="set_score_away" id="set_score_away" class="mglScore jsNumberNotNegative" />
                        </td>

                        <td>
                            <select name="set_away_team" id="set_away_team">
                                <option value="0"><?php echo __('Select Participant', 'joomsport-sports-league-results-management');?></option>
                                <?php
                                if(count($participiants)){
                                    foreach ($participiants as $part) {
                                        echo '<option value="'.$part->ID.'">'.$part->post_title.'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        
                        <?php
                        if ($enabla_extra && $jsconfig->get('mdf_et')) {
                            echo '<td class="col-extra-time" nowrap="nowrap">';
                            echo JoomSportHelperSelectBox::Simple('extra_timez', $is_field_extra,0,' id="extra_timez"',false);
                            echo '</td>';
                        }
                        if ($jsconfig->get('mdf_played',1)) {
                            echo '<td>';
                            echo JoomSportHelperSelectBox::Simple('m_played_foot', $is_field,0,' id="m_played_foot"',false);
                            echo '</td>';
                        }
                        if ($jsconfig->get('mdf_date',1)) {
                            echo '<td>';
                            echo '<input type="text" placeholder="YY-mm-dd" size="12" class="jsdatefield" name="m_date_foot" id="m_date_foot" value="" />';
                            echo '</td>';
                        }
                        
                        if ($jsconfig->get('mdf_time',1)) {
                            echo '<td><input type="time" placeholder="H:i" name="m_time_foot" size="12" id="m_time_foot" value="" /></td>';
                        }
                        
                        if ($jsconfig->get('mdf_venue')) {
                            echo '<td>';
                            echo JoomSportHelperSelectBox::Simple('venue_id_foot', $lists,0, ' id="venue_id_foot"');
                
                            echo '</td>';
                        }
                        
                        for($intA=0; $intA < count($efields); $intA ++){
                            $ef = $efields[$intA];
                            if($jsconfig->get('extra_'.$ef->id)){
                                echo '<td>';
                                JoomSportHelperEF::getEFInput($ef, null,'ef_foot');

                                if($ef->field_type == '2'){
                                }else{
                                    echo $ef->edit;
                                }
                                echo '<input type="hidden" name="jscef[]" value="'.$ef->id.'" />';
                                echo '</td>'; 
                            }
                        }
                        
                        ?>
                        
                        <td>
                            <input type="button" class="button mgl-add-button" value="<?php echo __("Add New", 'joomsport-sports-league-results-management');?>" />
                        </td>
                    </tr>
                </tfoot>
                <?php
                }
                ?>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }
    public function save(){
        $fdata = $_POST;
        
        $matches = array();
        $metaquery =  array(
                
            ) ;
        
        if(!current_user_can('manage_options')){
            $teamsToModer = JoomSportUserRights::getTeamsArray();
            $metaquery = 
                array(
                    'relation' => 'OR',
                        array(
                    'key' => '_joomsport_home_team',
                    'value' => $teamsToModer,
                    'compare' => 'IN'
                    ),

                    array(
                    'key' => '_joomsport_away_team',
                    'value' => $teamsToModer,
                        'compare' => 'IN'
                    ) 
                ) ;
        }
        $matches_old = get_posts(array(
            'post_type' => 'joomsport_match',
            'posts_per_page' => -1,
            'offset'           => 0,
            'tax_query' => array(
                array(
                'taxonomy' => 'joomsport_matchday',
                'field' => 'term_id',
                'terms' => $this->_mdID)
            ),
            'meta_query' => $metaquery)
        );

        if(isset($fdata['match_id']) && count($fdata['match_id'])){
            for($intA = 0; $intA < count($fdata['match_id']); $intA++){
                $home_team = get_the_title(intval($fdata['home_team'][$intA]));
                $away_team = get_the_title(intval($fdata['away_team'][$intA]));
                $score = intval($fdata['home_score'][$intA]) .' : '. intval($fdata['away_score'][$intA]);
                $title = $home_team.' vs '.$away_team;
                $arr = array(
                        'post_type' => 'joomsport_match',
                        'post_title' => wp_strip_all_tags( $title ),
                        'post_content' => '',
                        'post_status' => 'publish',
                        'post_author' => get_current_user_id()
                );
                if(intval($fdata['match_id'][$intA])){
                    $arr['ID'] = intval($fdata['match_id'][$intA]);
                }
                $groupID = 0;
                if(isset($fdata['group_id'][$intA])){
                    $groupID = intval($fdata['group_id'][$intA]);
                }
                $post_id = wp_insert_post( $arr );
                if($post_id){
                    update_post_meta($post_id, '_joomsport_home_team', intval($fdata['home_team'][$intA]));
                    update_post_meta($post_id, '_joomsport_away_team', intval($fdata['away_team'][$intA]));
                    update_post_meta($post_id, '_joomsport_home_score', intval($fdata['home_score'][$intA]));
                    update_post_meta($post_id, '_joomsport_away_score', intval($fdata['away_score'][$intA]));
                    update_post_meta($post_id, '_joomsport_groupID', $groupID);
                    update_post_meta($post_id, '_joomsport_seasonid', $this->_seasonID);
                    
                    if(isset($fdata['m_played'][$intA])){
                        update_post_meta($post_id, '_joomsport_match_played', intval($fdata['m_played'][$intA])); 
                    }
                    if(isset($fdata['m_date'][$intA])){
                        update_post_meta($post_id, '_joomsport_match_date', ($fdata['m_date'][$intA])); 
                    }
                    if(isset($fdata['m_time'][$intA])){
                        update_post_meta($post_id, '_joomsport_match_time', ($fdata['m_time'][$intA])); 
                    }
                    if(isset($fdata['venue_id'][$intA])){
                        update_post_meta($post_id, '_joomsport_match_venue', intval($fdata['venue_id'][$intA])); 
                    }
                    
                    //ef && extra
                    
                    if(isset($fdata['extra_time'][$intA])){
                        $jmscore = get_post_meta($post_id, '_joomsport_match_jmscore',true);
                        $jmscore['is_extra'] = $fdata['extra_time'][$intA];
                        update_post_meta($post_id, '_joomsport_match_jmscore', $jmscore); 
                    }
                    if(isset($fdata['jscef']) && count($fdata['jscef'])){
                        $metadata = get_post_meta($post_id,'_joomsport_match_ef',true);
                        foreach ($fdata['jscef'] as $efid) {
                            if(isset($fdata['ef_'.$efid][$intA])){
                                $metadata[$efid] = $fdata['ef_'.$efid][$intA];
                            }
                        }
                        update_post_meta($post_id, '_joomsport_match_ef', $metadata);
                    }

                }
                $matches[] = $post_id;
                wp_set_post_terms( $post_id, array((int) $this->_mdID), 'joomsport_matchday');
            }
        }
        for($intA=0; $intA < count($matches_old); $intA++){
            $match_id = $matches_old[$intA]->ID;
            if(!in_array($match_id, $matches)){
                wp_delete_post($match_id);
            }
        }
        
    }
    
    public function SaveMatch()
    {
        $form_data = isset($_POST['formdata'])?parse_str($_POST['formdata']):'';

        if(!JoomSportUserRights::canAddMatch($this->_seasonID, $set_home_team, $set_away_team)){
            $msg['error'] = __( 'Please select your team', 'joomsport-sports-league-results-management' ); 
            echo json_encode($msg);
            die();
        }
                $home_team = get_the_title(intval($set_home_team));
                $away_team = get_the_title(intval($set_away_team));
                
                $title = $home_team.' vs '.$away_team;
                $arr = array(
                        'post_type' => 'joomsport_match',
                        'post_title' => wp_strip_all_tags( $title ),
                        'post_content' => '',
                        'post_status' => 'publish',
                        'post_author' => get_current_user_id()
                );
                
                $groupID = 0;
                if(isset($js_groupid_add)){
                    $groupID = intval($js_groupid_add);
                }
                $post_id = wp_insert_post( $arr );
                if($post_id){
                    update_post_meta($post_id, '_joomsport_home_team', intval($set_home_team));
                    update_post_meta($post_id, '_joomsport_away_team', intval($set_away_team));
                    update_post_meta($post_id, '_joomsport_home_score', intval($set_score_home));
                    update_post_meta($post_id, '_joomsport_away_score', intval($set_score_away));
                    update_post_meta($post_id, '_joomsport_groupID', $groupID);
                    update_post_meta($post_id, '_joomsport_seasonid', $this->_seasonID);
                    
                    if(isset($m_played_foot)){
                        update_post_meta($post_id, '_joomsport_match_played', intval($m_played_foot)); 
                    }
                    if(isset($m_date_foot)){
                        update_post_meta($post_id, '_joomsport_match_date', sanitize_text_field($m_date_foot)); 
                    }
                    if(isset($m_time_foot)){
                        update_post_meta($post_id, '_joomsport_match_time', sanitize_text_field($m_time_foot)); 
                    }
                    
                    $m_date = get_post_meta( $post_id, '_joomsport_match_date', true );
                    $m_time = get_post_meta( $post_id, '_joomsport_match_time', true );
                    if(!$m_date){
                        update_post_meta($post_id, '_joomsport_match_date', ''); 
                    }
                    if(!$m_time){
                        update_post_meta($post_id, '_joomsport_match_time', ''); 
                    }
                    
                    
                    if(isset($venue_id_foot)){
                        update_post_meta($post_id, '_joomsport_match_venue', intval($venue_id_foot)); 
                    }
                    
                    //ef && extra
                    
                    if(isset($extra_timez)){
                        $jmscore = get_post_meta($post_id, '_joomsport_match_jmscore',true);
                        $jmscore['is_extra'] = $extra_timez;
                        update_post_meta($post_id, '_joomsport_match_jmscore', $jmscore); 
                    }
                    
                    $efields = JoomSportHelperEF::getEFList('2', 0);
                    $metadata = get_post_meta($post_id,'_joomsport_match_ef',true);
                    if(isset($ef_foot) && count($ef_foot)){
                        foreach ($ef_foot as $key => $value) {
                            $metadata[$key] = sanitize_text_field($value);
                        }
                    }
                    
                    update_post_meta($post_id, '_joomsport_match_ef', $metadata);    
                    

                    
                }

                wp_set_post_terms( $post_id, array((int)$this->_mdID), 'joomsport_matchday');

        echo intval($post_id);
    }

}
function joomsport_ordermatchbydatetime($orderby) {
        global $wpdb;
        return str_replace($wpdb->prefix.'posts.post_date',$wpdb->prefix.'postmeta.meta_value,  mt1.meta_value, '.$wpdb->prefix.'posts.post_date', $orderby);
}