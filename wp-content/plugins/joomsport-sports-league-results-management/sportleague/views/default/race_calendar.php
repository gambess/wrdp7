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
    
    <div>
        <?php

        for ($intAAA = 0; $intAAA < count($rows); ++$intAAA) {
            ?>
            <div>
                <?php echo classJsportLink::matchday($rows[$intAAA]->m_name, $rows[$intAAA]->id);
            ?>
                                 
            </div>
            <?php

        }
        ?>
    </div>
    
    <?php
    //var_dump($rows);
    ?>
    
    
    
</div>
