<?php
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Ship/Image.php');
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Settings/Item.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');

$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
$metaSettings = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
?>
<div class="b2s-container">
    <div class="b2s-inbox">
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.phtml'); ?>
        <div class="col-md-12 del-padding-left">
            <div class="col-md-9 del-padding-left">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="grid b2s-post">
                            <div class="grid-body">
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/post.navbar.phtml'); ?>
                                <div class="b2s-loading-area">
                                    <br>
                                    <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                    <div class="clearfix"></div>
                                    <div class="text-center b2s-loader-text"><?php _e("Loading...", "blog2social"); ?></div>
                                </div>
                                <div id='b2s_calendar'></div>
                                <br>
                                <script>
                                    var b2s_calendar_locale = '<?= strtolower(substr(get_locale(), 0, 2)); ?>';
                                    var b2s_calendar_date = '<?= date('Y-m-d'); ?>';
                                    var b2s_calendar_datetime = '<?= date('Y-m-d H:i:s'); ?>';
                                    var b2s_has_premium = <?= B2S_PLUGIN_USER_VERSION > 0 ? "true" : "false"; ?>;
                                    var b2s_plugin_url = '<?= B2S_PLUGIN_URL; ?>';
                                    var b2s_calendar_formats = <?= json_encode(array(__('Link Post', 'blog2social'), __('Photo Post', 'blog2social'))); ?>;
                                    var b2s_is_calendar = true;
                                </script>
                            </div>
                        </div>
                        <?php
                        require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.phtml');
                        ?> 
                    </div>
                </div>
            </div>
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/service.phtml'); ?>
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.phtml'); ?>
        </div>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo strtolower(substr(get_locale(), 0, 2)); ?>">
<input type='hidden' id="user_timezone" name="user_timezone" value="<?php echo $userTimeZoneOffset; ?>">
<input type="hidden" id="user_version" name="user_version" value="<?php echo B2S_PLUGIN_USER_VERSION; ?>">
<input type="hidden" id="b2sDefaultNoImage" value="<?php echo plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE); ?>">
<input type="hidden" id="b2sPostId" value="">
<input type="hidden" id="b2sInsertImageType" value="0">
<input type="hidden" id="isOgMetaChecked" value="<?php echo (isset($metaSettings['og_active']) ? (int) $metaSettings['og_active'] : 0); ?>">
<input type="hidden" id="isCardMetaChecked" value="<?php echo (isset($metaSettings['card_active']) ? (int) $metaSettings['card_active'] : 0); ?>">

<div id="b2s-post-ship-item-post-format-modal" class="modal fade" role="dialog" aria-labelledby="b2s-post-ship-item-post-format-modal" aria-hidden="true" data-backdrop="false" style="z-index: 1070">
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
                        <?php
                        $settingsItem = new B2S_Settings_Item();
                        echo $settingsItem->setNetworkSettingsHtml();
                        echo $settingsItem->getNetworkSettingsHtml();
                        ?>
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
                                <b><?php _e('Define the default settings for the custom post format for all of your Twitter accounts in the Blog2Social settings.', 'blog2social'); ?></b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="b2s-network-select-image" class="modal fade" role="dialog" aria-labelledby="b2s-network-select-image" aria-hidden="true" data-backdrop="false" style="z-index: 1070">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-select-image">&times;</button>
                <h4 class="modal-title"><?php _e('Select image for', 'blog2social') ?> <span class="b2s-selected-network-for-image-info"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="b2s-network-select-image-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>