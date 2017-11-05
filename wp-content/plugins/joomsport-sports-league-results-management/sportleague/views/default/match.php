<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$jmscore = get_post_meta($rows->id, '_joomsport_match_jmscore',true);
$m_venue = get_post_meta($rows->id,'_joomsport_match_venue',true);
?>
<div id="jsMatchViewID">
    <div class="heading col-xs-12 col-lg-12">
        <div class="col-xs-5 col-lg-5">
            <div class="matchdtime">
                <?php
                $m_date = get_post_meta($rows->id,'_joomsport_match_date',true);
                $m_time = get_post_meta($rows->id,'_joomsport_match_time',true);
                if ($m_date && $m_date != '0000-00-00') {
                    echo '<img src="'.JOOMSPORT_LIVE_ASSETS.'images/calendar-date.png" alt="" />';
                    if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $m_date)) {
                        echo classJsportDate::getDate($m_date, $m_time);
                    } else {
                        echo $m_date;
                    }
                }
                ?>
            </div>
        </div>
        <div class="col-xs-2 col-lg-2 jsTextAlignCenter">
            <?php if (isset($jmscore['is_extra']) && $jmscore['is_extra']) {
    ?>
            <img src="<?php echo JOOMSPORT_LIVE_ASSETS?>images/extra-t.png" alt="<?php echo __('Won in extra time','joomsport-sports-league-results-management');?>" title="<?php echo __('Won in extra time','joomsport-sports-league-results-management');?>" />
            <?php 
} ?>
        </div>
        <div class="col-xs-5 col-lg-5">
            
            <div class="matchvenue">
            <?php
            if ($m_venue) {
                if($rows->getLocation()){
                    echo $rows->getLocation();
                    echo '<img src="'.JOOMSPORT_LIVE_ASSETS.'images/location.png" />';
                }
            }
            ?>
            </div>
        </div>
        
        
    </div>
    <div class="jsClear"></div>
    <div class="jsmatchHeader table-responsive">
        <div class="topMHead"></div>
        <?php 
            global $jsConfig;
            $width = $jsConfig->get('set_emblemhgonmatch', 60);
            $match = $rows;
            $partic_home = $match->getParticipantHome();
            $partic_away = $match->getParticipantAway();
            
            

            ?>
        <?php
        if (jsHelper::isMobile()) {
            ?>
            <div class="jsMatchDivMain">
                <div>
                <div class="jsDivLineEmbl">

                    <?php echo jsHelper::nameHTML($partic_home->getName(true))?>
                </div>
                </div>    
                <div class="jsMatchDivScore">
                    <?php echo($partic_home->getEmblem()).
                        '<div class="jsScoreBonusB">'.jsHelper::getScore($match, '').'</div>'
                    .($partic_away->getEmblem())?>
                </div>
                <div>
                    <div class="jsDivLineEmbl">

                    <?php echo jsHelper::nameHTML($partic_away->getName(true))?>
                    </div>
                </div>
            </div>    
            <?php 
        } else {
            ?>
        <div class="jstable">
            
            <div class="jstable-row">
                <div class="jstable-cell jsMatchEmbl">
                    <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';
            ?>
                </div>
                <div class="jstable-cell jsMatchPartName">

                    <?php

                        echo ($partic_home) ? ($partic_home->getName(true)) : '';
            ?>
                </div>
                <div class="jstable-cell  mainScoreDiv">
                    <?php echo jsHelper::getScoreBigM($match);
            ?>
                </div>
                <div class="jstable-cell jsMatchPartName" style="text-align: right;">
                    <?php

                        echo ($partic_away) ? ($partic_away->getName(true)) : '';
            ?>
                </div>
                <div class="jstable-cell jsMatchEmbl">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
            ?>
                </div>
            </div>
            <?php

            ?>
        </div>
        <?php

        }
        ?>
        <!-- MAPS -->
        <?php
        
        if ($rows->lists['maps'] && count($rows->lists['maps'])) {
            echo jsHelper::getMap($rows->lists['maps']);
        }
        ?>
        <div class="botMHead"></div>
    </div>
    <div class="jsClear">
        <?php
            $tabs = $rows->getTabs();
            jsHelperTabs::draw($tabs, $rows);
        ?>
    </div>
    
    
</div>
