<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $jsConfig;
?>
<div class="table-responsive" id="jsPlayerListContainer">
    <div class="jsOverflowHidden">
    <?php
    if (count($rows->lists['players'])) {
        foreach ($rows->lists['players'] as $key => $value) {
            
            if($key != '0' && count($value)){
                echo '<div class="jsGroupedPlayersHeader"><h2>'.$key.'</h2></div>';
            }
            for ($intA = 0; $intA < count($value); ++$intA) {
                
                $player = $value[$intA];
               
                ?>
                
                <div class="jsplayerCart">
                    <?php
                     if($jsConfig->get('enbl_playerlogolinks',1) == '1' || $jsConfig->get('enbl_playerlinks',1) == '1'){
                        $link = classJsportLink::player('', $player->object->ID, $player->season_id, true); 

                        echo '<a href="'.$link.'">';
                     }
                     ?>
                    <div class="jsplayerCartInner">
                        <div class="imgPlayerCart">
                            <div class="innerjsplayerCart">
                                <?php echo $player->getEmblem(false, 10, 'emblInline', null, false);
                    ?>
                            </div>
                            <?php
                            /*if (count($rows->lists['ef_table'])) {
                                echo '<div class="jsPlPhListEF">';
                                foreach ($rows->lists['ef_table'] as $ef) {
                                    $keyEF = 'ef_'.$ef->id;
                                    $valueEF = $ef->name;
                                    echo '<div class="jsPlPhListEFChild">';
                                        echo '<div>';
                                        echo $valueEF;
                                        echo '</div>';
                                        echo '<div>';
                                        echo $player->{$keyEF};
                                        echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                            }*/
                            ?>
                        </div>
                        <div class="namePlayerCart">
                            <div class="LeftnamePlayerCart">
                                <div class="PlayerCardFIO">
                                    <?php echo jsHelper::nameHTML($player->getName(false));?>
                                </div>    
                                <?php if($rows->lists['playercardef']){?>
                                <div class="PlayerCardPos">
                                    <?php
                                    if(isset($player->{'ef_'.$rows->lists['playercardef']})){
                                        echo $player->{'ef_'.$rows->lists['playercardef']};
                                    }
                                    ?>
                                </div>
                                <?php } ?>
                            </div>   
                            <?php
                            if($rows->lists['playerfieldnumber']){
                            ?>
                            <div  class="PlayerCardPlNumber">
                                <?php
                                if(isset($player->{'ef_'.$rows->lists['playerfieldnumber']})){
                                    echo $player->{'ef_'.$rows->lists['playerfieldnumber']};
                                }
                                ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    if($jsConfig->get('enbl_playerlogolinks',1) == '1' || $jsConfig->get('enbl_playerlinks',1) == '1'){
                
                        echo '</a>';
                    }
                    ?>
                </div>    
                
                <?php
                

            }
        }
    }
    if(isset($rows->lists['team_staff']) && count($rows->lists['team_staff'])){
        for ($intS=0;$intS<count($rows->lists['team_staff']);$intS++) {
            $Ostaff = $rows->lists['team_staff'][$intS];
            echo '<div class="jsGroupedPlayersHeader"><h2>'.$Ostaff["name"].'</h2></div>';
            $obj = $Ostaff["obj"];
            ?>
                <div class="jsplayerCart">
                    <?php
                    if($jsConfig->get('enbl_playerlogolinks',1) == '1' || $jsConfig->get('enbl_playerlinks',1) == '1'){
                     
                        $link = classJsportLink::person('', $obj->object->ID, 0, true); 

                        echo '<a href="'.$link.'">';
                    }
                    ?>
                    <div class="jsplayerCartInner">
                        <div class="imgPlayerCart">
                            <div class="innerjsplayerCart">
                                <?php echo $obj->getEmblem(false, 10, 'emblInline', null, false);?>
                            </div>
                        </div>
                        <div class="namePlayerCart">
                            <div class="LeftnamePlayerCart">
                                <div class="PlayerCardFIO">
                                    <?php echo jsHelper::nameHTML($obj->getName(false));?>
                                </div>    

                            </div>   

                        </div>
                    </div>  
                    <?php
                    if($jsConfig->get('enbl_playerlogolinks',1) == '1' || $jsConfig->get('enbl_playerlinks',1) == '1'){
                     ?>
                        </a>
                    <?php } ?>    
                </div>
            <?php                
        }
    }
    ?>
    </div>
</div>
