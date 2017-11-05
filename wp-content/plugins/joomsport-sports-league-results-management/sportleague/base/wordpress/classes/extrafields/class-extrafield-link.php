<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classExtrafieldLink
{
    public static function getValue($ef)
    {
        $html = '';
        if ($ef) {
            $html = "<a target='_blank' href='".(substr($ef, 0, 7) == 'http://' ? $ef : 'http://'.$ef)."'>".$ef.'</a>';
        }

        return $html;
    }
}
