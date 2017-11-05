<?php
require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Navbar.php';
require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php';
require_once B2S_PLUGIN_DIR . 'includes/B2S/Ship/Portale.php';
require_once B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php';
delete_option('B2S_PLUGIN_POST_META_TAGES_' . (int) $_GET['postId']);
delete_option('B2S_PLUGIN_POST_CONTENT_' . (int) $_GET['postId']);
B2S_Tools::checkUserBlogUrl();
$userLang = strtolower(substr(get_locale(), 0, 2));
$postData = get_post((int) $_GET['postId']);
$postUrl = (get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid);
$postStatus = array('publish' => __('published', 'blog2social'), 'pending' => __('draft', 'blog2social'), 'future' => __('scheduled', 'blog2social'));
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? '<span class="label label-success">' . __("PREMIUM", "blog2social") . '</span>' : '';
$metaSettings = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <?php require_once B2S_PLUGIN_DIR . 'views/b2s/html/header.phtml'; ?>

        <div class="col-xs-12 col-md-9 del-padding-left">
            <div class="col-xs-12 del-padding-left hidden-xs">
                <div class="panel panel-group">
                    <div class="panel-body b2s-post-details">
                        <h3><?php _e('Social Media Scheduling & Sharing', 'blog2social') ?></h3>
                        <div class="info"><?php _e('Title', 'blog2social') ?>: <?php echo B2S_Util::getTitleByLanguage($postData->post_title, $userLang); ?></div>
                        <p class="info hidden-xs"># <?php echo $postData->ID; ?>  | <?php echo $postStatus[trim(strtolower($postData->post_status))] . ' ' . __('on blog', 'blog2social') . ': ' . B2S_Util::getCustomDateFormat($postData->post_date, substr(B2S_LANGUAGE, 0, 2)); ?></p>

                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <?php if (defined("B2S_PLUGIN_NOTICE_SITE_URL") && B2S_PLUGIN_NOTICE_SITE_URL != false) { ?>
                <div class="b2s-settings-user-sched-time-area col-xs-12 del-padding-left hidden-xs">
                    <button type="button" class="btn btn-link pull-left btn-xs  scroll-to-bottom"><span class="glyphicon glyphicon-chevron-down"></span> <?php _e('scroll to bottom', 'blog2social') ?> </button>
                    <div class="pull-right">
                        <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                            <a href="#" class="btn btn-primary btn-xs b2s-get-settings-sched-time-user">
                            <?php } else { ?>
                                <a href="#" class="btn btn-primary btn-xs b2s-btn-disabled" data-toggle="modal" data-title="<?php _e('You want to load your time settings?', 'blog2social') ?>" data-target="#b2sPreFeatureModal">
                                <?php } _e('Load My Time Settings', 'blog2social'); ?> <?php echo $isPremium; ?></a>

                            <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                                <a href="#" class="btn btn-primary btn-xs b2s-get-settings-sched-time-default">
                                <?php } else { ?>
                                    <a href="#" class="btn btn-primary btn-xs b2s-btn-disabled b2s-get-settings-sched-time-open-modal" data-toggle="modal" data-title="<?php _e('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social') ?>" data-target="#b2sPreFeatureModal">
                                    <?php } _e('Load Best Time Scheduler', 'blog2social'); ?> <?php echo $isPremium; ?></a>
                                <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                                    <a href="#" class="b2s-time-settings-modal-btn btn btn-primary btn-xs" data-toggle="modal" data-target="#b2s-time-settings-modal"><?php _e('Settings', 'blog2social'); ?></a>
                                <?php } ?>
                                <a href="#" data-toggle="modal" data-target="#b2sInfoSchedTimesModal" class="btn btn-link btn-xs hidden-sm b2s-load-settings-sched-time-default-info"><?php echo _e('Info', 'blog2social'); ?></a>
                                </div>
                                </div>
                            <?php } ?>


                            </div>
                            <?php require_once B2S_PLUGIN_DIR . 'views/b2s/html/service.phtml'; ?>

                            <div class="clearfix"></div>

                            <div id="b2s-wrapper" class="b2s-wrapper-content">
                                <div id="b2s-sidebar-wrapper" class="sidebar-default">
                                    <ul class="sidebar-nav b2s-sidbar-wrapper-nav-ul">
                                        <li class="btn-toggle-menu">
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-thumb">
                                                    <div class="toggelbutton">
                                                        <i class="glyphicon glyphicon-chevron-right btn-toggle-glyphicon"></i>
                                                    </div>
                                                    <div class="network-icon">
                                                        <i class="glyphicon glyphicon-user"></i>
                                                    </div>
                                                </div>
                                                <div class="b2s-network-details-header">
                                                    <?php
                                                    $navbar = new B2S_Ship_Navbar();
                                                    $mandantData = $navbar->getData();
                                                    ?>
                                                    <h3> <?php echo count($mandantData['auth']); ?> <?php _e('Social Accounts', 'blog2social') ?></h3>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="sidebar-brand">
                                            <div class="form-group">
                                                <?php
                                                echo $navbar->getSelectMandantHtml($mandantData['mandanten']);
                                                ?>
                                            </div>
                                        </li>
                                        <li class="b2s-sidbar-network-auth-btn">
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-thumb">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </div>
                                                <div class="b2s-network-details">
                                                    <h4><?php _e('Add more...', 'blog2social') ?></h4>
                                                    <p>
                                                        <?php _e('Profiles | Pages | Groups', 'blog2social') ?>
                                                    </p>
                                                </div>
                                                <div class="b2s-network-status"></div>
                                            </div>
                                        </li>
                                        <?php
                                        $orderArray = array();
                                        foreach ($mandantData['auth'] as $k => $channelData) {
                                            echo $navbar->getItemHtml($channelData, 0);
                                            $orderArray[] = $channelData->networkAuthId;
                                        }
                                        ?>

                                        <li>
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-thumb">
                                                    <i class="glyphicon glyphicon-save"></i>
                                                </div>
                                                <div class="b2s-network-details-header b2s-margin-top-8">

                                                    <a href="#" class="btn btn-primary btn-sm b2s-network-setting-save-btn b2s-loading-area-save-profile-change">
                                                        <?php _e('Save Network Selection', 'blog2social') ?>
                                                    </a>
                                                    <a href="#" data-toggle="modal" data-target="#b2s-network-setting-save" class="btn btn-primary btn-sm hidden-sm b2s-network-setting-save"><?php echo _e('Info', 'blog2social'); ?></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="b2s-network-list">
                                                <div class="b2s-network-details-legend">
                                                    <span class="b2s-no-textwarp"><i class="glyphicon glyphicon-ok glyphicon-success"></i> <?php _e('network connected', 'blog2social'); ?></span>
                                                    <span class="b2s-no-textwarp"><i class="glyphicon glyphicon-danger glyphicon-ban-circle"></i> <?php _e('requires image', 'blog2social'); ?></span>
                                                    <span class="b2s-no-textwarp"><i class="glyphicon glyphicon-danger glyphicon-refresh"></i> <?php _e('refresh authorization', 'blog2social'); ?></span>
                                                </div>
                                            </div>

                                        </li>
                                    </ul>
                                    <input type="hidden" class="b2s-network-navbar-order" value='<?php echo json_encode($orderArray) ?>'>
                                </div>

                                <div id="b2s-content-wrapper" class="b2s-content-wrapper-content-default">
                                    <div class="b2s-loading-area col-md-9 del-padding-left" style="display: none;">
                                        <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                        <div class="clearfix"></div>
                                        <small><?php _e('Loading...', 'blog2social') ?> .</small>
                                    </div>

                                    <?php if (defined("B2S_PLUGIN_NOTICE_SITE_URL") && B2S_PLUGIN_NOTICE_SITE_URL == false) { ?>
                                        <div class="b2s-info-blog-url-area">
                                            <div class="b2s-post-area col-md-9 del-padding-left">
                                                <div class="panel panel-group text-center">
                                                    <div class="panel-body" style="margin:15px;height:500px;background:url('<?php echo plugins_url('/assets/images/no-network-selected.png', B2S_PLUGIN_FILE); ?>') no-repeat;background-position:center;">
                                                        <div class="panel panel-no-shadow">
                                                            <div class="panel-body panel-no-padding">
                                                                <h4><?php _e('Notice:<br><p>Please make sure, that your website address is reachable. The Social Networks do not allow postings from local installations.</p>', 'blog2social') ?></h4>
                                                                <?php $settingsBlogUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/options-general.php'; ?>
                                                                <a href="<?php echo $settingsBlogUrl; ?>" class="btn btn-primary"><?php _e('change website address', 'blog2social') ?></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } else { ?>

                                        <form id="b2sNetworkSent" method="post">
                                            <div class="b2s-post-area col-md-9 del-padding-left">
                                                <div class="b2s-empty-area">
                                                    <div class="panel panel-group text-center">
                                                        <div class="panel-body" style="margin:15px;height:500px;background:url('<?php echo plugins_url('/assets/images/no-network-selected.png', B2S_PLUGIN_FILE); ?>') no-repeat;background-position:center;">
                                                            <div class="panel panel-no-shadow">
                                                                <div class="panel-body panel-no-padding">
                                                                    <h3><?php _e('First, connect or select network before posting', 'blog2social') ?></h3>
                                                                    <br>
                                                                    <a href="#" class="btn btn-primary btn-lg text-break" data-target="#b2s-network-list-modal" data-toggle="modal"><?php _e('connect', 'blog2social') ?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="b2s-post-list"></div>
                                                <div class="b2s-publish-area">
                                                    <button type="button" class="btn btn-link pull-left btn-xs scroll-to-top"><span class="glyphicon glyphicon-chevron-up"></span> <?php _e('scroll to top', 'blog2social') ?> </button>
                                                    <button class="btn btn-success pull-right btn-lg b2s-submit-btn"><?php _e('Share', 'blog2social') ?></button>
                                                </div>
                                                <div class="navbar navbar-default navbar-fixed-bottom navbar-small b2s-footer-menu" style="display: block;">
                                                    <div class="b2s-publish-navbar-btn">
                                                        <button class="btn btn-success btn-lg b2s-submit-btn-scroll"><?php _e('Share', 'blog2social') ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="publish_date" name="publish_date" value="">
                                            <input type="hidden" id="user_version" name="user_version" value="<?php echo B2S_PLUGIN_USER_VERSION; ?>">
                                            <input type="hidden" id="action" name="action" value="b2s_save_ship_data">
                                            <input type='hidden' id='post_id' name="post_id" value='<?php echo (int) $_GET['postId']; ?>'>
                                            <input type='hidden' id='user_timezone' name="user_timezone" value="<?php echo $userTimeZoneOffset; ?>">
                                            <input type='hidden' id='user_timezone_text' name="user_timezone_text" value="<?php echo _e('Time zone', 'blog2social') . ': (UTC ' . B2S_Util::humanReadableOffset($userTimeZoneOffset) . ') ' . $userTimeZone ?>">
                                            <input type='hidden' id='default_titel' name="default_titel" value='<?php echo B2S_Util::getTitleByLanguage($postData->post_title, $userLang); ?>'>
                                            <input type="hidden" id="b2sChangeOgMeta" name="change_og_meta" value="0">
                                            <input type="hidden" id="b2sChangeCardMeta" name="change_card_meta" value="0">    

                                            <div class="b2s-reporting-btn-area col-md-9 del-padding-left" style="display: none;">
                                                <div class="panel panel-group">
                                                    <div class="panel-body">
                                                        <div class="pull-right">
                                                            <?php $allPosts = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-post'; ?>
                                                            <a href="#" data-toggle="modal" data-target="#b2s-re-share-info" class="btn btn-link btn-sm hidden-sm del-padding-left b2s-info-btn b2s-re-share-info"><?php echo _e('Info', 'blog2social'); ?></a>
                                                            <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                                                                <button class="btn btn-primary b2s-re-share-btn"><?php _e('Re-share this post', 'blog2social') ?></button>
                                                            <?php } else { ?>
                                                                <a href="#" class="btn btn-primary b2s-btn-disabled" data-toggle="modal" data-title="You want to re-share your blog post?" data-target="#b2sPreFeatureModal"><?php _e('Re-share this post', 'blog2social') ?> <?php echo $isPremium; ?></a>
                                                            <?php } ?>
                                                            <a class="btn btn-primary" href="<?php echo $allPosts; ?>"><?php _e('Share new post on Social Media', 'blog2social') ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                            </div>
                            </div>
                            <?php
                            $noLegend = 1;
                            require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.phtml');
                            ?>

                            <!-- B2S-Network -->
                            <div id="b2s-network-list-modal" class="modal fade" role="dialog" aria-labelledby="b2s-network-list-modal" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-list-modal">&times;</button>
                                            <h4 class="modal-title"><?php _e('Connect for', 'blog2social') ?>: <span class="b2s-network-list-modal-mandant"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            $portale = new B2S_Ship_Portale();
                                            echo $portale->getItemHtml($mandantData['portale']);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="b2s-network-no-sched-time-user" class="modal fade" role="dialog" aria-labelledby="b2s-network-no-sched-time-user" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-no-sched-time-user" >&times;</button>
                                            <h4 class="modal-title"><?php _e('Time Scheduling', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php $settingsUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/') . 'wp-admin/admin.php?page=blog2social-settings'; ?>
                                            <?php _e('You have not yet defined personal time settings. To edit personal time settings click on the Blog2Social Settings on the left-hand menu and select Best Time Settings to change the predefined time settings to your own preferences.', 'blog2social') ?>
                                        </div>
                                        <div class="modal-footer">
                                            <a target="_blank" href="<?php echo $settingsUrl; ?>" class="btn btn-primary"><?php _e('Settings', 'blog2social') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="b2s-re-share-info" class="modal fade" role="dialog" aria-labelledby="b2s-re-share-info" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-re-share-info">&times;</button>
                                            <h4 class="modal-title"><?php _e('Re-share this Post', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php _e('You can re-share your post for a different sharing purpose, or to share on a different choice of networks, profiles, pages or groups, or with different comments or images, or if you want to share your blog post images to image networks only, or re-share them at different times. You may vary your comments and images in order to produce more variations of your social media posts to share more often without sharing the same message over and over again. Whatever your choose to do for re-sharing your post, you can simply click "Re-share this post" and you will be led to the preview page where your can select your networks and edit your texts, comments or images according to your current sharing preferences.', 'blog2social') ?>
                                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                                <hr>
                                                <h4><?php _e('You want re-share your blog post?', 'blog2social'); ?></h4>
                                                <?php _e('With Blog2Social Premium you can:', 'blog2social') ?>
                                                <br>
                                                <br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Post on pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Share on multiple profiles, pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Auto-post and auto-schedule new and updated blog posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule your posts at the best times on each network', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule your post for one time, multiple times or recurrently', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule and re-share old posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Select link format or image format for your posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Select individual images per post', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') ?><br>
                                                <br>
                                                <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>" class="btn btn-success center-block"><?php _e('Upgrade to PREMIUM', 'blog2social') ?></a>
                                                <br>
                                                <center><?php _e('or <a href="http://service.blog2social.com/trial" target="_blank">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social') ?></center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="b2s-network-setting-save" class="modal fade" role="dialog" aria-labelledby="b2s-network-setting-save" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-setting-save">&times;</button>
                                            <h4 class="modal-title"><?php _e('Save Network Settings', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php _e('You can save your current network settings as "Standard" network settings for any future sharing activities or as a "Profile" to choose from (Premium).<br><br>Your Standard selection will show as activated upon pressing the "share on social media" button on the right hand side bar. You can change these settings any time per click, or choose another network profile (Premium).<br><br>You can also pre-define various different sets of networks, for specific social media accounts, target groups, contents or sharing purposes. For example you can define a specific set of networks for sharing your posts images only or for re-sharing your evergreen content on a recurring basis. On the preview-page you may edit your selected or pre-selected networks anytime by simply clicking on the respective network account to select or remove an account from the current sharing scheme.', 'blog2social') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="b2s-network-sched-post-info" class="modal fade" role="dialog" aria-labelledby="b2s-network-sched-post-info" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-sched-post-info">&times;</button>
                                            <h4 class="modal-title"><?php _e('Your blog post is not yet published on your Wordpress!', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php _e('At least one of your selected networks is set to "Share Now"', 'blog2social') ?></p>
                                            <br>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6 del-padding-left">
                                                <button type="button" class="b2s-modal-close btn btn-success btn-block" data-modal-name="#b2s-network-sched-post-info"><?php _e('Schedule your post', 'blog2social') ?></button>
                                            </div>
                                            <div class="col-md-6 del-padding-right">
                                                <button type="button" class="b2s-modal-close btn btn-primary btn-block" data-modal-name="#b2s-network-sched-post-info" id="b2s-network-sched-post-info-ignore"><?php _e('Ignore & share', 'blog2social') ?></button>
                                            </div>
                                            <br>
                                            <br>

                                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                                <hr>
                                                <h4><?php _e('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social'); ?></h4>
                                                <?php _e('With Blog2Social Premium you can:', 'blog2social') ?>
                                                <br>
                                                <br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Post on pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Share on multiple profiles, pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Auto-post and auto-schedule new and updated blog posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule your posts at the best times on each network', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule your post for one time, multiple times or recurrently', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule and re-share old posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Select link format or image format for your posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Select individual images per post', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') ?><br>
                                                <br>
                                                <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>" class="btn btn-success center-block"><?php _e('Upgrade to PREMIUM', 'blog2social') ?></a>
                                                <br>
                                                <center><?php _e('or <a href="http://service.blog2social.com/trial" target="_blank">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social') ?></center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="b2s-sched-post-modal" class="modal fade" role="dialog" aria-labelledby="b2s-sched-post-modal" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-sched-post-modal">&times;</button>
                                            <h4 class="modal-title"><?php _e('Need to schedule your posts?', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php _e('Blog2Social Premium covers everything you need.', 'blog2social') ?></p>
                                            <br>
                                            <div class="clearfix"></div>
                                            <b><?php _e('Schedule post once', 'blog2social') ?></b>
                                            <p><?php _e('You want to publish a post on a specific date? No problem! Just enter your desired date and you are ready to go!', 'blog2social') ?></p>
                                            <br>
                                            <b><?php _e('Schedule post recurrently', 'blog2social') ?></b>
                                            <p><?php _e('You have evergreen content you want to re-share from time to time in your timeline? Schedule your evergreen content to be shared once, multiple times or recurringly at specific times.', 'blog2social') ?></p>
                                            <br>
                                            <b><?php _e('Best Time Scheduler', 'blog2social') ?></b>
                                            <p><?php _e('Whenever you publish a post, only a fraction of your followers will actually see your post. Use the Blog2Social Best Times Scheduler to share your post at the best times for each social network. Get more outreach and extend the lifespan of your posts.', 'blog2social') ?></p>
                                            <br>
                                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                                <hr>
                                                <?php _e('With Blog2Social Premium you can:', 'blog2social') ?>
                                                <br>
                                                <br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Post on pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Share on multiple profiles, pages and groups', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Auto-post and auto-schedule new and updated blog posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule your posts at the best times on each network', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Best Time Manager: use predefined best time scheduler to auto-schedule your social media posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule your post for one time, multiple times or recurrently', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Schedule and re-share old posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Select link format or image format for your posts', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Select individual images per post', 'blog2social') ?><br>
                                                <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php _e('Reporting & calendar: keep track of your published and scheduled social media posts', 'blog2social') ?><br>
                                                <br>
                                                <a target="_blank" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>" class="btn btn-success center-block"><?php _e('Upgrade to PREMIUM', 'blog2social') ?></a>
                                                <br>
                                                <center><?php _e('or <a href="http://service.blog2social.com/trial" target="_blank">start with free 30-days-trial of Blog2Social Premium</a> (no payment information needed)', 'blog2social') ?></center>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-select-image">&times;</button>
                                            <h4 class="modal-title"><?php _e('Select image for', 'blog2social') ?> <span class="b2s-selected-network-for-image-info"></span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <?php
                                                    $image = new B2S_Ship_Image();
                                                    echo $image->getItemHtml($postData->ID, $postData->post_content, $postUrl, $userLang);
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="b2s-time-settings-modal" class="modal fade" role="dialog" aria-labelledby="b2s-time-settings-modal" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close btn btn-link" data-modal-name="#b2s-time-settings-modal">&times;</button>
                                            <button id="b2s-save-time-settings-btn-trigger" class="btn btn-success btn-xs pull-right margin-right-15"><?php _e('save', 'blog2social') ?></button>
                                            <h4 class="modal-title"><?php _e('My Time Settings', 'blog2social') ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <?php
                                                    $settingsItem = new B2S_Settings_Item();
                                                    echo $settingsItem->getSchedSettingsHtml();
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="b2s-post-ship-item-post-format-modal" class="modal fade" role="dialog" aria-labelledby="b2s-post-ship-item-post-format-modal" aria-hidden="true" data-backdrop="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-post-ship-item-post-format-modal">&times;</button>
                                            <h4 class="modal-title"><?php _e('Choose your', 'blog2social') ?> <span id="b2s-post-ship-item-post-format-network-title"></span> <?php _e('Post Format', 'blog2social') ?>
                                                <?php if (B2S_PLUGIN_USER_VERSION >= 2) { ?>
                                                    <?php _e('for:', 'blog2social') ?> <span id="b2s-post-ship-item-post-format-network-display-name"></span>
                                                <?php } ?>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <?php echo $settingsItem->getNetworkSettingsHtml(); ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="text-center">
                                                        <br>
                                                        <div class="b2s-post-format-settings-info" data-network-id="1" style="display:none;">
                                                            <b><?php _e('Define the default settings for the custom post format for all of your Facebook accounts in the Blog2Social settings.', 'blog2social'); ?></b>
                                                        </div>
                                                        <div class="b2s-post-format-settings-info" data-network-id="2" style="display:none;">
                                                            <b><?php _e('Define the default settings for the custom post format for all of your Twitter accounts in the Blog2Social settings. ', 'blog2social'); ?></b>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="b2sLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">
                            <input type="hidden" id="b2sInsertImageType" value="0">
                            <input type="hidden" id="b2sUserLang" value="<?php echo $userLang; ?>">
                            <input type="hidden" id="b2sPostId" value="<?php echo $postData->ID; ?>">
                            <input type="hidden" id="b2sDefault_url" name="default_url" value="<?php echo get_permalink($postData->ID) !== false ? get_permalink($postData->ID) : $postData->guid; ?>">
                            <input type="hidden" id="b2sPortalImagePath" value="<?php echo plugins_url('/assets/images/portale/', B2S_PLUGIN_FILE); ?>">
                            <input type="hidden" id="b2sServerUrl" value="<?php echo B2S_PLUGIN_SERVER_URL; ?>">
                            <input type="hidden" id="b2sJsTextLoading" value="<?php _e('Loading...', 'blog2social') ?>">
                            <input type="hidden" id="b2sJsTextConnectionFail" value="<?php _e('The connection to the server failed. Try again!', 'blog2social') ?>">
                            <input type="hidden" id="b2sSelectedNetworkAuthId" value="<?php echo (isset($_GET['network_auth_id']) && (int) $_GET['network_auth_id'] > 0) ? (int) $_GET['network_auth_id'] : ''; ?>">
                            <input type="hidden" id="b2sDefaultNoImage" value="<?php echo plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE); ?>">
                            <input type="hidden" id="isMetaChecked" value="<?php echo $postData->ID; ?>">
                            <input type="hidden" id="isOgMetaChecked" value="<?php echo (isset($metaSettings['og_active']) ? (int) $metaSettings['og_active'] : 0); ?>">
                            <input type="hidden" id="isCardMetaChecked" value="<?php echo (isset($metaSettings['card_active']) ? (int) $metaSettings['card_active'] : 0); ?>">
                            
                            <?php echo $settingsItem->setNetworkSettingsHtml(); ?>
                            <?php if (trim(strtolower($postData->post_status)) == 'future') { ?>
                                <input type="hidden" id="b2sBlogPostSchedDate" value="<?php echo strtotime($postData->post_date); ?>000"> <!--for milliseconds-->
                                <input type="hidden" id="b2sSchedPostInfoIgnore" value="0">
                                <?php
                            }