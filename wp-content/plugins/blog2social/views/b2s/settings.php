<?php
require_once B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php';
require_once B2S_PLUGIN_DIR . 'includes/Options.php';
$settingsItem = new B2S_Settings_Item();
?>

<div class="b2s-container">
    <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.phtml'); ?>

    <div class=" b2s-inbox col-md-12 del-padding-left">
        <div class="col-md-9 del-padding-left">
            
            <div class="panel panel-group b2s-upload-image-no-permission" style="display:none;">
                <div class="panel-body">
                    <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php _e('You need a higher user role to upload an image on this blog. Please contact your administrator.', 'blog2social'); ?>
                </div>
            </div>  

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-12">                       
                        <div class="grid b2s-post"></div>
                        <div class="row b2s-loading-area width-100" style="display: none;">
                            <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                            <div class="text-center b2s-loader-text"><?php _e("save...", "blog2social"); ?></div>
                        </div>
                        <div class="row b2s-user-settings-area">
                            <ul  class="nav nav-pills">
                                <li class="active">
                                    <a href="#b2s-general" class="b2s-general" data-toggle="tab"><?php _e('General', 'blog2social') ?></a>
                                </li>
                                <li>
                                    <a href="#b2s-social-meta-data" class="b2s-social-meta-data" data-toggle="tab"><?php _e('Social Meta Data', 'blog2social') ?></a>
                                </li>
                                <li>
                                    <a href="#b2s-best-time-manager" class="b2s-best-time-manager" data-toggle="tab"><?php _e('Best Time Settings', 'blog2social') ?></a>
                                </li>
                                <li>
                                    <a href="#b2s-network" class="b2s-network" data-toggle="tab"><?php _e('Network Settings', 'blog2social') ?></a>
                                </li>
                            </ul>
                            <hr>
                            <div class="tab-content clearfix">
                                <div class="tab-pane active" id="b2s-general">
                                    <?php echo $settingsItem->getGeneralSettingsHtml(); ?>
                                </div>
                                <div class="tab-pane" id="b2s-social-meta-data">
                                    <form class="b2sSaveSocialMetaTagsSettings" method="post" novalidate="novalidate">           
                                        <?php echo $settingsItem->getSocialMetaDataHtml(); ?>
                                        <?php if (is_admin()) { ?>
                                            <button class="btn btn-primary pull-right" type="submit"><?php _e('save', 'blog2social') ?></button>  
                                            <input type="hidden" name="action" value="b2s_save_social_meta_tags">
                                        <?php } ?>
                                    </form>
                                    <?php if (!is_admin()) { ?>
                                        <button class="btn btn-primary b2s-btn-disabled pull-right" data-toggle = "modal" data-target = "#b2s-info-meta-tag-modal"><?php _e('save', 'blog2social') ?></button>
                                    <?php } ?>

                                </div>
                                <div class="tab-pane" id="b2s-best-time-manager">
                                    <h4><?php echo _e('My Time Settings', 'blog2social'); ?>
                                        <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                            <span class="label label-success label-sm"><a href="#" class="btn-label-premium" data-toggle="modal" data-target="#b2sInfoSchedTimesModal"><?php _e("PREMIUM", "blog2social") ?></a></span>         
                                        <?php } ?>
                                        <a href="#" data-toggle="modal" data-target="#b2sInfoSchedTimesModal" class="b2s-info-btn del-padding-left b2s-load-settings-sched-time-default-info"><?php echo _e('Info', 'blog2social'); ?></a>

                                        <div class="pull-right">
                                            <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>
                                                <a href="#" class="btn btn-primary btn-xs b2s-get-settings-sched-time-default">
                                                <?php } else { ?>
                                                    <a href="#" class="btn btn-primary b2s-btn-disabled btn-xs" data-title = "<?php _e('You want to schedule your posts and use the Best Time Scheduler?', 'blog2social') ?>" data-toggle ="modal" data-target ="#b2sInfoSchedTimesModal">
                                                    <?php } _e('Reset predefined best time settings', 'blog2social'); ?></a>
                                                <button id="b2s-save-time-settings-btn-trigger" class="btn btn-primary btn-xs <?php echo (B2S_PLUGIN_USER_VERSION == 0) ? 'b2s-btn-disabled' : ''; ?>"><?php _e('save', 'blog2social') ?></button>
                                        </div>
                                    </h4>
                                    <div class="clearfix"></div>
                                    <?php echo $settingsItem->getSchedSettingsHtml(); ?>
                                </div>
                                <div class="tab-pane" id="b2s-network"> 
                                    <div class="col-md-12">
                                        <h4> <?php
                                            _e('Post format', 'blog2social');
                                            if (B2S_PLUGIN_USER_VERSION <= 1) {
                                                ?>
                                                <span class="label label-success label-sm"><a href="#" class="btn-label-premium" data-toggle="modal" data-target="#b2sInfoFormatModal"><?php _e("PREMIUM", "blog2social") ?></a></span>  
                                            <?php }
                                            ?> 
                                            <a href="#" data-toggle="modal" data-target="#b2sInfoFormatModal" class="b2s-info-btn del-padding-left"><?php echo _e('Info', 'blog2social'); ?></a>
                                        </h4>
                                        <ul  class="nav nav-pills">
                                            <li class="active">
                                                <a href="#b2s-network-1" class="b2s-network-1" data-toggle="tab"><?php _e('Facebook', 'blog2social') ?></a>
                                            </li>
                                            <li>
                                                <a href="#b2s-network-2" class="b2s-network-2" data-toggle="tab"><?php _e('Twitter', 'blog2social') ?></a>
                                            </li>  
                                        </ul>
                                        <hr>
                                        <div class="tab-content clearfix">

                                            <div class="tab-pane active" id="b2s-network-1">
                                                <form class="b2sSaveUserSettingsPostFormatFb" method="post" novalidate="novalidate">                                                
                                                    <?php
                                                    echo $settingsItem->getNetworkSettingsPostFormatHtml(1);
                                                    if (B2S_PLUGIN_USER_VERSION > 0) {
                                                        ?>
                                                        <button class="btn btn-primary pull-right" type="submit"><?php _e('save', 'blog2social') ?></button>    
                                                    <?php } ?>
                                                    <input type="hidden" name="action" value="b2s_user_network_settings">
                                                    <input type="hidden" name="type" value="post_format">
                                                    <input type="hidden" name="network_id" value="1">
                                                </form>      
                                            </div>    
                                            <div class="tab-pane" id="b2s-network-2">
                                                <form class="b2sSaveUserSettingsPostFormatTw" method="post" novalidate="novalidate">                                                
                                                    <?php
                                                    echo $settingsItem->getNetworkSettingsPostFormatHtml(2);
                                                    if (B2S_PLUGIN_USER_VERSION > 0) {
                                                        ?>
                                                        <button class="btn btn-primary pull-right" type="submit"><?php _e('save', 'blog2social') ?></button>    
                                                    <?php } ?>
                                                    <input type="hidden" name="action" value="b2s_user_network_settings">
                                                    <input type="hidden" name="type" value="post_format">
                                                    <input type="hidden" name="network_id" value="2">
                                                </form>    
                                            </div>
                                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                                <button class="btn btn-primary b2s-btn-disabled pull-right" data-toggle = "modal" data-target = "#b2sInfoFormatModal"><?php _e('save', 'blog2social') ?></button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="b2s_user_version" value="<?php echo B2S_PLUGIN_USER_VERSION; ?>" />
                        <?php
                        $noLegend = 1;
                        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.phtml');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/service.phtml'); ?>
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.phtml'); ?>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo strtolower(substr(get_locale(), 0, 2)); ?>">
<input type="hidden" id="b2sShowSection" value="<?php echo (isset($_GET['show']) ? $_GET['show'] : ''); ?>">
<input type="hidden" id="b2s_wp_media_headline" value="<?php _e('Select or upload an image from mediathek', 'blog2social') ?>">';
<input type="hidden" id="b2s_wp_media_btn" value="<?php _e('Use image', 'blog2social') ?>">';

<div class="modal fade" id="b2sInfoAllowShortcodeModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoAllowShortcodeModal" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoAllowShortcodeModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e('Allow shortcodes in my post', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php _e('Shortcodes are used by some wordpress plugins like Elementor, Visual Composer and Content Builder. When a shortcode is inserted in a WordPress post or page, it is replaced with some other content when you publish the article on your blog. In other words, a shortcode instructs WordPress to find a special command that is placed in square brackets ([]) and replace it with the appropriate dynamic content by a plugin you use.<br><br>Activate this feature, if you should use dynamic elements in your articles.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="b2sInfoTimeZoneModal" tabindex="-1" role="dialog" aria-labelledby="b2sInfoTimeZoneModal" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoTimeZoneModal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e('Personal Time Zone', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php _e('Blog2Social applies the scheduled time settings based on the time zone defined in the general settings of your WordPress. You can select a user-specific time zone that deviates from the Wordpress system time zone for your social media scheduling.<br><br>Select the desired time zone from the drop-down menu.', 'blog2social') ?>
            </div>
        </div>
    </div>
</div>






