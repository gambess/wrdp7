<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$term_list = wp_get_post_terms($rows->object->ID, 'joomsport_tournament', array("fields" => "all"));
$descr = '';
if(count($term_list)){
    $descr = $term_list[0]->description;
}
?>
<div class="seasonTable">
    <div class="jsOverflowHidden" style="padding:0px 15px;">

 

            <?php
            $class = '';
            $extra_fields = jsHelper::getADF($rows->lists['ef']);
            if ($extra_fields) {
                $class = 'well well-sm';
            } else {
                ?>
                <div class="rmpadd" style="padding-right:0px;padding-left:15px;">
                    <?php echo $descr;
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
            <?php

            if ($descr && $extra_fields) {
                echo '<div class="col-xs-12 rmpadd" style="padding-right:0px;">';
                echo $descr;
                echo '</div>';
            }

            ?>


</div>
    <div>
        <?php
        //require_once JOOMSPORT_PATH_VIEWS_ELEMENTS . 'table-group.php';

        $tabs = $rows->getTabs();
        jsHelperTabs::draw($tabs, $rows);

        ?>
    </div>
    <div>
        <div>
            <?php
            if (isset($rows->season->lists['playoffs'])) {
                echo jsHelper::getMatches($rows->season->lists['playoffs']);
            }
            ?>
        </div>
    </div>
    <div class="jsClear"></div>
    <?php
    global $jsConfig;
    ?>
    <?php if ($jsConfig->get('jsbrand_on',1) == 1):?>
    <br />
    <div id="copy" class="copyright"><a href="http://joomsport.com">powered by JoomSport - sport WordPress plugin</a></div> 
    <?php endif;?>
     <div class="jsClear"></div>
</div>
