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
            for ($intA = 0; $intA < count($rows); ++$intA) {
                ?>
            <tr>
                <td>
                    <?php echo classJsportLink::tournament($rows[$intA]->name, $rows[$intA]->id);
                ?>
                </td>
            </tr>
            <?php

            }
            ?>
        </tbody>
    </table>
</div>
