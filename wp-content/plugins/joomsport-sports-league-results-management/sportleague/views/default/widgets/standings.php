<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
$intM = 0;
?>
<div id="joomsport-container" class="jsmodtbl_responsive">
<?php

if(isset($row->lists['columnsCell'])){
    foreach ($row->lists['columnsCell'] as $group => $vals )
    { 

        $intM ++;
        if($group){
            echo '<h2 class="groups">'.$group.'</h2>';
        }
        if(count($vals)){
            ?>
            <div class="table-responsive">
                <table class="table table-striped cansorttbl" id="jstable_<?php echo $intM;?>">
                    <thead>
                        <tr>
                            <th width="5%" class="jsalcenter jsNoWrap">

                                    <?php echo __("Rank",'joomsport-sports-league-results-management');?>

                            </th>

                            <th class="jsNoWrap jsalignleft">

                                    <?php echo $single?__("Players",'joomsport-sports-league-results-management'):__("Teams",'joomsport-sports-league-results-management');?>

                            </th>
                            <?php
                            
                            if(count($row->lists['columns']))
                            foreach($row->lists['columns'] as $key => $value){
                                if(in_array($key, $columns_list)){
                                    if($key != 'emblem_chk'){
                                       if($key != 'curform_chk'){
                                    ?>
                                        <th class="jsalcenter jsNoWrap" width="5%">
                                            <span jsattr-short="<?php echo $row->lists['available_options'][$key]['short']?>" jsattr-full="<?php echo $row->lists['available_options'][$key]['label']?>">
                                            
                                                <?php echo $row->lists['available_options'][$key]['label'];?>
                                            </span>
                                        </th>
                                    <?php
                                       }else{
                                            ?>
                                                <th class="noSort jsNoWrap jsalcenter">
                                                    <span jsattr-short="<?php echo $row->lists['available_options'][$key]['short']?>" jsattr-full="<?php echo $row->lists['available_options'][$key]['label']?>">
                                            
                                                        <?php echo $row->lists['available_options'][$key]['label'];?>
                                                    </span>
                                                </th>
                                            <?php 
                                       }
                                    }
                                }
                            }
                            ?>

                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $rank = 1;
                    foreach($vals as $val){
                        $options = json_decode($val->options, true);
                        if(($yteam_id && $yteam_id == $options['id']) || !$yteam_id){

                        if($place_display == '0' || $place_display >= $rank){



                        $partObj = $row->getPartById($options['id']);
                        $colortbl = '';

                        if (isset($row->lists['tblcolors'][$rank])) {
                            $colortbl = $row->lists['tblcolors'][$rank];
                        }

                        $coloryteam = $partObj->getYourTeam();

                        ?>
                        <tr <?php echo $coloryteam?'style="background-color:'.$coloryteam.'!important"':'';?>>
                            <td class="jsalcenter" 

                            <?php 
                            if (is_rtl()) {
                                 echo $colortbl?'style="box-shadow: inset -5px 0 0 0 '.$colortbl.'"':"";
                            } else {
                                echo $colortbl?'style="box-shadow: inset 5px 0 0 0 '.$colortbl.'"':"";
                            }
                            ?>><?php echo $rank;?></td>

                            <td class="jsNoWrap jsalignleft">
                                <?php 
                                
                                if(isset($row->lists['columns']['emblem_chk']) && in_array('emblem_chk', $columns_list)){
                                    echo ($partObj->getEmblem(true, 0, 'emblInline', 0));
                                }
                                echo $partObj->getName(true);
                                ?>
                            </td>
                            <?php
                            if(count($row->lists['columns']))
                            foreach($row->lists['columns'] as $key => $value){
                                if(in_array($key, $columns_list)){
                                    if($key != 'emblem_chk'){
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

                        </tr>
                        <?php
                        }
                        }
                        $rank ++;
                    }
                    ?>
                    </tbody>    
                </table>  
            </div>

            <?php
        }
    }
    ?>

    <?php
}
?>
</div>