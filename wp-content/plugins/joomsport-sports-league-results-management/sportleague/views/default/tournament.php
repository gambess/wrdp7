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
<div class="jsOverflowHidden" style="padding:0px 15px;">
    <?php
    /*if ($rows->logo) {
        ?>

            <div class="jsObjectPhoto rmpadd">

                    <?php //echo jsHelperImages::getEmblemBig($rows->logo, 1, 'emblInline', '150', false);
        ?>



            </div>    

        <?php

    }*/
    ?>
    <div class="rmpadd" style="padding-right:0px;padding-left:15px;">
        <?php echo $rows->description;?>
    </div>
</div>    
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>
                    <?php echo __('Name','joomsport-sports-league-results-management');?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($intA = 0; $intA < count($lists['slist']); ++$intA) {

                ?>
            <tr>
                <td>
                    <?php echo classJsportLink::season($lists['slist'][$intA]->post_title, $lists['slist'][$intA]->ID);
                ?>
                </td>
            </tr>
            <?php

            }
            ?>
        </tbody>
    </table>
</div>
