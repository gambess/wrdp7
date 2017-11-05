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
    if (count($rows->lists['players_Stat'])) {
        ?>
    <form role="form" method="post" lpformnum="1">
    <table class="table table-striped cansorttbl" id="jstable_plz">
        <thead>
            <tr>
                
                    <?php
                    $dest = (classJsportRequest::get('sortf') == 'post_title') ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
        $class = '';
        if (classJsportRequest::get('sortf') == 'post_title' || classJsportRequest::get('sortf') == '') {
            $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
        }
        ?>
                <th class="<?php echo $class?>">
                <?php 
                    if (isset($lists['pagination']) && $lists['pagination']) {
                        ?>
                    <a href="<?php echo classJsportLink::playerlist($rows->season_id, '&sortf=post_title&sortd='.$dest)?>"><span><?php echo __('Name','joomsport-sports-league-results-management');
                        ?></span><i class="fa"></i></a>

                    <?php

                    } else {
                        ?>
                    <a href="javascript:void(0);">
                    <span><?php echo __('Name','joomsport-sports-league-results-management');
                        ?></span><i class="fa"></i>
                    </a>
                    <?php

                    }
        ?>
                </th>

                <?php
                if (isset($rows->lists['played_matches_col']) && $rows->lists['played_matches_col']) {
                    $dest = (classJsportRequest::get('sortf') == 'played') ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                    $class = '';
                    if (classJsportRequest::get('sortf') == 'played') {
                        $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                    }
                    ?>
                    <th class="jsTextAlignCenter <?php echo $class?>">
                        <?php
                        if (isset($lists['pagination']) && $lists['pagination']) {
                            ?>
                        <a href="<?php echo classJsportLink::playerlist($rows->season_id, '&sortf=played&sortd='.$dest)?>"><span><?php echo $rows->lists['played_matches_col'];
                            ?></span><i class="fa"></i></a>

                        <?php

                        } else {
                            ?>
                        <a href="javascript:void(0);">
                                    
                            <span><?php echo $rows->lists['played_matches_col'];
                            ?></span><i class="fa"></i>
                        </a>

                        <?php

                        }
                    ?>
                        
                    </th>

                    <?php

                }

        if (count($rows->lists['events_col'])) {
            foreach ($rows->lists['events_col'] as $key => $value) {
                $dest = (classJsportRequest::get('sortf') == $key) ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                $class = '';
                if (classJsportRequest::get('sortf') == $key) {
                    $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                }
                ?>
                        <th class="jsTextAlignCenter <?php echo $class?>">
                            <?php
                            if (isset($lists['pagination']) && $lists['pagination']) {
                                ?>
                            <a href="<?php echo classJsportLink::playerlist($rows->season_id, '&sortf='.$key.'&sortd='.$dest)?>">
                                <span>
                                    <?php echo $value->getEmblem();
                                ?>
                                    <?php echo $value->getEventName();
                                ?>
                                </span>
                                <i class="fa"></i>
                            </a>
                            <?php

                            } else {
                                ?>
                            <a href="javascript:void(0);">
                                <span>
                                    <?php echo $value->getEmblem();
                                ?>
                                    <?php echo $value->getEventName();
                                ?>
                                </span>
                                <i class="fa"></i>
                            </a>    
                            <?php

                            }
                ?>
                        </th>
                        <?php

            }
        }
        if (count($rows->lists['ef_table'])) {
            foreach ($rows->lists['ef_table'] as $ef) {
                $key = 'ef_'.$ef->id;
                $value = $ef->name;
                $dest = (classJsportRequest::get('sortf') == $key) ? (classJsportRequest::get('sortd') == 'DESC' ? 'ASC' : 'DESC') : 'DESC';
                $class = '';
                if (classJsportRequest::get('sortf') == $key) {
                    $class = (classJsportRequest::get('sortd') == 'DESC') ? 'headerSortDown' : 'headerSortUp';
                }
                ?>
                        <th class="jsTextAlignCenter <?php echo $class?>">
                            <span><?php echo $value;
                ?></span>
                        </th>
                    <?php

            }
        }
        ?>
            </tr>
        </thead>
        <tbody>
        <?php

        for ($intA = 0; $intA < count($rows->lists['players_Stat']); ++$intA) {
            $playerST = $rows->lists['players_Stat'][$intA];
            
            $playerevents = $playerST->lists['tblevents'];
            ?>

            <tr>
                <td>
                    <div class="jsDivLineEmbl">
                        <?php echo $playerST->getEmblem(true, 0, '');
            ?>
                        <?php echo jsHelper::nameHTML($playerST->getName(true));
            ?>


                    </div>

                </td>
                <?php
                if (isset($rows->lists['played_matches_col']) && $rows->lists['played_matches_col']) {
                    ?>
                    <td class="jsTextAlignCenter">
                        <?php
                        echo $playerST->played_matches;
                    ?>
                    </td>
                    <?php

                }
            ?>
                <?php
                
                if (count($rows->lists['events_col'])) {
                    foreach ($rows->lists['events_col'] as $key => $value) {
                        ?>
                        <td class="jsTextAlignCenter">
                            <?php
                            if (isset($playerevents->{$key})) {
                                
                                if (is_float(floatval($playerevents->{$key}))) {
                                    echo round($playerevents->{$key}, 3);
                                } else {
                                    echo floatval($playerevents->{$key});
                                }
                            }
                        ?>
                            
                        </td>
                        <?php

                    }
                }
            ?>
                <?php
                if (count($rows->lists['ef_table'])) {
                    foreach ($rows->lists['ef_table'] as $ef) {
                        $key = 'ef_'.$ef->id;
                        $value = $ef->name;
                        ?>
                        <td class="jsTextAlignCenter">
                            <?php
                            if (isset($playerST->{$key})) {
                                echo $playerST->{$key};
                            }
                        ?>
                            
                        </td>
                        <?php

                    }
                }
            ?>
            </tr>
            <?php

        }
        ?>
        </tbody>
    </table>  
        
    
<?php
if (isset($lists['pagination']) && $lists['pagination']) {
    require_once JOOMSPORT_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'pagination.php';
    echo paginationView($lists['pagination']);
} else {
    ?>
<script>
    jQuery(document).ready(function() {
        jQuery('#jstable_plz').tablesorter();
    } );
</script> 
<?php 
}
        ?>
</form>
    <?php

    }
    ?>
</div>
