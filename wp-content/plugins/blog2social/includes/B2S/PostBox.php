<?php

class B2S_PostBox {

    private $b2sSiteUrl;
    private $postLang;

    public function __construct() {
        $this->b2sSiteUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/');
        $this->postLang = strtolower(substr(get_locale(), 0, 2));
    }

    public function getPostBox($postId = 0, $postType = 'post', $postStatus = '') {

        $isChecked = "";
        $lastPost = "";

        $userOption = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
        $autoPostOption = $userOption->_getOption('auto_post');
        $optionUserTimeZone = $userOption->_getOption('user_time_zone');
        $userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
        $userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
        $selectedProfileID = get_option('B2S_PLUGIN_SAVE_META_BOX_AUTO_SHARE_PROFILE_USER_' . B2S_PLUGIN_BLOG_USER_ID);
        $b2sHeartbeatFaqLink = '<a target="_blank" href="' . ((substr(B2S_LANGUAGE, 0, 2) == 'de' || (isset($_GET['lang']) && $_GET['lang'] == 'de')) ? 'https://www.blog2social.com/de/faq/content/1/63/de/systemvoraussetzungen-fuer-die-installation-von-blog2social.html' : 'https://www.blog2social.com/en/faq/content/1/58/en/system-requirements-for-installing-blog2social.html') . '">' . __('Please see FAQ', 'blog2social') . '</a>';
        $metaSettings = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
        
        if (B2S_PLUGIN_USER_VERSION > 0) {
            if ($postId > 0) {
                global $wpdb;
                $lastAutopost = $wpdb->get_results($wpdb->prepare("SELECT publish_date FROM b2s_posts WHERE post_id= %d AND blog_user_id= %d AND sched_type = 3 ORDER BY publish_date DESC LIMIT 1", $postId, B2S_PLUGIN_BLOG_USER_ID));
                if (!empty($lastAutopost) && isset($lastAutopost[0]) && !empty($lastAutopost[0]->publish_date) && $lastAutopost[0]->publish_date != '0000-00-00 00:00:00') {
                    $lastPost = '<b>' . __('last auto-post:', 'blog2social') . '</b> ' . B2S_Util::getCustomDateFormat($lastAutopost[0]->publish_date, substr(B2S_LANGUAGE, 0, 2)) . '<br>';
                }
            }

            if ($autoPostOption !== false) {
                $state = ($postId == 0) ? 'publish' : (($postStatus != '' && $postStatus == 'publish') ? 'update' : '');
                if (is_array($autoPostOption) && isset($autoPostOption[$state])) {
                    if (in_array($postType, $autoPostOption[$state])) {
                        $isChecked = 'checked';
                    }
                }
            }
        }

        $content = '<div class="b2s-post-meta-box">
                      <div id="b2s-server-connection-fail" class="b2s-info-error"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-server-connection-fail" title="close notice"></button>' . __('The connection to the server failed. Try again!', 'blog2social') . '</div>
                      <div id="b2s-heartbeat-fail" class="b2s-info-error"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-heartbeat-fail" title="close notice"></button>' . __('WordPress uses heartbeats by default, Blog2Social as well. Please enable heartbeats for using Blog2Social!', 'blog2social') . $b2sHeartbeatFaqLink . ' </div>
                      <div id="b2s-post-meta-box-state-no-publish-future-customize" class="b2s-info-error"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-post-meta-box-state-no-publish-future-customize" title="close notice"></button>' . __('Your post is still on draft or pending status. Please make sure that your post is published or scheduled to be published on this blog. You can then auto-post or schedule and customize your social media posts with Blog2Social.', 'blog2social') . '</div>
                      <div id="b2s-post-meta-box-state-no-auth" class="b2s-info-error"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-post-meta-box-state-no-auth" title="close notice"></button>' . __('There are no authorizations for your selected profile. Please, authorize with a social network or select a other profile.', 'blog2social') . '<a href="' . $this->b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-network' . '" target="_bank">' . __('Network settings', 'blog2social') . '</a></div>
                      <div id="b2s-post-meta-box-state-no-publish-future" class="b2s-info-error"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-post-meta-box-state-no-publish-future" title="close notice"></button>' . __('Your post is still on draft or pending status. Please make sure that your post is published or scheduled to be published on this blog. You can then auto-post or schedule and customize your social media posts with Blog2Social.', 'blog2social') . '</div>
                      <div id="b2s-url-valid-warning" class="b2s-info-warning"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-url-valid-warning" title="close notice"></button>' . __('Notice: Please make sure, that your website address is reachable. The Social Networks do not allow postings from local installations.', 'blog2social') . '</div>
                      <input type="hidden" id="b2s-redirect-url-customize" name="b2s-redirect-url-customize" value="' . $this->b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-ship&postId=' . $postId . '"/>
                      <input type="hidden" id="b2s-user-last-selected-profile-id" name="b2s-user-last-selected-profile-id" value="' . ($selectedProfileID !== false ? (int) $selectedProfileID : 0) . '" />
                      <input type="hidden" id="b2s-home-url" name="b2s-home-url" value="' . get_option('home') . '"/>
                      <input type="hidden" id="b2sLang" name="b2s-user-lang" value="' . strtolower(substr(get_locale(), 0, 2)) . '">
                      <input type="hidden" id="b2sUserLang" name="b2s-user-lang" value="' . strtolower(substr(get_locale(), 0, 2)) . '">
                      <input type="hidden" id="b2sPostLang" name="b2s-post-lang" value="' . substr($this->postLang, 0, 2) . '">
                      <input type="hidden" id="b2s-user-timezone" name="b2s-user-timezone" value="' . $userTimeZoneOffset . '"/>
                      <input type="hidden" id="b2s-post-status" name="b2s-post-status" value="' . trim(strtolower($postStatus)) . '"/>
                      <input type="hidden" name="b2s-post-meta-box-version" id="b2s-post-meta-box-version" value="' . B2S_PLUGIN_USER_VERSION . '"/>
                      <input type="hidden" id="isOgMetaChecked" name="isOgMetaChecked" value="'.(isset($metaSettings['og_active']) ? (int) $metaSettings['og_active'] : 0).'">
                      <input type="hidden" id="isCardMetaChecked" name="isCardMetaChecked" value="'.(isset($metaSettings['card_active']) ? (int) $metaSettings['card_active'] : 0).'">

                      <h3 class="b2s-meta-box-headline">' . __('Custom Sharing & Scheduling', 'blog2social') . ' <a class="b2s-info-btn" data-modal-target="b2sInfoMetaBoxModalSched" href="#">' . __('Info', 'blog2social') . '</a></h3>
                      <a id="b2s-meta-box-btn-customize" class="b2s-btn b2s-btn-success b2s-btn-sm b2s-center-block b2s-btn-margin-bottom-15" href="#">' . __('Customize & Schedule Social Media Posts', 'blog2social') . '</a>
                      <hr>
                      <h3 class="b2s-meta-box-headline">' . __('Social Media Auto-Posting', 'blog2social') . ' <a class="b2s-info-btn" data-modal-target="b2sInfoMetaBoxModalAutoPost" href="#">' . __('Info', 'blog2social') . '</a></h3>
                      ' . $lastPost . '
                      <input id="b2s-post-meta-box-time-dropdown-publish" class="post-format" ' . ((B2S_PLUGIN_USER_VERSION == 0) ? 'disabled' : '') . ' name="b2s-post-meta-box-time-dropdown" value="publish" type="checkbox" ' . $isChecked . '>
                      <label for="b2s-post-meta-box-time-dropdown-publish" class="post-format-icon">' . __('enable Auto-Posting', 'blog2social') . '</label>';

        if (B2S_PLUGIN_USER_VERSION <= 1) {
            $content .= ' <span class="b2s-label b2s-label-success"><a href="#" class="b2s-btn-label-premium b2s-info-btn" data-modal-target="b2sInfoMetaBoxModalAutoPost">' . __("PREMIUM", "blog2social") . '</a></span>';
        }

        $content .='<div class="b2s-loading-area" style="display:none">
                        <br>
                        <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                        <div class="clearfix"></div>
                        <small>' . __('Loading...', 'blog2social') . '</small>
                       </div>
                    </div>';

        $content .=' <div class="b2s-meta-box-modal" id="b2sInfoMetaBoxModalSched" aria-hidden="true" style="display:none;">
                        <div class="b2s-meta-box-modal-dialog">
                            <div class="b2s-meta-box-modal-header">
                                  <a href="#" class="b2s-meta-box-modal-btn-close" data-modal-target="b2sInfoMetaBoxModalSched" aria-hidden="true">×</a>
                              <h4 class="b2s-meta-box-modal-title">' . __('Blog2Social: Customize & Schedule Social Media Posts', 'blog2social') . '</h4>
                            </div>
                            <div class="b2s-meta-box-modal-body">
                              <p>' . __('Customize and schedule your social media posts on the one page preview for all your selected networks: tailor your posts with individual comments, #hashtags or @handles and schedule your posts for the best times to post, for multiple times or re-share recurrently for more visibility and engagement with your community.', 'blog2social') . '</p>
                            </div>
                        </div>
                    </div>';

        $content .= '<div class="b2s-meta-box-modal" id="b2sInfoMetaBoxModalAutoPost" aria-hidden="true" style="display:none;">
                        <div class="b2s-meta-box-modal-dialog">
                            <div class="b2s-meta-box-modal-header">
                                  <a href="#" class="b2s-meta-box-modal-btn-close" data-modal-target="b2sInfoMetaBoxModalAutoPost" aria-hidden="true">×</a>
                              <h4 class="b2s-meta-box-modal-title">' . __('Blog2Social: Social Media Auto-Posting', 'blog2social') . '</h4>
                            </div>
                            <div class="b2s-meta-box-modal-body">
                              <p>
                           ' . __('You have 2 general options to define the date and time to share your blog posts on social media with the Auto-Poster:', 'blog2social') . '
                    <br><br>' . __('1. Immediately after publishing your blog post', 'blog2social') . '
                    <br>' . __('Published blog posts: If you publish your blog post with click on publish in your WordPress post editor, Blog2Social will automatically share your social media post immediately.', 'blog2social') . '
                    <br>' . __('Scheduled blog posts: If you schedule your blog post with click on schedule in your WordPress post editor, Blog2Social will share your social media post on the publishing date of your blog post.', 'blog2social') . '
                    <br><br>' . __('2. Schedule your social media posts for a specific date and time If you want to share your post at a particular date and time, different from your publishing date, select the option at scheduled times and set any date and time to share your post on social media.', 'blog2social');

        if (B2S_PLUGIN_USER_VERSION == 0) {
            $content .= '<hr>
                            <h4 class="b2s-meta-box-modal-h4">' . __('You want to auto-post your blog post?', 'blog2social') . '</h4>
                            ' . __('With Blog2Social Premium you can:', 'blog2social') . '
                                <br>
                                <br>
                                - ' . __('Post on pages and groups', 'blog2social') . '<br>
                                - ' . __('Share on multiple profiles, pages and groups', 'blog2social') . '<br>
                                - ' . __('Auto-post and auto-schedule new and updated blog posts', 'blog2social') . '<br>
                                - ' . __('Schedule your posts at the best times on each network', 'blog2social') . '<br>
                                - ' . __('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') . '<br>
                                - ' . __('Schedule your post for one time, multiple times or recurrently', 'blog2social') . '<br>
                                - ' . __('Schedule and re-share old posts', 'blog2social') . '<br>
                                - ' . __('Select link format or image format for your posts', 'blog2social') . '<br>
                                - ' . __('Select individual images per post', 'blog2social') . '<br>
                                - ' . __('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') . '<br>
                                <br>
                                <a target="_blank" href="' . B2S_Tools::getSupportLink('affiliate') . '" class="b2s-btn b2s-btn-success b2s-center-block b2s-btn-none-underline">' . __('Upgrade to PREMIUM', 'blog2social') . '</a>
                                <br>
                                <center>' . __('or <a href="http://service.blog2social.com/trial" target="_blank">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social') . '</center>';
        }
        $content .= '</p>
                            </div>
                        </div>
                      </div>
                   ';

        return $content;
    }

    public function getPostBoxAutoHtml($mandant = array(), $auth = array()) {
        $authContent = '';
        $content = '<br><div class="b2s-meta-box-auto-post-area"><label for="b2s-post-meta-box-profil-dropdown">' . __('Select profile:', 'blog2social') . ' <div style="float:right;"><a href="' . $this->b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-network' . '" target="_blank">' . __('Network settings', 'blog2social') . '</a></div></label>
            <select style="width:100%;" id="b2s-post-meta-box-profil-dropdown" name="b2s-post-meta-box-profil-dropdown">';
        foreach ($mandant as $k => $m) {
            $content .= '<option value="' . $m->id . '">' . $m->name . '</option>';
            $profilData = (isset($auth->{$m->id}) && isset($auth->{$m->id}[0]) && !empty($auth->{$m->id}[0])) ? serialize($auth->{$m->id}) : '';
            $authContent .= "<input type='hidden' id='b2s-post-meta-box-profil-data-" . $m->id . "' name='b2s-post-meta-box-profil-data-" . $m->id . "' value='" . base64_encode($profilData) . "'/>";
        }
        $content .= '</select></div>';
        $content .= $authContent;

        //Opt: Get Best Time Settings
        global $wpdb;
        $bestTimeSettings = '';
        $myBestTimeSettings = $wpdb->get_results($wpdb->prepare("SELECT network_id, network_type, sched_time FROM b2s_post_sched_settings WHERE blog_user_id= %d", B2S_PLUGIN_BLOG_USER_ID));

        $content .='<label>' . __('When do you want to share your post on social media?', 'blog2social') . '</label>';
        $content .= '<div class="b2s-post-meta-box-sched-area">';
        $content .='<select class="b2s-post-meta-box-sched-select" style="width:100%;" name="b2s-post-meta-box-sched-select">
                        <option value="0">' . __('immediately after publishing', 'blog2social') . '</option>
                        <option value="1">' . __('at scheduled times', 'blog2social') . '</option>
                        </select>';

        $content .='<div id="b2s-post-meta-box-note-premium" class="b2s-info-success"><button class="b2s-btn-close-meta-box b2s-close-icon" data-area-id="b2s-post-meta-box-note-premium" title="close notice"></button><b>' . __('You want to sched your blog post with Auto-Poster?', 'blog2social') . '</b><br> <a class="" target="_blank" href="' . B2S_Tools::getSupportLink('affiliate') . '">' . __('Upgrade to PREMIUM PRO', 'blog2social') . '</a></div>';

        $content .='<div class="b2s-post-meta-box-sched-once" style="display:none;">';
        //Opt: CustomDatePicker
        $dateFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 'dd.mm.yyyy' : 'yyyy-mm-dd';
        $timeFormat = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? 'hh:ii' : 'hh:ii aa';
        $content .='<label class="b2s-font-bold">' . __('Select date:', 'blog2social') . '</label><br><span class="dashicons dashicons-calendar b2s-calendar-icon"></span><input style="width:88%;" id="b2s-post-meta-box-sched-date-picker" name="b2s-post-meta-box-sched-date" value="" readonly data-timepicker="true" data-language="' . substr(B2S_LANGUAGE, 0, 2) . '" data-time-format="' . $timeFormat . '" data-date-format="' . $dateFormat . '" type="text"><br>';

        //Opt: Best Time Settings
        if (!empty($myBestTimeSettings) && is_array($myBestTimeSettings)) {
            $bestTimeSettings = array('my_times' => 1, 'times' => $myBestTimeSettings);
            $content .="<input id='b2s-post-meta-box-best-time-settings' class='post-format' name='b2s-post-meta-box-best-time-settings' value='" . serialize($bestTimeSettings) . "' type='checkbox'> ";
            $content .="<label class='post-format-icon' for='b2s-post-meta-box-best-time-settings'>" . __('post at', 'blog2social');
            $content .=' <a href="' . $this->b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-settings' . '" target="_blank">' . __('my time settings', 'blog2social') . '</a></label>';
            $content .='<br><hr><span>' . __('Note: If you ​have​ not ​specified​ your own times, we automatically provide you with the best times to post​ on the social networks. You can always ​edit​ your own times in the settings.', 'blog2social') . '</span>';
        }
        $content .="</div>";

        $content .='</div>';
        return $content;
    }

}
