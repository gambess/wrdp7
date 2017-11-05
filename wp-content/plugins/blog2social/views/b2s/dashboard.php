<div class="b2s-container">
    <div class="b2s-inbox">
        <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.phtml'); ?>
        <div class="col-md-12 del-padding-left">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="grid-body">
                        <?php
                        $updateMail = get_option('B2S_UPDATE_MAIL_' . B2S_PLUGIN_BLOG_USER_ID);
                        if ($updateMail == false || empty($updateMail)) {
                            ?>
                            <div class="well b2s-mail-update-area">
                                <h4 class="b2s-dashboard-h4"><?php _e('Get news and updates for promoting your blog on social media', 'blog2social') ?></h4>
                                <div class="form-inline">
                                    <div class="form-group">
                                        <input id="b2s-mail-update-input" class="form-control" name="b2sMailUpdate" value="<?php echo $wpUserData->user_email; ?>" placeholder="E-Mail" type="text">
                                        <input type="hidden" id="user_lang" value="<?php echo substr(B2S_LANGUAGE, 0, 2) ?>">
                                        <a class="btn btn-success b2s-mail-btn" href="#"><?php _e('Get updates', 'blog2social') ?></a>
                                    </div>
                                    <div class="b2s-info-sm hidden-xs"><?php _e('We hate spam, too. We will never sell your email address to any other company or for any other purpose.', 'blog2social') ?></div>
                                </div>
                            </div> 
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="col-md-6 b2s-padding-bottom-50">
                            <br>
                            <h5 class="b2s-dashboard-h5"><?php _e('How to use Blog2Social – Step by Step', 'blog2social') ?></h5>
                            <p><?php _e('Learn how to get the most out of Blog2Social to promote your blog on social media.', 'blog2social') ?>
                                <a target="_blank" class="b2s-btn-link" href="<?php echo B2S_Tools::getSupportLink('howto'); ?>"><?php _e('Learn more', 'blog2social') ?></a>
                            </p>
                            <br>
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="border embed-responsive-item" type="text/html" src="https://www.youtube.com/embed/YYjlIgWOGTU" frameborder="0" allowfullscreen></iframe>
                            </div>
                            <div class="clearfix"></div>
                            <br>
                            <h5 class="b2s-dashboard-h5"><?php _e('Do you need help?', 'blog2social') ?></h5>
                            <p><?php _e('Find answers to common questions in', 'blog2social') ?>
                                <a target="_blank" class="btn-success btn-xs" href="<?php echo B2S_Tools::getSupportLink('faq'); ?>"><?php _e('our FAQ', 'blog2social') ?></a>
                            </p>
                            <div class="clearfix"></div>
                            <br>
                            <div class="b2s-faq-area well">
                                <div class="b2s-loading-area-faq" style="display:block">
                                    <br>
                                    <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                                    <div class="clearfix"></div>
                                    <small><?php _e('Loading Top 5 FAQ', 'blog2social') ?></small>
                                </div>
                                <div class="b2s-faq-content"></div>
                                <a target="_blank" class="btn btn-primary btn-block btn-lg" href="<?php echo B2S_Tools::getSupportLink('faq'); ?>"><?php _e('Blog2Social FAQ', 'blog2social') ?></a>
                            </div>
                            <div class="clearfix"></div>
                            <?php if (B2S_PLUGIN_USER_VERSION > 0) { ?>   
                                <br> 
                                <h6 class="b2s-dashboard-h6"><?php _e('Couldn\'t find your answer?', 'blog2social') ?></h6>
                                <a target="_blank" class="btn btn-primary btn-xs" href="<?php echo B2S_Tools::getSupportLink('faq'); ?>">
                                    <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> <?php _e('Contact Support by Email', 'blog2social') ?>
                                </a>  
                                <span class="btn btn-success b2s-dashoard-btn-phone hidden-xs btn-xs"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span> <?php _e('Call us: +49 2181 7569-277', 'blog2social') ?></span>
                                <br>
                                <div class="b2s-info-sm hidden-xs"><?php _e('(Call times: from 9:00 a.m. to 5:00 p.m. CET on working days)', 'blog2social') ?></div>
                            <?php } ?>
                            <br><br>
                            <h6 class="b2s-dashboard-h6 hidden-xs hidden-sm"><?php _e('Follow us:', 'blog2social') ?>
                                <a target="_blank" href="https://www.facebook.com/Blog2Social"><img class="b2s-wdith-35" src="<?php echo plugins_url('/assets/images/portale/1_flat.png', B2S_PLUGIN_FILE) ?>" alt="facebook"></a>
                                <a target="_blank" href="https://twitter.com/blog2social<?php echo (substr(B2S_LANGUAGE, 0, 2) != 'de') ? '_com' : ''; ?>"><img class="b2s-wdith-35" src="<?php echo plugins_url('/assets/images/portale/2_flat.png', B2S_PLUGIN_FILE) ?>" alt="twitter"></a>
                            </h6>
                        </div>
                        <div class="col-md-6 b2s-padding-bottom-50">
                            <br>  
                            <h5 class="b2s-dashboard-h5"><?php _e('Your license: Blog2Social', 'blog2social') ?>
                                <span class="b2s-key-name">
                                    <?php
                                    $versionType = unserialize(B2S_PLUGIN_VERSION_TYPE);
                                    if (defined("B2S_PLUGIN_TRAIL_END") && strtotime(B2S_PLUGIN_TRAIL_END) > time()) {
                                        echo 'FREE-TRIAL (' . $versionType[B2S_PLUGIN_USER_VERSION] . ')';
                                    } else {
                                        echo $versionType[B2S_PLUGIN_USER_VERSION];
                                    }
                                    ?> 
                                </span>
                            </h5>
                            <p><?php _e('Upgrade to Blog2Social Premium to schedule your posts for the best time, once or recurringly with the Best Time Scheduler and post to pages, groups and multiple accounts per network.', 'blog2social') ?>
                                <a target="_blank" class="b2s-btn-link" href="<?php echo B2S_Tools::getSupportLink('feature'); ?>"><?php _e('Learn more', 'blog2social') ?></a></p>
                            <div class="clearfix"></div>
                            <br>
                            <div class="b2s-key-area">
                                <div class="input-group">
                                    <input class="form-control input-sm b2s-key-area-input" placeholder="<?php _e('Enter license key and change your version', 'blog2social'); ?>" value="" type="text">
                                    <span class="input-group-btn">
                                        <button class="btn btn-success btn-sm b2s-key-area-btn-submit"><?php _e('Activate', 'blog2social'); ?></button>
                                    </span>
                                </div>
                            </div>
                            <!--Features-->
                            <div class="hidden-xs">
                                <br>
                                <div class="row">
                                    <div class="col-xs-2 col-md-3 col-lg-2 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo plugins_url('/assets/images/features/networks-choice.png', B2S_PLUGIN_FILE); ?>" alt="Network">
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-9 col-lg-10">
                                        <h6 class="b2s-dashboard-h6"><?php _e('Network Choice', 'blog2social') ?></h6>
                                        <p><?php _e('Cross-share to all popular social networks', 'blog2social') ?></p>               
                                        <span class="pull-right label label-info">FREE</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 col-md-3 col-lg-2 col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo plugins_url('/assets/images/features/automation.png', B2S_PLUGIN_FILE); ?>" alt="Auto-Posting">
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-9 col-lg-10">
                                        <h6 class="b2s-dashboard-h6"><?php _e('Auto-Posting', 'blog2social') ?></h6>
                                        <p><?php _e('Automatically share your posts whenever you publish a new blog post', 'blog2social') ?></p>               
                                        <span class="pull-right label label-success"><a target="_blank" class="btn-label-premium" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>">PREMIUM</a></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 col-md-3 col-lg-2  col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo plugins_url('/assets/images/features/advanced-customization.png', B2S_PLUGIN_FILE); ?>" alt="Customization">
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-9 col-lg-10">
                                        <h6 class="b2s-dashboard-h6"><?php _e('Custom Sharing', 'blog2social') ?></h6>
                                        <p><?php _e('Edit or add comments, hashtags or handles. Edit posts in HTML for re-publishing on blogging networks', 'blog2social') ?></p>                
                                        <span class="pull-right label label-info">FREE</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 col-md-3 col-lg-2  col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo plugins_url('/assets/images/features/best-time-scheduling.png', B2S_PLUGIN_FILE); ?>" alt="Scheduling">
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-9 col-lg-10">
                                        <h6 class="b2s-dashboard-h6"><?php _e('Best Time Scheduler', 'blog2social') ?></h6>
                                        <p><?php _e('Choose pre-defined times to post or edit and define your own time settings', 'blog2social') ?></p>               
                                        <span class="pull-right label label-success"><a target="_blank" class="btn-label-premium" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>">PREMIUM</a></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 col-md-3 col-lg-2  col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo plugins_url('/assets/images/features/advanced-scheduling.png', B2S_PLUGIN_FILE); ?>" alt="Advanced Scheduling">
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-9 col-lg-10">
                                        <h6 class="b2s-dashboard-h6"><?php _e('Custom Scheduling', 'blog2social') ?></h6>
                                        <p><?php _e('Unlimited scheduling options: once, repeatedly or recurringly to multiple profiles, pages and groups', 'blog2social') ?></p>               
                                        <span class="pull-right label label-success"><a target="_blank" class="btn-label-premium" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>">PREMIUM</a></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 col-md-3 col-lg-2  col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo plugins_url('/assets/images/features/one-step-workflow.png', B2S_PLUGIN_FILE); ?>" alt="One-Step Workflow">
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-9 col-lg-10">
                                        <h6 class="b2s-dashboard-h6"><?php _e('One-Step Workflow', 'blog2social') ?></h6>
                                        <p><?php _e('One-page preview editor for all social networks for easy customizing', 'blog2social') ?></p>             
                                        <span class="pull-right label label-info">FREE</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 col-md-3 col-lg-2  col-hide-padding-left">
                                        <div class="thumbnail text-center">
                                            <img class="b2s-feature-img-with-90" src="<?php echo plugins_url('/assets/images/features/reporting.png', B2S_PLUGIN_FILE); ?>" alt="Reporting">
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-9 col-lg-10">
                                        <h6 class="b2s-dashboard-h6"><?php _e('Reporting', 'blog2social') ?></h6>
                                        <p><?php _e('All scheduled and published social media posts with direct links for easy access or re-sharing', 'blog2social') ?></p>                
                                        <span class="pull-right label label-success"><a target="_blank" class="btn-label-premium" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>">PREMIUM</a></span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                                <a class="btn btn-primary btn-lg btn-block" href="<?php echo B2S_Tools::getSupportLink('affiliate'); ?>"><?php _e('Unlock Premium', 'blog2social') ?></a>
                            <?php } ?>
                        </div>


                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                $noLegend = 1;
                                require_once (B2S_PLUGIN_DIR . 'views/b2s/html/footer.phtml');
                                ?> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
