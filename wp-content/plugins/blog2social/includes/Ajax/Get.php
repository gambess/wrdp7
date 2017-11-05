<?php

class Ajax_Get {

    static private $instance = null;

    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('wp_ajax_b2s_ship_item', array($this, 'getShipItem'));
        add_action('wp_ajax_b2s_sort_data', array($this, 'getSortData'));
        add_action('wp_ajax_b2s_get_sched_posts_by_user_auth', array($this, 'getSchedPostsByUserAuth'));
        add_action('wp_ajax_b2s_publish_post_data', array($this, 'getPublishPostData'));
        add_action('wp_ajax_b2s_sched_post_data', array($this, 'getSchedPostData'));
        add_action('wp_ajax_b2s_ship_navbar_item', array($this, 'getNavbarItem'));
        add_action('wp_ajax_b2s_post_meta_box', array($this, 'getPostMetaBox'));
        add_action('wp_ajax_b2s_get_settings_sched_time_default', array($this, 'getSettingsSchedTimeDefault'));
        add_action('wp_ajax_b2s_get_settings_sched_time_user', array($this, 'getSettingsSchedTimeUser'));
        add_action('wp_ajax_b2s_ship_item_full_text', array($this, 'getShipItemFullText'));
        add_action('wp_ajax_b2s_ship_item_reload_url', array($this, 'getShipItemReloadUrl'));
        add_action('wp_ajax_b2s_get_faq_entries', array($this, 'getFaqEntries'));
        add_action('wp_ajax_b2s_get_calendar_events', array($this, 'getCalendarEvents'));
        add_action('wp_ajax_b2s_get_calendar_edit_modal', array($this, 'getCalendarEditModal'));
        add_action('wp_ajax_b2s_get_image_modal', array($this, 'getImageModal'));
    }

    public function getSortData() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
        require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
        /* Sort */
        $b2sType = isset($_POST['b2sType']) ? trim($_POST['b2sType']) : "";
        $b2sPagination = (int) isset($_POST['b2sPagination']) ? $_POST['b2sPagination'] : 1;
        $b2sSortPostTitle = isset($_POST['b2sSortPostTitle']) ? trim($_POST['b2sSortPostTitle']) : "";
        $b2sSortPostAuthor = isset($_POST['b2sSortPostAuthor']) ? trim($_POST['b2sSortPostAuthor']) : "";
        $b2sSortPostSchedDate = isset($_POST['b2sSortPostSchedDate']) ? trim($_POST['b2sSortPostSchedDate']) : "";
        $b2sSortPostPublishDate = isset($_POST['b2sSortPostPublishDate']) ? trim($_POST['b2sSortPostPublishDate']) : "";
        $b2sSortPostStatus = isset($_POST['b2sSortPostStatus']) ? trim($_POST['b2sSortPostStatus']) : "";
        $b2sShowByDate = isset($_POST['b2sShowByDate']) ? trim($_POST['b2sShowByDate']) : ""; //YYYY-mm-dd
        $b2sUserAuthId = isset($_POST['b2sUserAuthId']) ? (int) $_POST['b2sUserAuthId'] : 0;
        $b2sSortPostCat = isset($_POST['b2sSortPostCat']) ? trim($_POST['b2sSortPostCat']) : "";
        $b2sSortPostType = isset($_POST['b2sSortPostType']) ? trim($_POST['b2sSortPostType']) : "";
        $b2sUserLang = isset($_POST['b2sUserLang']) ? trim($_POST['b2sUserLang']) : strtolower(substr(B2S_LANGUAGE, 0, 2));
        if (!empty($b2sType) && in_array($b2sType, array('all', 'sched', 'publish'))) {
            $postItem = new B2S_Post_Item($b2sType, $b2sSortPostTitle, $b2sSortPostAuthor, $b2sSortPostStatus, $b2sSortPostPublishDate, $b2sSortPostSchedDate, $b2sShowByDate, $b2sUserAuthId, $b2sPagination, $b2sSortPostCat, $b2sSortPostType, $b2sUserLang);
            echo json_encode(array('result' => true, 'content' => $postItem->getItemHtml(), 'schedDates' => json_encode($postItem->getCalendarSchedDate()), 'pagination' => $postItem->getPaginationHtml()));
            wp_die();
        }
        echo json_encode(array('result' => false, 'content' => '', 'schedDates' => 0, 'pagination' => ''));
        wp_die();
    }

    public function getFaqEntries() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getFaqEntries', 'lang' => substr(B2S_LANGUAGE, 0, 2), 'token' => B2S_PLUGIN_TOKEN)));
        if (isset($result->result) && isset($result->content) && !empty($result->content)) {
            echo json_encode(array('result' => true, 'content' => B2S_Notice::getFaqEntriesHtml($result->content)));
            wp_die();
        }
        echo json_encode(array('result' => false, 'content' => ''));
        wp_die();
    }

    public function getPostMetaBox() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getProfileUserAuth', 'token' => B2S_PLUGIN_TOKEN)));
        if (isset($result->result) && (int) $result->result == 1 && isset($result->data) && !empty($result->data) && isset($result->data->mandant) && isset($result->data->auth) && !empty($result->data->mandant) && !empty($result->data->auth)) {
            $metaBox = new B2S_PostBox();
            echo json_encode(array('result' => true, 'content' => $metaBox->getPostBoxAutoHtml($result->data->mandant, $result->data->auth)));
            wp_die();
        }
        echo json_encode(array('result' => false, 'content' => ''));
        wp_die();
    }

    public function getShipItemFullText() {
        if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0) {
            $userLang = isset($_POST['userLang']) ? trim($_POST['userLang']) : strtolower(substr(B2S_LANGUAGE, 0, 2));
            $data = get_post((int) $_POST['postId']);
            if (isset($data->post_content)) {
                $postUrl = (get_permalink($data->ID) !== false) ? get_permalink($data->ID) : $data->guid;
                $content = trim(B2S_Util::prepareContent($data->ID, $data->post_content, $postUrl, '', false, $userLang));
                echo json_encode(array('result' => true, 'text' => trim(strip_tags($content)), 'networkAuthId' => (int) $_POST['networkAuthId']));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function getShipItem() {
        if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0) {
            $userLang = isset($_POST['userLang']) ? trim($_POST['userLang']) : strtolower(substr(B2S_LANGUAGE, 0, 2));
            require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Item.php';
            $itemData = array('networkAuthId' => (int) $_POST['networkAuthId'],
                'networkId' => (int) $_POST['networkId'],
                'network_display_name' => strip_tags(stripslashes($_POST['networkDisplayName'])),
                'networkType' => (int) $_POST['networkType']);
            $item = new B2S_Ship_Item((int) $_POST['postId'], $userLang);
            echo json_encode(array('result' => true, 'networkAuthId' => (int) $_POST['networkAuthId'], 'networkType' => (int) $_POST['networkType'], 'networkId' => (int) $_POST['networkId'], 'content' => $item->getItemHtml((object) $itemData)));
        } else {
            echo json_encode(array('result' => false));
        }
        wp_die();
    }

    public function getPublishPostData() {
        if ((int) $_POST['postId']) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
            require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
            $postData = new B2S_Post_Item();
            $showByDate = isset($_POST['showByDate']) ? trim($_POST['showByDate']) : '';
            $result = $postData->getPublishPostDataHtml((int) $_POST['postId'], $showByDate);
            if ($result !== false) {
                echo json_encode(array('result' => true, 'postId' => (int) $_POST['postId'], 'content' => $result));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function getSchedPostsByUserAuth() {
        if ((int) $_POST['networkAuthId']) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php');
            $networkData = new B2S_Network_Item(false);
            $count = $networkData->getCountSchedPostsByUserAuth((int) $_POST['networkAuthId']);
            if ($count !== false) {
                echo json_encode(array('result' => true, 'count' => $count));
                wp_die();
            }
        }
        echo json_encode(array('result' => false, 'count' => 0));
        wp_die();
    }

    public function getSchedPostData() {
        if ((int) $_POST['postId']) {
            require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Item.php');
            require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
            $postData = new B2S_Post_Item();
            $showByDate = isset($_POST['showByDate']) ? trim($_POST['showByDate']) : '';
            $userAuthId = isset($_POST['userAuthId']) ? (int) $_POST['userAuthId'] : 0;
            $result = $postData->getSchedPostDataHtml((int) $_POST['postId'], $showByDate, $userAuthId);
            if ($result !== false) {
                echo json_encode(array('result' => true, 'postId' => (int) $_POST['postId'], 'content' => $result));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function getNavbarItem() {
        if (isset($_POST['networkId']) && isset($_POST['networkAuthId']) && isset($_POST['networkType']) && isset($_POST['displayName']) && isset($_POST['mandandId'])) {
            require_once (B2S_PLUGIN_DIR . '/includes/B2S/Ship/Navbar.php');
            global $wpdb;
            $mandantCount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(mandant_id)FROM b2s_user_network_settings  WHERE mandant_id =%d AND blog_user_id=%d ", $_POST['mandandId'], B2S_PLUGIN_BLOG_USER_ID));
            if ($mandantCount > 0) {
                $wpdb->insert('b2s_user_network_settings', array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID, 'mandant_id' => $_POST['mandandId'], 'network_auth_id' => $_POST['networkAuthId']), array('%d', '%d', '%d'));
            }
            $data = array(
                'networkId' => isset($_POST['networkId']) ? (int) $_POST['networkId'] : 0,
                'networkAuthId' => isset($_POST['networkAuthId']) ? (int) $_POST['networkAuthId'] : 0,
                'networkType' => isset($_POST['networkType']) ? (int) $_POST['networkType'] : 0,
                'networkUserName' => isset($_POST['displayName']) ? strip_tags($_POST['displayName']) : '',
                'mandantId' => isset($_POST['mandandId']) ? (int) $_POST['mandandId'] : 0,
                'expiredDate' => date('Y-m-d', strtotime('+3 days')));

            $navbar = new B2S_Ship_Navbar();
            echo json_encode(array('result' => true, 'networkAuthId' => (int) $_POST['networkAuthId'], 'content' => $navbar->getItemHtml((object) $data)));
            wp_die();
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function getSettingsSchedTimeDefault() {
        $userTimes = B2S_Tools::getRandomBestTimeSettings();
        if (!empty($userTimes) && is_array($userTimes)) {
            echo json_encode(array('result' => true, 'times' => $userTimes));
            wp_die();
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function getSettingsSchedTimeUser() {
        $lang = substr(B2S_LANGUAGE, 0, 2);
        global $wpdb;
        $userTimes = array();
        $saveSchedData = $wpdb->get_results($wpdb->prepare("SELECT network_id, network_type, sched_time FROM b2s_post_sched_settings WHERE blog_user_id= %d", B2S_PLUGIN_BLOG_USER_ID));
        if (!empty($saveSchedData) && is_array($saveSchedData)) {
            foreach ($saveSchedData as $k => $v) {
                $slug = ($lang == 'en') ? 'h:i A' : 'H:i';
                $userTimes[$v->network_id][$v->network_type] = date($slug, strtotime(date('Y-m-d ' . $v->sched_time . ':00')));
            }
            if (!empty($userTimes)) {
                echo json_encode(array('result' => true, 'times' => $userTimes));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function getShipItemReloadUrl() {
        if (isset($_POST['networkId']) && (int) $_POST['networkId'] > 0 && isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0 && isset($_POST['url']) && !empty($_POST['url'])) {
            if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['defaultUrl']) && $_POST['defaultUrl'] == $_POST['url']) {
                $postData = get_post($_POST['postId']);
                if ($postData->post_status != 'publish') {
                    $postUrl = (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid);
                    $metaInfo = array('title' => B2S_Util::getExcerpt(B2S_Util::remove4byte($postData->post_title), 50) . ' - ' . get_option('blogname'), 'description' => B2S_Util::getExcerpt(B2S_Util::prepareContent($postData->ID, $postData->post_content, $postUrl, false, false), 150));
                } else {
                    $metaInfo = B2S_Util::getMetaTags((int) $_POST['postId'], strip_tags($_POST['url']), (int) $_POST['networkId']);
                }
            } else {
                $metaInfo = B2S_Util::getMetaTags(0, strip_tags($_POST['url']), (int) $_POST['networkId']);
            }
            echo json_encode(array('result' => true, 'networkId' => (int) $_POST['networkId'], 'networkAuthId' => $_POST['networkAuthId'], 'title' => isset($metaInfo['title']) ? $metaInfo['title'] : '', 'description' => isset($metaInfo['description']) ? $metaInfo['description'] : '', 'image' => isset($metaInfo['image']) ? $metaInfo['image'] : ''));
            wp_die();
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function getCalendarEvents() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php');
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php');
        require_once (B2S_PLUGIN_DIR . 'includes/Util.php');

        if(isset($_GET['start']))
        {
            $calendar = B2S_Calendar_Filter::getByTimespam($_GET['start']." 00:00:00",$_GET['end']." 23:59:59");
        }
        else{
            $calendar = B2S_Calendar_Filter::getAll();
        }
        echo json_encode($calendar->asCalendarArray());
        wp_die();
    }

    public function getCalendarEditModal() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');
        if (isset($_POST['id']) && (int) $_POST['id'] > 0) {
            $item = B2S_Calendar_Filter::getById((int)$_POST['id']);
            if ($item != null) {
                $lock_user_id = get_option("B2S_PLUGIN_CALENDAR_BLOCKED_".$_POST['id']);
                if(!$lock_user_id) {
                    update_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . $_POST['id'], get_current_user_id());
                    $options = new B2S_Options(get_current_user_id());
                    $block_old = $options->_getOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED");

                    if($block_old) {
                        delete_option("B2S_PLUGIN_CALENDAR_BLOCKED_".$block_old);
                    }
                    $options->_setOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED",$_POST['id']);
                }
                if($lock_user_id)
                {
                    $lock_user = get_userdata($lock_user_id);
                }
                include (B2S_PLUGIN_DIR . 'views/b2s/modal.calendar.php');
                wp_die();
            }
        }
        echo "0";
        wp_die();
    }

    public function getImageModal() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php');
        if (isset($_POST['id']) && (int) $_POST['id'] > 0) {
            $postData = get_post((int) $_POST['id']);
            if (isset($postData->ID)) {
                $postUrl = (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid);
                include (B2S_PLUGIN_DIR . 'views/b2s/image.calendar.php');
                wp_die();
            }
        }
        echo "0";
        wp_die();
    }

}
