<?php

class B2S_Settings_Item {

    private $userSchedTimeData = array();
    private $networkData = array();
    private $settings = array();
    private $lang;
    private $allowPage;
    private $options;
    private $generalOptions;
    private $allowGroup;
    private $timeInfo;

    public function __construct() {
        $this->getSettings();
        $this->getSchedDataByUser();
        $this->options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $this->generalOptions = new B2S_Options(0, 'B2S_PLUGIN_GENERAL_OPTIONS');
        $this->lang = substr(B2S_LANGUAGE, 0, 2);
        $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $this->timeInfo = unserialize(B2S_PLUGIN_SCHED_DEFAULT_TIMES_INFO);
    }

    private function getSettings() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getSettings', 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));
        if (is_object($result) && isset($result->result) && (int) $result->result == 1 && isset($result->portale) && is_array($result->portale)) {
            $this->networkData = $result->portale;
            if (isset($result->settings) && is_object($result->settings)) {
                $this->settings = $result->settings;
            }
        }
    }

    public function getSchedDataByUser() {
        global $wpdb;
        $saveSchedData = $wpdb->get_results($wpdb->prepare("SELECT network_id, network_type, sched_time FROM b2s_post_sched_settings WHERE blog_user_id= %d", B2S_PLUGIN_BLOG_USER_ID));
        if (!empty($saveSchedData)) {
            $this->userSchedTimeData = $saveSchedData;
        }
    }

    private function selectSchedTime($network_id = 0, $network_type = 0) {
        if (!empty($this->userSchedTimeData) && is_array($this->userSchedTimeData)) {
            foreach ($this->userSchedTimeData as $k => $v) {
                if ((int) $network_id == (int) $v->network_id && (int) $network_type == (int) $v->network_type) {
                    $slug = ($this->lang == 'en') ? 'h:i A' : 'H:i';
                    return date($slug, strtotime(date('Y-m-d ' . $v->sched_time . ':00')));
                }
            }
        }
        return null;
    }

    public function getGeneralSettingsHtml() {

        $isCheckedAllowShortcode = (get_option('B2S_PLUGIN_USER_ALLOW_SHORTCODE_' . B2S_PLUGIN_BLOG_USER_ID) !== false) ? 1 : 0;
        $optionAutoPost = $this->options->_getOption('auto_post');
        $optionUserTimeZone = $this->options->_getOption('user_time_zone');
        $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
        $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
        $userInfo = get_user_meta(B2S_PLUGIN_BLOG_USER_ID);
        $isChecked = (isset($this->settings->short_url) && (int) $this->settings->short_url == 0) ? 1 : 0;
        $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? ' <span class="label label-success label-sm"><a href="#" class="btn-label-premium" data-toggle="modal" data-target="#b2sInfoAutoShareModal">' . __("PREMIUM", "blog2social") . '</a></span>' : '';

        $content = '';
        $content .='<h4>' . __('Account', 'blog2social') . '</h4>';
        $content .='<div class="form-inline">';
        $content .='<div class="col-xs-12 del-padding-left">';
        $content .='<label class="b2s-user-time-zone-label" for="b2s-user-time-zone">' . __('Personal Time Zone', 'blog2social') . '</label>';
        $content .=' <select id="b2s-user-time-zone" class="form-control b2s-select" name="b2s-user-time-zone">';
        $content .= B2S_Util::createTimezoneList($userTimeZone);
        $content .= '</select>';
        $content .= ' <a href="#" data-toggle="modal" data-target="#b2sInfoTimeZoneModal" class="b2s-info-btn hidden-xs">' . __('Info', 'Blog2Social') . '</a>';
        $content .='</div>';
        $content .='<br><div class="b2s-settings-time-zone-info">' . __('Timezone for Scheduling', 'blog2social') . ' (' . __('User', 'blog2social') . ': ' . (isset($userInfo['nickname'][0]) ? $userInfo['nickname'][0] : '-') . ') <code id="b2s-user-time">' . B2S_Util::getLocalDate($userTimeZoneOffset, substr(B2S_LANGUAGE, 0, 2)) . '</code></span></div>';
        $content .='</div>';
        $content .='<div class="clearfix"></div>';
        $content .= '<br>';

        $content .='<hr>';
        $content .='<h4>' . __('Auto-Posting', 'blog2social') . $isPremium . '</h4>';
        $content .='<label class="b2s-auto-post-label">' . __('What post type of items do you want load the Auto-Poster?', 'blog2social') . ' <a href="#" data-toggle="modal" data-target="#b2sInfoAutoShareModal" class="b2s-info-btn del-padding-left">' . __('Info', 'Blog2Social') . '</a></label>';
        $content .='<br>';
        $content .='<br>';
        $content .= '<form id = "b2s-user-network-settings-auto-post" method = "post" ' . (!empty($isPremium) ? 'class="b2s-btn-disabled"' : '') . ' >';
        $content .='<div class="row">';
        $content .='<div class="col-xs-12 col-md-2">';
        $content .='<label class="b2s-auto-post-publish-label">' . __('new posts', 'blog2social') . '</label>';
        $content .='<br><small><button class="btn btn-link btn-xs hidden-xs b2s-post-type-select-btn" data-post-type="publish" data-select-toogle-state="0" data-select-toogle-name="' . __('Unselect all', 'blog2social') . '">' . __('Select all', 'blog2social') . '</button></small>';
        $content .='</div>';
        $content .='<div class="col-xs-12 col-md-6">';
        $content .= $this->getPostTypesHtml($optionAutoPost);
        $content .='</div>';
        $content .='</div>';
        $content .='<br>';
        $content .='<div class="row">';
        $content .='<div class="col-xs-12 col-md-2">';
        $content .='<label class="b2s-auto-post-update-label">' . __('updating existing posts', 'blog2social') . '</label>';
        $content .='<br><small><button class="btn btn-link btn-xs hidden-xs b2s-post-type-select-btn" data-post-type="update" data-select-toogle-state="0" data-select-toogle-name="' . __('Unselect all', 'blog2social') . '">' . __('Select all', 'blog2social') . '</button></small>';
        $content .='</div>';
        $content .='<div class="col-xs-12 col-md-6">';
        $content .= $this->getPostTypesHtml($optionAutoPost, 'update');
        $content .='</div>';
        $content .='</div>';
        if (B2S_PLUGIN_USER_VERSION > 0) {
            $content .= '<button class="pull-right btn btn-primary btn-sm" type="submit">';
        } else {
            $content .= '<button class="pull-right btn btn-primary btn-sm b2s-btn-disabled b2s-save-settings-pro-info" data-toggle = "modal" data-target = "#b2sInfoAutoShareModal">';
        }
        $content .= __('Save', 'blog2social') . '</button>';
        $content .= '<input type="hidden" name="action" value="b2s_user_network_settings">';
        $content .= '<input type="hidden" name="type" value="auto_post">';
        $content .='</form>';
        $content .='<div class="clearfix"></div>';
        $content .='<br>';
        $content .='<hr>';
        $content .='<h4>' . __('Content', 'blog2social') . '</h4>';
        $content .= '<input type="checkbox" value="' . $isChecked . '" id="b2s-user-network-settings-short-url" ' . (($isChecked == 0) ? 'checked="checked"' : '') . ' /><label for="b2s-user-network-settings-short-url"> ' . __('use b2s.pm Link Shortener', 'blog2social') . ' <a href="#" data-toggle="modal" data-target="#b2sInfoLinkModal" class="b2s-info-btn del-padding-left">' . __('Info', 'Blog2Social') . '</a></label>';
        $content .= '<br>';
        $content .= '<input type="checkbox" value="' . $isCheckedAllowShortcode . '" id="b2s-user-network-settings-allow-shortcode" ' . (($isCheckedAllowShortcode == 1) ? 'checked="checked"' : '') . ' /><label for="b2s-user-network-settings-allow-shortcode"> ' . __('allow shortcodes in my post', 'blog2social') . ' <a href="#" data-toggle="modal" data-target="#b2sInfoAllowShortcodeModal" class="b2s-info-btn del-padding-left">' . __('Info', 'Blog2Social') . '</a></label>';
        $content .= '<br>';

        return $content;
    }

    public function getSocialMetaDataHtml() {

        $og = $this->generalOptions->_getOption('og_active');
        $card = $this->generalOptions->_getOption('card_active');
        $og_isChecked = ($og !== false && $og == 1) ? 0 : 1;
        $card_isChecked = ($card !== false && $card == 1) ? 0 : 1;

        $content = '<div class="col-md-12">';
        if (B2S_PLUGIN_ADMIN) {
            $content .= '<a href="#" class="pull-right btn btn-primary btn-xs b2sClearSocialMetaTags">' . __('Reset all page and post meta data', 'blog2social') . '</a>';
        }
        $content .='<strong>' . __('This is a global feature for your blog, which can only be edited by users with admin rights.', 'blog2social') . '</strong>';
        $content .= '<br>';
        $content .='<h4>' . __('Meta Tags Settings for Posts and Pages', 'blog2social') . '</h4>';
        $content .= '<input type="checkbox" value="' . $og_isChecked . '" name="b2s_og_active" id="b2s_og_active" ' . (($og_isChecked == 0) ? 'checked="checked"' : '') . ' /><label for="b2s_og_active"> ' . __('Add Open Graph meta tags to your shared posts or pages, required by Facebook and other social networks to display your post or page image, title and description correctly.', 'blog2social', 'blog2social') . ' <a href="#" class="b2s-load-info-meta-tag-modal b2s-info-btn del-padding-left" data-meta-type="og" data-meta-origin="settings">' . __('Info', 'Blog2Social') . '</a></label>';
        $content .='<br>';
        $content .= '<input type="checkbox" value="' . $card_isChecked . '" name="b2s_card_active" id="b2s_card_active" ' . (($card_isChecked == 0) ? 'checked="checked"' : '') . ' /><label for="b2s_card_active"> ' . __('Add Twitter Card meta tags to your shared posts or pages, required by Twitter to display your post or page image, title and description correctly.', 'blog2social', 'blog2social') . ' <a href="#" class="b2s-load-info-meta-tag-modal b2s-info-btn del-padding-left" data-meta-type="card" data-meta-origin="settings">' . __('Info', 'Blog2Social') . '</a></label>';
        $content .='<br><br><hr>';
        $content .='<h4>' . __('Frontpage Settings', 'blog2social') . '</h4>';
        $content .='<div><img alt="" class="b2s-post-item-network-image" src="' . plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE) . '"> <b>Facebook</b></div>';
        $content .= '<p>' . __('Add the default Open Graph parameters for title, description and image you want Facebook to display, if you share the frontpage of your blog as link post (http://www.yourblog.com)', 'blog2social') . '</p>';
        $content .='<br>';
        $content .='<div class="col-md-8">';
        $content .='<div class="form-group"><label for="b2s_og_default_title"><strong>' . __("Title", "blog2social") . ':</strong></label><input type="text" value="' . ( ($this->generalOptions->_getOption('og_default_title') !== false) ? $this->generalOptions->_getOption('og_default_title') : get_bloginfo('name') ) . '" name="b2s_og_default_title" class="form-control" id="b2s_og_default_title"></div>';
        $content .='<div class="form-group"><label for="b2s_og_default_desc"><strong>' . __("Description", "blog2social") . ':</strong></label><input type="text" value="' . ( ($this->generalOptions->_getOption('og_default_desc') !== false) ? $this->generalOptions->_getOption('og_default_desc') : get_bloginfo('description') ) . '" name="b2s_og_default_desc" class="form-control" id="b2s_og_default_desc"></div>';
        $content .='<div class="form-group"><label for="b2s_og_default_image"><strong>' . __("Image URL", "blog2social") . ':</strong></label> <button class="btn btn-link btn-xs b2s-upload-image pull-right" data-id="b2s_og_default_image">' . __("Image upload / Mediathek", "blog2social") . '</button><input type="text" value="' . (($this->generalOptions->_getOption('og_default_image') !== false) ? $this->generalOptions->_getOption('og_default_image') : '') . '" name="b2s_og_default_image" class="form-control" id="b2s_og_default_image">';
        $content .='<span>' . __('Please note: Facebook supports images with a minimum dimension of 200x200 pixels and an aspect ratio of 1:1.', 'blog2social') . '</span>';
        $content .='</div>';
        $content .='</div>';
        $content .='<div class="clearfix"></div>';
        $content .='<br>';
        $content .='<div><img alt="" class="b2s-post-item-network-image" src="' . plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE) . '"> <b>Twitter</b></div>';
        $content .='<p>' . __('Add the default Twitter Card parameters for title, description and image you want Twitter to display, if you share the frontpage of your blog as link post (http://www.yourblog.com)', 'blog2social') . '</p>';
        $content .='<br>';
        $content .='<div class="col-md-8">';
        $content .='<div class="form-group"><label for="b2s_card_default_title"><strong>' . __("Title", "blog2social") . ':</strong></label><input type="text" value="' . ( ($this->generalOptions->_getOption('card_default_title') !== false) ? $this->generalOptions->_getOption('card_default_title') : get_bloginfo('name') ) . '" name="b2s_card_default_title" class="form-control" id="b2s_card_default_title"></div>';
        $content .='<div class="form-group"><label for="b2s_card_default_desc"><strong>' . __("Description", "blog2social") . ':</strong></label><input type="text" value="' . ( ($this->generalOptions->_getOption('card_default_desc') !== false) ? $this->generalOptions->_getOption('card_default_desc') : get_bloginfo('description') ) . '" name="b2s_card_default_desc" class="form-control" id="b2s_card_default_desc"></div>';
        $content .='<div class="form-group"><label for="b2s_card_default_image"><strong>' . __("Image URL", "blog2social") . ':</strong></label> <button class="btn btn-link btn-xs pull-right b2s-upload-image" data-id="b2s_card_default_image">' . __("Image upload / Mediathek", "blog2social") . '</button><input type="text" value="' . (($this->generalOptions->_getOption('card_default_image') !== false) ? $this->generalOptions->_getOption('card_default_image') : '') . '" name="b2s_card_default_image" class="form-control" id="b2s_card_default_image">';
        $content .='<span>' . __('Please note: Twitter supports images with a minimum dimension of 144x144 pixels and a maximum dimension of 4096x4096 pixels and less than 5 BM. The image will be cropped to a square. Twitter supports JPG, PNG, WEBP and GIF formats.', 'blog2social') . '</span>';
        $content .='</div>';
        $content .='</div>';
        $content .='</div>';

        return $content;
    }

    public function getNetworkSettingsHtml() {
        $optionPostFormat = $this->options->_getOption('post_format');
        $content = '';

        if (B2S_PLUGIN_USER_VERSION < 2) {
            $content .='<div class="alert alert-default">';
            $content .= '<b>' . __('Did you know?', 'blog2social') . '</b><br>';
            $content .= __('With Premium Pro, you can change the custom post format photo post or link post for each individual social media post and channel (profile, page, group).', 'blog2social') . ' <a target="_blank" href="' . B2S_Tools::getSupportLink('affiliate') . '">' . __('Upgrade to Premium Pro now.', 'blog2social') . '</a>';
            $content .='<hr></div>';
        }

        foreach (array(1, 2) as $n => $networkId) { //FB,TW
            $type = ($networkId == 1) ? array(0, 1, 2) : array(0);
            foreach ($type as $t => $typeId) { //Profile,Page,Group
                $networkName = ($networkId == 1) ? 'Facebook' : 'Twitter';
                if (!isset($optionPostFormat[$networkId]['all'])){
                    $optionPostFormat[$networkId]['all'] = 0;
                }

                $linkPost = ((isset($optionPostFormat[$networkId]) && is_array($optionPostFormat[$networkId]) && (((int) $optionPostFormat[$networkId]['all'] == 0) || (isset($optionPostFormat[$networkId][$typeId]) && (int) $optionPostFormat[$networkId][$typeId] == 0)) ) ? 'b2s-settings-checked' : (!isset($optionPostFormat[$networkId]) ? 'b2s-settings-checked' : '' ));
                $photoPost = empty($linkPost) ? 'b2s-settings-checked' : '';

                $content .='<div class="b2s-user-network-settings-post-format-area col-md-12" data-network-type="' . $typeId . '"  data-network-id="' . $networkId . '" data-network-title="' . $networkName . '" style="display:none;" >';
                $content .='<div class="col-md-6 col-xs-12">';
                $content .= '<b>1) ' . __('Link Post', 'blog2social') . ' <span class="glyphicon glyphicon-link b2s-color-green"></span></b><br><br>';
                $content .= '<label><input type="radio" name="b2s-user-network-settings-post-format-' . $networkId . '" class="b2s-user-network-settings-post-format ' . $linkPost . '" data-network-type="' . $typeId . '" data-network-id="' . $networkId . '" data-post-format="0" value="0"/><img class="img-responsive b2s-display-inline" src="' . plugins_url('/assets/images/settings/b2s-post-format-' . $networkId . '-1-' . (($this->lang == 'de') ? $this->lang : 'en') . '.png', B2S_PLUGIN_FILE) . '">';
                $content .='</label>';
                $content .='<br><br>';
                $content .= __('The link post format displays posts title, link address and the first one or two sentences of the post. The networks scan this information from your META or OpenGraph. Link posts display the post image, you selected in your WordPress. In case, you have not selected a post image, some networks display the first image detected on your page. The image links to your blog post.', 'blog2social');
                $content .='</div>';
                $content .='<div class="col-md-6 col-xs-12">';
                $content .= '<b>2) ' . __('Photo Post', 'blog2social') . ' <span class="glyphicon glyphicon-picture b2s-color-green"></span></b><br><br>';
                $content .= '<label><input type="radio" name="b2s-user-network-settings-post-format-' . $networkId . '" class="b2s-user-network-settings-post-format ' . $photoPost . '" data-network-type="' . $typeId . '" data-network-id="' . $networkId . '" data-post-format="1" value="1" /><img class="img-responsive b2s-display-inline" src="' . plugins_url('/assets/images/settings/b2s-post-format-' . $networkId . '-2-' . (($this->lang == 'de') ? $this->lang : 'en') . '.png', B2S_PLUGIN_FILE) . '">';
                $content .='</label>';
                $content .='<br><br>';
                $content .= __('A photo or image post displays the selected image in the one-page preview of Blog2Social and your comment above the image. The image links to the image view on your image gallery in the respective network. Blog2Social adds the link to your post in your comment. The main benefit of photo posts is that your image is uploaded to your personal image albums or gallery. In Facebook you can edit the album’s name with a description of your choice.', 'blog2social');
                $content .='</div>';
                $content .='</div>';
            }
        }
        return $content;
    }

    public function getNetworkSettingsPostFormatHtml($networkId = 1) {

        $optionPostFormat = $this->options->_getOption('post_format');

        //Take old settings
        if (!isset($optionPostFormat[$networkId])) {
            $oldPostFormatSettings = ($networkId == 1) ? (isset($this->settings->network_post_format_1) ? (int) $this->settings->network_post_format_1 : 0) : (isset($this->settings->network_post_format_2) ? (int) $this->settings->network_post_format_2 : 1);  // Twitter Default Photopost
            $post_format[$networkId] = array();
            $post_format[$networkId] = array('all' => $oldPostFormatSettings);
            $optionPostFormat = $post_format;
            $this->options->_setOption('post_format', $post_format);
        }

        if (!isset($optionPostFormat[$networkId]['all'])) {
            $optionPostFormat[$networkId]['all'] = 0;
        }

        $disabledInputType = (B2S_PLUGIN_USER_VERSION < 2) ? 'disabled' : '';
        $disabledInputAll = (B2S_PLUGIN_USER_VERSION == 0) ? 'disabled' : '';
        $disabledTextType = (B2S_PLUGIN_USER_VERSION < 2) ? 'font-gray' : '';
        $disabledTextAll = (B2S_PLUGIN_USER_VERSION == 0) ? 'font-gray' : '';
        $textAll = ($networkId == 1) ? __('All', 'blog2social') : __('Profile', 'blog2social');

        $content = '';
        $content .='<div class="col-md-6 col-xs-12">';
        $content .= '<b>1) ' . __('Link Post', 'blog2social') . ' <span class="glyphicon glyphicon-link b2s-color-green"></span></b><br><br>';
        $content .= '<img class="img-responsive b2s-display-inline" src="' . plugins_url('/assets/images/settings/b2s-post-format-' . $networkId . '-1-' . (($this->lang == 'de') ? $this->lang : 'en') . '.png', B2S_PLUGIN_FILE) . '">';
        $content .= '<br><br>';
        $content .='<div class="padding-left-15">';

        if ((B2S_PLUGIN_USER_VERSION < 2 && $networkId == 1) || $networkId == 2) {
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right b2s-input-margin-bottom-5"><input type="radio" ' . $disabledInputAll . ' id="all-' . $networkId . '-1"  ' . ( (isset($optionPostFormat[$networkId]) && is_array($optionPostFormat[$networkId]) && (int) $optionPostFormat[$networkId]['all'] == 0) ? 'checked' : ((!isset($optionPostFormat[$networkId])) ? 'checked' : '' )) . '   name="all" value="0"><label class="' . $disabledTextAll . '" for="all-' . $networkId . '-1">' . $textAll . '</label></div><div class="clearfix"></div>';
        }
        if ($networkId == 1) {
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right"><input type="radio" ' . $disabledInputType . ' id="type[0]-' . $networkId . '-1" ' . ((isset($optionPostFormat[$networkId][0]) && (int) $optionPostFormat[$networkId][0] == 0) ? 'checked' : ( (int) $optionPostFormat[$networkId]['all'] == 0 && !isset($optionPostFormat[$networkId][0])) ? 'checked' : '') . ' name="type-format[0]" value="0"><label class="' . $disabledTextType . '" for="type[0]-' . $networkId . '-1">' . __('Profile', 'blog2social') . '</label></div><div class="clearfix"></div>';
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right"><input type="radio" ' . $disabledInputType . ' id="type[1]-' . $networkId . '-1" ' . ( (isset($optionPostFormat[$networkId][1]) && (int) $optionPostFormat[$networkId][1] == 0) ? 'checked' : ( (int) $optionPostFormat[$networkId]['all'] == 0 && !isset($optionPostFormat[$networkId][0])) ? 'checked' : '') . ' name="type-format[1]" value="0"><label class="' . $disabledTextType . '" for="type[1]-' . $networkId . '-1">' . __('Page', 'blog2social') . '</label></div><div class="clearfix"></div>';
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right"><input type="radio" ' . $disabledInputType . ' id="type[2]-' . $networkId . '-1" ' . ( (isset($optionPostFormat[$networkId][2]) && (int) $optionPostFormat[$networkId][2] == 0) ? 'checked' : ( (int) $optionPostFormat[$networkId]['all'] == 0 && !isset($optionPostFormat[$networkId][0])) ? 'checked' : '') . ' name="type-format[2]" value="0"><label class="' . $disabledTextType . '" for="type[2]-' . $networkId . '-1">' . __('Group', 'blog2social') . '</label></div><div class="clearfix"></div>';
        }
        $content .='</div>';
        $content .='</div>';

        $content .='<div class="col-md-6 col-xs-12">';
        $content .= '<b>2) ' . __('Photo Post', 'blog2social') . ' <span class="glyphicon glyphicon-picture b2s-color-green"></span></b><br><br>';
        $content .= '<img class="img-responsive b2s-display-inline" src="' . plugins_url('/assets/images/settings/b2s-post-format-' . $networkId . '-2-' . (($this->lang == 'de') ? $this->lang : 'en') . '.png', B2S_PLUGIN_FILE) . '">';
        $content .= '<br><br>';
        $content .='<div class="padding-left-15">';

        if ((B2S_PLUGIN_USER_VERSION < 2 && $networkId == 1) || $networkId == 2) {
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right b2s-input-margin-bottom-5"><input type="radio" ' . $disabledInputAll . ' id="all-' . $networkId . '-2" ' . ((isset($optionPostFormat[$networkId]) && is_array($optionPostFormat[$networkId]) && (int) $optionPostFormat[$networkId]['all'] == 1) ? 'checked' : '') . '  name="all"  value="1"><label class="' . $disabledTextAll . '" for="all-' . $networkId . '-2">' . $textAll . '</label></div><div class="clearfix"></div>';
        }
        if ($networkId == 1) {
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right"><input type="radio" ' . $disabledInputType . ' id="type[0]-' . $networkId . '-2" ' . ( (isset($optionPostFormat[$networkId][0]) && (int) $optionPostFormat[$networkId][0] == 1) ? 'checked' : ( (int) $optionPostFormat[$networkId]['all'] == 1 && !isset($optionPostFormat[$networkId][0])) ? 'checked' : '') . ' name="type-format[0]" value="1"><label class="' . $disabledTextType . '" for="type[0]-' . $networkId . '-2">' . __('Profile', 'blog2social') . '</label></div><div class="clearfix"></div>';
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right"><input type="radio" ' . $disabledInputType . ' id="type[1]-' . $networkId . '-2" ' . ( (isset($optionPostFormat[$networkId][1]) && (int) $optionPostFormat[$networkId][1] == 1) ? 'checked' : ( (int) $optionPostFormat[$networkId]['all'] == 1 && !isset($optionPostFormat[$networkId][1])) ? 'checked' : '') . ' name="type-format[1]" value="1"><label class="' . $disabledTextType . '" for="type[1]-' . $networkId . '-2">' . __('Page', 'blog2social') . '</label></div><div class="clearfix"></div>';
            $content .= '<div class="col-lg-3 col-md-4 col-xs-5 del-padding-left del-padding-right"><input type="radio" ' . $disabledInputType . ' id="type[2]-' . $networkId . '-2" ' . ( (isset($optionPostFormat[$networkId][2]) && (int) $optionPostFormat[$networkId][2] == 1) ? 'checked' : ( (int) $optionPostFormat[$networkId]['all'] == 1 && !isset($optionPostFormat[$networkId][1])) ? 'checked' : '') . ' name="type-format[2]" value="1"><label class="' . $disabledTextType . '" for="type[2]-' . $networkId . '-2">' . __('Group', 'blog2social') . '</label></div><div class="clearfix"></div>';
        }
        $content .='</div>';
        $content .='</div>';
        return $content;
    }

    //view=ship
    public function setNetworkSettingsHtml() {
        $optionPostFormat = $this->options->_getOption('post_format');

        $content = "<input type='hidden' class='b2sNetworkSettingsPostFormatText' value='" . json_encode(array(__('Link Post', 'blog2social'), __('Photo Post', 'blog2social'))) . "'/>";
        foreach (array(1, 2) as $n => $networkId) { //FB,TW
            //Take old settings
            if (!isset($optionPostFormat[$networkId])) {
                $oldPostFormatSettings = ($networkId == 1) ? (isset($this->settings->network_post_format_1) ? (int) $this->settings->network_post_format_1 : 0) : (isset($this->settings->network_post_format_2) ? (int) $this->settings->network_post_format_2 : 1);  // Twitter Default Photopost
                $post_format[$networkId] = array();
                $post_format[$networkId] = array('all' => $oldPostFormatSettings);
                $optionPostFormat = $post_format;
                $this->options->_setOption('post_format', $post_format);
            }

            $type = ($networkId == 1) ? array(0, 1, 2) : array(0);
            foreach ($type as $t => $typeId) { //Profile,Page,Group                
                if (!isset($optionPostFormat[$networkId]['all']) && !isset($optionPostFormat[$networkId][$typeId])) { //DEFAULT
                    $optionPostFormat[$networkId]['all'] = 0;
                }
                $value = ((isset($optionPostFormat[$networkId]) && is_array($optionPostFormat[$networkId]) && ((isset($optionPostFormat[$networkId]['all']) && (int) $optionPostFormat[$networkId]['all'] == 0) || (isset($optionPostFormat[$networkId][$typeId]) && (int) $optionPostFormat[$networkId][$typeId] == 0)) ) ? 0 : (!isset($optionPostFormat[$networkId]) ? 0 : 1 ));
                $content .= "<input type='hidden' class='b2sNetworkSettingsPostFormatCurrent' data-network-id='" . $networkId . "' data-network-type='" . $typeId . "' value='" . (int) $value . "' />";
            }
        }
        return $content;
    }

    public function getSchedSettingsHtml() {
        if (!empty($this->networkData)) {
            $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? 'class="b2s-btn-disabled"' : '';
            $content = '<form id = "b2sSaveUserSettingsSchedTime" method = "post"  ' . $isPremium . '>
        <ul class = "list-group b2s-settings-sched-details-container-list">';
            foreach ($this->networkData as $k => $v) {
                $content .= '<li class = "list-group-item">
        <div class = "media">
        <img class = "pull-left hidden-xs b2s-img-network" src = "' . plugins_url('/assets/images/portale/' . $v->id . '_flat.png', B2S_PLUGIN_FILE) . '" alt = "' . $v->name . '">
        <div class = "media-body network">
        <h4><span class = "pull-left">' . ucfirst($v->name) . '</span>
        <div class = "b2s-box-sched-time-area">';

                $content .= '<div class = "col-xs-12">
        <div class = "form-group col-xs-2">
        <label class = "b2s-box-sched-time-area-label">' . __('Profile', 'blog2social') . '</label>
        <input class = "form-control b2s-box-sched-time-input b2s-settings-sched-item-input-time form-control valid" type = "text" value = "' . $this->selectSchedTime($v->id, 0) . '" readonly = "" data-network-id = "' . $v->id . '" data-network-type = "0" name = "b2s[user-sched-time][' . $v->id . '][0]">';
                if (in_array($v->id, $this->allowPage)) {
                    $content .= '<label class = "b2s-box-sched-time-area-label">' . __('Page', 'blog2social') . '</label>
        <input class = "form-control b2s-box-sched-time-input b2s-settings-sched-item-input-time form-control valid" type = "text" value = "' . $this->selectSchedTime($v->id, 1) . '" readonly = "" data-network-id = "' . $v->id . '" data-network-type = "1" name = "b2s[user-sched-time][' . $v->id . '][1]">';
                }
                if (in_array($v->id, $this->allowGroup)) {
                    $content .= '<label class = "b2s-box-sched-time-area-label">' . __('Group', 'blog2social') . '</label>
        <input class = "form-control b2s-box-sched-time-input b2s-settings-sched-item-input-time form-control valid" type = "text" value = "' . $this->selectSchedTime($v->id, 2) . '" readonly = "" data-network-id = "' . $v->id . '" data-network-type = "2" name = "b2s[user-sched-time][' . $v->id . '][2]">';
                }
                $content .= '</div>';

                if (isset($this->timeInfo[$v->id]) && !empty($this->timeInfo[$v->id]) && is_array($this->timeInfo[$v->id])) {
                    $time = '';
                    $slug = ($this->lang == 'de') ? __('Uhr', 'blog2social') : '';
                    foreach ($this->timeInfo[$v->id] as $k => $v) {
                        $time .= B2S_Util::getTimeByLang($v[0], $this->lang) . '-' . B2S_Util::getTimeByLang($v[1], $this->lang) . $slug . ', ';
                    }
                    $content .= '<div class = "form-group col-xs-10 hidden-xs hidden-sm"><div class = "b2s-settings-sched-time-info">' . __('Best times to post', 'blog2social') . ': ' . substr($time, 0, -2) . '</div></div>';
                }
                $content .= '</div>
        </div>
        </h4>
        </div>
        </div>
        </li>';
            }
            $content .= '</ul><div class = "pull-right">';
            if (B2S_PLUGIN_USER_VERSION > 0) {
                $content .= '<button id="b2s-save-time-settings-btn" class = "btn btn-primary" type = "submit">';
            } else {
                $content .= '<button id="b2s-save-time-settings-btn" class= "btn btn-primary b2s-btn-disabled b2s-save-settings-pro-info" data-title = "' . __('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social') . '" data-toggle = "modal" data-target = "#b2sInfoSchedTimesModal">';
            }
            $content .= __('save', 'blog2social') . '</button>';
            $content .= '</div>';
            $content .= '<input id = "action" type = "hidden" value = "b2s_save_user_settings_sched_time" name = "action">';
            $content .= '</form>';
        } else {
            $content = '<div class = "alert alert-info">' . __('Sorry, we can not load your data at the moment...', 'blog2social') . '</div>';
        }
        return $content;
    }

    private function getPostTypesHtml($selected = array(), $type = 'publish') {
        $content = '';
        $selected = (is_array($selected) && isset($selected[$type])) ? $selected[$type] : array();
        $post_types = get_post_types(array('public' => true));
        if (is_array($post_types) && !empty($post_types)) {
            foreach ($post_types as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $selItem = (in_array($v, $selected)) ? 'checked' : '';
                    $content .= ' <div class="b2s-post-type-list"><input id="b2s-post-type-item-' . $type . '-' . $v . '" class="b2s-post-type-item-' . $type . '" value="' . $v . '" name="b2s-settings-auto-post-' . $type . '[]" type="checkbox" ' . $selItem . '><label for="b2s-post-type-item-' . $type . '-' . $v . '">' . $v . '</label></div>';
                }
            }
        }
        return $content;
    }

}
