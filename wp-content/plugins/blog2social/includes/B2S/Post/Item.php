<?php

class B2S_Post_Item {

    protected $postData;
    protected $postTotal = 0;
    protected $postItem = '';
    protected $postPagination = '';
    protected $postPaginationLinks = 5;
    protected $searchAuthorId;
    protected $searchPostStatus;
    protected $searchShowByDate;
    protected $searchPublishDate;
    protected $searchSchedDate;
    protected $searchPostTitle;
    protected $searchPostCat;
    protected $searchPostType;
    protected $postCalendarSchedDates;
    protected $searchUserAuthId;
    protected $userLang;
    public $currentPage = 0;
    public $type;

    function __construct($type = 'all', $title = "", $authorId = "", $postStatus = "", $publishDate = '', $schedDate = '', $showByDate = '', $userAuthId = 0, $currentPage = 0, $postCat = "", $postType = "", $userLang = "en") {
        $this->type = $type;
        $this->searchPostTitle = $title;
        $this->searchAuthorId = $authorId;
        $this->searchPostStatus = $postStatus;
        $this->searchPublishDate = $publishDate;
        $this->searchSchedDate = $schedDate;
        $this->searchShowByDate = $showByDate;
        $this->searchUserAuthId = $userAuthId;
        $this->currentPage = $currentPage;
        $this->searchPostCat = $postCat;
        $this->searchPostType = $postType;
        $this->userLang = $userLang; //Plugin: qTranslate
    }

    protected function getData() {
        global $wpdb;

        $addSearchAuthorId = '';
        $addSearchPostTitle = '';
        $order = 'post_date';
        $sortType = 'DESC';
        $leftJoin = "";
        $leftJoin2 = "";
        $leftJoinWhere = "";

        if (!empty($this->searchPublishDate)) {
            $sortType = $this->searchPublishDate;
        }
        if (!empty($this->searchSchedDate)) {
            $sortType = $this->searchSchedDate;
        }
        if (!empty($this->searchPostTitle)) {
            $addSearchPostTitle = $wpdb->prepare(' AND posts.`post_title` LIKE %s', '%' . trim($this->searchPostTitle) . '%');
        }
        if (!empty($this->searchAuthorId)) {
            $addSearchAuthorId = $wpdb->prepare(' AND posts.`post_author` = %d', $this->searchAuthorId);
        }
        if (!empty($this->searchPostCat)) {
            if ($this->type == 'all') {
                $leftJoin = "LEFT JOIN $wpdb->term_relationships ON posts.`ID` = $wpdb->term_relationships.`object_id`";
            } else {
                $leftJoin = "LEFT JOIN $wpdb->term_relationships ON posts.`ID` = $wpdb->term_relationships.`object_id`";
            }
            $leftJoin2 = "LEFT JOIN $wpdb->term_taxonomy ON $wpdb->term_taxonomy.`term_taxonomy_id` = $wpdb->term_relationships.`term_taxonomy_id`";
            $leftJoinWhere = "AND  $wpdb->term_taxonomy.`term_id` = " . $this->searchPostCat;
        }

        if (!empty($this->searchPostStatus)) {
            $addSearchType = $wpdb->prepare(' posts.`post_status` = %s', $this->searchPostStatus);
        } else {
            $addSearchType = " (posts.`post_status` = 'publish' OR posts.`post_status` = 'pending' OR posts.`post_status` = 'future') ";
        }

        $postTypes = " ";
        if (!empty($this->searchPostType)) {
            $postTypes .= " posts.`post_type` LIKE '%" . $this->searchPostType . "%' ";
        } else {
            $post_types = get_post_types(array('public' => true));
            if (is_array($post_types) && !empty($post_types)) {
                $postTypes .= " posts.`post_type` IN("; // AND
                foreach ($post_types as $k => $v) {
                    if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                        $postTypes .= "'" . $v . "',";
                    }
                }
                $postTypes = rtrim($postTypes, ',');
                $postTypes .= " ) ";
            } else {
                $postTypes .= " (posts.`post_type` LIKE '%product%' OR posts.`post_type` LIKE '%book%' OR posts.`post_type` LIKE '%article%' OR posts.`post_type` LIKE '%job%' OR posts.`post_type` LIKE '%event%' OR posts.`post_type` = 'post' OR posts.`post_type` = 'page') ";
            }
        }

        $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND posts.`post_author` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
        $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND a.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';

        if ($this->type == 'all') {
            $sqlPosts = "SELECT posts.`ID`, posts.`post_author`, posts.`post_date`, posts.`post_type`, posts.`post_status`, posts.`post_title`
		FROM `$wpdb->posts` posts $leftJoin $leftJoin2
		WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle $addNotAdmin
                AND  $postTypes $leftJoinWhere
		ORDER BY `" . $order . "` " . $sortType . "
                LIMIT " . (($this->currentPage - 1) * B2S_PLUGIN_POSTPERPAGE) . "," . B2S_PLUGIN_POSTPERPAGE;
            $this->postData = $wpdb->get_results($sqlPosts);
            $sqlPostsTotal = "SELECT COUNT(*)
		FROM `$wpdb->posts` posts $leftJoin $leftJoin2
		WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle $addNotAdmin
                AND $postTypes $leftJoinWhere";
            $this->postTotal = $wpdb->get_var($sqlPostsTotal);
        }

        if ($this->type == 'publish' || $this->type == 'sched') {
            //ExistsTable
            if ($wpdb->get_var("SHOW TABLES LIKE 'b2s_posts'") == 'b2s_posts') {
                $where = ($this->type == 'publish') ? " a.`hide` = 0 AND (a.`sched_date`= '0000-00-00 00:00:00' OR a.`sched_type` = 3) $addNotAdminPosts GROUP BY a.`post_id` ORDER BY a.`publish_date` " . $sortType : " a.`hide` = 0 AND a.`sched_type` != 3 AND a.`publish_date`= '0000-00-00 00:00:00' $addNotAdminPosts GROUP BY a.`post_id` ORDER BY a.`sched_date` " . $sortType;
                $orderBy = ($this->type == 'publish') ? " ORDER BY filter.`publish_date` " . $sortType : " ORDER BY filter.`sched_date` " . $sortType;
                $addSearchShowByDate = (!empty($this->searchShowByDate)) ? (($this->type == 'publish') ? " DATE_FORMAT(a.`publish_date`,'%Y-%m-%d') = '" . $this->searchShowByDate . "' AND " : " DATE_FORMAT(a.`sched_date`,'%Y-%m-%d') = '" . $this->searchShowByDate . "' AND ") : '';
                $addInnerJoinLeftJoin = ((int) $this->searchUserAuthId != 0) ? ' LEFT JOIN b2s_posts_network_details b ON b.`id` = a.`network_details_id` ' : '';
                $addInnnerJoinLeftJoinWhere = ((int) $this->searchUserAuthId != 0) ? ' b.`network_auth_id` =' . $this->searchUserAuthId . ' AND ' : '';

                $select = ($this->type == 'publish') ? 'filter.`blog_user_id`, filter.`publish_date`' : 'filter.`blog_user_id`, filter.`sched_date`';
                $selectInnerJoin = ($this->type == 'publish') ? '`publish_date`' : '`sched_date`';
                $sqlPosts = "SELECT posts.`ID`, posts.`post_author`,posts.`post_type`,posts.`post_title`, " . $select . ", filter.`id`
                            FROM `$wpdb->posts` posts $leftJoin $leftJoin2
                                INNER JOIN(
                                        SELECT a.`id`,$selectInnerJoin, a.`blog_user_id`, a.`post_id`
                                            FROM `b2s_posts` a $addInnerJoinLeftJoin
                                                  WHERE $addInnnerJoinLeftJoinWhere $addSearchShowByDate $where
                                         ) filter
                                     ON posts.`ID` = filter.`post_id`
                             WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle AND $postTypes $leftJoinWhere $orderBy
                        LIMIT " . (($this->currentPage - 1) * B2S_PLUGIN_POSTPERPAGE) . "," . B2S_PLUGIN_POSTPERPAGE;

                $this->postData = $wpdb->get_results($sqlPosts);

                if ($this->type == 'publish') {
                    $sqlPostsTotal = "SELECT COUNT(posts.`ID`)
                            FROM `$wpdb->posts` posts $leftJoin $leftJoin2
                                INNER JOIN(
                                        SELECT a.`post_id`
                                            FROM `b2s_posts` a
                                                 WHERE $addSearchShowByDate $where
                                         ) filter
                                     ON posts.`ID` = filter.`post_id`
                             WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle AND $postTypes $leftJoinWhere";
                    $this->postTotal = $wpdb->get_var($sqlPostsTotal);

                    //for Calender (mark Event)
                } else {
                    $where = ($this->type == 'publish') ? " a.`hide` = 0 AND (a.`sched_date`= '0000-00-00 00:00:00' OR a.`sched_type` = 3) $addNotAdminPosts ORDER BY a.`publish_date` " . $sortType : " a.`hide` = 0 AND a.`sched_type` != 3 AND a.`publish_date`= '0000-00-00 00:00:00' $addNotAdminPosts  ORDER BY a.`sched_date` " . $sortType;
                    $sqlPostsTotal = "SELECT posts.`ID`, DATE_FORMAT(filter.`sched_date`,'%Y-%m-%d') AS sched
                            FROM `$wpdb->posts` posts $leftJoin $leftJoin2
                                INNER JOIN(
                                        SELECT a.`post_id`, a.`sched_date`
                                            FROM `b2s_posts` a $addInnerJoinLeftJoin
                                                 WHERE $addInnnerJoinLeftJoinWhere $addSearchShowByDate $where 
                                         ) filter
                                     ON posts.`ID` = filter.`post_id`
                             WHERE $addSearchType $addSearchAuthorId $addSearchPostTitle AND $postTypes $leftJoinWhere";

                    $schedResult = $wpdb->get_results($sqlPostsTotal);
                    if (is_array($schedResult) && !empty($schedResult)) {
                        $this->postCalendarSchedDates = array();
                        $postIds = array();
                        foreach ($schedResult as $k => $v) {
                            if (!in_array($v->ID, $postIds)) {
                                $postIds[] = $v->ID;
                            }
                            if (!in_array($v->sched, $this->postCalendarSchedDates)) {
                                $this->postCalendarSchedDates[] = $v->sched;
                            }
                        }
                        $this->postTotal = count($postIds);
                    }
                }
            }
        }
    }

    public function getItemHtml() {
        $this->getData();
        $postStatus = array('publish' => __('published', 'blog2social'), 'pending' => __('draft', 'blog2social'), 'future' => __('scheduled', 'blog2social'));

        if (empty($this->postData)) {
            $text = __('You have no posts published or scheduled.', 'blog2social');
            return '<li class="list-group-item"><div class="media"><div class="media-body"></div>' . $text . '</div></li>';
        }

        foreach ($this->postData as $var) {
            $postType = 'post';
            if (strpos(strtolower($var->post_type), 'event') !== false) {
                $postType = 'event';
            }
            if (strpos(strtolower($var->post_type), 'job') !== false) {
                $postType = 'job';
            }

            //Plugin: qTranslate
            $postTitle = B2S_Util::getTitleByLanguage($var->post_title, $this->userLang);
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                $postTitle = (mb_strlen(trim($postTitle), 'UTF-8') > 80 ? mb_substr($postTitle, 0, 77, 'UTF-8') . '...' : $postTitle);
            }

            if ($this->type == 'all') {
                $userInfo = get_user_meta($var->post_author);
                $lastPublish = $this->getLastPublish($var->ID);
                $lastPublish = ($lastPublish != false) ? ' | ' . __('last shared on social media', 'blog2social') . ' ' . B2S_Util::getCustomDateFormat($lastPublish, substr(B2S_LANGUAGE, 0, 2)) : '';

                $this->postItem .= '<li class="list-group-item">
                                <div class="media">
                                    <img class="post-img-10 pull-left hidden-xs" src="' . plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE) . '" alt="posttype">
                                        <div class="media-body">
                                                <strong><a target="_blank" href="' . get_permalink($var->ID) . '">' . $postTitle . '</a></strong>
                                            <span class="pull-right b2s-publish-btn">
                                                <a class="btn btn-success btn-sm publishPostBtn" href="admin.php?page=blog2social-ship&postId=' . $var->ID . '">' . __('Share on Social Media', 'blog2social') . '</a>
                                            </span>
                                            <p class="info hidden-xs">#' . $var->ID . ' | ' . __('Author', 'blog2social') . ' <a href="' . get_author_posts_url($var->post_author) . '">' . (isset($userInfo['nickname'][0]) ? $userInfo['nickname'][0] : '-') . '</a> | ' . $postStatus[trim(strtolower($var->post_status))] . ' ' . __('on blog', 'blog2social') . ': ' . B2S_Util::getCustomDateFormat($var->post_date, substr(B2S_LANGUAGE, 0, 2)) . $lastPublish . '</p>
                                        </div>
                                    </div>
                                </li>';
            }

            if ($this->type == 'publish') {
                $userInfo = get_user_meta($var->blog_user_id);
                $countPublish = $this->getPostCount($var->ID);
                $lastPublish = $this->getLastPost($var->ID);
                $this->postItem .= '<li class="list-group-item">
                                        <div class="media">
                                            <img class="post-img-10 pull-left hidden-xs" src="' . plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE) . '" alt="posttype">
                                                <div class="media-body">
                                                    <div class="pull-left media-nav">
                                                            <strong><a target="_blank" href="' . get_permalink($var->ID) . '">' . $postTitle . '</a></strong>
                                                        <span class="pull-right">
                                                        <a class="btn btn-success hidden-xs btn-sm" href="admin.php?page=blog2social-ship&postId=' . $var->ID . '">' . __('Re-share this post', 'blog2social') . '</a>
                                                            <button type="button" class="btn btn-primary btn-sm b2sDetailsPublishPostBtn" data-search-date="' . $this->searchShowByDate . '" data-post-id="' . $var->ID . '"><i class="glyphicon glyphicon-chevron-down"></i> ' . __('Details', 'blog2social') . '</button>
                                                        </span>
                                                        <p class="info hidden-xs"><a class="b2sDetailsPublishPostTriggerLink" href="#"><span class="b2s-publish-count" data-post-id="' . $var->ID . '">' . $countPublish . '</span> ' . __('shared social media posts', 'blog2social') . '</a> | ' . __('latest share by', 'blog2social') . ' <a href="' . get_author_posts_url($var->blog_user_id) . '">' . (isset($userInfo['nickname'][0]) ? $userInfo['nickname'][0] : '-') . '</a> ' . B2S_Util::getCustomDateFormat($lastPublish, substr(B2S_LANGUAGE, 0, 2)) . '</p>
                                                    </div>
                                                    <div class="pull-left">
                                                        <div class="b2s-post-publish-area" data-post-id="' . $var->ID . '"></div>
                                                    </div>
                                                </div>
                                         </div>
                                    </li>';
            }

            if ($this->type == 'sched') {
                $userInfo = get_user_meta($var->blog_user_id);
                $schedPublish = $this->getPostCount($var->ID);
                $nextSched = $this->getLastPost($var->ID);
                $this->postItem .= '<li class="list-group-item">
                                        <div class="media">
                                             <img class="post-img-10 pull-left hidden-xs" src="' . plugins_url('/assets/images/b2s/' . $postType . '-icon.png', B2S_PLUGIN_FILE) . '" alt="posttype">
                                                <div class="media-body">
                                                    <div class="pull-left media-head">
                                                            <strong><a target="_blank" href="' . get_permalink($var->ID) . '">' . $postTitle . '</a></strong>
                                                        <span class="pull-right">
                                                            <button type="button" class="btn btn-primary btn-sm b2sDetailsSchedPostBtn" data-search-date="' . $this->searchShowByDate . '" data-post-id="' . $var->ID . '"><i class="glyphicon glyphicon-chevron-down"></i> ' . __('Details', 'blog2social') . '</button>
                                                        </span>
                                                        <p class="info hidden-xs"><a class="b2sDetailsSchedPostTriggerLink" href="#"><span class="b2s-sched-count" data-post-id="' . $var->ID . '">' . $schedPublish . '</span> ' . __('scheduled social media posts', 'blog2social') . '</a> | ' . __('next share by', 'blog2social') . ' <a href="' . get_author_posts_url($var->blog_user_id) . '">' . (isset($userInfo['nickname'][0]) ? $userInfo['nickname'][0] : '-') . '</a> ' . B2S_Util::getCustomDateFormat($nextSched, substr(B2S_LANGUAGE, 0, 2)) . '</p>
                                                    </div>
                                                    <div class="pull-left">
                                                        <div class="b2s-post-sched-area" data-post-id="' . $var->ID . '"></div>
                                                 </div>
                                             </div>
                                         </div>
                                    </li>';
            }
        }

        return html_entity_decode($this->postItem, ENT_COMPAT, 'UTF-8');
    }

    private function getPostCount($post_id = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
            $addSearchShowByDate = (!empty($this->searchShowByDate)) ? (($this->type == 'publish') ? " AND DATE_FORMAT(posts.publish_date,'%Y-%m-%d') = '" . $this->searchShowByDate . "' " : " AND DATE_FORMAT(posts.sched_date,'%Y-%m-%d') = '" . $this->searchShowByDate . "' ") : '';
            $addLeftJoin = ((int) $this->searchUserAuthId != 0) ? ' LEFT JOIN b2s_posts_network_details details ON details.`id` = posts.`network_details_id` ' : '';
            $addLeftJoinWhere = ((int) $this->searchUserAuthId != 0) ? ' details.`network_auth_id` =' . $this->searchUserAuthId . ' AND ' : '';
            $where = ($this->type == 'publish') ? " (posts.`sched_date`= '0000-00-00 00:00:00' OR posts.`sched_type` = 3) " : " posts.`sched_type` != 3 AND posts.`publish_date` = '0000-00-00 00:00:00'";
            $sqlPostsTotal = "SELECT COUNT(posts.`post_id`) FROM `b2s_posts` posts $addLeftJoin WHERE $addLeftJoinWhere $where $addNotAdmin $addSearchShowByDate AND posts.`hide` = 0 AND posts.`post_id` = " . $post_id;
            return $wpdb->get_var($sqlPostsTotal);
        }
        return 0;
    }

    private function getLastPost($post_id = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND `blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
            $order = ($this->type == 'publish') ? " `publish_date` DESC" : " `sched_date` ASC ";
            $where = ($this->type == 'publish') ? " (`sched_date`= '0000-00-00 00:00:00' OR `sched_type` = 3) " : " `sched_type` != 3 AND `publish_date` = '0000-00-00 00:00:00'";
            $fields = $this->type == 'publish' ? "publish_date" : "sched_date";
            $sqlLast = "SELECT $fields FROM `b2s_posts` WHERE $where $addNotAdmin AND `hide` = 0 AND `post_id` = " . $post_id . " ORDER BY $order LIMIT 1";
            return $wpdb->get_var($sqlLast);
        }
        return date('Y-m-d H:i:s');
    }

    private function getLastPublish($post_id = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND `blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
            $order = "`publish_date` DESC";
            $where = "(`sched_date`= '0000-00-00 00:00:00' OR `sched_type` = 3) ";
            $fields = "publish_date";
            $sqlLast = "SELECT $fields FROM `b2s_posts` WHERE $where $addNotAdmin AND `hide` = 0 AND `post_id` = " . $post_id . " ORDER BY $order LIMIT 1";
            $result = $wpdb->get_results($sqlLast);
            if (!empty($result) && isset($result[0]->publish_date)) {
                return $result[0]->publish_date;
            }
        }
        return false;
    }

    public function getCalendarSchedDate() {
        if ((int) $this->postTotal > 0) {
            if (is_array($this->postCalendarSchedDates) && !empty($this->postCalendarSchedDates)) {
                return $this->postCalendarSchedDates;
            }
        }
        return 0;
    }

    public function getPaginationHtml() {
        if ((int) $this->postTotal > 0) {
            $last = ceil($this->postTotal / B2S_PLUGIN_POSTPERPAGE);
            $start = (($this->currentPage - $this->postPaginationLinks ) > 0 ) ? $this->currentPage - $this->postPaginationLinks : 1;
            $end = (( $this->currentPage + $this->postPaginationLinks ) < $last ) ? $this->currentPage + $this->postPaginationLinks : $last;
            $this->postPagination = '<ul class="pagination">';
            $class = ( $this->currentPage == 1 ) ? "disabled" : "";
            $linkpre = ( $this->currentPage == 1 ) ? $this->currentPage : ( $this->currentPage - 1);
            $this->postPagination .= '<li class="' . $class . '"><a class="b2s-pagination-btn" data-page="' . $linkpre . '" href="#">&laquo;</a></li>';
            if ($start > 1) {
                $this->postPagination .= '<li><a class="b2s-pagination-btn" data-page="1" href="#">1</a></li>';
                $this->postPagination .= '<li class="disabled"><span>...</span></li>';
            }
            for ($i = $start; $i <= $end; $i++) {
                $class = ( $this->currentPage == $i ) ? "active" : "";
                $this->postPagination .= '<li class="' . $class . '"><a class="b2s-pagination-btn" data-page="' . $i . '" href="#">' . $i . '</a></li>';
            }
            if ($end < $last) {
                $this->postPagination .= '<li class="disabled"><span>...</span></li>';
                $this->postPagination .= '<li><a class="b2s-pagination-btn" data-page="' . $last . '" href="#">' . $last . '</a></li>';
            }
            $class = ( $this->currentPage == $last ) ? "disabled" : "";
            $linkpast = ( $this->currentPage == $last ) ? $this->currentPage : ( $this->currentPage + 1 );
            $this->postPagination .= '<li class="' . $class . '"><a class="b2s-pagination-btn" data-page="' . $linkpast . '" href="#">&raquo;</a></li>';
            $this->postPagination .= '</ul>';
        }
        return $this->postPagination;
    }

    public function getPublishPostDataHtml($post_id = 0, $showByDate = '') {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdminPosts = (!B2S_PLUGIN_ADMIN) ? (' AND blog_user_id =' . B2S_PLUGIN_BLOG_USER_ID) : '';
            $addSearchShowByDate = (!empty($showByDate)) ? " AND DATE_FORMAT(`b2s_posts`.`publish_date`,'%%Y-%%m-%%d') = '" . $showByDate . "' " : '';
            $sqlData = $wpdb->prepare("SELECT `b2s_posts`.`id`,`blog_user_id`, `sched_date`,`publish_date`,`publish_link`,`sched_type`,`publish_error_code`,`b2s_posts_network_details`.`network_id`,`b2s_posts_network_details`.`network_type`, `b2s_posts_network_details`.`network_auth_id`, `b2s_posts_network_details`.`network_display_name` FROM `b2s_posts` LEFT JOIN `b2s_posts_network_details` ON `b2s_posts`.`network_details_id` = `b2s_posts_network_details`.`id` WHERE `b2s_posts`.`hide` = 0  AND (`b2s_posts`.`sched_date` = '0000-00-00 00:00:00' OR `b2s_posts`.`sched_type` = 3) $addNotAdminPosts $addSearchShowByDate AND `b2s_posts`.`post_id` = %d ORDER BY `b2s_posts`.`publish_date` DESC", $post_id);
            $result = $wpdb->get_results($sqlData);
            if (!empty($result) && is_array($result)) {
                $networkType = unserialize(B2S_PLUGIN_NETWORK_TYPE);
                $networkName = unserialize(B2S_PLUGIN_NETWORK);
                $networkErrorCode = unserialize(B2S_PLUGIN_NETWORK_ERROR);
                $content = '<div class="row"><div class="col-md-12"><ul class="list-group">';
                $content .='<li class="list-group-item"><label class="checkbox-inline checkbox-all-label"><input class="checkbox-all" data-blog-post-id="' . $post_id . '" name="selected-checkbox-all" value="" type="checkbox"> ' . __('select all', 'blog2social') . '</label></li>';
                foreach ($result as $var) {
                    $autoPosting = (isset($var->sched_type) && (int) $var->sched_type == 3) ? ' - <strong>' . __('Auto-Posting', 'blog2social') . '</strong>' : '';
                    $publishLink = (!empty($var->publish_link)) ? '<a target="_blank" href="' . $var->publish_link . '">' . __('show', 'blog2social') . '</a> | ' : '';
                    $error = (!empty($var->publish_error_code)) ? '<span class="network-text-info text-danger hidden-xs"> <i class="glyphicon glyphicon-remove-circle glyphicon-danger"></i> ' . $networkErrorCode[trim($var->publish_error_code)] . '</span>' : '';
                    $publishDate = ($var->sched_date == "0000-00-00 00:00:00") ? B2S_Util::getCustomDateFormat($var->publish_date, substr(B2S_LANGUAGE, 0, 2)) : '';
                    $publishText = (empty($publishDate)) ? __('sharing in progress by', 'blog2social') : __('shared by', 'blog2social');
                    $userInfo = get_user_meta($var->blog_user_id);
                    $content .= ' <li class="list-group-item b2s-post-publish-area-li" data-post-id="' . $var->id . '">
                                    <div class="media">';

                    if (!empty($publishDate)) {
                        $content .='<input class="checkboxes pull-left checkbox-item" data-blog-post-id="' . $post_id . '" name="selected-checkbox-item" value="' . $var->id . '" type="checkbox">';
                    } else {
                        $content .='<div class="checbox-item-empty"></div>';
                    }

                    if (!empty($var->publish_link)) {
                        $content .= '<a class="pull-left" target="_blank" href="' . $var->publish_link . '"><img class="pull-left hidden-xs" src="' . plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE) . '" alt="posttype"></a>';
                    } else {
                        $content .= '<img class="pull-left hidden-xs" src="' . plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE) . '" alt="posttype">';
                    }
                    $content .= '<div class="media-body">
                                            <strong>' . $networkName[$var->network_id] . '</strong> ' . $error . '
                                            <p class="info">' . $networkType[$var->network_type] . (!empty($var->network_display_name) ? (': ' . $var->network_display_name) : '' ) . ' | ' . $publishText . ' <a href="' . get_author_posts_url($var->blog_user_id) . '">' . (isset($userInfo['nickname'][0]) ? $userInfo['nickname'][0] : '-') . '</a> ' . $publishDate . $autoPosting . '</p>
                                            <p class="info">' . $publishLink;

                    if (!empty($var->publish_link) || !empty($var->publish_error_code)) {
                        $content .= (B2S_PLUGIN_USER_VERSION > 0) ? '<a href="#" class="b2s-post-publish-area-drop-btn" data-post-id="' . $var->id . '">' : '<a href="#" data-toggle="modal" data-title="' . __('You want to delete a publish post entry?', 'blog2social') . '" data-target="#b2sPreFeatureModal" >';
                        $content .= __('delete from reporting', 'blog2social') . '</a> ';
                    }

                    if (!empty($error)) {
                        $content .= '| <a href="admin.php?page=blog2social-ship&postId=' . $post_id . '&network_auth_id=' . $var->network_auth_id . '">' . __('re-share', 'blog2social') . '</a>';
                    }

                    $content . '</p>
                                        </div>
                                    </div>
                                </li>';
                }
                $content .='<li class="list-group-item"><label class="checkbox-inline checkbox-all-label-btn"><span class="glyphicon glyphicon glyphicon-trash "></span> ';
                $content .= B2S_PLUGIN_USER_VERSION > 0 ? '<a class="checkbox-post-publish-all-btn" data-blog-post-id="' . $post_id . '" href="#">' : '<a href="#" data-toggle="modal" data-title="' . __('You want to delete a publish post entry?', 'blog2social') . '" data-target="#b2sPreFeatureModal" >';
                $content .= __('delete from reporting', 'blog2social') . '</a></label></li>';
                $content .= '</ul></div></div>';
                return $content;
            }
        }
        return false;
    }

    public function getSchedPostDataHtml($post_id = 0, $showByDate = '', $userAuthId = 0) {
        if ($post_id > 0) {
            global $wpdb;
            $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND `b2s_posts`.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
            $addSearchShowByDate = (!empty($showByDate)) ? " AND DATE_FORMAT(`b2s_posts`.`sched_date`,'%%Y-%%m-%%d') = '" . $showByDate . "' " : '';
            $addSearchUserAuthId = ($userAuthId != 0) ? " AND `b2s_posts_network_details`.`network_auth_id` =" . $userAuthId . " " : '';
            $sqlData = $wpdb->prepare("SELECT `b2s_posts`.`id`,`blog_user_id`,`v2_id`, `sched_date`,`b2s_posts_network_details`.`network_id`,`b2s_posts_network_details`.`network_type`,`b2s_posts_network_details`.`network_display_name` FROM `b2s_posts` LEFT JOIN `b2s_posts_network_details` ON `b2s_posts`.`network_details_id` = `b2s_posts_network_details`.`id` WHERE `b2s_posts`.`hide` = 0 AND `b2s_posts`.`sched_type` != 3  AND `b2s_posts`.`publish_date` = '0000-00-00 00:00:00' $addNotAdminPosts $addSearchShowByDate $addSearchUserAuthId AND `b2s_posts`.`post_id` = %d ORDER BY `b2s_posts`.`sched_date` ASC ", $post_id);
            $result = $wpdb->get_results($sqlData);
            if (!empty($result) && is_array($result)) {
                $networkType = unserialize(B2S_PLUGIN_NETWORK_TYPE);
                $networkName = unserialize(B2S_PLUGIN_NETWORK);
                $content = '<div class="row"><div class="col-md-12"><ul class="list-group">';
                $content .='<li class="list-group-item"><label class="checkbox-inline checkbox-all-label"><input class="checkbox-all" data-blog-post-id="' . $post_id . '" name="selected-checkbox-all" value="" type="checkbox"> ' . __('select all', 'blog2social') . '</label></li>';
                $blogPostDate = strtotime(get_the_date('Y-m-d H:i:s', $post_id)) . '000';
                foreach ($result as $var) {
                    $userInfo = get_user_meta($var->blog_user_id);
                    $content .= '<li class="list-group-item b2s-post-sched-area-li" data-post-id="' . $var->id . '">
                                    <div class="media">';
                    $content .='<input class="checkboxes pull-left checkbox-item" data-blog-post-id="' . $post_id . '" name="selected-checkbox-item" value="' . $var->id . '" type="checkbox">';

                    $content .='<img class="pull-left hidden-xs" src="' . plugins_url('/assets/images/portale/' . $var->network_id . '_flat.png', B2S_PLUGIN_FILE) . '" alt="posttype">
                                            <div class="media-body">
                                                <strong>' . $networkName[$var->network_id] . '</strong>
                                                <p class="info">' . $networkType[$var->network_type] . (!empty($var->network_display_name) ? (': ' . $var->network_display_name) : '' ) . ' | ' . __('scheduled by', 'blog2social') . ' <a href="' . get_author_posts_url($var->blog_user_id) . '">' . (isset($userInfo['nickname'][0]) ? $userInfo['nickname'][0] : '-') . '</a> <span class="b2s-post-sched-area-sched-time" data-post-id="' . $var->id . '">' . B2S_Util::getCustomDateFormat($var->sched_date, substr(B2S_LANGUAGE, 0, 2)) . '</span></p>
                                                <p class="info">';

                    $content .= '<a href="#" class="b2s-post-sched-area-drop-btn" data-post-id="' . $var->id . '"> ' . __('delete scheduling', 'blog2social') . '</a> ';

                    if ((int) $var->v2_id == 0) {
                        $content .= '|';
                        $content .= (B2S_PLUGIN_USER_VERSION > 0) ? ' <a href="#" class="b2s-post-sched-area-edittime-btn" data-post-id="' . $var->id . '" data-blog-sched-date="' . $blogPostDate . '" data-b2s-sched-date="' . strtotime($var->sched_date) . '000">' : ' <a href="#" data-toggle="modal" data-title="' . __('You want to change the time for your scheduled post?', 'blog2social') . '" data-target="#b2sPreFeatureModal">';
                        $content .= __('change time', 'blog2social') . '</a>';
                    }
                    $content .= '</p>
                                            </div>
                                    </div>
                                </li>';
                }
                $content .= '<li class="list-group-item"><label class="checkbox-inline checkbox-all-label-btn"><span class="glyphicon glyphicon glyphicon-trash "></span> ';
                $content .= '<a class="checkbox-post-sched-all-btn" data-blog-post-id="' . $post_id . '" href="#"> ' . __('delete scheduling', 'blog2social');
                $content .= '</a></label></li>';
                $content .= '</ul></div></div>';
                return $content;
            }
        }
        return false;
    }

}
