<?php
require_once B2S_PLUGIN_DIR . 'includes/B2S/Network/Item.php';
$b2sSiteUrl = get_option('siteurl') . ((substr(get_option('siteurl'), -1, 1) == '/') ? '' : '/');
$networkItem = new B2S_Network_Item();
$networkData = $networkItem->getData();
?>

<div class="b2s-container">
    <?php require_once (B2S_PLUGIN_DIR . 'views/b2s/html/header.phtml'); ?>
    <div class=" b2s-inbox col-md-12 del-padding-left">
        <div class="col-md-9 del-padding-left">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="grid b2s-post">
                            <div class="grid-body">
                                <h3><?php _e('Networks', 'blog2social') ?>
                                    <a class="b2s-info-btn" data-target="#b2sInfoAddMandant" data-toggle="modal" href="#"><?php echo _e('Info', 'blog2social'); ?></a>

                                </h3>
                                <hr>
                                <div class="hidden-lg hidden-md hidden-sm filterShow"><a href="#" onclick="showFilter('show');return false;"><i class="glyphicon glyphicon-chevron-down"></i> <?php _e('filter', 'blog2social') ?></a></div>
                                <div class="hidden-lg hidden-md hidden-sm filterHide"><a href="#" onclick="showFilter('hide');return false;"><i class="glyphicon glyphicon-chevron-up"></i> <?php _e('filter', 'blog2social') ?></a></div>
                                <div class="form-inline" role="form">
                                    <?php echo $networkItem->getSelectMandantHtml($networkData['mandanten']); ?>
                                    <div class="form-group">
                                        <button href="#" class="btn btn-primary btn-sm b2s-network-mandant-btn-delete" style="display:none;">
                                            <span class="glyphicon glyphicon-trash"></span> <?php _e('Delete', 'blog2social') ?>
                                        </button>
                                    </div>
                                    <?php if (B2S_PLUGIN_USER_VERSION > 1) { ?> 
                                        <a href="#" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#b2s-network-add-mandant">
                                            <span class="glyphicon glyphicon-plus"></span> <?php _e('Create new profile', 'blog2social') ?> <span class="label label-success"></a>
                                    <?php } else { ?>
                                        <a href="#" class="btn btn-primary btn-sm b2s-btn-disabled pull-right" data-toggle="modal" data-type="create-network-profile" data-title="<?php _e('You want to define a new combination of networks?', 'blog2social') ?>" data-target="#b2sProFeatureModal">
                                            <span class="glyphicon glyphicon-plus"></span> <?php _e('Create new profile', 'blog2social') ?> <span class="label label-success"> <?php _e("PREMIUM", "blog2social") ?></span></a>
                                    <?php } ?>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="row b2s-network-auth-area">
                            <?php echo $networkItem->getPortale($networkData['mandanten'], $networkData['auth'], $networkData['portale'], $networkData['auth_count']); ?>
                        </div>                        
                        <div class="row b2s-loading-area width-100" style="display: none">
                            <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                            <div class="clearfix"></div>
                            <?php _e('Loading...', 'blog2social') ?>
                        </div>
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
<input type="hidden" id="lang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">

<div class="modal fade" id="b2s-network-add-mandant" tabindex="-1" role="dialog" aria-labelledby="b2s-network-add-mandant" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-add-mandant" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <?php _e('Create new profile', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <input type="text" class="form-control b2s-network-add-mandant-input" placeholder="Profil">
                    <span class="input-group-btn">
                        <button class="btn btn-success b2s-network-add-mandant-btn-save" type="button"><?php _e('create', 'blog2social') ?></button>
                    </span>
                    <div class="input-group-btn">
                        <div class="btn btn-default b2s-network-add-mandant-btn-loading b2s-loader-impulse b2s-loader-impulse-sm" style="display:none"></div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>


<div class="modal fade" id="b2sInfoAddMandant" tabindex="-1" role="dialog" aria-labelledby="b2sInfoAddMandant" aria-hidden="true"  data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2sInfoAddMandant" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <?php _e('Create new profile', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <p><?php _e('All connected networks will be displayed as default "Standard" networks profile.<br><br>You may define various sets of social media accounts, profiles, pages or groups for different sharing purposes. For example pre-select specific set of all your networks for initial sharing and another set of specific networks for re-sharing your post to. Or, if you have multiple Twitter accounts or multiple Facebook pages and you want to share your post to specific accounts or pages only, this feature may come handy for even faster access to a specific selection of your sharing purposes. You may also select sets of networks for re-sharing or scheduling your posts once or recurrently at various days and times and with different comments or images.<br><br> This feature gives you an easier and faster access to an unlimited number of variations for pre-selected sets of social media accounts for any sharing and scheduling scheme you may think of.<br><br> You can always select and remove any account from any of your networks profiles on your preview page with just a click of your mouse for an easy variation of your current sharing scheme.', 'blog2social'); ?></p>
                <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                    <hr>
                    <h4><?php _e('You want to add another network profile, pages or groups?', 'blog2social'); ?></h4>
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

<div class="modal fade" id="b2s-network-delete-mandant" tabindex="-1" role="dialog"  aria-labelledby="b2s-network-delete-mandant" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-delete-mandant" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e('Delete Profile', 'blog2social') ?></h4>
            </div>
            <div class="modal-body">
                <?php _e('Do you really want to delete this profile', 'blog2social') ?>?
            </div> 
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" data-dismiss="modal"><?php _e('NO', 'blog2social') ?></button>
                <button class="btn btn-sm btn-danger b2s-btn-network-delete-mandant-confirm"><?php _e('YES, delete', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="b2s-network-delete-auth" tabindex="-1" role="dialog" aria-labelledby="b2s-network-delete-auth" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close" data-modal-name="#b2s-network-delete-auth" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php _e('Delete Authorization', 'blog2social') ?></h4>
            </div>
            <div class="row b2s-loading-area width-100">
                <br>
                <div class="b2s-loader-impulse b2s-loader-impulse-md"></div>
                <div class="clearfix"></div>
                <?php _e('Loading...', 'blog2social') ?>
            </div>
            <div class="modal-body b2s-btn-network-delete-auth-confirm-text">
                <?php _e('Do you really want to delete this authorization', 'blog2social') ?>
            </div> 
            <div class="modal-body b2s-btn-network-delete-auth-show-post-text">
                <p><?php _e('You have still set up scheduled posts for this network:', 'blog2social'); ?></p>
                <p><span id="b2s-btn-network-delete-auth-show-post-count"></span> <?php _e('scheduled posts', 'blog2social') ?></p>
            </div> 
            <div class="modal-footer">
                <input type="hidden" value="" id="b2s-delete-network-auth-id">
                <input type="hidden" value="" id="b2s-delete-network-id">
                <button class="btn btn-sm btn-danger b2s-btn-network-delete-auth-confirm-btn"><?php _e('YES, delete', 'blog2social') ?></button>
                <button class="btn btn-sm btn-success b2s-btn-network-delete-auth-show-post-btn"><?php _e('View schedule posts for this profile', 'blog2social') ?></button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="b2sUserLang" value="<?php echo substr(B2S_LANGUAGE, 0, 2); ?>">
<input type="hidden" id="b2sServerUrl" value="<?php echo B2S_PLUGIN_SERVER_URL; ?>">
<input type="hidden" id="b2s-redirect-url-sched-post" value="<?php echo $b2sSiteUrl . 'wp-admin/admin.php?page=blog2social-sched'; ?>"/>
