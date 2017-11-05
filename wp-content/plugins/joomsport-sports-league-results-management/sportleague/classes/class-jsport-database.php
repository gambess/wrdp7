<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class classJsportDatabase
{
    abstract public function select($query, $vars);
    abstract public function insert($query, $vars);
    abstract public function update($query, $vars);
    abstract public function delete($query, $vars);
    abstract public function insertedId();
    abstract public function selectObject($query, $vars);
    abstract public function selectValue($query, $vars);
    abstract public function selectColumn($query, $vars);
}
