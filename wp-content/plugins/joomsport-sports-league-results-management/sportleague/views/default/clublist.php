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
    <div class="jstable">
        <?php 

            for ($intA = 0; $intA < count($rows); ++$intA) {
                ?>

                <div class="jstable-row">
                    <div class="jstable-cell">
                        <div class="jsDivLineEmbl">
                            <?php
                            echo jsHelperImages::getEmblem($rows[$intA]->getDefaultPhoto(), 0, '');
                echo jsHelper::nameHTML($rows[$intA]->getName(true));
                ?>

                        </div>

                    </div>

                </div>
            <?php

            }
            ?>
    </div>

</div>
