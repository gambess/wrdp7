<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$match = $rows;
$partic_home = $match->getParticipantHome();
$partic_away = $match->getParticipantAway();
?>
<?php if($rows->lists['boxscore_home']){?>
<div class="jsDivLineEmbl" style="padding-top:10px;">

    <?php echo $partic_home->getEmblem();?>
    <?php echo jsHelper::nameHTML($partic_home->getName(true));?>

</div>
<div style="padding-top:10px;">
    <?php echo $rows->lists['boxscore_home'];?>
</div>
<?php } ?>
<?php if($rows->lists['boxscore_away']){?>
<div class="jsDivLineEmbl" style="padding-top:10px;">

    <?php echo $partic_away->getEmblem();?>
    <?php echo jsHelper::nameHTML($partic_away->getName(true));?>

</div>
<div style="padding-top:10px;">
    <?php echo $rows->lists['boxscore_away'];?>
</div>
<?php } ?>