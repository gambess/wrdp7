<?php

class B2S_User_Info {

    public static function getVersion() {
        
    }

    public static function getStatsTodayHtml() {
        $content = '<li>
                       <a href="admin.php?page=blog2social-publish&b2sShowByDate=' . date('Y-m-d', current_time('timestamp')) . '">' . __('Number of shared posts', 'blog2social') . '<span class="label label-success">' . ((int) self::getStatsTodayCount()) . '</span></a>
                    </li>
                    <li>
                       <a href="admin.php?page=blog2social-sched&b2sShowByDate=' . date('Y-m-d', current_time('timestamp')) . '">' . __('Number of scheduled posts', 'blog2social') . '<span class="label label-primary">' . ((int) self::getStatsTodayCount('sched')) . '</span></a>
                    </li>';

        return $content;
    }

    private static function getStatsTodayCount($type = 'publish') {
        global $wpdb;
        $addNotAdmin = (B2S_PLUGIN_ADMIN == false) ? ' AND `blog_user_id` = ' . B2S_PLUGIN_BLOG_USER_ID : '';
        $where = ($type == 'publish') ? " `sched_date` = '0000-00-00 00:00:00' AND DATE_FORMAT(publish_date,'%Y-%m-%d') = '" . date('Y-m-d', current_time('timestamp')) . "' " : " `publish_date` = '0000-00-00 00:00:00' AND DATE_FORMAT(sched_date,'%Y-%m-%d') = '" . date('Y-m-d', current_time('timestamp')) . "' ";
        $sqlPostsTotal = "SELECT COUNT(`id`) FROM `b2s_posts` WHERE " . $where . $addNotAdmin . ' AND hide="0"';
        return $wpdb->get_var($sqlPostsTotal);
    }

}
