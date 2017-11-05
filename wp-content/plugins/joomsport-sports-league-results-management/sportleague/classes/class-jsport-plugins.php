<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class classJsportPlugins
{
    public static function get($name, $arguments = array())
    {
        if ($name) {
            if ($arguments == null) {
                $arguments = $_GET;
            }
            foreach (glob(JOOMSPORT_PATH_PLUGINS.'plugin-joomsport-*.php') as $filename) {
                include_once $filename;

                $dir_array = explode(DIRECTORY_SEPARATOR, $filename);
                if (count($dir_array)) {
                    $classname = $dir_array[count($dir_array) - 1];
                    $classname = str_replace('.php', '', $classname);
                    $classname = str_replace('-', '', $classname);
                    if (class_exists($classname)) {

                        //$classname::$name($arguments);
                        if (method_exists($classname, $name)) {
                            $classname::$name($arguments);
                        }
                    }
                }
            }
        }
        //load all plugin classes
        //check all plugins for current task
        //execute plugin function 
        return '';
    }
}
