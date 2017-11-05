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
<?php
$sho = $rows->hideTable();

$row = $rows->season;
$intM = 0;
if(!$sho || !isset($row->lists['knockout'])){
if (isset($row->lists['columnsCell'])) {
    foreach ($row->lists['columnsCell'] as $group => $vals) {
        ++$intM;
        if ($group) {
            echo '<h2 class="groups">'.$group.'</h2>';
        }
        if (count($vals)) {
            ?>
            <div class="table-responsive">
                <table class="table table-striped cansorttbl" id="jstable_<?php echo $intM;
            ?>">
                    <thead>
                        <tr>
                            <th class="jsalcenter jsNoWrap jsCell5perc">
                                <a href="javascript:void(0);">
                                    <?php echo __('Rank','joomsport-sports-league-results-management');
            ?> <i class="fa"></i>
                                </a>
                            </th>
                            
                            <th class="jsNoWrap jsalignleft">
                                <a href="javascript:void(0);">
                                    <?php echo $rows->getSingle()?__('Participants','joomsport-sports-league-results-management'):__('Teams','joomsport-sports-league-results-management');
            ?> <i class="fa"></i>
                                </a>
                            </th>
                            <?php
                            if (count($row->lists['columns'])) {
                                foreach ($row->lists['columns'] as $key => $value) {
                                    if ($key != 'emblem_chk') {
                                        if ($key != 'curform_chk') {
                                            ?>
                                    <th class="jsalcenter jsNoWrap jsCell5perc">
                                        <a href="javascript:void(0);">
                                            <span jsattr-short="<?php echo (isset($row->lists['available_options_short'][$key]))?$row->lists['available_options_short'][$key]:$row->lists['available_options'][$key]['short']?>" jsattr-full="<?php echo $row->lists['available_options'][$key]['label']?>">
                                            <?php echo $row->lists['available_options'][$key]['label'];
                                            ?> 
                                            </span>
                                            <i class="fa"></i>
                                        </a>
                                    </th>
                                <?php

                                        } else {
                                            ?>
                                            <th class="noSort jsalcenter jsNoWrap" width="135">
                                                <span jsattr-short="<?php echo $row->lists['available_options'][$key]['short']?>" jsattr-full="<?php echo $row->lists['available_options'][$key]['label']?>">
                                            
                                                    <?php echo $row->lists['available_options'][$key]['label'];
                                            ?>
                                                </span>
                                                
                                            </th>
                                        <?php 
                                        }
                                    }
                                }
                            }
            ?>
                            <?php
                            if (count($row->lists['ef_table'])) {
                                foreach ($row->lists['ef_table'] as $ef) {
                                    ?>
                                        <th nowrap>
                                            <a href="javascript:void(0);">
                                                <?php echo $ef->name;
                                    ?> <i class="fa"></i>
                                            </a>
                                        </th>
                                    <?php

                                }
                            }
            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $rank = 1;
            foreach ($vals as $val) {
                $options = json_decode($val->options, true);

                $partObj = $row->getPartById($options['id']);
                $colortbl = '';

                if (isset($row->lists['tblcolors'][$rank])) {
                    $colortbl = $row->lists['tblcolors'][$rank];
                }

                $coloryteam = $partObj->getYourTeam();

                ?>
                        <tr <?php echo $coloryteam ? 'style="background-color:'.$coloryteam.'!important"' : '';
                ?>>
                            <td class="jsalcenter" <?php 
                            if (is_rtl()) {
                                 echo $colortbl?'style="box-shadow: inset -5px 0 0 0 '.$colortbl.'"':"";
                            } else {
                                echo $colortbl?'style="box-shadow: inset 5px 0 0 0 '.$colortbl.'"':"";
                            }
                            ?>><?php echo $rank;
                ?></td>
                            
                            <td class="jsNoWrap jsalignleft">
                                <?php 
                                if (isset($row->lists['columns']['emblem_chk'])) {
                                    echo $partObj->getEmblem();
                                }
                echo $partObj->getName(true);
                ?>
                            </td>
                            <?php
                            if (count($row->lists['columns'])) {
                                foreach ($row->lists['columns'] as $key => $value) {
                                    if ($key != 'emblem_chk') {
                                        if ($key != 'curform_chk') {
                                            ?>
                                    <td class="jsalcenter jsNoWrap">
                                        <?php echo isset($options[$key]) ? $options[$key] : '';
                                            ?>
                                    </td>
                                <?php

                                        } else {
                                            ?>
                                    <td class="jsalcenter jsNoWrap">
                                        <?php echo isset($val->$key) ? $val->$key : '';
                                            ?>
                                    </td>
                                        <?php

                                        }
                                    }
                                }
                            }
                ?>
                            <?php
                            if (count($row->lists['ef_table'])) {
                                foreach ($row->lists['ef_table'] as $ef) {
                                    $efid = 'ef_'.$ef->id;
                                    ?>
                                    <td class="jsNoWrap jsalignleft">
                                            <?php echo $val->{$efid};
                                    ?>
                                        </td>
                                    <?php

                                }
                            }
                ?>
                        </tr>
                        <?php
                        ++$rank;
            }
            ?>
                    </tbody>    
                </table>  
            </div>

<script>
    jQuery(document).ready(function() {
        var theHeaders = {}
        jQuery('#jstable_<?php echo $intM;
            ?>').find('th.noSort').each(function(i,el){
            theHeaders[jQuery(this).index()] = { sorter: false };
        });
        jQuery('#jstable_<?php echo $intM;
            ?>').tablesorter({headers: theHeaders});
    } );
    
</script>    
            <?php

        }
    }
    ?>
    <div class="matchExtraFields">
        <?php 
        if($rows->lists['bonuses']){
            echo __('Bonuses','joomsport-sports-league-results-management');
            echo $rows->lists['bonuses'];
        }
        
        ?>
    </div>
    <div class="matchExtraFields">
        <?php 

        if($rows->lists['legend']){
            foreach($rows->lists['legend'] as $legend){
                if($legend['legend']){
                    echo '<div class="jstbl_legend">';
                    echo '<div style="background-color:'.$legend['color'].'">&nbsp;</div>';
                    echo '<div>'.$legend['legend'].'</div>';
                    echo '</div>';
                }
            }
        }
        
        ?>
    </div>
    <?php
}
}
if(isset($row->lists['knockout'])){
    for ($intK = 0; $intK < count($row->lists['knockout']); ++$intK) {
        ?>
        <div>
            <?php echo $row->lists['knockout'][$intK];
        ?>
        </div>
        <?php

    }
}

?>
