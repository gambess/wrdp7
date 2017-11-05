<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classExtrafieldRadio
{
    public static function getValue($ef)
    {
        $html = '';
        if ($ef != '') {
            $html = $ef ? __('Yes','joomsport-sports-league-results-management') : __('No','joomsport-sports-league-results-management');
        }

        return $html;
    }
}
