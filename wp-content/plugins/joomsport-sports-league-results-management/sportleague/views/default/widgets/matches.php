<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
global $jsConfig;
?>
<div  id="joomsport-container" class="<?php echo $classname;?>">
    <div class="jsmainscroll jsrelatcont">
        <?php if($enbl_slider){?>
        <button class="jsprev" id="jsScrollMatchesPrev<?php echo $module_id;?>">
          <?php if (!is_rtl()) {?>
            <svg viewBox="6 0 18 25" height="20" width="11" xmlns="http://www.w3.org/2000/svg"><path opacity="1" d="M20.654 9.43l-6.326 6.525 6.326 6.6c.242.253.36.545.36.88 0 .333-.118.625-.36.877l-1.244 1.3c-.24.25-.523.375-.84.375-.32 0-.6-.124-.84-.376l-8.367-8.72c-.24-.25-.363-.54-.363-.88 0-.334.122-.63.363-.88l8.367-8.75c.23-.252.51-.377.83-.377.325 0 .607.126.85.378l1.242 1.326c.242.252.36.54.36.864 0 .32-.118.61-.36.86z" fill-rule="evenodd" fill="#5e5e5e"/> </svg>
          <?php } else { ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="6 0 18 25" height="20" width="11"><path fill-rule="evenodd" opacity="1" fill="#5e5e5e" d="M13.286 25.61c-.24.253-.52.377-.84.377-.318 0-.6-.124-.84-.376l-1.245-1.297c-.24-.252-.36-.544-.36-.878 0-.334.12-.626.36-.878l6.328-6.6L10.36 9.43c-.24-.25-.36-.54-.36-.864 0-.323.12-.612.36-.864l1.245-1.325c.24-.252.523-.377.848-.377.323 0 .602.125.833.377l8.366 8.746c.24.25.363.546.363.882 0 .34-.122.634-.363.886l-8.366 8.72z"/></svg>
          <?php } ?>

        </button>

        <?php
        }
        ?>
        <div>
            <?php
            if($args['layout'] == '1'){
                ?>
                <div class="jscaruselcont jsview1" id="jsScrollMatches<?php echo $module_id;?>">

                    <ul style="margin: 0px; padding: 0px; position: relative; list-style: none; z-index: 1;">
                        <?php
                        $cur_md = 0;
                        foreach ($list as $match) {
                            if($jsConfig->get('partdisplay_awayfirst',0) == 1){
                                
                                $away_score = get_post_meta( $match->id, '_joomsport_home_score', true );
                                $home_score = get_post_meta( $match->id, '_joomsport_away_score', true );
                            }else{
                                
                                $home_score = get_post_meta( $match->id, '_joomsport_home_score', true );
                                $away_score = get_post_meta( $match->id, '_joomsport_away_score', true );
                            }
                            $partic_home = $match->getParticipantHome();
                            $partic_away = $match->getParticipantAway();

                            $m_played = get_post_meta($match->id,'_joomsport_match_played',true);
                            $match_date = get_post_meta($match->id,'_joomsport_match_date',true);
                            
                            $match_time = get_post_meta($match->id,'_joomsport_match_time',true);
                            $match_date = classJsportDate::getDate($match_date, $match_time);

                            $m_venue = get_post_meta($match->id,'_joomsport_match_venue',true);
                            $md_name = $match->getMdayName();
                            $md_id = $match->getMdayID();
                           ?>
                        <li>
                            <div class="jsmatchcont">
                                <?php
                                
                                if($args['groupbymd'] && $md_id != $cur_md){
                                    echo '<div class="jsmatchseason">'.$md_name.'</div>';
                                    $cur_md = $md_id;
                                }
                                
                                if($args['season']){
                                    $seasid = JoomSportHelperObjects::getMatchSeason($match->id);
                                    if($seasid){
                                    $seasObj =  new modelJsportSeason($seasid);
                                    echo '<div class="jsmatchseason">'.$seasObj->getName().'</div>';
                                    }
                                }
                                ?>
                                <div class="jsmatchdate">



                                    <?php echo $match_date;?>
                                    <?php if($m_venue && $args['venue']){
                                        $venue_name = $match->getLocation(false);
                                        ?>
                                        <a href="<?php echo classJsportLink::venue($venue_name, $m_venue, true);?>" title="<?php echo $match->getLocation(false);?>"><img src="<?php echo JOOMSPORT_LIVE_ASSETS;?>images/location.png" /></a>
                                    <?php } ?>
                                </div>
                                <table class="jsMNS">
                                    <tr>
                                        <td class="tdminembl">
                                            <?php
                                            if($args['emblems'] && is_object($partic_home)){
                                                echo $partic_home->getEmblem(true, 0, 'emblInline', 0);
                                            }
                                            ?>
                                        </td>
                                        <td class="tdminwdt jspaddright20">
                                            <?php
                                            if(is_object($partic_home)){
                                                echo jsHelper::nameHTML($partic_home->getName(true));
                                            }
                                            ?>
                                        </td>
                                        <td width="30">

                                            <?php
                                            if($m_played){
                                                echo '<div class="scoreScrMod">'.classJsportLink::match($home_score, $match->id,false,'').'</div>';
                                            }
                                            ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tdminembl">
                                            <?php
                                            if($args['emblems'] && is_object($partic_away)){
                                                echo $partic_away->getEmblem(true, 0, 'emblInline', 0);
                                            }
                                            ?>
                                        </td>
                                        <td class="tdminwdt jspaddright20">
                                            <?php
                                            if(is_object($partic_away)){
                                                echo jsHelper::nameHTML($partic_away->getName(true));
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if($m_played){
                                                echo '<div class="scoreScrMod">'.classJsportLink::match($away_score, $match->id,false,'').'</div>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>

                            </div>

                        </li>
                            <?php

                        }
                        ?>
                    </ul>
                </div>
                <?php
            }else{
                ?>
                <div class="jscaruselcont jsview2" id="jsScrollMatches<?php echo $module_id;?>">


                <?php if($enbl_slider){?>
                    <ul style="margin: 0px; padding: 0px; position: relative; list-style: none; z-index: 1;">
                <?php }else{?>
                    <div class="jsmatchcont">
                        <table width="100%">
                <?php } ?>

                        <?php
                        $cur_md = 0;
                        foreach ($list as $match) {
                            $partic_home = $match->getParticipantHome();
                            $partic_away = $match->getParticipantAway();
                            $m_played = get_post_meta($match->id,'_joomsport_match_played',true);
                            $match_date = get_post_meta($match->id,'_joomsport_match_date',true);
                            $match_time = get_post_meta($match->id,'_joomsport_match_time',true);
                            $match_date = classJsportDate::getDate($match_date, $match_time);


                            $m_venue = get_post_meta($match->id,'_joomsport_match_venue',true);
                            $md_name = $match->getMdayName();
                            $md_id = $match->getMdayID();
                           ?>
                            <?php if($enbl_slider){?>
                            <li>
                                <table width="100%">
                                    <?php
                                    if($args['groupbymd'] && $md_id != $cur_md){
                                        echo '<div class="jsmatchseason">'.$md_name.'</div>';
                                        $cur_md = $md_id;
                                    }
                                    if($args['season']){
                                        $seasid = JoomSportHelperObjects::getMatchSeason($match->id);
                                        $seasObj =  new modelJsportSeason($seasid);
                                        echo '<div class="jsmatchseason">'.$seasObj->getName().'</div>';
                                    }
                                    ?>
                            <?php }else{?>
                                    <tr>
                                        <td colspan="3">
                                            <?php
                                            if($args['groupbymd'] && $md_id != $cur_md){
                                                echo '<div class="jsmatchseason">'.$md_name.'</div>';
                                                $cur_md = $md_id;
                                            }
                                            if($args['season']){
                                                $seasid = JoomSportHelperObjects::getMatchSeason($match->id);
                                                $seasObj =  new modelJsportSeason($seasid);
                                                echo '<div class="jsmatchseason">'.$seasObj->getName().'</div>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                            <?php }?>        



                                            <td colspan="3">
                                                <div class="jsmatchdate">



                                                    <?php echo $match_date;?>
                                                    <?php if($m_venue && $args['venue']){
                                                        $venue_name = $match->getLocation(false);
                                                        ?>
                                                        <a href="<?php echo classJsportLink::venue($venue_name, $m_venue, true);?>" title="<?php echo $match->getLocation(false);?>"><img src="<?php echo JOOMSPORT_LIVE_ASSETS;?>/images/location.png" /></a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td class="tdminwdt jstdhometeam">
                                                <div>
                                                    <?php
                                                if($args['emblems'] && is_object($partic_home)){
                                                    echo $partic_home->getEmblem(true, 0, 'emblInline', 0);
                                                }
                                                ?>
                                                </div>
                                                <?php
                                                if(is_object($partic_home)){
                                                    echo jsHelper::nameHTML($partic_home->getName(true));
                                                }
                                                ?>
                                            </td>
                                            <td class="jsvalignmdl jscenter jsNoWrap">

                                                <?php
                                                echo jsHelper::getScore($match,'','');
                                                ?>

                                            </td>
                                            <td class="tdminwdt jstdawayteam">
                                                <div>
                                                    <?php
                                                if($args['emblems'] && is_object($partic_away)){
                                                    echo $partic_away->getEmblem(true, 0, 'emblInline', 0);
                                                }
                                                ?>
                                                </div>
                                                <?php
                                                if(is_object($partic_away)){
                                                    echo jsHelper::nameHTML($partic_away->getName(true));
                                                }
                                                ?>
                                            </td>
                                        </tr>

                                <?php if($enbl_slider){?>
                                        </table>
                                    </li>
                                <?php }?>    


                                <?php

                            }
                            ?>
                                <?php if($enbl_slider){?>
                                    </ul>
                                <?php }else{ ?>
                                    </table>
                                </div>
                                <?php } ?>
                    </div>
                <?php
            }
            ?>
        </div>
         <?php if($enbl_slider){?>
         <button class="jsnext" id="jsScrollMatchesNext<?php echo $module_id;?>">
          <?php if (!is_rtl()) {?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="6 0 18 25" height="20" width="11"><path fill-rule="evenodd" opacity="1" fill="#5e5e5e" d="M13.286 25.61c-.24.253-.52.377-.84.377-.318 0-.6-.124-.84-.376l-1.245-1.297c-.24-.252-.36-.544-.36-.878 0-.334.12-.626.36-.878l6.328-6.6L10.36 9.43c-.24-.25-.36-.54-.36-.864 0-.323.12-.612.36-.864l1.245-1.325c.24-.252.523-.377.848-.377.323 0 .602.125.833.377l8.366 8.746c.24.25.363.546.363.882 0 .34-.122.634-.363.886l-8.366 8.72z"/></svg>
          <?php } else { ?>
            <svg viewBox="6 0 18 25" height="20" width="11" xmlns="http://www.w3.org/2000/svg"><path opacity="1" d="M20.654 9.43l-6.326 6.525 6.326 6.6c.242.253.36.545.36.88 0 .333-.118.625-.36.877l-1.244 1.3c-.24.25-.523.375-.84.375-.32 0-.6-.124-.84-.376l-8.367-8.72c-.24-.25-.363-.54-.363-.88 0-.334.122-.63.363-.88l8.367-8.75c.23-.252.51-.377.83-.377.325 0 .607.126.85.378l1.242 1.326c.242.252.36.54.36.864 0 .32-.118.61-.36.86z" fill-rule="evenodd" fill="#5e5e5e"/> </svg>
          <?php } ?>
        </button>
        <script>
            jQuery(document).ready(function(){
                var arw = jQuery("#jsScrollMatches<?php echo $module_id;?>").parent().width();
                var arw_li = jQuery("#jsScrollMatches<?php echo $module_id;?>").find('li').width()+7;//+ margin
                var num = Math.floor(arw/arw_li);
                var curpos = parseInt(<?php echo $curpos;?>);
                var matchnum = parseInt(<?php echo count($matches);?>);
                if(num == 0){
                    num = 1;
                }   


                if((curpos + num >= matchnum) && (matchnum - num >= 0)){
                    curpos = matchnum - num;
                
                //jQuery('#jsScrollMatchesNext<?php echo $module_id;?>').addClass('disabled');
                }else if((curpos + num >= matchnum) && (matchnum - num < 0)){   
                    curpos = 0;
                    //jQuery('#jsScrollMatchesNext<?php echo $module_id;?>').addClass('disabled');
                }
                
                jQuery(function() {
                    jQuery("#jsScrollMatches<?php echo $module_id;?>").jCarouselLite({
                        btnNext: "#jsScrollMatchesNext<?php echo $module_id;?>",
                        btnPrev: "#jsScrollMatchesPrev<?php echo $module_id;?>",
                        circular: false,
                        visible:num,
                        start:curpos,
                        speed:0,
                    });
                });
                 
                jQuery(window).trigger('resize');

                jQuery(window).resize(function(){
                        var arw = jQuery("#jsScrollMatches<?php echo $module_id;?>").parent().width();
                        var arw_li = jQuery("#jsScrollMatches<?php echo $module_id;?>").find('li').width()+6;//+ margin
                        var num = Math.floor(arw/arw_li);
                        if(num == 0){
                            num = 1;
                        }  
                        jQuery('#jsScrollMatchesNext<?php echo $module_id;?>, #jsScrollMatchesPrev<?php echo $module_id;?>').unbind('click');
                        jQuery('#jsScrollMatches<?php echo $module_id;?>').jCarouselLite({
                            btnNext: "#jsScrollMatchesNext<?php echo $module_id;?>",
                                btnPrev: "#jsScrollMatchesPrev<?php echo $module_id;?>",
                                circular: false,
                                visible:num,
                                speed:0
                        });

                });
            });
        </script>    
        <?php
         }
        ?>
    </div>
</div>