<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php
//load defines
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'defines.php';

//load DB class (joomla in this case)
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-database-base.php';
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-addtag.php';
// get database object
global $jsDatabase,$joomsportSettings;
$jsDatabase = new classJsportDatabaseBase();

global $jsConfig;
$jsConfig = $joomsportSettings;
//load request class classJsportRequest
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-request.php';
//load session class
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-session.php';
//load date class
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-date.php';
//load language class
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-language.php';
//load link class
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-link.php';
//load user class
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-user.php';
//load text class
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-text.php';
//load plugin class
require_once JOOMSPORT_PATH_CLASSES.'class-jsport-plugins.php';
//load extra fields class
require_once JOOMSPORT_PATH_CLASSES.'class-jsport-extrafields.php';
//load pagination class
require_once JOOMSPORT_PATH_CLASSES.'class-jsport-pagination.php';
//load helper
require_once JOOMSPORT_PATH_SL_HELPERS.'js-helper-images.php';
require_once JOOMSPORT_PATH_SL_HELPERS.'js-helper-tabs.php';
require_once JOOMSPORT_PATH_SL_HELPERS.'js-helper.php';

//execute task
require_once JOOMSPORT_PATH_ENV.'classes'.DIRECTORY_SEPARATOR.'class-jsport-controller.php';
$controllerSportLeague = new classJsportController();
// add css

//echo memory_get_usage()/1024.0 . " kb <br />";
//echo microtime(TRUE)-$time_start;

?>