<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
require_once JOOMSPORT_PATH_MODELS.'model-jsport-club.php';
require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-team.php';

class classJsportClub
{
    private $id = null;
    public $object = null;
    public $lists = null;
    const VIEW = 'common';

    public function __construct($id = null)
    {
        if (!$id) {
            $this->id = get_the_ID();
        } else {
            $this->id = $id;
        }
        if (!$this->id) {
            die('ERROR! Club ID not DEFINED');
        }

        $this->loadObject();
        $term = get_term_by( 'id', (int) $this->id, 'joomsport_club' );
        $this->lists['options']['title'] =  $term->name;
    }

    private function loadObject()
    {
        $obj = new modelJsportClub($this->id);
        $this->object = $obj->getRow();

        $this->lists = $obj->loadLists();
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getName($linkable = false)
    {
        $html = '';
        if ($this->id > 0) {
            $term = get_term_by( 'id', (int) $this->id, 'joomsport_club' );

            $html = classJsportLink::club($term->name, $this->id, 0, '', $linkable);
        }

        return $html;
    }

    public function getDefaultPhoto()
    {
        return $this->lists['def_img'];
    }
    public function getEmblem()
    {
        //return $this->object->c_emblem;
    }
    public function getRow()
    {
        return $this;
    }
    public function getTabs()
    {
        $tabs = array();
        $intA = 0;
        //main tab
        $tabs[$intA]['id'] = 'stab_main';
        $tabs[$intA]['title'] = __('Club','joomsport-sports-league-results-management');
        $tabs[$intA]['body'] = 'object-view.php';
        $tabs[$intA]['text'] = '';
        $tabs[$intA]['class'] = '';
        $tabs[$intA]['ico'] = 'flag';

        $this->getTeams();
        //teams
        if (count($this->lists['teamsObj'])) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_teams';
            $tabs[$intA]['title'] = __('Teams','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = 'team-list.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'flag';
        }

        //photos
        if (count($this->lists['photos']) > 1) {
            ++$intA;
            $tabs[$intA]['id'] = 'stab_photos';
            $tabs[$intA]['title'] = __('Photos','joomsport-sports-league-results-management');
            $tabs[$intA]['body'] = 'gallery.php';
            $tabs[$intA]['text'] = '';
            $tabs[$intA]['class'] = '';
            $tabs[$intA]['ico'] = 'photos';
        }

        return $tabs;
    }

    public function getTeams()
    {
        $players_object = array();

        if (count($this->lists['teams'])) {
            foreach ($this->lists['teams'] as $row) {
                $obj = new classJsportTeam($row->id);
                $players_object[] = $obj->getRow();
            }
        }
        $this->lists['teamsObj'] = $players_object;
    }
    public function getDescription()
    {
        $term = get_term_by( 'id', (int) $this->id, 'joomsport_club' );
        return classJsportText::getFormatedText($term->description);
    }
    public function getView()
    {
        return self::VIEW;
    }
}
