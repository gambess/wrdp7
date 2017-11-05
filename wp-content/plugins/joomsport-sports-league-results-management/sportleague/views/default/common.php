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

        <div>
            <?php //echo $rows->getName(false);?>
        </div>
        <div>
            <?php
                //var_dump($rows);
                $tabs = $rows->getTabs();
                jsHelperTabs::draw($tabs, $rows);
            ?>
        </div>
    </div>

    
    
    
</div>
