<?php

//by Install
class B2S_System {

    public function __construct() {
        
    }

    public function check($action = 'before') {
        $result = array();
        if ($action == 'before') {
            if (!$this->checkCurl()) {
                $result['curl'] = false;
            }
            /*if(!$this->checkPHP()){
                $result['php'] = false;
            }*/
        }
        if ($action == 'after') {
            if (!$this->checkDbTables()) {
                $result['dbTable'] = false;
            }
        }

        return empty($result) ? true : $result;
    }

    private function checkCurl() {
        return function_exists('curl_version');
    }
    
    private function checkPHP(){
        if (version_compare(phpversion(), '5.5.3', '<')) {
            return false;
         }
         return true;
    }
    

    private function checkDbTables() {
        global $wpdb;
        $b2sUserCols = $wpdb->get_results('SHOW COLUMNS FROM b2s_user');
        if (is_array($b2sUserCols) && isset($b2sUserCols[0])) {
            $b2sUserColsData = array();
            foreach ($b2sUserCols as $key => $value) {
                if (isset($value->Field) && !empty($value->Field)) {
                    $b2sUserColsData[] = $value->Field;
                }
            }
            return (in_array("state_url", $b2sUserColsData)) ? true : false;
        }
        return false;
    }

    public function getErrorMessage($errors, $removeBreakline = false) {
        $output = '';
        if (is_array($errors) && !empty($errors)) {
            foreach ($errors as $error => $status) {
                if ($error == 'curl' && $status == false) {
                    $output .= __('Blog2Social used cURL. cURL is not installed in your PHP installation on your server. Install cURL and activate Blog2Social again.', 'blog2social');
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= __('Please see <a href="https://www.blog2social.com/en/faq/content/1/58/en/system-requirements-for-installing-blog2social.html" target="_blank">FAQ</a>', 'blog2social') . '</a>';
                }
                if ($error == 'php' && $status == false) {
                    $output .= __('Blog2Social used PHP. Your installed PHP version on your server is not high enough to use Blog2Social. Update your PHP version on 5.5.3 or higher.', 'blog2social');
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= __('Please see <a href="https://www.blog2social.com/en/faq/content/1/58/en/system-requirements-for-installing-blog2social.html" target="_blank">FAQ</a>', 'blog2social') . '</a>';
                }
                if ($error == 'dbTable' && $status == false) {
                    $output .= __('Blog2Social seems to have no permission to write in your WordPress database. Please make sure to assign Blog2Social the permission to write in the WordPress database.', 'blog2social');
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= (!$removeBreakline) ? '<br>' : ' ';
                    $output .= __('<a href="https://www.blog2social.com/en/faq/content/1/58/en/system-requirements-for-installing-blog2social.html" target="_blank"> Please find more Information and help in our FAQ</a>', 'blog2social') . '</a>.';
                }
            }
        }
        return $output;
    }

    public function deactivatePlugin() {
        deactivate_plugins(B2S_PLUGIN_BASENAME);
    }

}
