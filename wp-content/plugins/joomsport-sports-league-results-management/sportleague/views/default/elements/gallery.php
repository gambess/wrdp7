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
<div class="jsOverflowHidden">
    <ul>
        
        <?php
        if (count($rows->lists['photos'])) {
            foreach ($rows->lists['photos'] as $photo) {
                ?>
                <li class="col-xs-6 col-sm-3 col-md-3 col-lg-2">
                    <?php echo jsHelperImages::getEmblemBig($photo, 2, 'emblInline', 120);
                ?>
                </li>
                <?php

            }
        }
        ?>
    </ul>
</div>