<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportAddtag
{
    public static function addCustom($name, $value)
    {
        $doc = JFactory::getDocument();
        $doc->addCustomTag('<meta property="'.$name.'" content="'.htmlspecialchars(strip_tags(addslashes($value))).'"/> ');
    }
    public static function addJS($link)
    {
        JHtml::script($link);
    }
    public static function addCSS($link)
    {
        JHtml::stylesheet($link);
    }
}
