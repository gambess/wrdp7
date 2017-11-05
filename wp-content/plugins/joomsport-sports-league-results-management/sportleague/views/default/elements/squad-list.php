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
<div>
    <div class="center-block jscenter">
        <h3 class="solid"><?php echo __('Line up','joomsport-sports-league-results-management');?></h3>
    </div>
    <div class="jsOverflowHidden">
        <div class="jstable jsInline">
            <?php
            for ($intP = 0; $intP < count($rows->lists['squard1']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <?php
                    if(property_exists($rows->lists['squard1'][$intP],'efFirst')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard1'][$intP]->efFirst;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell width5prc" >
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard1'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard1'][$intP]->obj->getName(true);
                ?>
                    </div>
                    <?php
                    if(property_exists($rows->lists['squard1'][$intP],'efLast')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard1'][$intP]->efLast;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard1'][$intP]->is_subs == '1') {
                            $cimg = $rows->lists['squard1'][$intP]->player_subs?'out-new.png':'out-raw.png';
                            echo '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.$cimg.'" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard1'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard1'][$intP]->minutes."'";
                            }
                        }
                ?>
                    </div>
                </div>    
                <?php

            }
            if (!count($rows->lists['squard1'])) {
                echo '&nbsp;';
            }
            ?>
        </div>
        <div  class="jstable jsInline">
            <?php
            
            for ($intP = 0; $intP < count($rows->lists['squard2']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <?php
                    if(property_exists($rows->lists['squard2'][$intP],'efFirst')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard2'][$intP]->efFirst;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell width5prc">
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard2'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard2'][$intP]->obj->getName(true);
                ?>
                    </div> 
                    <?php
                    if(property_exists($rows->lists['squard2'][$intP],'efLast')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard2'][$intP]->efLast;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard2'][$intP]->is_subs == '1') {
                            $cimg = $rows->lists['squard2'][$intP]->player_subs?'out-new.png':'out-raw.png';
                            
                            echo '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.$cimg.'" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard2'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard2'][$intP]->minutes."'";
                            }
                        }
                ?>
                    </div>
                </div>    
                <?php

            }
            ?>
        </div>
    </div>
</div>
<?php if (count($rows->lists['squard1_res']) || count($rows->lists['squard2_res'])) {
    ?>
<div>
    <div class="center-block jscenter">
        <h3 class="solid"><?php echo __('Substitutes','joomsport-sports-league-results-management');
    ?></h3>
    </div>    
    <div class="jsOverflowHidden">
        <div class="jstable jsInline">
            <?php
            for ($intP = 0; $intP < count($rows->lists['squard1_res']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <?php
                    if(property_exists($rows->lists['squard1_res'][$intP],'efFirst')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard1_res'][$intP]->efFirst;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell width5prc">
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard1_res'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard1_res'][$intP]->obj->getName(true);
                ?>
                    </div>
                    <?php
                    if(property_exists($rows->lists['squard1_res'][$intP],'efLast')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard1_res'][$intP]->efLast;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard1_res'][$intP]->is_subs == -1) {
                            echo '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.'in-new.png" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard1_res'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard1_res'][$intP]->minutes."'";
                            }
                            $subsA = explode(',',$rows->lists['squard1_res'][$intP]->player_subsarray);
                            if(isset($subsA[1])){
                                $minA = explode(',',$rows->lists['squard1_res'][$intP]->minarray);
                                $cimg = $subsA[1]?'out-new.png':'out-raw.png';
                                echo '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.$cimg.'" class="sub-player-ico" title="" alt="" />';
                            
                                echo '&nbsp;'.$minA[1]."'";
                            }
                        }
                ?>
                    </div>
                </div>    
                <?php

            }
    if (!count($rows->lists['squard1_res'])) {
        echo '&nbsp;';
    }
    ?>
        </div>
        <div  class="jstable jsInline">
            <?php
            for ($intP = 0; $intP < count($rows->lists['squard2_res']); ++$intP) {
                ?>
                <div class="jstable-row">
                    <?php
                    if(property_exists($rows->lists['squard2_res'][$intP],'efFirst')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard2_res'][$intP]->efFirst;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell width5prc">
                        <?php echo jsHelperImages::getEmblem($rows->lists['squard2_res'][$intP]->obj->getDefaultPhoto(), 0, '');
                ?>
                    </div>
                    <div class="jstable-cell jsTextAlignLeft">
                        <?php echo $rows->lists['squard2_res'][$intP]->obj->getName(true);
                ?>
                    </div> 
                    <?php
                    if(property_exists($rows->lists['squard2_res'][$intP],'efLast')){
                       echo '<div class="jstable-cell">';
                       echo $rows->lists['squard2_res'][$intP]->efLast;
                       echo '</div>';
                    }
                    ?>
                    <div class="jstable-cell">
                        <?php

                        if ($rows->lists['squard2_res'][$intP]->is_subs == -1) {
                            echo '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.'in-new.png" class="sub-player-ico" title="" alt="" />';
                            if ($rows->lists['squard2_res'][$intP]->minutes) {
                                echo '&nbsp;'.$rows->lists['squard2_res'][$intP]->minutes."'";
                            }
                            $subsA = explode(',',$rows->lists['squard2_res'][$intP]->player_subsarray);
                            if(isset($subsA[1])){
                                $minA = explode(',',$rows->lists['squard2_res'][$intP]->minarray);
                                $cimg = $subsA[1]?'out-new.png':'out-raw.png';
                                echo '<img src="'.JOOMSPORT_LIVE_URL_IMAGES_DEF.$cimg.'" class="sub-player-ico" title="" alt="" />';
                            
                                echo '&nbsp;'.$minA[1]."'";
                            }
                        }
                ?>
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