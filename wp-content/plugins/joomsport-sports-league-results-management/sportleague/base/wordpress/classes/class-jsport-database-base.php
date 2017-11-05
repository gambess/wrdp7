<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_CLASSES.'class-jsport-database.php';

class classJsportDatabaseBase extends classJsportDatabase
{
    public $db = null;

    public function __construct()
    {
        global $wpdb;
        $this->db = $db = $wpdb;
    }
    public function select($query, $vars = '')
    {
        return $this->db->get_results($query);

    }
    public function insert($query, $vars = array())
    {

        return $this->db->query($query, $vars);
    }
    public function update($query, $vars = array())
    {

        return $this->db->query($query, $vars);
    }
    public function delete($query, $vars = array())
    {

        return $this->db->query($query, $vars);
    }
    public function insertedId()
    {
        return $this->db->insertid();
    }
    public function selectObject($query, $vars = array())
    {
        return $this->db->get_row($query);

    }
    public function selectValue($query, $vars = array())
    {
        return $this->db->get_var($query);

    }
    public function selectColumn($query, $vars = array())
    {
        return $this->db->get_col($query);

    }
    public function selectArray($query, $vars = array())
    {
        $this->db->setQuery($query);

        return $this->db->loadAssoc();
    }
    public function selectKeyPair($query, $vars = array())
    {
        $result = $this->db->get_results($query);


        $return = array();
        foreach ($result as $res) {
            $return[$res->name] = $res->value;
        }

        return $return;
    }

    private function query($query, $args = array())
    {
        $sth = $this->db->prepare($query);
        if (!is_array($args)) {
            $args = explode(',', $args);
        }
        $sth->execute($args);

        return $sth;
    }
}
