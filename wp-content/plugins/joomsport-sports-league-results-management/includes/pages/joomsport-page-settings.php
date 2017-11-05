<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomsportPageSettings{
    public static function action(){
        global $wpdb;
        
        if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            //general settings
            $general = $_POST['general'];
            $general = array_map( 'sanitize_text_field', wp_unslash( $_POST['general'] ) );
            if(isset($_POST['mdf']) && count($_POST['mdf'])){
                $mdf = array_map( 'sanitize_text_field', wp_unslash( $_POST['mdf'] ) );
                $general = array_merge($general,$mdf);
            }
            if(isset($_POST['yteams']) && count($_POST['yteams'])){
                $yteams = array();
                $yteams = array_map( 'sanitize_text_field', wp_unslash( $_POST['yteams'] ) );
                $yteams['yteams'] = $yteams;
                $general = array_merge($general,$yteams);
            }
            $mstatuses = array();
            if (isset($_POST['mstatusesId']) && count($_POST['mstatusesId'])) {
                for ($intA = 0; $intA < count($_POST['mstatusesId']); ++$intA) {
                    if ($_POST['mstatusesId'][$intA] == 0 && $_POST['mstatusesName'][$intA] && $_POST['mstatusesShortName'][$intA]) {
                        $wpdb->insert($wpdb->joomsport_match_statuses,array('stName' => addslashes(sanitize_text_field($_POST['mstatusesName'][$intA])),'stShort' => addslashes(sanitize_text_field($_POST['mstatusesShortName'][$intA])),'ordering' => $intA),array('%s','%s','%d'));
                        $id = $wpdb->insert_id;
                    } elseif ($_POST['mstatusesId'][$intA]) {
                        $wpdb->update($wpdb->joomsport_match_statuses,array('stName' => addslashes(sanitize_text_field($_POST['mstatusesName'][$intA])),'stShort' => addslashes(sanitize_text_field($_POST['mstatusesShortName'][$intA])),'ordering' => $intA),array("id" => intval($_POST['mstatusesId'][$intA])),array('%s','%s','%d'),array('%d'));
                        $id = intval($_POST['mstatusesId'][$intA]);
                    }
                    $mstatuses[] = $id;
                }
            }
            if (count($mstatuses)) {
                $wpdb->query('DELETE FROM '.$wpdb->joomsport_match_statuses.' WHERE id NOT IN ('.implode(',', $mstatuses).')');
            }
            
            $options = json_encode($general);
            
            $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'general'), array('%s'), array('%s'));
            
            /*
            //team moderator settings
            $teammoder = $_POST['teammoder'];
            if (isset($_POST['adf_tm']) && count($_POST['adf_tm'])) {
                foreach ($_POST['adf_tm'] as $map) {
                    $wpdb->update($wpdb->joomsport_ef, array('reg_exist' =>((isset($_POST['adf_reg_'.$map]) && $_POST['adf_reg_'.$map] == 1) ? 1 : 0),'reg_require'=>((isset($_POST['adf_rq_'.$map]) && $_POST['adf_rq_'.$map] == 1) ? 1 : 0)),array("id"=>$map), array('%s','%s'),array('%d'));
                }
            }
            $options = json_encode($teammoder);
            $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'team_moder'), array('%s'), array('%s'));
            
            //season admin settings
            $seasonadmin = $_POST['seasonadmin'];
            $options = json_encode($seasonadmin);
            $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'season_admin'), array('%s'), array('%s'));
            */
            //layouts settings
            $layouts = $_POST['layouts'];
            if($layouts['columnshort']){
                $layouts['columnshort'] = json_encode($layouts['columnshort']);
            }
            if(isset($layouts['jsblock_career_options']) && $layouts['jsblock_career_options']){
                $layouts['jsblock_career_options'] = json_encode($layouts['jsblock_career_options']);
            }
            $layouts = array_map( 'sanitize_text_field', ( $layouts ) );
            $options = json_encode($layouts);
            $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'layouts'), array('%s'), array('%s'));
            
            //other settings
            if(isset($_POST['other_settings']) && count($_POST['other_settings'])){
                $other_settings = $_POST['other_settings'];
                $other_settings = array_map( 'sanitize_text_field', wp_unslash( $_POST['other_settings'] ) );
                $options = json_encode($other_settings);
                $wpdb->update($wpdb->joomsport_config, array('cValue' => $options), array('cName' => 'other'), array('%s'), array('%s'));
            }
            
        }
        
        $lists = array();
        $is_field_yn = array();
        $is_field_yn[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field_yn[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        
        $is_field_tourntype = array();
        $is_field_tourntype[] = JoomSportHelperSelectBox::addOption(0, __("Team", "joomsport-sports-league-results-management"));
        $is_field_tourntype[] = JoomSportHelperSelectBox::addOption(1, __("Single", "joomsport-sports-league-results-management"));
        
        $is_field_date = array();
        $is_field_date[] = JoomSportHelperSelectBox::addOption("d-m-Y H:M", "d-m-Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("d.m.Y H:M", "d.m.Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("Y.m.d H:M", "Y.m.d H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("m-d-Y I:M p", "m-d-Y I:M p");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("m B, Y H:M", "m B, Y H:M");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("m B, Y I:H p", "m B, Y I:H p");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("d-m-Y", "d-m-Y");
        $is_field_date[] = JoomSportHelperSelectBox::addOption("A d B, Y H:M","A d B, Y H:M");
        
        $query = "SELECT ef.*
		            FROM ".$wpdb->joomsport_ef." as ef
		            
		            WHERE ef.published=1 AND ef.type='2'
		            ORDER BY ef.ordering";

        $lists['mday_extra'] = $wpdb->get_results($query);
        
        
        $args = array(
                'offset'           => 0,
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'joomsport_team',
                'post_status'      => 'publish',
                'posts_per_page'   => -1,
        );
        $teamlist = get_posts( $args );
        
        $query = "SELECT * FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='0' AND season_related='0' AND published='1'"
                . " ORDER BY ordering";
        $lists['adf_player'] = $wpdb->get_results($query);
        $query = "SELECT * FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='0' AND field_type IN('0','3') AND published='1'"
                . " ORDER BY ordering";
        $lists['adf_player_squad'] = $wpdb->get_results($query);
        $query = "SELECT * FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='1' AND season_related='0' "
                . " ORDER BY ordering";
        $lists['adf_team'] = $wpdb->get_results($query);
        
        $is_field_inv = array();
        $is_field_inv[] = JoomSportHelperSelectBox::addOption(0, __("Moderator adds player to team", "joomsport-sports-league-results-management"));
        $is_field_inv[] = JoomSportHelperSelectBox::addOption(1, __("Moderator invites player to team", "joomsport-sports-league-results-management"));
        
        $query = "SELECT name, CONCAT(id,'_1') as id"
                . " FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='0' AND (field_type = 0 OR field_type = 3)"
                . " ORDER BY name";
        $adf = $wpdb->get_results($query);
        $alltmp['op'] = JoomSportHelperSelectBox::addOption(0, __('Name','joomsport-sports-league-results-management'));

        if(count($adf)){
            $alltmp[__('Extra fields','joomsport-sports-league-results-management')] = $adf;
        }
        
        $query = "SELECT CONCAT(ev.id,'_2') as id,ev.e_name as name
		            FROM ".$wpdb->joomsport_events." as ev
                            WHERE ev.player_event IN (1, 2)
		            ORDER BY ev.e_name";

        $events_cd = $wpdb->get_results($query);
        if(count($events_cd)){
            $alltmp[__('Events','joomsport-sports-league-results-management')] = $events_cd;
        }
        
        $is_field_pltab = array();
        $is_field_pltab[] = JoomSportHelperSelectBox::addOption(0, __("Player statistics list", "joomsport-sports-league-results-management"));
        $is_field_pltab[] = JoomSportHelperSelectBox::addOption(1, __("Player photos", "joomsport-sports-league-results-management"));
        
        $query = "SELECT name, CONCAT(id,'_1') as id"
                . " FROM ".$wpdb->joomsport_ef."
		            WHERE type='0' AND field_type = 3
		            ORDER BY ordering";
        $adf_se = $wpdb->get_results($query);

        $alltmp_se['op'] = JoomSportHelperSelectBox::addOption(0, __('Name','joomsport-sports-league-results-management'));

        if(count($adf_se)){
            $alltmp_se[__('Extra fields','joomsport-sports-league-results-management')] = $adf_se;
        }
        
        $lists['mstatuses'] = $wpdb->get_results('SELECT * FROM '.$wpdb->joomsport_match_statuses.' ORDER BY ordering');
        
        
        $query = "SELECT name, id"
                . " FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='0' AND field_type = 3"
                . " ORDER BY name";
        $adfSel = $wpdb->get_results($query);
        
        $query = "SELECT name, id"
                . " FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='0' AND field_type = 0"
                . " ORDER BY name";
        $adfText = $wpdb->get_results($query);
        
        $query = "SELECT name, id"
                . " FROM ".$wpdb->joomsport_ef.""
                . " WHERE type='0'"
                . " ORDER BY name";
        $adfPlayer = $wpdb->get_results($query);


        $jsconfig =  new JoomsportSettings();
        $lists['available_options'] = $jsconfig->getStandingColumns();

        $events = $wpdb->get_results("SELECT CONCAT('ev_',id) as id,e_name as name FROM {$wpdb->joomsport_events} WHERE player_event != 0");
        
        $is_data_career = array();

        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_mplayed', __('Matches played','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_mlineup', __('Matches Line Up','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_minutes', __('Played minutes','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_subsin', __('Subs in','joomsport-sports-league-results-management'));
        $is_data_career[] = JoomSportHelperSelectBox::addOption('op_subsout', __('Subs out','joomsport-sports-league-results-management'));
        if(!empty($events)){
           $is_data_career = array_merge($is_data_career, $events);
        }

        
        wp_enqueue_script( 'joomsport-colorgrid-js', plugins_url('../../includes/3d/color_piker/201a.js', __FILE__) );
        require_once JOOMSPORT_PATH_HELPERS . 'tabs.php';
        $etabs = new esTabs();
        ?>
        <script type="text/javascript">
		
                
            function addMatchStatus(){
                if(jQuery("#custstat_name").val() && jQuery("#custstat_shortname").val()){
                    var tr = jQuery("<tr>");
                    tr.append('<td><input type="hidden" name="mstatusesId[]" value="0" /><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo __('Delete', 'joomsport-sports-league-results-management');?>"><i class="fa fa-trash" aria-hidden="true"></i></a></td>');
                    tr.append('<td><input type="text" name="mstatusesName[]" value="'+jQuery("#custstat_name").val()+'" /></td>');
                    tr.append('<td><input type="text" name="mstatusesShortName[]" value="'+jQuery("#custstat_shortname").val()+'" /></td>');
                    jQuery('#matchStatusesTable').append(tr);
                    jQuery("#custstat_name").val("");
                    jQuery("#custstat_shortname").val("");
                }
            }
            function Delete_tbl_row(element) {
                    var del_index = element.parentNode.parentNode.sectionRowIndex;
                    var tbl_id = element.parentNode.parentNode.parentNode.parentNode.id;
                    element.parentNode.parentNode.parentNode.deleteRow(del_index);
            }

        </script>
        <div class="jsSettingsPage">
            <div class="jsBEsettings" style="padding:0px;">
                <!-- <tab box> -->
                <ul class="tab-box">
                    <?php
                    echo $etabs->newTab(__('General','joomsport-sports-league-results-management'), 'main_conf', '', 'vis');
                    
                    echo $etabs->newTab(__('Moderator','joomsport-sports-league-results-management'), 'moder_conf', '');
                    
                    //echo $etabs->newTab("Team moderation", 'team_conf', '');
                    //echo $etabs->newTab("Season administration", 'season_conf', '');
                    echo $etabs->newTab(__('Layouts','joomsport-sports-league-results-management'), 'layout_conf', '');
                    ?>
                </ul>	
                <div style="clear:both"></div>
            </div>
            
        <div class="mgl-panel-wrap">
            <script type="text/javascript" id="UR_initiator"> (function () { var iid = 'uriid_'+(new Date().getTime())+'_'+Math.floor((Math.random()*100)+1); if (!document._fpu_) document.getElementById('UR_initiator').setAttribute('id', iid); var bsa = document.createElement('script'); bsa.type = 'text/javascript'; bsa.async = true; bsa.src = '//beardev.useresponse.com/sdk/supportCenter.js?initid='+iid+'&wid=6'; (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(bsa); })(); </script>
            <form method="post">
            <div  id="main_conf_div" class="tabdiv">
                <div class="jsrespdiv6">
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo __('General', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <table class="adminlistsNoBorder">
                            
                            <tr>
                                        <td>
                                            <?php echo __('League type', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('general[tournament_type]', $is_field_tourntype,$jsconfig->get('tournament_type',0),'',array('lclasses'=>array(1,1)));
                                            ?>

                                        </td>
                                </tr>
                                <tr>
                                        <td width="270">
                                            <?php echo __('Date format', 'joomsport-sports-league-results-management');?>

                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Simple('general[dateFormat]', $is_field_date,$jsconfig->get('dateFormat','d-m-Y H:M'),'',false);
        
                                            ?>


                                        </td>

                                </tr>

                                <tr>
                                        <td>
                                            <?php echo __('Enable match comments', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('general[comments]', $is_field_yn,$jsconfig->get('comments'),'');
                                            ?>

                                        </td>
                                </tr>
                                <?php
                                $stdoptions = '';
                                 $stdoptions = "std"; 
                                ?>

                                <tr>
                                        <td>
                                            <?php echo __('Enable Club', 'joomsport-sports-league-results-management');?>

                                        </td>
                                        <td>
                                            <?php 
                                            
                                            echo JoomSportHelperSelectBox::Radio('general[enbl_club]', $is_field_yn,$jsconfig->get('enbl_club',0));
                                            
                                            ?>

                                        </td>
                                </tr>



                                <tr>
                                        <td>
                                            <?php echo __('Enable Venue', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                            echo JoomSportHelperSelectBox::Radio('general[unbl_venue]', $is_field_yn,$jsconfig->get('unbl_venue',1));
                                            
                                            ?>

                                        </td>

                                </tr>



                                <tr>
                                        <td>
                                            <?php echo __('Enable JoomSport branding', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('general[jsbrand_on]', $is_field_yn,$jsconfig->get('jsbrand_on',1),'');
                                            ?>


                                        </td>
                                </tr>
                                <tr>
                                        <td width="270">
                                            <?php echo __('Group Box Score by', 'joomsport-sports-league-results-management');?>

                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Simple('general[boxExtraField]', $adfSel,$jsconfig->get('boxExtraField','0'),'',true);
        
                                            ?>


                                        </td>

                                </tr>
                                <tr>
                                        <td>
                                            <?php echo __('Hierarchical seasons', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('general[hierarchical_season]', $is_field_yn,$jsconfig->get('hierarchical_season', 0),'');
                                            ?>

                                        </td>
                                </tr>
                                
                        </table>
                    </div>
                </div>
                <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo __('Quick matchday creation', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="">
                                <tr>
                                    <th align="left">
                                        <?php echo __('Field', 'joomsport-sports-league-results-management');?>
                                    </th>
                                    <th>
                                        <?php echo __('Show on page', 'joomsport-sports-league-results-management');?>
                                    </th>
                                </tr>
                                <tr>
                                    <td width="280">
                                        <?php echo __('Extra Time', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo JoomSportHelperSelectBox::Radio('mdf[mdf_et]', $is_field_yn,$jsconfig->get('mdf_et'),'');
                                        ?>

                                    </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo __('Status', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('mdf[mdf_played]', $is_field_yn,$jsconfig->get('mdf_played',1),'');
                                            ?>
                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo __('Date', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('mdf[mdf_date]', $is_field_yn,$jsconfig->get('mdf_date',1),'');
                                            ?>
                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo __('Time', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('mdf[mdf_time]', $is_field_yn,$jsconfig->get('mdf_time',1),'');
                                            ?>
                                        </td>

                                </tr>
                                <?php
                                
                                ?>
                                <tr>
                                        <td width="200">
                                            <?php echo __('Venue', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('mdf[mdf_venue]', $is_field_yn,$jsconfig->get('mdf_venue'),'');
                                            ?>
                                        </td>

                                </tr>
                                <?php
                                
                                ?>
                                <?php
                                if(isset($lists['mday_extra']) && count($lists['mday_extra'])){
                                    foreach ($lists['mday_extra'] as $extra) {
                                        $extraname = 'extra_'.$extra->id;
                                        ?>
                                        <tr>
                                            <td width="200">
                                                    <?php echo $extra->name; ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo JoomSportHelperSelectBox::Radio('mdf[extra_'.$extra->id.']', $is_field_yn,$jsconfig->get('extra_'.$extra->id),'');
                                                ?>
                                                
                                            </td>

                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </table>    
                        </div>    
                    </div>
                    <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo '<a href="http://app.joomsport.com/?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro" target="_blank">Mobile Application</a> settings';?>
                    </div>
                    <div class="jsBEsettings">
                        <?php ?>
                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>';  ?>
                    </div>
                </div>   
            </div>
            <div class="jsrespdiv6 jsrespmarginleft2">
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo __('Team highlighting', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <table class="adminlistsNoBorder">
                            <tr>
                                <td width="30%">
                                    <?php echo __('Highlight selected teams in season standings', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td>
                                    <div class="controls">
                                        <fieldset class="radio btn-group">
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('general[highlight_team]', $is_field_yn,$jsconfig->get('highlight_team'),'');
                                            ?>
                                        </fieldset>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="jsEnblHGL">
                                    <?php echo __('Highlight color', 'joomsport-sports-league-results-management');?>
                                </td>
                                <td class="jsEnblHGL">
                                        <div id="colorpicker201" class="colorpicker201"></div>
                                        <input class="button" type="button" style="cursor:pointer;" onclick="showColorGrid2('yteam_color','sample_1');" value="...">&nbsp;<input type="text" name="general[yteam_color]" id="yteam_color" size="5" style="width:70px;margin-bottom: 0px;" maxlength="30" value="<?php echo $jsconfig->get('yteam_color','');?>" /><input type="text" id="sample_1" size="1" value="" style="margin-bottom: 0px;background-color:<?php echo $jsconfig->get('yteam_color','');?>" class="color-kind" />
                                </td>

                            </tr>
                            <tr>
                                <td class="jsEnblHGL">
                                    <?php echo __('Select teams', 'joomsport-sports-league-results-management');?>
                                </td>
                            
                            
                                <td class="jsEnblHGL">

                                    <?php
                                    if(count($teamlist)){
                                        echo '<select name="yteams[]" class="jswf-chosen-select" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" multiple>';
                                        foreach ($teamlist as $tm) {
                                            $selected = '';
                                            if(in_array($tm->ID, $jsconfig->get('yteams',array()))){
                                                $selected = ' selected';
                                            }
                                            echo '<option value="'.$tm->ID.'" '.$selected.'>'.$tm->post_title.'</option>';
                                        }
                                        echo '</select>';
                                    }
                                    ?>

                                </td>
                                            
                            </tr>
                        </table>
                        <script>
                            if('<?php echo isset($lists['highlight_team'])?$lists['highlight_team']:"";?>' != '1'){
                                //jQuery(".jsEnblHGL").hide();
                            }
                        </script>    
                    </div>
                </div>
                
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo __('Custom match statuses', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <?php ?>
                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>';  ?>
                    </div>
                </div>   
            </div>
            <div style="clear:both;" ></div>
        </div>
                
        <div  id="moder_conf_div" class="tabdiv visuallyhidden">
            <div class="jsrespdiv12">
                <div class="jsBepanel">
                    <div class="jsBEheader">
                        <?php echo __('Permissions', 'joomsport-sports-league-results-management');?>
                    </div>
                    <div class="jsBEsettings">
                        <?php  echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; ?>
                        <?php ?>  
                    </div>    
                </div>

                  

            </div>

        </div>
         
            <div id="season_conf_div" class="tabdiv visuallyhidden">
                <div class="jsrespdiv6">
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo __('Team tournament', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">

                                <table class="adminlistsNoBorder">

                                        <tr>
                                                <td><?php echo __('Can edit players', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[jssa_editplayer]', $is_field_yn,$jsconfig->get('jssa_editplayer'),'');
                                                ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo __('Can edit teams', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[cf_team_cjssa_editteamity_required]', $is_field_yn,$jsconfig->get('cf_team_cjssa_editteamity_required'),'');
                                                    ?>


                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo __('Can remove player from season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[jssa_deleteplayers]', $is_field_yn,$jsconfig->get('jssa_deleteplayers'),'');
                                                    ?>
                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo __('Can remove teams from season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[jssa_delteam]', $is_field_yn,$jsconfig->get('jssa_delteam'),'');
                                                    ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo __('Can add existing team to season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[jssa_addexteam]', $is_field_yn,$jsconfig->get('jssa_addexteam'),'');
                                                    ?>
                                                </td>
                                        </tr>
                                </table>

                            </div>    
                        </div>
                    </div>
                    <div class="jsrespdiv6 jsrespmarginleft2">
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo __('Single tournament', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">

                                <table class="adminlistsNoBorder">

                                        <tr>
                                                <td><?php echo __('Can add existing participant to season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php 
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[jssa_addexteam_single]', $is_field_yn,$jsconfig->get('jssa_addexteam_single'),'');
                                                    ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo __('Can edit participant', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[jssa_editplayer_single]', $is_field_yn,$jsconfig->get('jssa_editplayer_single'),'');
                                                    ?>

                                                </td>
                                        </tr>
                                        <tr>
                                                <td><?php echo __('Can remove participant from season', 'joomsport-sports-league-results-management');?></td>
                                                <td>
                                                    <?php
                                                    echo JoomSportHelperSelectBox::Radio('seasonadmin[jssa_deleteplayers_single]', $is_field_yn,$jsconfig->get('jssa_deleteplayers_single'),'');
                                                    ?>
                                                </td>
                                        </tr>
                                </table>

                            </div>
                        </div>
                    </div>
                <div style="clear:both;" ></div>
            </div>
                <div  id="layout_conf_div" class="tabdiv visuallyhidden" >
                <div class="jsrespdiv6">
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo __('Team page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            
                            <?php
                            $stdoptions = '';
                             $stdoptions = "std"; 
                            ?>
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250"><?php echo __('Order players by', 'joomsport-sports-league-results-management');?></td>
                                    <td>
                                        <?php echo JoomSportHelperSelectBox::Optgroup('layouts[pllist_order]', $alltmp,$jsconfig->get('pllist_order'));?>

                                    </td>
                                </tr>
                            </table>
                            <h4>
                                <?php echo __('Player Stats tab settings', 'joomsport-sports-league-results-management');?>
                            </h4>
                            <table class="adminlistsNoBorder">
                                
                                <tr>
                                    <td width="250">
                                        <?php echo __('Display Player Stats tab', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        
                                        <?php 
                                        echo JoomSportHelperSelectBox::Radio('layouts[show_playerstattab]', $is_field_yn,$jsconfig->get('show_playerstattab','1'),'');
                                        ?>
                                        
                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo __('Show empty players tab', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo JoomSportHelperSelectBox::Radio('layouts[show_playertab]', $is_field_yn,$jsconfig->get('show_playertab'),'');
                                        ?>

                                    </td>

                                </tr>
                                
                            </table>
                            <h4>
                                <?php echo __('Roster tab settings', 'joomsport-sports-league-results-management');?>
                            </h4>
                            <table class="adminlistsNoBorder">
                            
                                <tr>
                                    <td width="250">
                                        <?php echo __('Display Roster tab', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        
                                        <?php 
                                        echo JoomSportHelperSelectBox::Radio('layouts[show_rostertab]', $is_field_yn,$jsconfig->get('show_rostertab','1'),'');
                                        ?>
                                        
                                    </td>

                                </tr>
                                
                                <tr>
                                    <td width="250">
                                        <?php echo __('Group players by', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo JoomSportHelperSelectBox::Simple('layouts[set_teampgplayertab_groupby]', $adfSel,$jsconfig->get('set_teampgplayertab_groupby','0'),'',true);
                                        ?>
                                        
                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo __('Field for number', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo JoomSportHelperSelectBox::Simple('layouts[set_playerfieldnumber]', $adfText,$jsconfig->get('set_playerfieldnumber','0'),'',true);
                                        ?>
                                        
                                    </td>

                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo __('Extra card field', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo JoomSportHelperSelectBox::Simple('layouts[set_playercardef]', $adfPlayer,$jsconfig->get('set_playercardef','0'),'',true);
                                        ?>
                                        
                                    </td>

                                </tr>
                            </table>
                            <h4>
                                <?php echo __('Overview tab settings', 'joomsport-sports-league-results-management');?>
                            </h4>
                            <table class="adminlistsNoBorder">

                                <tr>
                                        <td width="250">
                                            <?php echo __('Display standings position', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td>
                                            <?php echo __('Display team form block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo __('Display match results block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td width="200">
                                            <?php echo __('Display next matches block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                            </table>
                        </div>    
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo __('Players page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <?php
                            $stdoptions = '';
                             $stdoptions = "std"; 
                            ?>
                            <table class="adminlistsNoBorder">

                                <tr>
                                        <td width="250">
                                            <?php echo __('Enable Career block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                <tr>
                                        <td width="250">
                                            <?php echo __('Career fields', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                

                                <tr>
                                        <td>
                                            <?php echo __('Enable match statistics block', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                            ?>

                                        </td>

                                </tr>
                                
                            </table>
                            

                        </div>    
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo __('Calendar page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="adminlistsNoBorder">

                                <tr>
                                        <td width="250">
                                            <?php echo __('Display venue', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                            echo JoomSportHelperSelectBox::Radio('layouts[cal_venue]', $is_field_yn,$jsconfig->get('cal_venue',1),'');
                                            
                                            ?>

                                        </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo __('Enable player list button', 'joomsport-sports-league-results-management');?>
                                    <td>
                                        <?php 
                                        echo JoomSportHelperSelectBox::Radio('layouts[enbl_linktoplayerlistcal]', $is_field_yn,$jsconfig->get('enbl_linktoplayerlistcal',1),'');
                                        ?>


                                    </td>
                                </tr>
                                <?php
                                $stdoptions = '';
                                 $stdoptions = "std"; 
                                ?>
                                <tr>
                                    <td>
                                        <?php echo __('Enable matches search', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        
                                        echo JoomSportHelperSelectBox::Radio('layouts[enbl_calmatchsearch]', $is_field_yn,$jsconfig->get('enbl_calmatchsearch',1));
                                        
                                        ?>


                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo __('Matchday name on calendar', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo JoomSportHelperSelectBox::Radio('layouts[enbl_mdnameoncalendar]', $is_field_yn,$jsconfig->get('enbl_mdnameoncalendar',1),'');
                                        ?>


                                    </td>
                                </tr>


                            </table>
                        </div>
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo __('Season standings page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250">
                                        <?php echo __('Enable player list button', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                        echo JoomSportHelperSelectBox::Radio('layouts[enbl_linktoplayerlist]', $is_field_yn,$jsconfig->get('enbl_linktoplayerlist',1),'');
                                        ?>


                                    </td>
                                </tr>
                            </table>
                            <table class="adminlistsNoBorder">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo __('Standings Column', 'joomsport-sports-league-results-management');?>
                                        </th>
                                        <th>
                                            <?php echo __('Shorten name', 'joomsport-sports-league-results-management');?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $columnshort = json_decode($jsconfig->get('columnshort'),true);
                                
                                foreach($lists['available_options'] as $key => $val){
                                    $currentValue = (isset($columnshort[$key]) && $columnshort[$key])?$columnshort[$key]:$val['short'];
                                    echo '<tr>';
                                    echo '<td width="250">'.$val['label'].'</td>';
                                    echo '<td><input type="text" name="layouts[columnshort]['.$key.']" value="'.$currentValue.'" /></td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tbody>    
                            </table>
                            
                        </div>
                    </div>
                    <div class="jsBepanel">
                        <div class="jsBEheader">
                            <?php echo __('Match page', 'joomsport-sports-league-results-management');?>
                        </div>
                        <div class="jsBEsettings">
                            <table class="adminlistsNoBorder">
                                <tr>
                                    <td width="250">
                                        <?php echo __('Order squad by', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php echo JoomSportHelperSelectBox::Optgroup('layouts[pllist_order_se]', $alltmp_se,$jsconfig->get('pllist_order_se'));?>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo __('Squad first column', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php echo JoomSportHelperSelectBox::Optgroup('layouts[jsmatch_squad_firstcol]', $lists['adf_player_squad'],$jsconfig->get('jsmatch_squad_firstcol'));?>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td width="250">
                                        <?php echo __('Squad last column', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php echo JoomSportHelperSelectBox::Optgroup('layouts[jsmatch_squad_lastcol]', $lists['adf_player_squad'],$jsconfig->get('jsmatch_squad_lastcol'));?>
                                        
                                    </td>
                                </tr>
                                
                                
                                <tr>
                                    <td>
                                        <?php echo __('Reverse Home/Away', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php 
                                        echo JoomSportHelperSelectBox::Radio('layouts[partdisplay_awayfirst]', $is_field_yn,$jsconfig->get('partdisplay_awayfirst'),'');
                                        ?>


                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo __('Default match duration', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                        <?php
                                            
                                             echo '<div class="jslinktopro">Available in <a href="http://joomsport.com/web-shop/joomsport-for-wordpress.html?utm_source=js-st-wp&utm_medium=backend-wp&utm_campaign=buy-js-pro">Pro Edition</a> only</div>'; 
                                        ?>
                                            

                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    </div>
                    <div class="jsrespdiv6 jsrespmarginleft2">
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo __('Image settings', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">
                                <table class="adminlistsNoBorder">
                                    <tr>
                                            <td width="250">
                                                <?php echo __('Logo height for all lists', 'joomsport-sports-league-results-management');?>
                                            <td>

                                                <input type="text" maxlength="5" name="layouts[teamlogo_height]" style="width:50px;" value="<?php echo $jsconfig->get('teamlogo_height',40);?>" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>
                                                <?php echo __('Participant logo height for match page', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>

                                                <input type="text" maxlength="5" name="layouts[set_emblemhgonmatch]" style="width:50px;" value="<?php echo $jsconfig->get('set_emblemhgonmatch',60);?>" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>
                                                <?php echo __('Default photo width', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>

                                                <input type="text" maxlength="5" name="layouts[set_defimgwidth]" style="width:50px;" value="<?php echo $jsconfig->get('set_defimgwidth',200);?>" onblur="extractNumber(this,0,false);" onkeyup="extractNumber(this,0,false);" onkeypress="return blockNonNumbers(this, event, false, false);" />
                                            </td>
                                    </tr>
                                </table>   
                            </div>
                        </div>
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo __('Players settings', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">
                                <table class="adminlistsNoBorder">
                                    <tr>
                                            <td width="250">
                                                <?php echo __('Enable links for player logos', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo JoomSportHelperSelectBox::Radio('layouts[enbl_playerlogolinks]', $is_field_yn,$jsconfig->get('enbl_playerlogolinks',1),'');
                                                ?>

                                            </td>
                                    </tr>
                                    <tr>
                                            <td width="250">
                                                <?php echo __('Enable links for player names', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo JoomSportHelperSelectBox::Radio('layouts[enbl_playerlinks]', $is_field_yn,$jsconfig->get('enbl_playerlinks',1),'');
                                                ?>

                                            </td>
                                    </tr>
                                    <?php
                                    $stdoptions = '';
                                     $stdoptions = "std"; 
                                    if($stdoptions == 'std'){
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo __('Show played matches statistic', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('layouts[played_matches]', $is_field_yn,$jsconfig->get('played_matches'),'');
                                            ?>

                                        </td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div> 
                        <div class="jsBepanel">
                            <div class="jsBEheader">
                                <?php echo __('Team settings', 'joomsport-sports-league-results-management');?>
                            </div>
                            <div class="jsBEsettings">
                                <table class="adminlistsNoBorder">
                                    <tr>
                                            <td width="250">
                                                <?php echo __('Enable links for team logos', 'joomsport-sports-league-results-management');?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo JoomSportHelperSelectBox::Radio('layouts[enbl_teamlogolinks]', $is_field_yn,$jsconfig->get('enbl_teamlogolinks',1),'');
                                                ?>

                                            </td>
                                    </tr>
                                    <tr>
                                            <td width="250">
                                                <?php echo __('Enable links for team names', 'joomsport-sports-league-results-management');?>
                                            </td>    
                                            <td>
                                                <?php 
                                                echo JoomSportHelperSelectBox::Radio('layouts[enbl_teamlinks]', $is_field_yn,$jsconfig->get('enbl_teamlinks',1),'');
                                                ?>

                                            </td>
                                    </tr>
                                    <tr>
                                        <td width="250" class="hdn_div_enblink">
                                            <?php echo __('Enable links for highlighted team only', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td class="hdn_div_enblink">
                                            <?php 
                                            echo JoomSportHelperSelectBox::Radio('layouts[enbl_teamhgllinks]', $is_field_yn,$jsconfig->get('enbl_teamhgllinks'),'');
                                            ?>

                                        </td>
                                    </tr>


                                </table>
                            </div>
                        </div> 
                        
                </div>
                <div style="clear:both;"></div>
            </div>  

            <div>
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input name="save" class="button-primary" type="submit" value="<?php echo __("Save changes",'joomsport-sports-league-results-management');?>">
            </div>
            </form>
        </div>
        </div>    
        <?php
    }
}