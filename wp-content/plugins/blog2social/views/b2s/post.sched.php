<?php
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
$b2sShowByDate = isset($_GET['b2sShowByDate']) ? trim($_GET['b2sShowByDate']) : "";
$b2sUserAuthId = isset($_GET['b2sUserAuthId']) ? (int) $_GET['b2sUserAuthId'] : "";
$options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID);
$optionUserTimeZone = $options->_getOption('user_time_zone');
$userTimeZone = ($optionUserTimeZone !== false) ? $optionUserTimeZone : get_option('timezone_string');
$userTimeZoneOffset = (empty($userTimeZone)) ? get_option('gmt_offset') : B2S_Util::getOffsetToUtcByTimeZone($userTimeZone);
?>

<div class="b2s-container">
    <div class="b2s-inbox">
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.phtml'); ?>
        <div class="col-md-12 del-padding-left">
            <div class="col-md-9 del-padding-left">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!--Posts from Wordpress Start-->
                        <!--Filter Start-->
                        <div class="grid b2s-post">
                            <div class="grid-body">
                                <div class="pull-right"><code id="b2s-user-time"><?php echo B2S_Util::getLocalDate($userTimeZoneOffset, substr(B2S_LANGUAGE, 0, 2)); ?> <?php echo ((substr(B2S_LANGUAGE, 0, 2) == 'de')? __('Uhr','blog2social'): '') ?></code></div>
                                <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/post.navbar.phtml'); ?>
                                <!-- Filter Post Start-->
                                <form class="b2sSortForm form-inline pull-left" action="#">
                                    <input id="b2sType" type="hidden" value="sched" name="b2sType">
                                    <input id="b2sShowByDate" type="hidden" value="<?php echo $b2sShowByDate; ?>" name="b2sShowByDate">
                                    <input id="b2sUserAuthId" type="hidden" value="<?php echo $b2sUserAuthId; ?>" name="b2sUserAuthId">
                                    <input id="b2sPagination" type="hidden" value="1" name="b2sPagination">
                                    <?php
                                    $postFilter = new B2S_Post_Filter('sched');
                                    echo $postFilter->getItemHtml('blog2social-sched');
                                    ?>
                                </form>
                                <!-- Filter Post Ende-->
                                <br/>
                            </div>       
                        </div>
                        <div class="clearfix"></div> 
                        <!--Filter End-->
                        <div class="b2s-sort-area">
                            <div class="b2s-loading-area" style="display:none">
                                <br>
                                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                <div class="clearfix"></div>
                                <div class="text-center b2s-loader-text"><?php _e("Loading...", "blog2social"); ?></div>
                            </div>
                            <div class="row b2s-sort-result-area">
                                <div class="col-md-12">
                                    <ul class="list-group b2s-sort-result-item-area"></ul>
                                    <br>
                                    <nav class="b2s-sort-pagination-area text-center"></nav>
                                    <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.phtml'); ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/service.phtml'); ?>
            <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/sidebar.phtml'); ?>
        </div>
    </div>
</div>


<div class="modal fade b2s-change-datetime-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-change-datetime-modal" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-change-datetime-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e('Change Time', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control" id="b2s-change-date" placeholder="Date" value="<?php echo (substr(B2S_LANGUAGE, 0, 2) == 'de') ? date('d.m.Y') : date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="b2s-change-time" placeholder="Time" value="<?php echo date('H:i'); ?>">
                    </div>
                    <input type="hidden" value="" id="b2s-data-post-id">
                    <input type="hidden" value="<?php echo $userTimeZoneOffset; ?>" id="user_timezone">
                    <input type="hidden" value="" id="b2s-data-blog-sched-date">
                    <input type="hidden" value="" id="b2s-data-b2s-sched-date">                      
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary b2s-change-date-btn"><?php _e('save', 'blog2social') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade b2s-delete-sched-modal" tabindex="-1" role="dialog" aria-labelledby="b2s-delete-sched-modal" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name=".b2s-delete-sched-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e('Delete entries form the scheduling', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <b><?php _e('You are sure, you want to delete entries from the scheduling?', 'blog2social') ?> </b>
                <br>
                (<?php _e('Number of entries', 'blog2social') ?>:  <span id="b2s-delete-confirm-post-count"></span>)
                <input type="hidden" value="" id="b2s-delete-confirm-post-id">
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><?php _e('NO', 'blog2social') ?></button>
                <button class="btn btn-danger b2s-sched-delete-confirm-btn"><?php _e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="b2sLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">
<input type="hidden" id="b2sUserLang" value="<?php echo strtolower(substr(get_locale(), 0, 2)); ?>">
<input type="hidden" id="b2sCalendarSchedDates" value='0'>
