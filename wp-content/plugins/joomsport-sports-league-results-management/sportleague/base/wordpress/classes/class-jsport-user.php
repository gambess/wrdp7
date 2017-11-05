<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportUser
{
    public static function getUserId()
    {
        $user = get_current_user_id();

        return ( isset( $user->ID ) ? (int) $user->ID : 0 );
    }
    public static function getUserValue()
    {
    }
    public static function getUser()
    {
    }
}
