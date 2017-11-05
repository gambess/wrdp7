<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
?>
<div id="joomsport-container">
    <table class="jstblevwid">

    <?php
    if(count($players['list'])){
        ?>
        <tr>
            <?php
            if($args['photo']){
                echo '<th></th>';
            }
            ?>
            
            <th class="jsalignleft"><?php echo __('Player','joomsport-sports-league-results-management');?></th>
            
            <?php if($args['teamname']){?>
            <th><?php echo __('Team','joomsport-sports-league-results-management');?></th>
            <?php } ?>
            <th class="jsaligncenter"><?php echo $eventObj->getEventName();?></th>
        </tr>
        <?php
        foreach ($players['list'] as $player){
            echo '<tr>';
            $playerObj = new classJsportPlayer($player->player_id,$args['seasonid']);
            if($args['photo']){
                echo '<td>' . $playerObj->getEmblem(true, 0, '') . '</td>';
            }
            
            echo '<td>' .jsHelper::nameHTML($playerObj->getName(true)) . '</td>';
            if($args['teamname']){
                $teamObj = new classJsportTeam($player->team_id,$args['seasonid']);
                echo '<td>' . jsHelper::nameHTML($teamObj->getName(true)) . '</td>';
            }
            echo '<td class="jsaligncenter jspaddinleft10">' .$player->{$eventid} .'</td>';
            echo '</tr>';
        }
    }
    ?>
    </table>    
</div>