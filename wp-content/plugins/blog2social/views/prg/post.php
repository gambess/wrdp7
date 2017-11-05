<?php
/* Data */
require_once (B2S_PLUGIN_DIR . 'includes/PRG/Post/Filter.php');
require_once (B2S_PLUGIN_DIR . 'includes/PRG/Post/Item.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');
/* Sort */
$currentPage = (int) isset($_GET['b2sPage']) ? $_GET['b2sPage'] : 1;
$prgSortPostType = isset($_GET['prgSortPostType']) ? trim($_GET['prgSortPostType']) : "";
$prgSortPostStatus = isset($_GET['prgSortPostStatus']) ? trim($_GET['prgSortPostStatus']) : "";
$prgSortPostTitle = isset($_GET['prgSortPostTitle']) ? trim($_GET['prgSortPostTitle']) : "";
$prgSortPostAuthor = isset($_GET['prgSortPostAuthor']) ? trim($_GET['prgSortPostAuthor']) : "";
$prgUserLang = strtolower(substr(get_locale(), 0, 2));
?>

<div class="b2s-container">
    <div class="b2s-inbox">
        <?php require_once (B2S_PLUGIN_DIR . 'views/prg/html/header.phtml'); ?>
        <div class="col-md-12 del-padding-left">
            <div class="col-md-12 del-padding-left">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <!--Filter Start-->
                        <div class="grid b2s-post">
                            <div class="grid-body">
                                <div class="hidden-lg hidden-md hidden-sm filterShow"><a href="#" onclick="showFilter('show');return false;"><i class="glyphicon glyphicon-chevron-down"></i> <?php _e('filter','blog2social') ?></a></div>
                                <div class="hidden-lg hidden-md hidden-sm filterHide"><a href="#" onclick="showFilter('hide');return false;"><i class="glyphicon glyphicon-chevron-up"></i> <?php _e('filter','blog2social') ?></a></div>

                                <!-- Filter Post Start-->
                                <form class="form-inline pull-left" action="#" method="GET">
                                    <input id="page" type="hidden" value="prg-post" name="page">
                                    <?php
                                    $postFilter = new PRG_Post_Filter('all', $prgSortPostTitle, $prgSortPostAuthor, $prgSortPostType,$prgSortPostStatus);
                                    echo $postFilter->getItemHtml();
                                    ?>
                                </form>
                                <!-- Filter Post Ende-->
                                <br>
                            </div>       
                        </div>
                        <div class="clearfix"></div>
                        <!--Filter End-->
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-group">
                                    <?php
                                    $postItem = new PRG_Post_Item('all', $prgSortPostTitle, $prgSortPostAuthor, $prgSortPostType,$prgSortPostStatus, '', '', $currentPage,$prgUserLang);
                                    echo $postItem->getItemHtml();
                                    ?>
                                </ul>
                                <br>
                                <nav class="text-center">
                                    <?php
                                    echo $postItem->getPaginationHtml();
                                    ?>
                                </nav>
                                <?php require_once (B2S_PLUGIN_DIR . 'views/prg/html/footer.phtml'); ?> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>