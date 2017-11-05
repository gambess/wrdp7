<div id="b2s-edit-event-modal-<?= $item->getB2SId(); ?>" class="modal fade" role="dialog" aria-labelledby="b2s-trial-modal" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="b2s-modal-close close release_locks" data-modal-name="#b2s-edit-event-modal-<?= $item->getB2SId(); ?>">&times;</button>
                <h4 class="modal-title">
                    <?php echo __("Edit Post", "blog2social"); ?>
                    <?php if (B2S_PLUGIN_USER_VERSION == 0) { ?>
                        <span class="label label-success"><a href="#" class="b2s-btn-label-premium btn-label-premium-xs b2s-info-btn" data-modal-target="b2sInfoMetaBoxModalAutoPost"><?= __("PREMIUM", "blog2social"); ?></a></span>
                    <?php } ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <form>
                            <input type="hidden" name="action" value="b2s_calendar_save" />
                            <input type="hidden" id="post_id" name="post_id" value="<?= $item->getPostId(); ?>">
                            <input type="hidden" name="b2s_id" value="<?= $item->getB2SId(); ?>">
                            <input type="hidden" name="sched_details_id" value="<?= $item->getSchedDetailsId(); ?>">
                            <input type="hidden" id="save_method" name="save_method" value="apply-this" />
                            <input type="hidden" id="b2sChangeOgMeta" name="change_og_meta" value="0">
                            <input type="hidden" id="b2sChangeCardMeta" name="change_card_meta" value="0">
                            <input type="hidden" id="b2sUserTimeZone" name="user_timezone" value="0">
                            
                            <?php if($lock_user_id && $lock_user_id != get_current_user_id()){ ?>
                                <div class="alert alert-danger">
                                    <?= str_replace("%1",esc_html($lock_user->user_login),__('This post is blocked by %1', 'blog2social')); ?>.
                                </div>
                            <?php } ?>

                            <?= $item->getEditHtml(); ?>
                            <div class="panel panel-group">
                                <div class="b2s-post-item-details-release-area-details">
                                    <ul class="list-group b2s-post-item-details-release-area-details-ul" data-network-auth-id="<?= $item->getNetworkAuthId(); ?>">
                                        <li class="list-group-item">
                                            <div class="form-group b2s-post-item-details-releas-area-details-row" data-network-count="1"  data-network-auth-id="<?= $item->getNetworkAuthId(); ?>">
                                                <div class="clearfix"></div>

                                                <label class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="<?= $item->getNetworkAuthId(); ?>" data-network-count="1"><?= __('Date', 'blog2social'); ?></label>
                                                <label class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="<?= $item->getNetworkAuthId(); ?>" data-network-count="1"><?= __('Time', 'blog2social'); ?></label>

                                                <div class="clearfix"></div>

                                                <div class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="<?= $item->getNetworkAuthId(); ?>" data-network-count="1"><input type="text" placeholder="<?= __('Date', 'blog2social'); ?>" name="b2s[<?= $item->getNetworkAuthId(); ?>][date][0]" data-network-id="<?= $item->getNetworkId(); ?>" data-network-type="<?= $item->getNetworkType(); ?>" data-network-count="0" data-network-auth-id="<?= $item->getNetworkAuthId(); ?>"  class="b2s-post-item-details-release-input-date form-control" value="<?= (substr(B2S_LANGUAGE, 0, 2) == 'de') ? date('d.m.Y', $item->getSchedDate()) : date('Y-m-d', $item->getSchedDate()); ?>" style="min-width: 93px;"></div>
                                                <div class="col-xs-3 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="<?= $item->getNetworkAuthId(); ?>" data-network-count="1"><input type="text" placeholder="<?= __('Time', 'blog2social'); ?>" name="b2s[<?= $item->getNetworkAuthId(); ?>][time][0]" data-network-id="<?= $item->getNetworkId(); ?>" data-network-type="<?= $item->getNetworkType(); ?>" data-network-count="0"  data-network-auth-id="<?= $item->getNetworkAuthId(); ?>"  class="b2s-post-item-details-release-input-time form-control" value="<?= date('H:i', $item->getSchedDate()); ?>"></div>
                                                <div class="col-xs-5 del-padding-left b2s-post-item-details-release-area-label-day" data-network-auth-id="<?= $item->getNetworkAuthId(); ?>" data-network-count="1">
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php if(!$lock_user_id || $lock_user_id == get_current_user_id()){ ?>
                                <div class="col-xs-12" style="margin-top: 20px;">
                                    <div class="pull-left" style="line-height: 33px">
                                        <span class="b2s-calendar-delete btn btn-danger" data-post-id="<?= $item->getPostId(); ?>" data-b2s-id="<?=  $item->getB2SId(); ?>">
                                            <span class="glyphicon glyphicon glyphicon-trash "></span> <?=  esc_attr(__("Delete", "blog2social")); ?>
                                        </span>
                                    </div>
                                    <div class="pull-right">
                                        <input class="btn btn-success pull-right b2s-calendar-save-this" type="submit" value="<?= esc_attr(__('Change details', 'blog2social')); ?>" data-post-id="<?=  $item->getPostId(); ?>" data-b2s-id="<?= $item->getB2SId(); ?>">
                                    </div>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
