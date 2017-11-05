<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportController
{
    private $task = null;
    private $model = null;
    public function __construct()
    {
        $this->task = classJsportRequest::get('task');
        if (!$this->task) {
            $this->task = classJsportRequest::get('view');
        }
    }

    private function getModel()
    {
        global $post_type;
        switch($post_type){
            case 'joomsport_season':
                if(isset($_GET['action']) && $_GET['action'] == 'calendar'){
                    $this->task = 'calendar';
                }elseif(isset($_GET['action']) && $_GET['action'] == 'playerlist'){
                    $this->task = 'playerlist';
                }else{
                    $this->task = 'season';
                }
                
                break;
            case 'joomsport_match':
                $this->task = 'match';
                break;
            case 'joomsport_team':
                $this->task = 'team';
                break;
            case 'joomsport_player':
                $this->task = 'player';
                break;
            case 'joomsport_venue':
                $this->task = 'venue';
                break;
            case 'joomsport_person':
                $this->task = 'person';
                break;
            default:

                if(get_query_var('joomsport_tournament')){
                    $this->task = 'tournament';
                }elseif(get_query_var('joomsport_matchday')){
                    $this->task = 'matchday';
                
                }elseif(get_query_var('joomsport_club')){
                    $this->task = 'clublist';
                }elseif($_REQUEST['wpjoomsport'] == 'playerlist'){
                    $this->task = 'playerlist';
                }
        }
        if (!$this->task) {
            $this->task = 'seasonlist';
        } else {
            if ($this->task == 'table') {
                $this->task = 'season';
            }
            if ($this->task == 'tournlist') {
                $this->task = 'tournament';
            }
        }
        require_once JOOMSPORT_PATH_OBJECTS.'class-jsport-'.$this->task.'.php';
        $class = 'classJsport'.ucwords($this->task);
        $this->model = new $class();
    }

    public function execute()
    {
        $this->getModel();

        if($this->task == 'season'){
            if(method_exists($this->model, 'isComplex') && $this->model->isComplex() == '1'){
                $childrens = $this->model->getSeasonChildrens();
                
                if(count($childrens)){
                    $this->getSLHeader();
                    echo '<div id="joomsport-container" class="jsIclass">
                                <div class="page-content-js jmobile">';
                    $rows = $this->model->getRow();

                    $lists = $this->model->lists;
                    $options = isset($lists['options']) ? $lists['options'] : null;

                    $view = $this->task;
                    echo jsHelper::JsHeader($options);
                    
                    foreach ($childrens as $ch) {
                        $classChild = new classJsportSeason($ch->ID);
                        $rows = $classChild->getRow();

                        $lists = $classChild->lists;
                        $view = $this->task;

                        if (method_exists($classChild, 'getView')) {
                            $view = $classChild->getView();
                        }
                        $options = isset($lists['options']) ? $lists['options'] : null;
                        
                        echo '<div><h2>'.get_the_title($classChild->object->ID).'</h2></div>';
                        
                        //echo jsHelper::JsHeader($options);
                            //echo '<div class="under-module-header">';
                            if (is_file(JOOMSPORT_PATH_VIEWS.$view.'.php')) {
                                require JOOMSPORT_PATH_VIEWS.$view.'.php';
                            }else{
                                echo '<div class="error" ><p> File '.(JOOMSPORT_PATH_VIEWS.$view.'.php').' doesn\'t exist</p></div>';
                            }

                            //echo '</div>';
                            
                        
                    }
                    echo '</div>';
                        echo '</div>';
                    $this->getSLFooter();
                    return '';
                }
            }
        }
        
        $rows = $this->model->getRow();

        $lists = $this->model->lists;
        $view = $this->task;
        
        if (method_exists($this->model, 'getView')) {
            $view = $this->model->getView();
        }
        $options = isset($lists['options']) ? $lists['options'] : null;
        $this->getSLHeader();
        echo '<div id="joomsport-container" class="jsIclass">
                <div class="page-content-js jmobile">';
        echo jsHelper::JsHeader($options);
            //echo '<div class="under-module-header">';
            if (is_file(JOOMSPORT_PATH_VIEWS.$view.'.php')) {
                require_once JOOMSPORT_PATH_VIEWS.$view.'.php';
            }else{
                echo '<div class="error" ><p> File '.(JOOMSPORT_PATH_VIEWS.$view.'.php').' doesn\'t exist</p></div>';
            }
            
            //echo '</div>';
            echo '</div>';
        echo '</div>';
        $this->getSLFooter();
    }
    
    
    public function getSLHeader()
    {
        require_once JOOMSPORT_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'header.php';
    }
    public function getSLFooter()
    {
        require_once JOOMSPORT_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'footer.php';
    }
}
