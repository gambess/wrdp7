<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="table-responsive">
    <?php
            global $jsConfig;
            $width = $jsConfig->get('teamlogo_height');
            $match = $rows;
            global $jsConfig;
            if($jsConfig->get('partdisplay_awayfirst',0) == 1){
                $partic_home = $match->getParticipantHome();
                $partic_away = $match->getParticipantAway();
                $tmp = $rows->lists['m_events_away'];
                $rows->lists['m_events_away'] = $rows->lists['m_events_home'];
                $rows->lists['m_events_home'] = $tmp;
                $tmp = $rows->lists['squard1'];
                $rows->lists['squard1'] = $rows->lists['squard2'];
                $rows->lists['squard2'] = $tmp;
                $tmp = $rows->lists['squard1_res'];
                $rows->lists['squard1_res'] = $rows->lists['squard2_res'];
                $rows->lists['squard2_res'] = $tmp;
            }else{
                $partic_home = $match->getParticipantHome();
                $partic_away = $match->getParticipantAway();
            }
            
            ?>
    <?php
    if (count($rows->lists['m_events_home']) || count($rows->lists['m_events_away'])) {
        ?>
    <div class="center-block jscenter jsMarginBottom30">
        <h3 class="jsInlineBlock"><?php echo __('Player statistic','joomsport-sports-league-results-management');
        ?></h3>
    </div>
    <div class="jsPaddingBottom30">
        <div class="jsOverflowHidden">
            
            <div class="jsInline">
                <div class="jsDivwMinHg">
                    
                    <div class="jstable-cell ">
                    <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                    <div class="jstable-cell ">

                        <?php

                            echo ($partic_home) ? ($partic_home->getName(true)) : '';
        ?>
                    </div>
                </div>
                <?php 
                if($rows->lists['m_events_display'] == 1){
                ?>
                <table class="jsTblMatchTab firstTeam">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php echo __('Quantity','joomsport-sports-league-results-management');
        ?></th>
                            <th><?php echo __('Event','joomsport-sports-league-results-management');
        ?></th>
                            <th><?php echo __('Time','joomsport-sports-league-results-management');
        ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($intP = 0; $intP < count($rows->lists['m_events_home']); ++$intP) {
                        ?>
                        <tr class="jsMatchTRevents">
                            <td class="evPlayerName">
                                <?php echo $rows->lists['m_events_home'][$intP]->obj->getName(true);
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->ecount;
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_home'][$intP]->objEvent->getEmblem(false);
                        ?>
                            </td>
                            
                            
                            <td>
                                <?php 
                                if($rows->lists['m_events_home'][$intP]->minutes_input){
                                    echo $rows->lists['m_events_home'][$intP]->minutes_input;
                                    if(strpos($rows->lists['m_events_home'][$intP]->minutes_input,':') === false){
                                        echo "'";
                                    }
                                }else{
                                    echo $rows->lists['m_events_home'][$intP]->minutes ? $rows->lists['m_events_home'][$intP]->minutes."'" : '';
                                }
                                ?>
                            </td>
                        </tr>    
                        <?php

                    }
        if (!count($rows->lists['m_events_home'])) {
            //echo "&nbsp";
        }
        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
            <div  class="jsInline">
                <div class="jsDivwMinHg" style="text-align: right;">
                    
                    
                    <div class="jstable-cell" style="display:inline-block;">

                        <?php

                            echo ($partic_away) ? ($partic_away->getName(true)) : '';
        ?>
                    </div>
                    <div class="jstable-cell" style="display:inline-block;">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                </div>
                <?php 
                if($rows->lists['m_events_display'] == 1){
                ?>
                <table class="jsTblMatchTab">
                    <thead>
                        <tr>
                            <th><?php echo __('Time','joomsport-sports-league-results-management');
        ?></th>
                            <th><?php echo __('Event','joomsport-sports-league-results-management');
        ?></th>
                            <th><?php echo __('Quantity','joomsport-sports-league-results-management');
        ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($intP = 0; $intP < count($rows->lists['m_events_away']); ++$intP) {
                        ?>
                        <tr class="jsMatchTRevents">
                            <td>
                                <?php 
                                if($rows->lists['m_events_away'][$intP]->minutes_input){
                                    echo $rows->lists['m_events_away'][$intP]->minutes_input;
                                    if(strpos($rows->lists['m_events_away'][$intP]->minutes_input,':') === false){
                                        echo "'";
                                    }
                                }else{
                                    echo $rows->lists['m_events_away'][$intP]->minutes ? $rows->lists['m_events_away'][$intP]->minutes."'" : '';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->objEvent->getEmblem(false);
                        ?>
                            </td>
                            <td>
                                <?php echo $rows->lists['m_events_away'][$intP]->ecount;
                        ?>
                            </td>
                            <td class="evPlayerName">
                                <?php echo $rows->lists['m_events_away'][$intP]->obj->getName(true);
                        ?>
                            </td>
                            
                        </tr>    
                        <?php

                    }
        if (!count($rows->lists['m_events_away'])) {
            //echo "&nbsp";
        }
        ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
        if($rows->lists['m_events_display'] == 0){
            
        ?>
        <table class="jsTblVerticalTimeLine table table-striped">
            <tbody>
                <?php 
                for($intE=0;$intE<count($rows->lists['m_events_all']);$intE++){
                ?>
                <tr>
                    <td>
                        <?php 
                        if($partic_home->object->ID == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->obj->getName(true);
                        }else{
                            echo '&nbsp;';
                        }
                        
                        ?>
                    </td>
                    <td>
                        <?php 
                        if($partic_home->object->ID == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->objEvent->getEmblem(false);
                        }else{
                            echo '&nbsp;';
                        }
                        
                        ?>
                    </td>
                    <td>
                        <?php 
                            if($rows->lists['m_events_all'][$intE]->minutes_input){
                                echo $rows->lists['m_events_all'][$intE]->minutes_input;
                                if(strpos($rows->lists['m_events_all'][$intE]->minutes_input,':') === false){
                                    echo "'";
                                }
                            }else{
                                echo $rows->lists['m_events_all'][$intE]->minutes ? $rows->lists['m_events_all'][$intE]->minutes."'" : '';
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                        if($partic_away->object->ID == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->objEvent->getEmblem(false);
                        }else{
                            echo '&nbsp;';
                        }
                        
                        ?>
                        
                    </td>
                    <td>
                        <?php 
                        if($partic_away->object->ID == $rows->lists['m_events_all'][$intE]->t_id){
                            echo $rows->lists['m_events_all'][$intE]->obj->getName(true);
                        }else{
                            echo '&nbsp;';
                        }
                        
                        ?>
                        
                    </td>
                </tr>
                
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php
        }
    }
    ?>
    <?php
    if (count($rows->lists['team_events'])) {
        ?>
    <div class="center-block jscenter jsMarginBottom30">
        <h3 class="jsInlineBlock"><?php echo __('Match statistic','joomsport-sports-league-results-management');
        ?></h3>
    </div>
    <div class="jsPaddingBottom30 jsTeamStat">
        <div class="jsOverflowHidden">
            <div class="jsInline">
                <div>
                    
                    <div class="jstable-cell ">
                    <?php echo $partic_home ? ($partic_home->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                    <div class="jstable-cell ">

                        <?php

                            echo ($partic_home) ? ($partic_home->getName(true)) : '';
        ?>
                    </div>
                </div>
            </div> 
            <div class="jsInline">
                <div style="text-align: right;">
                    
                    
                    <div class="jstable-cell" style="display:inline-block;">

                        <?php

                            echo ($partic_away) ? ($partic_away->getName(true)) : '';
        ?>
                    </div>
                    <div class="jstable-cell" style="display:inline-block;">
                    <?php echo $partic_away ? ($partic_away->getEmblem(true, 0, 'emblInline', $width)) : '';
        ?>
                    </div>
                </div>
            </div>
            <div class="jstable">
                <?php
                for ($intP = 0; $intP < count($rows->lists['team_events']); ++$intP) {
                    $graph_sum = $rows->lists['team_events'][$intP]->home_value + $rows->lists['team_events'][$intP]->away_value;
                    $graph_home_class = ' jsGray';
                    $graph_away_class = ' jsRed';
                    if ($graph_sum) {
                        $graph_home = round(100 * $rows->lists['team_events'][$intP]->home_value / $graph_sum);
                        $graph_away = round(100 * $rows->lists['team_events'][$intP]->away_value / $graph_sum);
                        if ($graph_home > $graph_away) {
                            //$graph_home_class = ' jsRed';
                        } else {
                            //$graph_away_class = ' jsRed';
                        }
                    }
                    if (!$graph_home) {
                        $graph_home_class = '';
                    }
                    if (!$graph_away) {
                        $graph_away_class = '';
                    }
                    ?>
                    <div class="jstable-row jsColTeamEvents">
                        
                        <div class="jstable-cell jsCol5">
                            <div class="teamEventGraph">
                                <div class="teamEventGraphHome<?php echo $graph_home_class?>" style="width:<?php echo $graph_home?>%"><?php echo $rows->lists['team_events'][$intP]->home_value;
                    ?></div>
                            </div>
                            
                        </div>
                        <div class="jstable-cell jsCol6">

                            <div>
                                <?php 
                                echo $rows->lists['team_events'][$intP]->objEvent->getEmblem();
                                echo $rows->lists['team_events'][$intP]->objEvent->getEventName();
                    ?>
                            </div>
 
                        </div>
                        <div class="jstable-cell jsCol5">
                            <div class="teamEventGraph">
                                <div class="teamEventGraphAway<?php echo $graph_away_class?>" style="width:<?php echo $graph_away?>%"><?php echo $rows->lists['team_events'][$intP]->away_value;
                    ?></div>
                            </div>
                            
                        </div>
                        

                    </div>    
                    <?php

                }
        ?>
            </div>
            
        </div>
    </div>
    <?php

    }
    ?>
    <?php

    if (jsHelper::getADF($rows->lists['ef'])) {
        ?>
        <div class="center-block jscenter">
            <h3><?php echo __('Additional information','joomsport-sports-league-results-management');
        ?></h3>
        </div>
        <div class="matchExtraFields jsPaddingBottom30">
            <?php
            $ef = $rows->lists['ef'];
        if (count($ef)) {
            foreach ($ef as $key => $value) {
                if ($value != null) {
                    echo '<div class="JSplaceM">';
                    echo  '<div class="labelEFM">'.$key.'</div>';
                    echo  '<div class="valueEFM">'.$value.'</div>';
                    echo  '</div>';
                }
            }
        }
        ?>
        </div>
    <?php

    }
    ?>
</div>