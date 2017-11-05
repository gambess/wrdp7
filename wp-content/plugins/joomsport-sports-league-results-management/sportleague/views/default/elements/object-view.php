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
<div class="row">    
    <div class="col-xs-12 rmpadd" style="padding-right:0px;">
        <div class="jsObjectPhoto rmpadd">
            <div class="photoPlayer">

                    <?php echo jsHelperImages::getEmblemBig($rows->getDefaultPhoto());?>

                    

            </div>    
        </div>
        <?php
        $class = '';
        $extra_fields = jsHelper::getADF($rows->lists['ef']);
        if ($extra_fields) {
            $class = 'well well-sm';
        } else {
            ?>
            <div class="rmpadd" style="padding-right:0px;padding-left:15px;">
                <?php echo $rows->getDescription();
            ?>
            </div>
            <?php

        }
        ?>
        <div class="<?php echo $class;?> pt10 extrafldcn">
            <?php

                echo $extra_fields;
            ?>
        </div>
    </div>
    <?php if ($extra_fields) {
    ?>
    <div class="col-xs-12 rmpadd" style="padding-right:0px;">
        <?php echo $rows->getDescription();
    ?>
    </div>
    <?php 
} ?>
</div>    