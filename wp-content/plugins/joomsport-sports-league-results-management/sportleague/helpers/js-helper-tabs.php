<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class jsHelperTabs
{
    /*
     * $tabs array
     * $tabs['id'] - string
     * $tabs['title'] - string
     * $tabs['body'] - text
     */
    public static function draw($tabs, $rows)
    {
        if (count($tabs)) {
            $jscurtab = classJsportRequest::get('jscurtab');
            if ($jscurtab && substr($jscurtab, 0, 1) != '#') {
                $jscurtab = '#'.$jscurtab;
            }
            ?>
        <div class="tabs">    
            <?php
            if (count($tabs) > 1) {
                ?>
            
            <ul class="nav nav-tabs">
              <?php
              $is_isset_tab = false;
                for ($intA = 0; $intA < count($tabs); ++$intA) {
                    if ($jscurtab == '#'.$tabs[$intA]['id']) {
                        $is_isset_tab = true;
                    }
                }
                if (!$is_isset_tab) {
                    $jscurtab = '';
                }
                for ($intA = 0; $intA < count($tabs); ++$intA) {
                    $tab_ico = isset($tabs[$intA]['ico']) ? $tabs[$intA]['ico'] : tableS;
                    ?>
                <li <?php echo (($intA == 0 && !$jscurtab) || ($jscurtab == '#'.$tabs[$intA]['id'])) ? 'class="active"' : '';
                    ?>><a data-toggle="tab" href="#<?php echo $tabs[$intA]['id'];
                    ?>"><i class="<?php echo $tab_ico;
                    ?>"></i> <span><?php echo $tabs[$intA]['title'];
                    ?></span></a></li>
              <?php 
                }
                ?>
              
            </ul>
            <?php

            }
            ?>
            <div class="tab-content">
                <?php
                for ($intAi = 0; $intAi < count($tabs); ++$intAi) {
                    ?>
                    <div id="<?php echo $tabs[$intAi]['id'];
                    ?>" class="tab-pane fade<?php echo (($intAi == 0 && !$jscurtab) || ($jscurtab == '#'.$tabs[$intAi]['id'])) ? ' in active' : '';
                    ?>">
                        <?php if ($tabs[$intAi]['text']) {
    ?>
                            <p><?php echo $tabs[$intAi]['text'];
    ?></p>
                        <?php 
} elseif (is_file(JOOMSPORT_PATH_VIEWS_ELEMENTS.$tabs[$intAi]['body'])) {
    ?>
                            <?php require JOOMSPORT_PATH_VIEWS_ELEMENTS.$tabs[$intAi]['body'];
    ?>
                        <?php 
}
                    ?>
                    </div>
                <?php 
                }
            ?>
                
            </div>
        </div>
        <?php

        }
    }
}
