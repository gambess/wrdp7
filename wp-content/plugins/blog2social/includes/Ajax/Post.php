<?php

class Ajax_Post {

    static private $instance = null;

    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('wp_ajax_b2s_save_ship_data', array($this, 'saveShipData'));
        add_action('wp_ajax_b2s_save_user_mandant', array($this, 'saveUserMandant'));
        add_action('wp_ajax_b2s_delete_mandant', array($this, 'deleteUserMandant'));
        add_action('wp_ajax_b2s_delete_user_auth', array($this, 'deleteUserAuth'));
        add_action('wp_ajax_b2s_update_user_version', array($this, 'updateUserVersion'));
        add_action('wp_ajax_b2s_create_trail', array($this, 'createTrail'));
        add_action('wp_ajax_b2s_delete_user_sched_post', array($this, 'deleteUserSchedPost'));
        add_action('wp_ajax_b2s_update_user_sched_time_post', array($this, 'updateUserSchedTimePost'));
        add_action('wp_ajax_b2s_delete_user_publish_post', array($this, 'deleteUserPublishPost'));
        add_action('wp_ajax_b2s_user_network_settings', array($this, 'saveUserNetworkSettings'));
        add_action('wp_ajax_b2s_save_social_meta_tags', array($this, 'saveSocialMetaTags'));
        add_action('wp_ajax_b2s_reset_social_meta_tags', array($this, 'resetSocialMetaTags'));
        add_action('wp_ajax_b2s_save_user_settings_sched_time', array($this, 'saveUserSettingsSchedTime'));
        add_action('wp_ajax_b2s_prg_login', array($this, 'prgLogin'));
        add_action('wp_ajax_b2s_prg_logout', array($this, 'prgLogout'));
        add_action('wp_ajax_b2s_prg_ship', array($this, 'prgShip'));
        add_action('wp_ajax_b2s_notice_hide', array($this, 'noticeHide'));
        add_action('wp_ajax_b2s_ship_navbar_save_settings', array($this, 'b2sShipNavbarSaveSettings'));
        add_action('wp_ajax_b2s_post_mail_update', array($this, 'b2sPostMailUpdate'));
        add_action('wp_ajax_b2s_calendar_move_post', array($this, 'b2sCalendarMovePost'));
        add_action('wp_ajax_b2s_calendar_delete', array($this, 'b2sCalendarDelete'));
        add_action('wp_ajax_b2s_calendar_save', array($this, 'b2sCalendarSave'));
        add_action("wp_ajax_b2s_get_calendar_release_locks", array($this, 'releaseLocks'));
    }

    public function prgShip() {

        if (!empty($_POST) && isset($_POST['token']) && !empty($_POST['token']) && isset($_POST['prg_id']) && (int) $_POST['prg_id'] > 0 && isset($_POST['blog_user_id']) && (int) $_POST['blog_user_id'] > 0 && isset($_POST['post_id']) && (int) $_POST['post_id'] > 0) {
            $dataPost = $_POST;
            $type = $dataPost['publish'];
            $dataPost['status'] = ((int) $type == 1) ? 'hold' : 'open';
            unset($dataPost['confirm']);
            unset($dataPost['blog_user_id']);
            unset($dataPost['post_id']);
            unset($dataPost['publish']);
            $result = json_decode(trim(PRG_Api_Post::post(B2S_PLUGIN_PRG_API_ENDPOINT . 'post.php', $dataPost)));

            if (is_object($result) && !empty($result) && isset($result->result) && (int) $result->result == 1 && isset($result->create) && (int) $result->create == 1) {
                //Contact
                global $wpdb;
                $sqlCheckUser = $wpdb->prepare("SELECT `id` FROM `b2s_user_contact` WHERE `blog_user_id` = %d", $_POST['blog_user_id']);
                $userEntry = $wpdb->get_var($sqlCheckUser);
                $userContact = array('name_mandant' => strip_tags($_POST['name_mandant']),
                    'created' => date('Y-m-d H:i;s'),
                    'name_presse' => strip_tags($_POST['name_presse']),
                    'anrede_presse' => strip_tags($_POST['anrede_presse']),
                    'vorname_presse' => strip_tags($_POST['vorname_presse']),
                    'nachname_presse' => strip_tags($_POST['nachname_presse']),
                    'strasse_presse' => strip_tags($_POST['strasse_presse']),
                    'nummer_presse' => strip_tags($_POST['nummer_presse']),
                    'plz_presse' => strip_tags($_POST['plz_presse']),
                    'ort_presse' => strip_tags($_POST['ort_presse']),
                    'land_presse' => strip_tags($_POST['land_presse']),
                    'email_presse' => strip_tags($_POST['email_presse']),
                    'telefon_presse' => strip_tags($_POST['telefon_presse']),
                    'fax_presse' => isset($_POST['fax_presse']) ? strip_tags($_POST['fax_presse']) : '',
                    'url_presse' => strip_tags($_POST['url_presse'])
                );

                if (!$userEntry) {
                    $insertData = array_merge(array('blog_user_id' => (int) $_POST['blog_user_id']), $userContact);
                    $wpdb->insert('b2s_user_contact', $insertData);
                } else {
                    $wpdb->update('b2s_user_contact', $userContact, array('blog_user_id' => (int) $_POST['blog_user_id']));
                }
                echo json_encode(array('result' => true, 'error' => 0, 'type' => $type));
                wp_die();
            }
            echo json_encode(array('result' => false, 'error' => 2, 'type' => $type)); //NOTSHIP
            wp_die();
        }
        echo json_encode(array('result' => false, 'error' => 1, 'type' => $type)); //INVALIDDATA
        wp_die();
    }

    public function prgLogin() {
        if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password'])) {
            $pubKey = json_decode(PRG_Api_Get::get(B2S_PLUGIN_PRG_API_ENDPOINT . 'auth.php?publicKey=true', array()));
            if (!empty($pubKey) && is_object($pubKey) && isset($pubKey->publicKey) && !empty($pubKey->publicKey) && function_exists('openssl_public_encrypt')) {
                $usernameCrypted = '';
                $passwordCrypted = '';
                openssl_public_encrypt(trim($_POST['username']), $usernameCrypted, $pubKey->publicKey);
                openssl_public_encrypt(trim($_POST['password']), $passwordCrypted, $pubKey->publicKey);
                $datas = array(
                    'action' => 'loginPRG',
                    'username' => base64_encode($usernameCrypted),
                    'password' => base64_encode($passwordCrypted),
                );
                $result = json_decode(trim(PRG_Api_Post::post(B2S_PLUGIN_PRG_API_ENDPOINT . 'auth.php', $datas)));
                if (!empty($result) && is_object($result) && isset($result->prg_token) && !empty($result->prg_token) && isset($result->prg_id) && !empty($result->prg_id)) {
                    if ((int) $result->prg_id > 0) {
                        $prgInfo = array('B2S_PRG_ID' => $result->prg_id,
                            'B2S_PRG_TOKEN' => $result->prg_token);

                        update_option('B2S_PLUGIN_PRG_' . B2S_PLUGIN_BLOG_USER_ID, $prgInfo);
                        echo json_encode(array('result' => true, 'error' => 0));
                        wp_die();
                    }
                }
                echo json_encode(array('result' => false, 'error' => 1));
                wp_die();
            }
            echo json_encode(array('result' => false, 'error' => 2)); //SSL ERRROR
            wp_die();
        }
        echo json_encode(array('result' => false, 'error' => 1));
        wp_die();
    }

    public function prgLogout() {
        delete_option('B2S_PLUGIN_PRG_' . B2S_PLUGIN_BLOG_USER_ID);
        echo json_encode(array('result' => true));
        wp_die();
    }

    public function saveShipData() {

        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Save.php');
        $post = $_POST;
        $metaOg = false;
        $metaCard = false;

        if (!isset($post['b2s']) || !is_array($post['b2s'])) {
            echo json_encode(array('result' => false));
            wp_die();
        }

        $b2sShipSend = new B2S_Ship_Save();

        delete_option('B2S_PLUGIN_POST_META_TAGES_' . (int) $post['post_id']);

        $content = array();
        $defaultPostData = array('token' => B2S_PLUGIN_TOKEN,
            'blog_user_id' => B2S_PLUGIN_BLOG_USER_ID,
            'post_id' => (int) $post['post_id'],
            'default_titel' => isset($post['default_titel']) ? $post['default_titel'] : '',
            'lang' => trim(strtolower(substr(B2S_LANGUAGE, 0, 2))));

        foreach ($post['b2s'] as $networkAuthId => $data) {
            if (!isset($data['url']) || !isset($data['content']) || !isset($data['network_id'])) {
                continue;
            }

            //Change/Set MetaTags
            if ((int) $data['network_id'] == 1 && $metaOg == false && (int) $post['post_id'] > 0 && isset($data['post_format']) && (int) $data['post_format'] == 0 && isset($post['change_og_meta']) && (int) $post['change_og_meta'] == 1) {  //LinkPost
                $metaOg = true;
                $meta = B2S_Meta::getInstance();
                $meta->getMeta((int) $post['post_id']);
                if (isset($data['og_title']) && !empty($data['og_title'])) {
                    $meta->setMeta('og_title', $data['og_title']);
                }
                if (isset($data['og_desc']) && !empty($data['og_desc'])) {
                    $meta->setMeta('og_desc', $data['og_desc']);
                }
                if (isset($data['image_url']) && !empty($data['image_url'])) {
                    $meta->setMeta('og_image', trim($data['image_url']));
                }
                $meta->updateMeta((int) $post['post_id']);
            }

            //Change/Set MetaTags
            if ((int) $data['network_id'] == 2 && $metaCard == false && (int) $post['post_id'] > 0 && isset($data['post_format']) && (int) $data['post_format'] == 0 && isset($post['change_card_meta']) && (int) $post['change_card_meta'] == 1) {  //LinkPost
                $metaCard = true;
                $meta = B2S_Meta::getInstance();
                $meta->getMeta((int) $post['post_id']);
                if (isset($data['card_title']) && !empty($data['card_title'])) {
                    $meta->setMeta('card_title', $data['card_title']);
                }
                if (isset($data['card_desc']) && !empty($data['card_desc'])) {
                    $meta->setMeta('card_desc', $data['card_desc']);
                }
                if (isset($data['image_url']) && !empty($data['image_url'])) {
                    $meta->setMeta('card_image', trim($data['image_url']));
                }
                $meta->updateMeta((int) $post['post_id']);
            }

            $sendData = array("board" => isset($data['board']) ? $data['board'] : '',
                "group" => isset($data['group']) ? $data['group'] : '',
                "custom_title" => isset($data['custom_title']) ? strip_tags($data['custom_title']) : '',
                "content" => (isset($data['content']) && !empty($data['content'])) ? strip_tags(html_entity_decode($data['content']), '<p><h1><h2><br><i><b><a><img>') : '',
                'url' => isset($data['url']) ? $data['url'] : '',
                'image_url' => isset($data['image_url']) ? trim($data['image_url']) : '',
                'tags' => isset($data['tags']) ? $data['tags'] : array(),
                'network_id' => isset($data['network_id']) ? $data['network_id'] : '',
                'network_type' => isset($data['network_type']) ? $data['network_type'] : '',
                'network_display_name' => isset($data['network_display_name']) ? $data['network_display_name'] : '',
                'network_auth_id' => $networkAuthId,
                'post_format' => isset($data['post_format']) ? (int) $data['post_format'] : '',
                'user_timezone' => isset($post['user_timezone']) ? $post['user_timezone'] : 0,
                'publish_date' => isset($post['publish_date']) ? date('Y-m-d H:i:s', strtotime($post['publish_date'])) : date('Y-m-d H:i:s', current_time('timestamp'))
            );

            $oneSchedTimeRemove = false;
            if (isset($data['releaseSelect']) && (int) $data['releaseSelect'] == 1 && isset($data['date'][0]) && isset($data['time'][0])) {
                if ((int) strtotime(B2S_Util::getUTCForDate($data['date'][0] . ' ' . $data['time'][0], $sendData['user_timezone'] * (-1))) < (int) strtotime(gmdate('Y-m-d H:i:s'))) {
                    $oneSchedTimeRemove = true;
                }
            }
            if ($data['releaseSelect'] == 0 || $oneSchedTimeRemove != false) {
                $b2sShipSend->savePublishDetails(array_merge($defaultPostData, $sendData));
            } else {
                $schedData = array(
                    'weeks' => isset($data['weeks']) ? $data['weeks'] : 0,
                    'date' => isset($data['date']) ? $data['date'] : array(),
                    'time' => isset($data['time']) ? $data['time'] : array(),
                    'mo' => isset($data['mo']) ? $data['mo'] : array(),
                    'di' => isset($data['di']) ? $data['di'] : array(),
                    'mi' => isset($data['mi']) ? $data['mi'] : array(),
                    'do' => isset($data['do']) ? $data['do'] : array(),
                    'fr' => isset($data['fr']) ? $data['fr'] : array(),
                    'sa' => isset($data['sa']) ? $data['sa'] : array(),
                    'so' => isset($data['so']) ? $data['so'] : array(),
                    'releaseSelect' => isset($data['releaseSelect']) ? $data['releaseSelect'] : 0,
                    'user_timezone' => isset($post['user_timezone']) ? $post['user_timezone'] : 0,
                    'saveSetting' => isset($data['saveSchedSetting']) ? true : false
                );

                $schedResult [] = $b2sShipSend->saveSchedDetails(array_merge($defaultPostData, $sendData), $schedData);
                $content = array_merge($content, $schedResult);
            }
        }
        if (!empty($b2sShipSend->postData)) {
            $sendResult = $b2sShipSend->postPublish();
            $content = array_merge($content, $sendResult);
        }

        echo json_encode(array('result' => true, 'content' => $content));
        wp_die();
    }

    public function saveSocialMetaTags() {

        $options = new B2S_Options(0, 'B2S_PLUGIN_GENERAL_OPTIONS');

        $og_active = (!isset($_POST['b2s_og_active'])) ? 0 : 1;
        $options->_setOption('og_active', $og_active);
        $options->_setOption('og_default_title', $_POST['b2s_og_default_title']);
        $options->_setOption('og_default_desc', $_POST['b2s_og_default_desc']);
        $options->_setOption('og_default_image', $_POST['b2s_og_default_image']);

        $card_active = (!isset($_POST['b2s_card_active'])) ? 0 : 1;
        $options->_setOption('card_active', $card_active);
        $options->_setOption('card_default_title', $_POST['b2s_card_default_title']);
        $options->_setOption('card_default_desc', $_POST['b2s_card_default_desc']);
        $options->_setOption('card_default_image', $_POST['b2s_card_default_image']);

        $meta = B2S_Meta::getInstance();
        $result = array('result' => true);
        $result['b2s'] = ($card_active == 1 || $og_active == 1) ? true : false;
        $result['yoast'] = $meta->is_yoast_seo_active();
        $result['aioseop'] = $meta->is_aioseop_active();
        $result['webdados'] = $meta->is_webdados_active();
        echo json_encode($result);
        wp_die();
    }

    public function resetSocialMetaTags() {
        global $wpdb;
        $sql = "DELETE FROM " . $wpdb->postmeta . " WHERE meta_key = %s";
        $sql = $wpdb->prepare($sql, "_b2s_post_meta");
        $wpdb->query($sql);
        echo json_encode(array('result' => true));
        wp_die();
    }

    public function saveUserNetworkSettings() {

        if (isset($_POST['short_url'])) {
            $post = array('token' => B2S_PLUGIN_TOKEN,
                'action' => 'saveSettings',
                'short_url' => (int) $_POST['short_url']);
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $post));
            if ($result->result == true) {
                echo json_encode(array('result' => true, 'content' => (((int) $_POST['short_url'] >= 1) ? 0 : 1)));
                wp_die();
            }

            echo json_encode(array('result' => true, 'content' => (isset($_POST['short_url']) ? (int) $_POST['short_url'] : 0)));
            wp_die();
        }

        if (isset($_POST['type']) && $_POST['type'] == 'post_format') {
            $options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
            $post_format = $options->_getOption('post_format');

            $post_format[(int) $_POST['network_id']] = array();

            if (isset($_POST['type-format']) && is_array($_POST['type-format'])) {
                $post_format[(int) $_POST['network_id']] = $_POST['type-format'];
            } else {
                $post_format[(int) $_POST['network_id']] = array('all' => $_POST['all']);
            }

            $options->_setOption('post_format', $post_format);
            echo json_encode(array('result' => true));
            wp_die();
        }

        if (isset($_POST['allow_shortcode'])) {
            if ((int) $_POST['allow_shortcode'] == 1) {
                delete_option('B2S_PLUGIN_USER_ALLOW_SHORTCODE_' . B2S_PLUGIN_BLOG_USER_ID);
            } else {
                update_option('B2S_PLUGIN_USER_ALLOW_SHORTCODE_' . B2S_PLUGIN_BLOG_USER_ID, 1);
            }
            echo json_encode(array('result' => true, 'content' => (((int) $_POST['allow_shortcode'] == 1) ? 0 : 1)));
            wp_die();
        }

        if (isset($_POST['type']) && $_POST['type'] == 'auto_post') {
            $publish = isset($_POST['b2s-settings-auto-post-publish']) && is_array($_POST['b2s-settings-auto-post-publish']) ? $_POST['b2s-settings-auto-post-publish'] : array();
            $update = isset($_POST['b2s-settings-auto-post-update']) && is_array($_POST['b2s-settings-auto-post-update']) ? $_POST['b2s-settings-auto-post-update'] : array();
            $auto_post = array('publish' => $publish, 'update' => $update);
            $options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
            $options->_setOption('auto_post', $auto_post);
            echo json_encode(array('result' => true));
            wp_die();
        }

        if (isset($_POST['user_time_zone']) && !empty($_POST['user_time_zone'])) {
            $options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
            $options->_setOption('user_time_zone', $_POST['user_time_zone']);
            echo json_encode(array('result' => true));
            wp_die();
        }

        echo json_encode(array('result' => false));
        wp_die();
    }

    public function saveUserMandant() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Network/Save.php');
        $mandant = isset($_POST['mandant']) ? strip_tags($_POST['mandant']) : '';
        if (empty($mandant)) {
            echo json_encode(array('result' => false, 'content' => ""));
            wp_die();
        }
        $mandantResult = B2S_Network_Save::saveUserMandant($mandant);
        echo json_encode(array('result' => $mandantResult['result'], 'mandantId' => $mandantResult['mandantId'], 'mandantName' => $mandantResult['mandantName'], 'content' => $mandantResult['content']));
        wp_die();
    }

    public function deleteUserMandant() {
        if (isset($_POST['mandantId']) && (int) $_POST['mandantId'] > 0) {
            $post = array('token' => B2S_PLUGIN_TOKEN,
                'action' => 'deleteUserMandant',
                'mandantId' => (int) $_POST['mandantId']);
            $deleteResult = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $post));
            if ($deleteResult->result == true) {
                global $wpdb;
                $wpdb->delete('b2s_user_network_settings', array('mandant_id' => $_POST['mandantId'], 'blog_user_id' => B2S_PLUGIN_BLOG_USER_ID), array('%d', '%d'));
                echo json_encode(array('result' => true, 'mandantId' => (int) $_POST['mandantId']));
                wp_die();
            }
        }
        echo json_encode(array('result' => false, 'mandantId' => ''));
        wp_die();
    }

    public function deleteUserAuth() {
        if (isset($_POST['networkAuthId']) && (int) $_POST['networkAuthId'] > 0 && isset($_POST['networkId']) && (int) $_POST['networkId'] > 0) {
            $post = array('token' => B2S_PLUGIN_TOKEN,
                'action' => 'deleteUserAuth',
                'networkAuthId' => (int) $_POST['networkAuthId']);
            $deleteResult = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $post));
            if ($deleteResult->result == true) {
                global $wpdb;
                $wpdb->delete('b2s_user_network_settings', array('network_auth_id' => $_POST['networkAuthId'], 'blog_user_id' => B2S_PLUGIN_BLOG_USER_ID), array('%d', '%d'));
                echo json_encode(array('result' => true, 'networkId' => (int) $_POST['networkId'], 'networkAuthId' => (int) $_POST['networkAuthId']));
                wp_die();
            }
        }
        echo json_encode(array('result' => false, 'networkId' => 0, 'networkAuthId' => 0));
        wp_die();
    }

    public function updateUserVersion() {
        require_once (B2S_PLUGIN_DIR . '/includes/Tools.php');
        if (isset($_POST['key']) && !empty($_POST['key'])) {
            $post = array('token' => B2S_PLUGIN_TOKEN,
                'action' => 'updateUserVersion',
                'version' => B2S_PLUGIN_VERSION,
                'key' => $_POST['key']);
            $keyResult = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $post));
            if ($keyResult->result == true) {
                B2S_Tools::setUserDetails();
                $lizenzName = unserialize(B2S_PLUGIN_VERSION_TYPE);
                $printName = (isset($keyResult->trail) && $keyResult->trail == true) ? 'FREE-TRIAL' : $lizenzName[$keyResult->version];
                echo json_encode(array('result' => true, 'lizenzName' => $printName));
                wp_die();
            } else if (isset($keyResult->reason)) {
                echo json_encode(array('result' => false, 'reason' => $keyResult->reason));
                wp_die();
            }
        }
        echo json_encode(array('result' => false, 'reason' => 0));
        wp_die();
    }

    public function createTrail() {
        require_once (B2S_PLUGIN_DIR . '/includes/Tools.php');
        if (isset($_POST['vorname']) && !empty($_POST['vorname']) && isset($_POST['nachname']) && !empty($_POST['nachname']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['url']) && !empty($_POST['url'])) {
            $data = array('token' => B2S_PLUGIN_TOKEN,
                'action' => 'createTrail',
                'vorname' => $_POST['vorname'],
                'nachname' => $_POST['nachname'],
                'email' => $_POST['email'],
                'url' => $_POST['url'],
                'lang' => trim(strtolower(substr(B2S_LANGUAGE, 0, 2))));
            $trailResult = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $data));
            if ($trailResult->result == true) {
                B2S_Tools::setUserDetails();
                $lizenzName = unserialize(B2S_PLUGIN_VERSION_TYPE);
                $printName = 'FREE-TRIAL (' . $lizenzName[$trailResult->version] . ')';
                echo json_encode(array('result' => true, 'lizenzName' => $printName));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function updateUserSchedTimePost() {
        require_once (B2S_PLUGIN_DIR . '/includes/B2S/Post/Tools.php');
        if (isset($_POST['postId']) && (int) $_POST['postId'] > 0 && isset($_POST['date']) && !empty($_POST['date']) && isset($_POST['time']) && !empty($_POST['time'])) {
            echo json_encode(B2S_Post_Tools::updateUserSchedTimePost($_POST['postId'], $_POST['date'], $_POST['time'], $_POST['user_timezone']));
            wp_die();
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function deleteUserSchedPost() {
        require_once (B2S_PLUGIN_DIR . '/includes/B2S/Post/Tools.php');

        if (isset($_POST['postId']) && !empty($_POST['postId'])) {
            $postIds = explode(',', $_POST['postId']);
            if (is_array($postIds) && !empty($postIds)) {
                echo json_encode(B2S_Post_Tools::deleteUserSchedPost($postIds));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function deleteUserPublishPost() {
        require_once (B2S_PLUGIN_DIR . '/includes/B2S/Post/Tools.php');
        if (isset($_POST['postId']) && !empty($_POST['postId'])) {
            $postIds = explode(',', $_POST['postId']);
            if (is_array($postIds) && !empty($postIds)) {
                echo json_encode(B2S_Post_Tools::deleteUserPublishPost($postIds));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function sendTrailFeedback() {
        require_once (B2S_PLUGIN_DIR . '/includes/Tools.php');
        if (isset($_POST['feedback']) && !empty($_POST['feedback'])) {
            $post = array('token' => B2S_PLUGIN_TOKEN,
                'action' => 'sendTrailFeedback',
                'feedback' => $_POST['feedback']);
            $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $post));
            if ($result->result == true) {
                echo json_encode(array('result' => true));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function saveUserSettingsSchedTime() {
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Settings/Save.php');
        if (isset($_POST['b2s']['user-sched-time']) && !empty($_POST['b2s']['user-sched-time'])) {
            $settings = new B2S_Settings_Save($_POST['b2s']['user-sched-time']);
            if ($settings->saveSchedTime()) {
                echo json_encode(array('result' => true));
                wp_die();
            }
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function noticeHide() {
        global $wpdb;
        $wpdb->update('b2s_user', array('feature' => 1), array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID), array('%d'), array('%d'));
        echo json_encode(array('result' => true));
        wp_die();
    }

    public function b2sShipNavbarSaveSettings() {
        if (isset($_POST['mandantId'])) {
            global $wpdb;

            $wpdb->delete('b2s_user_network_settings', array('mandant_id' => $_POST['mandantId'], 'blog_user_id' => B2S_PLUGIN_BLOG_USER_ID), array('%d', '%d'));
            if (isset($_POST['selectedAuth']) && is_array($_POST['selectedAuth'])) {
                foreach ($_POST['selectedAuth'] as $k => $networkAuthId) {
                    $wpdb->insert('b2s_user_network_settings', array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID, 'mandant_id' => $_POST['mandantId'], 'network_auth_id' => $networkAuthId), array('%d', '%d', '%d'));
                }
            }
            echo json_encode(array('result' => true));
            wp_die();
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function b2s_network_save_auth_to_settings() {
        if (isset($_POST['mandantId']) && isset($_POST['networkAuthId'])) {
            global $wpdb;
            $mandantCount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(mandant_id)FROM b2s_user_network_settings  WHERE mandant_id =%d AND blog_user_id=%d ", $_POST['mandandId'], B2S_PLUGIN_BLOG_USER_ID));
            if ($mandantCount > 0) {
                $wpdb->insert('b2s_user_network_settings', array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID, 'mandant_id' => $_POST['mandandId'], 'network_auth_id' => $_POST['networkAuthId']), array('%d', '%d', '%d'));
            }
            echo json_encode(array('result' => true));
            wp_die();
        }
        echo json_encode(array('result' => false));
        wp_die();
    }

    public function b2sPostMailUpdate() {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            require_once (B2S_PLUGIN_DIR . '/includes/Tools.php');
            $post = array('action' => 'updateMail',
                'email' => $_POST['email'],
                'lang' => $_POST['lang']);
            B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, $post);
            update_option('B2S_UPDATE_MAIL_' . B2S_PLUGIN_BLOG_USER_ID, $post['email']);
        }
        echo json_encode(array('result' => true));
        wp_die();
    }

    public function b2sCalendarMovePost() {
        global $wpdb;
        if (is_numeric($_POST['b2s_id']) && is_string($_POST['sched_date'])) {
            $sql = "UPDATE b2s_posts "
                    . "SET sched_date = '" . date('Y-m-d H:i:s', strtotime($_POST['sched_date'])) . "', "
                    . "sched_date_utc = '" . B2S_Util::getUTCForDate($_POST['sched_date'], $_POST['user_timezone'] * -1) . "', "
                    . "hook_action = 2 "
                    . "WHERE id = " . $_POST['b2s_id'];

            $wpdb->query($sql);
        }
        echo json_encode(array('result' => true));
        wp_die();
    }

    public function b2sCalendarDelete() {
        global $wpdb;
        if (isset($_POST['b2s_id']) && (int) $_POST['b2s_id'] > 0 && isset($_POST['post_id']) && (int) $_POST['post_id'] > 0) {
            $sql = $wpdb->prepare("SELECT id,post_id FROM b2s_posts WHERE id =%d AND publish_date = %s", (int) $_POST['b2s_id'], "0000-00-00 00:00:00");
            $row = $wpdb->get_row($sql);
            if (isset($row->id) && (int) $row->id == (int) $_POST['b2s_id']) {
                $wpdb->update('b2s_posts', array('hook_action' => 3, 'hide' => 1), array('id' => (int) $_POST['b2s_id']));
            }
            delete_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . $_POST['b2s_id']);
            delete_option('B2S_PLUGIN_POST_META_TAGES_' . (int) $_POST['post_id']);
        }
        echo json_encode(array('result' => true));
        wp_die();
    }

    public function b2sCalendarSave() {
        global $wpdb;
        require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Save.php');
        $post = $_POST;
        $metaOg = false;
        $metaCard = false;

        if ($post['save_method'] == "apply-all") {
            $b2sids = array();
            $sql = "SELECT id "
                    . "FROM b2s_posts "
                    . "WHERE post_id = %d";

            $sql = $wpdb->prepare($sql, array($_POST['post_id']));
            $items = $wpdb->get_results($sql);
            foreach ($items as $item) {
                $b2sids[] = $item->id;
            }
        } else {
            $b2sids = array($post['b2s_id']);
        }

        delete_option('B2S_PLUGIN_POST_META_TAGES_' . (int) $post['post_id']);

        foreach ($b2sids as $b2s_id) {
            $b2sShipSend = new B2S_Calendar_Save();

            $defaultPostData = array('token' => B2S_PLUGIN_TOKEN,
                'blog_user_id' => B2S_PLUGIN_BLOG_USER_ID,
                'post_id' => (int) $post['post_id'],
                'b2s_id' => (int) $b2s_id,
                'default_titel' => isset($post['default_titel']) ? $post['default_titel'] : '',
                'lang' => trim(strtolower(substr(B2S_LANGUAGE, 0, 2))));


            foreach ($post['b2s'] as $networkAuthId => $data) {
                if (!isset($data['url']) || !isset($data['content']) || !isset($data['network_id'])) {
                    continue;
                }

                //Change/Set MetaTags
                if ((int) $data['network_id'] == 1 && $metaOg == false && (int) $post['post_id'] > 0 && isset($data['post_format']) && (int) $data['post_format'] == 0 && isset($post['change_og_meta']) && (int) $post['change_og_meta'] == 1) {  //LinkPost
                    $metaOg = true;
                    $meta = B2S_Meta::getInstance();
                    $meta->getMeta((int) $post['post_id']);
                    if (isset($data['og_title']) && !empty($data['og_title'])) {
                        $meta->setMeta('og_title', $data['og_title']);
                    }
                    if (isset($data['og_desc']) && !empty($data['og_desc'])) {
                        $meta->setMeta('og_desc', $data['og_desc']);
                    }
                    if (isset($data['image_url']) && !empty($data['image_url'])) {
                        $meta->setMeta('og_image', trim($data['image_url']));
                    }
                    $meta->updateMeta((int) $post['post_id']);
                }

                //Change/Set MetaTags
                if ((int) $data['network_id'] == 2 && $metaCard == false && (int) $post['post_id'] > 0 && isset($data['post_format']) && (int) $data['post_format'] == 0 && isset($post['change_card_meta']) && (int) $post['change_card_meta'] == 1) {  //LinkPost
                    $metaCard = true;
                    $meta = B2S_Meta::getInstance();
                    $meta->getMeta((int) $post['post_id']);
                    if (isset($data['card_title']) && !empty($data['card_title'])) {
                        $meta->setMeta('card_title', $data['card_title']);
                    }
                    if (isset($data['card_desc']) && !empty($data['card_desc'])) {
                        $meta->setMeta('card_desc', $data['card_desc']);
                    }
                    if (isset($data['image_url']) && !empty($data['image_url'])) {
                        $meta->setMeta('card_image', trim($data['image_url']));
                    }
                    $meta->updateMeta((int) $post['post_id']);
                }

                $sendData = array("board" => isset($data['board']) ? $data['board'] : '',
                    "group" => isset($data['group']) ? $data['group'] : '',
                    "custom_title" => isset($data['custom_title']) ? strip_tags($data['custom_title']) : '',
                    "content" => (isset($data['content']) && !empty($data['content'])) ? strip_tags(html_entity_decode($data['content']), '<p><h1><h2><br><i><b><a><img>') : '',
                    'url' => isset($data['url']) ? $data['url'] : '',
                    'image_url' => isset($data['image_url']) ? trim($data['image_url']) : '',
                    'tags' => isset($data['tags']) ? $data['tags'] : array(),
                    'network_id' => isset($data['network_id']) ? $data['network_id'] : '',
                    'network_type' => isset($data['network_type']) ? $data['network_type'] : '',
                    'network_display_name' => isset($data['network_display_name']) ? $data['network_display_name'] : '',
                    'network_auth_id' => $networkAuthId,
                    'post_format' => isset($data['post_format']) ? (int) $data['post_format'] : '',
                    'user_timezone' => isset($post['user_timezone']) ? $post['user_timezone'] : 0,
                    'sched_details_id' => isset($post['sched_details_id']) ? $post['sched_details_id'] : null,
                    'publish_date' => isset($post['publish_date']) ? date('Y-m-d H:i:s', strtotime($post['publish_date'])) : date('Y-m-d H:i:s', current_time('timestamp'))
                );

                $schedData = array(
                    'weeks' => isset($data['weeks']) ? $data['weeks'] : 0,
                    'date' => isset($data['date']) ? $data['date'] : array(),
                    'time' => isset($data['time']) ? $data['time'] : array(),
                    'mo' => isset($data['mo']) ? $data['mo'] : array(),
                    'di' => isset($data['di']) ? $data['di'] : array(),
                    'mi' => isset($data['mi']) ? $data['mi'] : array(),
                    'do' => isset($data['do']) ? $data['do'] : array(),
                    'fr' => isset($data['fr']) ? $data['fr'] : array(),
                    'sa' => isset($data['sa']) ? $data['sa'] : array(),
                    'so' => isset($data['so']) ? $data['so'] : array(),
                    'releaseSelect' => 1,
                    'user_timezone' => isset($post['user_timezone']) ? $post['user_timezone'] : 0,
                    'saveSetting' => isset($data['saveSchedSetting']) ? true : false
                );
                
                $b2sShipSend->saveSchedDetails(array_merge($defaultPostData, $sendData), $schedData);
            }

            delete_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . $b2s_id);
        }

        echo json_encode(array('result' => true));
        wp_die();
    }

    public function releaseLocks() {
        require_once(B2S_PLUGIN_DIR . 'includes/Options.php');
        $options = new B2S_Options(get_current_user_id());
        $lock = $options->_getOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED");

        if (isset($_POST['post_id']) && (int) $_POST['post_id'] > 0) {
            delete_option('B2S_PLUGIN_POST_META_TAGES_' . (int) $_POST['post_id']);
        }
        if ($lock) {
            delete_option("B2S_PLUGIN_CALENDAR_BLOCKED_" . $lock);
            $options->_setOption("B2S_PLUGIN_USER_CALENDAR_BLOCKED", false);
        }
    }

}
