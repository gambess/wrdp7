<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classExtrafieldSelect
{
    public static function getValue($ef)
    {
        global $jsDatabase;
        $query = 'SELECT sel_value FROM '.$jsDatabase->db->joomsport_ef_select." WHERE id='".(int) $ef."'";

        return $jsDatabase->selectValue($query);
    }
}
