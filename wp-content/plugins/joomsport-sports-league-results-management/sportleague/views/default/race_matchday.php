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
    <div>
        {header}
    </div>
    <div>
        {tabs}
    </div>
    
    <?php 
    if (count($rows)) {
        for ($intQ = 0; $intQ < count($rows); ++$intQ) {
            ?>
            <div class="round_container">
   
                <div class="js_round_header_div">
                    <div>
                        <?php
                            echo '<h2 class="dotted">'.$rows[$intQ]->round_title.'</h2>';
            ?>
                    </div>    
                </div>
                <div class="js_round_main_div">
                    <table class="table">
                        <tr>
                            <th class="sort asc" axis="int">
                                <?php echo __('Participants','joomsport-sports-league-results-management');
            ?>
                            </th>
                            <?php
                            for ($intB = 0; $intB < intval($lists['options']->attempts); ++$intB) {
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo __('Attempts','joomsport-sports-league-results-management');
                                ?>&nbsp;<?php echo $intB + 1;
                                ?>
                                </th>
                                <?php

                            }
            ?>
                            <?php 
                            if ($lists['options']->penalty == 1) {
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo __('Penalty','joomsport-sports-league-results-management');
                                ?> (<?php echo $lists['options']->postfix;
                                ?>)
                                </th>

                                <?php

                            }
            ?>
                            <?php
                            for ($intB = 0; $intB < count($lists['extracol']); ++$intB) {
                                ?>
                                <th class="sort asc" axis="int">
                                    <?php echo $lists['extracol'][$intB]->name;
                                ?>
                                </th>
                                <?php

                            }
            ?>      
                            <th class="sort asc" axis="int">
                                <?php echo __('Results','joomsport-sports-league-results-management');
            ?> (<?php echo $lists['options']->postfix;
            ?>)
                            </th>

                        </tr>    
                        <?php
                        for ($intA = 0; $intA < count($rows[$intQ]->res); ++$intA) {
                            $objRes = $rows[$intQ]->res[$intA];
                            ?>
                                <tr <?php echo ($intA % 2) ? '' : 'class="gray"';
                            ?>>
                                    <td class="teams jsNoWrap">
                                        <?php echo $objRes->t_name?>
                                    </td>
                                    <?php
                                    $attempts = isset($objRes->attempts) ? $objRes->attempts : '';
                            $attempts_col = explode('|', $attempts);
                            for ($intB = 0; $intB < intval($lists['options']->attempts); ++$intB) {
                                ?>
                                        <td>
                                            <?php echo isset($attempts_col[$intB]) ? $attempts_col[$intB] : '';
                                ?>
                                        </td>
                                        <?php

                            }
                            ?>
                                    <?php 
                                    if ($lists['options']->penalty == 1) {
                                        ?>
                                        <td>
                                            <?php echo $objRes->penalty;
                                        ?>
                                        </td>

                                        <?php

                                    }
                                    //var_dump($lists['race']['rounds'][$index]);
                                    ?>
                                        <?php
                                    $ecol = isset($objRes->extracol) ? $objRes->extracol : '';
                            $ecol_col = explode('|', $ecol);
                            for ($intB = 0; $intB < count($lists['extracol']); ++$intB) {
                                ?>
                                        <td>
                                            <?php echo isset($ecol_col[$intB]) ? $ecol_col[$intB] : '';
                                ?>
                                        </td>
                                        <?php

                            }
                            ?>  
                                    <td class="js_div_round_result">
                                        <?php echo $objRes->result_string;
                            ?>
                                    </td>
                                </tr>

                            <?php

                        }
            ?>
                    </table>   

                </div>
            </div>
            <?php

        }
    }
    ?>
    
    <?php
    //var_dump($rows);
    ?>
    
    
    
</div>
