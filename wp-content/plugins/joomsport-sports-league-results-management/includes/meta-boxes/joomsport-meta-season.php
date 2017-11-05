<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomSportMetaSeason {
    public static function output( $post ) {
        global $post, $thepostid, $wp_meta_boxes;
        wp_enqueue_style( 'jquery-jsui-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' ); 
			
        
        $thepostid = $post->ID;
        $lists = self::getLists($thepostid);
        
        wp_nonce_field( 'joomsport_season_savemetaboxes', 'joomsport_season_nonce' );
        
        $iscomplex = get_post_meta($post->ID,'_joomsport_season_complex',true);
        
        if((isset($_GET['iscomplex']) && intval($_GET['iscomplex'])) || $iscomplex == 1){
            echo '<input type="hidden" name="s_complex" id="s_complex" value="1" />';
            remove_meta_box( 'joomsport_season_stages_form_meta_box', 'joomsport_season', 'side' );
            return '';
        }
        
        
        $season_options = array();
        if(isset($lists->season_options)){
            $season_options = json_decode($lists->season_options);
        }
        require_once JOOMSPORT_PATH_HELPERS . 'tabs.php';
        $etabs = new esTabs();
        
        ?>
        <div id="joomsportContainerBE">
            <div class="jsBEsettings" style="padding:0px;">
                <!-- <tab box> -->
                <ul class="tab-box">
                    <?php
                    echo $etabs->newTab(__('Main','joomsport-sports-league-results-management'), 'main_conf', '', 'vis');

                    //if($this->lists['t_type'] == 0){
                    echo $etabs->newTab(__('Season standings view settings','joomsport-sports-league-results-management'), 'col_conf', '');
                    
                    echo $etabs->newTab(__('Groups','joomsport-sports-league-results-management'), 'groups', '');


                    ?>
                </ul>	
                <div style="clear:both"></div>
            </div>
            <div id="main_conf_div" class="tabdiv">
                <div>
                    <div>
                        <?php
                        do_meta_boxes(get_current_screen(), 'joomsportintab_season1', array($post,$lists));
                        unset($wp_meta_boxes[get_post_type($post)]['joomsportintab_season1']);
                        ?>

                    </div>    
                </div>
            </div>   
            <div id="col_conf_div" class="tabdiv visuallyhidden">
                <div>
                    <?php
                    do_meta_boxes(get_current_screen(), 'joomsportintab_season2', array($post,$lists));
                    unset($wp_meta_boxes[get_post_type($post)]['joomsportintab_season2']);
                    ?>
                </div>    
            </div>
            <div id="esport_conf_div" class="tabdiv visuallyhidden">
                <div>
                    <?php
                    
                    ?>
                </div>    
            </div>
            <div id="groups_div" class="tabdiv visuallyhidden">
                <?php
               
                    do_meta_boxes(get_current_screen(), 'joomsportintab_season4', $post);
                    unset($wp_meta_boxes[get_post_type($post)]['joomsportintab_season4']);
                    ?>

            </div>
        </div>
        <?php
    }
        
    public static function js_meta_attr($post){
        global $wpdb;
        $iscomplex = get_post_meta($post->ID,'_joomsport_season_complex',true);
        if(isset($_GET['iscomplex']) && intval($_GET['iscomplex'])){
            $iscomplex = 1;
        }
        $terms = get_the_terms($post->ID, 'joomsport_tournament');
        $terms_id = null;
        if(isset($terms[0]->term_id) && $terms[0]->term_id){
            $terms_id = $terms[0]->term_id;
        }
        if(!$terms_id && isset($_GET['tid']) && intval($_GET['tid'])){
            $terms_id = intval($_GET['tid']);
        }
        $args = array(
                'post_type'  => 'joomsport_season',
                'meta_query' => array(
                        array(
                                'key'     => '_joomsport_season_complex',
                                'value'   => '1',
                        ),
                ),
                'tax_query' => array(
                    array(
                            'taxonomy' => 'joomsport_tournament',
                            'field'    => 'term_id',
                            'terms'    => $terms_id,
                    ),
                ),
        );

        $query = new WP_Query( $args );
        //var_dump($query);
        //if(count($posts)){
            if($iscomplex != 1){
            ?>
            <p><strong><?php echo __('Parent');?></strong></p>
            <label class="screen-reader-text" for="parent_id"><?php echo __('Parent');?></label>
            <select name="parent_id">
                <option value="0"><?php _e('(no parent)') ?></option>
                <?php
                if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            ?>
                <option value="<?php echo $query->post->ID;?>" <?php echo $post->post_parent?" selected":"";?>><?php echo get_the_title();?></option>
                    
                            <?php
                        }
                }
                
                ?>
            </select>
            <?php
            }
        //}
        
        ?>

        <!--div id="js_seasparentDIV">
        <?php 
        /*$terms = get_the_terms($post->ID, 'joomsport_tournament');
        $terms_id = null;
        if(isset($terms[0]->term_id) && $terms[0]->term_id){
            $terms_id = $terms[0]->term_id;
        }
        echo JoomSportHelperObjects::wp_dropdown_posts($post,$terms_id);*/
        ?>
        </div-->
        <p><strong><?php echo __('Order');?></strong></p>
        <p>
            <label class="screen-reader-text" for="menu_order"><?php echo __('Order');?></label>
            <input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo intval($post->menu_order)?>">
        </p>
        <p></p>

        <?php
    }    

    public static function js_meta_points($vars){
        $post = $vars[0];

        $lists = $vars[1];
        $season_options = get_post_meta($post->ID,'_joomsport_season_point',true);

        
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        $lists['s_enbl_extra'] = JoomSportHelperSelectBox::Radio('s_enbl_extra', $is_field,(isset($season_options['s_enbl_extra'])?$season_options['s_enbl_extra']:0),'onclick="javascript:showopt();"');

        
        ?>
                    <table class="jsminwdhtd">    
                        <tr>
                            <td>

                            </td>
                            <td>
                                <table class="tblforpoints">
                                    <tr>
                                        <td style="width:55px;">
                                            <?php echo __('Home', 'joomsport-sports-league-results-management');?>
                                        </td>
                                        <td>
                                            <?php echo __('Away', 'joomsport-sports-league-results-management');?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                    </tr>
                    <tr>
                        <td>
                                <?php echo __('Won points', 'joomsport-sports-league-results-management');?>
                        </td>
                        <td>
                            <table class="tblforpoints">
                                <tr>
                                    <td>
                                        <input type="number" maxlength="5" size="10" style="width:50px;" name="s_win_point" step="any" value="<?php echo isset($season_options['s_win_point'])?floatval($season_options['s_win_point']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                    <td>
                                        <input type="number" maxlength="5" size="10" style="width:50px;" name="s_win_away" step="any" value="<?php echo isset($season_options['s_win_away'])?floatval($season_options['s_win_away']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td>
                        <?php echo __('Draw points', 'joomsport-sports-league-results-management');?>
                        </td>
                        <td>
                            <table class="tblforpoints">
                                <tr>
                                    <td>
                                        <input type="number" maxlength="5" size="10" step="any" style="width:50px;" name="s_draw_point" value="<?php echo isset($season_options['s_draw_point'])?floatval($season_options['s_draw_point']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                    <td>
                                        <input type="number" maxlength="5" size="10" step="any" style="width:50px;" name="s_draw_away" value="<?php echo isset($season_options['s_draw_away'])?floatval($season_options['s_draw_away']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo __('Lost points', 'joomsport-sports-league-results-management');?>
                        </td>
                        <td>
                            <table class="tblforpoints">
                                <tr>
                                    <td>
                                        <input type="number" maxlength="5" size="10" step="any" style="width:50px;" name="s_lost_point" value="<?php echo isset($season_options['s_lost_point'])?floatval($season_options['s_lost_point']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                    <td>
                                        <input type="number" maxlength="5" size="10" step="any" style="width:50px;" name="s_lost_away" value="<?php echo isset($season_options['s_lost_away'])?floatval($season_options['s_lost_away']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>



                    <tr>
                        <td>
                            <?php echo __('Enable extra time', 'joomsport-sports-league-results-management');?>
                        </td>
                            <td>
                                <div class="controls"><fieldset class="radio btn-group"><?php echo $lists['s_enbl_extra'];?></fieldset></div>

                            </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="jsTableEqual" cellpadding="1" cellspacing="0" id="extraoptions" <?php if (!isset($season_options['s_enbl_extra']) || !$season_options['s_enbl_extra']) {echo "style='display:none'";}?>>
                                <tr>
                                    <td width="150">
                                            <div style="width:150px;">
                                                <?php echo __('Won points in extra time', 'joomsport-sports-league-results-management');?>
                                             </div>
                                    </td>
                                    <td>
                                            <input type="number" maxlength="5" step="any" style="width:50px;" size="10" name="s_extra_win" value="<?php echo isset($season_options['s_extra_win'])?floatval($season_options['s_extra_win']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                </tr>
                                <tr>
                                    <td width="150">
                                        <?php echo __('Lost points in extra time', 'joomsport-sports-league-results-management');?>
                                    </td>
                                    <td>
                                            <input type="number" maxlength="5" step="any" style="width:50px;" size="10" name="s_extra_lost" value="<?php echo isset($season_options['s_extra_lost'])?floatval($season_options['s_extra_lost']):""?>" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" />
                                    </td>
                                </tr>
                            </table>		
                        </td>		
                    </tr>
                </table>    
        <?php
    }
    public static function js_meta_rules($vars){
        $post = $vars[0];
        $lists = $vars[1];
        $metadata = get_post_meta($post->ID,'_joomsport_season_rules',true);
        echo wp_editor($metadata, 's_rules',array("textarea_rows"=>3));


    }
    public static function js_meta_ef($vars){
        $post = $vars[0];
        $lists = $vars[1];

        $metadata = get_post_meta($post->ID,'_joomsport_season_ef',true);
        
        $efields = JoomSportHelperEF::getEFList('3', 0);

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
    
    public static function js_meta_standingscolumn($vars){
        $post = $vars[0];
        $lists = $vars[1];
        $metadata = get_post_meta($post->ID,'_joomsport_season_standindgs',true);
        
        $jsconfig =  new JoomsportSettings();
        $lists['available_options'] = $jsconfig->getStandingColumns();
        
        $optionsByDefault = array("emblem_chk","played_chk","win_chk","lost_chk","draw_chk","point_chk");
        $is_new = true;
        if(!$metadata){
            $metadata = $lists['available_options'];
        }else{
            $is_new = false;
            foreach ($lists['available_options'] as $key => $value) {
                if(!isset($metadata[$key])){
                    $metadata[$key] = 0;
                }
            }
        }
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("Hide", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Show", "joomsport-sports-league-results-management"));
        
        ?>
        <table>
            <thead>

                <tr>
                    <td></td>
                    <td><?php echo __('Emblem', 'joomsport-sports-league-results-management');?></td>
                    <td align="right">
                        <div class="controls">
                            <fieldset class="radio btn-group">
                                <?php
                                $emb_value = isset($metadata['emblem_chk'])?$metadata['emblem_chk']:0;
                                if($is_new){
                                    if(in_array("emblem_chk", $optionsByDefault)){
                                        $emb_value = 1;
                                    }
                                }
                                ?>
                                <?php echo JoomSportHelperSelectBox::Radio('standings[emblem_chk]', $is_field,$emb_value,'');?>
                            </fieldset>
                        </div>

                    </td>
                </tr>
            </thead> 
            <tbody id="id_column_seas">
                <?php
                $curcol = 0;
                    if (count($metadata)) {
                        
                        foreach ($metadata as $key => $value) {
                            if ($key && $key != 'emblem_chk') {
                                if(is_array($value)){
                                    $value=0;
                                }
                                if($is_new){
                                    if(in_array($key, $optionsByDefault)){
                                        $value = 1;
                                    }
                                }
                                ?>
                                <tr class="ui-state-default">
                                    <td class="jsdadicon">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </td>
                                    <td style="padding-right:15px;"><?php echo $lists['available_options'][$key]['label']?></td>
                                    <td align="right" nowrap="nowrap">
                                        <div class="controls">
                                            <fieldset class="radio btn-group">
                                                <?php echo JoomSportHelperSelectBox::Radio('standings['.$key.']', $is_field,$value,'');?>
                                            </fieldset>
                                        </div>
                                        <input type="hidden" name="opt_columns[]" value="<?php echo $key?>" />
                                    </td>	
                                </tr>
                                <?php
                                ++$curcol;
                            }
                        }
                    }


                    ?>

            </tbody>
        </table>
            
    <?php            
    }
    public static function js_meta_highlight($vars){
        $post = $vars[0];
        $lists = $vars[1];
        $metadata = get_post_meta($post->ID,'_joomsport_season_colors',true);

        $lists['colors'] = array();
        ?>
        <table>
            <tr>
                <td>
                        <div id="colorpicker201" class="colorpicker201"></div>
                </td>
            </tr>
            <tr>
                <td id="app_newcol">
                    <?php
                    if(count($metadata) && $metadata){
                        for($intA=0; $intA < count($metadata); $intA++){
                            $ch = rand(0, 100000);
                        ?> 
                        <div class="jscolordivcont">

                            <input class="button" type="button" style="cursor:pointer;" onclick="showColorGrid2('input_field_<?php echo $ch;?>','sample_<?php echo $ch;?>');" value="...">&nbsp;<input type="text"  style="width:100px;" ID="input_field_<?php echo $ch;?>" class="jscolorinp" name="color_field[]" size="9" value="<?php echo $metadata[$intA]['color_field'];?>"><input type="text" ID="sample_<?php echo $ch;?>"  size="1" value="" class="color-kind" style="width:30px; background-color: <?php echo $metadata[$intA]['color_field'];?>"/>
                            <?php echo __('Place', 'joomsport-sports-league-results-management');?>
                            <input type="text" ID="place_<?php echo $ch;?>" name="place[]" style="width:30px;" size="5" value="<?php echo $metadata[$intA]['places'];?>"/>
                            <input type="text" ID="legend_<?php echo $ch;?>" style="width:100px;" name="legend[]"  size="5" value="<?php echo (isset($metadata[$intA]['legend']))?$metadata[$intA]['legend']:'';?>" />
                        </div>
                         <?php   
                        }
                    }
                    ?>
                    <div class="jscolordivcont">

                        <input class="button" type="button" style="cursor:pointer;" onclick="showColorGrid2('input_field_1','sample_1');" value="...">&nbsp;<input type="text" ID="input_field_1" class="jscolorinp"  style="width:100px;" name="color_field[]" size="9" value=""><input type="text"  style="width:30px;" ID="sample_1" size="1" value="" class="color-kind"/>
                        <?php echo __('Place', 'joomsport-sports-league-results-management');?>
                        <input type="text" ID="place_1" name="place[]" style="width:30px;" size="5" value=""/>
                        <?php echo __('Legend', 'joomsport-sports-league-results-management');?>
                        <input type="text" ID="legend_1" style="width:100px;" name="legend[]"  size="5" value="" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                <input class="button" type="button" style="cursor:pointer;" value="<?php echo __('Add color', 'joomsport-sports-league-results-management');?>" onclick="javascript:add_colors();" />
                </td>
            </tr>
        </table>
        <?php
    }
    public static function js_meta_rankcriteria($vars){
        $post = $vars[0];
        $lists = $vars[1];
        $metadata = get_post_meta($post->ID,'_joomsport_season_ranking',true);

        $is_field1 = array();
        $is_field1[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field1[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        
        $is_field = array();
        $is_field[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(1, __("Points", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(2, __("Win Percent", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(4, __("Goal difference", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(5, __("Goal scored", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(6, __("Played", "joomsport-sports-league-results-management"));
        $is_field[] = JoomSportHelperSelectBox::addOption(7, __("Win games", "joomsport-sports-league-results-management"));
        
        $is_field2 = array();
        $is_field2[] = JoomSportHelperSelectBox::addOption(0, __("Descending", "joomsport-sports-league-results-management"));
        $is_field2[] = JoomSportHelperSelectBox::addOption(1, __("Ascending", "joomsport-sports-league-results-management"));
        ?>
        <table class="admin">
                            
            <tr>
                <td colspan="2">
                    <?php echo __('Compare games when participants have equal rank','joomsport-sports-league-results-management');?>
                    <div class="controls" style="display:inline; margin-left:10px;">
                        <fieldset class="radio btn-group">
                            <?php echo JoomSportHelperSelectBox::Radio('equalpts_chk', $is_field1,isset($metadata['equalpts_chk'])?$metadata['equalpts_chk']:0,'');?>
                        </fieldset>
                    </div>
                    <br />
                    <br />

                </td>
            </tr>
        </table>        
        <table class="admin" id="divrankingsbox">        
            <?php
            $default_criteria = array(1, 4, 5, 7, 0);
            for ($i = 0;$i < 5;++$i) {
                if($metadata){
                    $sortfield_val = isset($metadata['ranking'][$i]['sortfield'])?$metadata['ranking'][$i]['sortfield']:$default_criteria[$i];
                    $sortway_val = isset($metadata['ranking'][$i]['sortway'])?$metadata['ranking'][$i]['sortway']:0;
                }else{
                    $sortfield_val = $default_criteria[$i];
                    $sortway_val = 0;
                }
                
                echo '<tr>';
                echo '<td>'.JoomSportHelperSelectBox::Simple('sortfield[]', $is_field,$sortfield_val,'',false).'</td>';
                echo '<td>'.JoomSportHelperSelectBox::Simple('sortway[]', $is_field2,$sortway_val,'',false).'</td>';
                echo '</tr>';
            }
            ?>
        </table>

        <div id="divcririadescr">
            <?php 
            echo __('The first ranking criteria is Points number.','joomsport-sports-league-results-management');
            echo "<br />";
            echo __('If participants have equal points then system will compare their games for:','joomsport-sports-league-results-management');
            
            ?>
            <ul>
                <li><?php echo __('Points acquired','joomsport-sports-league-results-management');?></li>
                <li><?php echo __('Goal Difference','joomsport-sports-league-results-management');?></li>
                <li><?php echo __('Goals Scored','joomsport-sports-league-results-management');?></li>
            </ul>
        </div>
    <?php            
    }
    
    public static function js_meta_sregistration($vars){
        $post = $vars[0];
        $lists = $vars[1];
        $metadata = get_post_meta($post->ID,'_joomsport_season_sreg',true);
        $is_field1 = array();
        $is_field1[] = JoomSportHelperSelectBox::addOption(0, __("No", "joomsport-sports-league-results-management"));
        $is_field1[] = JoomSportHelperSelectBox::addOption(1, __("Yes", "joomsport-sports-league-results-management"));
        ?>
        <div class="jstable jsminwdhtd">
            <div class="jstable-row">
                <div class="jstable-cell" style="width:200px;">
                    <?php echo __('Enable registration','joomsport-sports-league-results-management'); ?>
                </div>
                <div class="jstable-cell">
                    <div class="controls">
                        <fieldset class="radio btn-group">
                            <?php echo JoomSportHelperSelectBox::Radio('s_reg', $is_field1,isset($metadata['s_reg'])?$metadata['s_reg']:0,'');?>
                        
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
         <div class="jstable jsminwdhtd" id="partRegDiv">
            <div class="jstable-row">
                <div class="jstable-cell"  style="width:200px;">
                    <?php echo __('Limit the number of participants','joomsport-sports-league-results-management'); ?>
                </div>

                <div class="jstable-cell">
                    <div class="controls">
                        <fieldset class="radio btn-group" style="display:inline-block;">
                            <?php echo JoomSportHelperSelectBox::Radio('s_reg_to', $is_field1,isset($metadata['s_reg_to'])?$metadata['s_reg_to']:0,'');?>
                        </fieldset>

                        <div class="jsregnumpart dependonilmit" style="display:inline-block;
    margin-top: 15px;
    position: absolute;
    margin-left: 10px;">

                                <?php echo __('to','joomsport-sports-league-results-management'); ?>
                                <input type="number" maxlength="6" size="10" name="s_participant" value="<?php echo isset($metadata['s_participant'])?$metadata['s_participant']:'';?>" />

                        </div>
                    </div>
                </div>
            </div>


            <div class="jstable-row">
                    <div class="jstable-cell">
                            <?php echo __('Start registration','joomsport-sports-league-results-management'); ?>
                    </div>
                    <div class="jstable-cell">
                        <input type="text" class="jsdatefield" name="reg_start" value="<?php echo isset($metadata['reg_start'])?$metadata['reg_start']:""?>" />

                    </div>
            </div>
            <div class="jstable-row">
                    <div class="jstable-cell">
                            <?php echo __('End registration','joomsport-sports-league-results-management'); ?>
                    </div>
                    <div class="jstable-cell">
                        <input type="text" class="jsdatefield" name="reg_end" value="<?php echo isset($metadata['reg_end'])?$metadata['reg_end']:""?>" />
                    </div>
            </div>
        </div>        
        <?php
    }
    
    public static function js_meta_participiants($vars){
        global $joomsportSettings;
        $post = $vars[0];
        $lists = $vars[1];
        $metadata = get_post_meta($post->ID,'_joomsport_season_participiants',true);

        $t_single = JoomSportHelperObjects::getTournamentType($post->ID);
        
        $post_type = $t_single ? 'joomsport_player' :'joomsport_team';
        $args = array(
            'posts_per_page' => -1,
            'offset'           => 0,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_type'        => $post_type,
            'post_status'      => 'publish',

        );
        $posts_array = get_posts( $args );

        if(count($posts_array)){
            echo '<select name="participiants[]" class="jswf-chosen-select" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" multiple>';
            foreach ($posts_array as $tm) {
                $selected = '';
                if(in_array($tm->ID, $metadata)){
                    $selected = ' selected';
                }
                echo '<option value="'.$tm->ID.'" '.$selected.'>'.$tm->post_title.'</option>';
            }
            echo '</select>';
        }else{
            $link = get_admin_url(get_current_blog_id(), 'edit.php?post_type='.$post_type);
            printf( __( "There are no participants. Create new one on  %s Participant list %s", 'joomsport-sports-league-results-management' ), '<a href="'.$link.'">','</a>' );

        }

        
    }
    public static function js_meta_stages($post){
        global $joomsportSettings, $wpdb;

        $metadata = get_post_meta($post->ID,'_joomsport_season_stages',true);
        
        $stages = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_maps}");

        if(count($stages)){
            echo '<select name="stages[]" class="jswf-chosen-select" data-placeholder="'.__('Add item','joomsport-sports-league-results-management').'" multiple>';
            foreach ($stages as $tm) {
                $selected = '';
                if(in_array($tm->id, $metadata)){
                    $selected = ' selected';
                }
                echo '<option value="'.$tm->id.'" '.$selected.'>'.$tm->m_name.'</option>';
            }
            echo '</select>';
        }else{
            $link = get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-gamestages');
            printf( __( "There are no game stages available. Create new one on  %s Game Stages list %s", 'joomsport-sports-league-results-management' ), '<a href="'.$link.'">','</a>' );

        }

        
    }
    
    public static function js_meta_groups($post){
        global  $wpdb;
        $prt = get_post_meta($post->ID,'_joomsport_season_participiants',true);
        $groups = $wpdb->get_results("SELECT * FROM {$wpdb->joomsport_groups} WHERE s_id = {$post->ID} ORDER BY ordering"); 
        if(get_post_status( $post->ID ) != 'auto-draft' && $prt){?>
        <div>
            <input type="button" class="jspopupGroups button" attrid="0" value="<?php echo __('Create new group','joomsport-sports-league-results-management')?>" />
        </div>  
        <?php }else{
            echo __('Add participants and save season to add groups','joomsport-sports-league-results-management');
        } ?>
    
        <table class="table table-striped" >
            
            <tbody id="jsGroupList" class="ui-sortable">
                <?php
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
                ?>
            </tbody>
        </table>
        
    <?php            
    }

    public static function getLists($postid){
        global $wpdb;
        $lists = array();
       

        return $lists;
    }
    
    
    public static function joomsport_season_save_metabox($post_id, $post){
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['joomsport_season_nonce'] ) ? $_POST['joomsport_season_nonce'] : '';
        $nonce_action = 'joomsport_season_savemetaboxes';
        
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
        
        if('joomsport_season' == $_POST['post_type'] ){
            
            $s_complex = isset($_POST['s_complex'])?  intval($_POST['s_complex']):0;
            
            if($s_complex == 1){
                update_post_meta($post_id, '_joomsport_season_complex', $s_complex);
            }else{
                self::saveMetaPoints($post_id);
                self::saveMetaParticipiants($post_id);
                self::saveMetaRules($post_id);

                self::saveMetaEF($post_id);

                self::saveMetaStandings($post_id);
                self::saveMetaColors($post_id);
                self::saveMetaRankCriteria($post_id);
                self::saveMetaRegister($post_id);
                
                self::saveMetaStages($post_id);
                self::saveGroupsOrder($post_id);

                do_action('joomsport_update_standings',$post_id);
                do_action('joomsport_update_playerlist',$post_id);
            }
            
        }
    }
    
    private static function saveMetaPoints($post_id){
        $meta_array = array();
        $meta_array['s_win_point'] = isset($_POST['s_win_point'])?  floatval($_POST['s_win_point']):'';
        $meta_array['s_win_away'] = isset($_POST['s_win_away'])?  floatval($_POST['s_win_away']):'';
        $meta_array['s_draw_point'] = isset($_POST['s_draw_point'])?  floatval($_POST['s_draw_point']):'';
        $meta_array['s_draw_away'] = isset($_POST['s_draw_away'])?  floatval($_POST['s_draw_away']):'';
        $meta_array['s_lost_point'] = isset($_POST['s_lost_point'])?  floatval($_POST['s_lost_point']):'';
        $meta_array['s_lost_away'] = isset($_POST['s_lost_away'])?  floatval($_POST['s_lost_away']):'';
        $meta_array['s_extra_win'] = isset($_POST['s_extra_win'])?  floatval($_POST['s_extra_win']):'';
        $meta_array['s_extra_lost'] = isset($_POST['s_extra_lost'])?  floatval($_POST['s_extra_lost']):'';
        $meta_array['s_enbl_extra'] = isset($_POST['s_enbl_extra'])?  intval($_POST['s_enbl_extra']):0;
        //$meta_data = json_encode($meta_array);
        update_post_meta($post_id, '_joomsport_season_point', $meta_array);
    }
    private static function saveMetaParticipiants($post_id){
        
        $meta_data = isset($_POST['participiants'])?  ($_POST['participiants']):'';
        if($meta_data){
            $meta_data = array_map( 'sanitize_text_field', wp_unslash( $_POST['participiants'] ) );
        }
        update_post_meta($post_id, '_joomsport_season_participiants', $meta_data);
    }
    private static function saveMetaRules($post_id){
        $meta_data = isset($_POST['s_rules'])?  wp_kses_post($_POST['s_rules']):'';
        update_post_meta($post_id, '_joomsport_season_rules', $meta_data);
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
        update_post_meta($post_id, '_joomsport_season_ef', $meta_array);
    }
    private static function saveMetaStandings($post_id){
        $meta_array = isset($_POST['standings'])?$_POST['standings']:array();
        update_post_meta($post_id, '_joomsport_season_standindgs', $meta_array);
    }
    private static function saveMetaColors($post_id){
        $meta_array = array();
        
        if(isset($_POST['color_field']) && count($_POST['color_field'])){
            for($intA = 0; $intA < count($_POST['color_field']); $intA++){
                if($_POST['place'][$intA] && $_POST['color_field'][$intA]){
                    $meta_array[$intA]['color_field'] = sanitize_text_field($_POST['color_field'][$intA]);
                    $meta_array[$intA]['places'] = sanitize_text_field($_POST['place'][$intA]);
                    $meta_array[$intA]['legend'] = sanitize_text_field($_POST['legend'][$intA]);
                }
            }
        }
        update_post_meta($post_id, '_joomsport_season_colors', $meta_array);
    }
    private static function saveMetaRankCriteria($post_id){
        $meta_array = array();
        $meta_array['equalpts_chk'] = sanitize_text_field($_POST['equalpts_chk']);
        if(isset($_POST['sortfield']) && count($_POST['sortfield'])){
            for($intA = 0; $intA < count($_POST['sortfield']); $intA++){
                $meta_array['ranking'][$intA]['sortfield'] = sanitize_text_field($_POST['sortfield'][$intA]);
                $meta_array['ranking'][$intA]['sortway'] = sanitize_text_field($_POST['sortway'][$intA]);
            }
        }
        update_post_meta($post_id, '_joomsport_season_ranking', $meta_array);
    }
    private static function saveMetaRegister($post_id){
        $meta_array = array();
        $meta_array['s_reg'] = intval($_POST['s_reg']);
        $meta_array['s_reg_to'] = intval($_POST['s_reg_to']);
        $meta_array['s_participant'] = intval($_POST['s_participant']);
        $meta_array['reg_start'] = sanitize_text_field($_POST['reg_start']);
        $meta_array['reg_end'] = sanitize_text_field($_POST['reg_end']);
        
        update_post_meta($post_id, '_joomsport_season_sreg', $meta_array);
    } 
    private static function saveMetaStages($post_id){
        $meta_data = isset($_POST['stages'])?  ($_POST['stages']):'';
        if($meta_data){
            $meta_data = array_map( 'sanitize_text_field', wp_unslash( $_POST['stages'] ) );
        }
        update_post_meta($post_id, '_joomsport_season_stages', $meta_data);
    }
    
    //list columns
    public static function season_type_columns( $taxonomies ) {
        
        $columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Title' ),
		'tournament' => __('League','joomsport-sports-league-results-management'),
		'date' => __( 'Date' )
	);

	return $columns;
  
    }

 
    public static function manage_joomsport_season_columns($column_name, $tax_id) {

        $terms = get_the_terms($tax_id, 'joomsport_tournament');

        $out = '';
        switch ($column_name) {
            case 'tournament': 
                if($terms && count($terms)){
                    foreach ($terms as $term) {
                        echo $term->name;
                    }
                }
                break;

            default:
                break;
        }
        return $out;    
    }
    
    public static function saveGroupsOrder($post_id){
        global $wpdb;
        $groups = isset($_POST['groupId'])?$_POST['groupId']:array();
        if($groups && count($groups)){
            for($intA=0;$intA<count($groups);$intA++){
                if(intval($groups[$intA])){
                    $wpdb->update($wpdb->joomsport_groups, array("ordering"=>$intA), array("id"=>intval($groups[$intA])), array("%d"), array("%d")); 
            
                }
            }
        }
        
    }
    
}
add_filter('manage_edit-joomsport_season_columns', array( 'JoomSportMetaSeason','season_type_columns') );
add_action("manage_joomsport_season_posts_custom_column", array( 'JoomSportMetaSeason','manage_joomsport_season_columns'), 10, 3);
       