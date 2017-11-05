<?php

class B2S_Post_Tools {

    public static function updateUserSchedTimePost($post_id, $date, $time, $timezone) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT id FROM b2s_posts WHERE id =%d AND blog_user_id = %d AND publish_date = %s", (int) $post_id, (int) get_current_user_id(), "0000-00-00 00:00:00");
        $id = $wpdb->get_col($sql);
        if (isset($id[0]) && $id[0] == $post_id) {
            $insert_time = strtotime($date . ' ' . $time);
            if ($insert_time < time()) {
                $insert_time = time();
            }
            $insert_datetime_utc = B2S_Util::getUTCForDate(date('Y-m-d H:i:s', $insert_time), $timezone * (-1));
            $wpdb->update('b2s_posts', array('hook_action' => 2, 'sched_date' => date('Y-m-d H:i:s', $insert_time), 'sched_date_utc' => $insert_datetime_utc), array('id' => $post_id));
            return array('result' => true, 'postId' => $post_id, 'time' => B2S_Util::getCustomDateFormat(date('Y-m-d H:i:s', $insert_time), substr(B2S_LANGUAGE, 0, 2)));
        }
        return array('result' => false);
    }

    public static function deleteUserSchedPost($postIds = array()) {
        global $wpdb;
        $resultPostIds = array();
        $blogPostId = 0;
        $count = 0;
        foreach ($postIds as $v) {
            $sql = $wpdb->prepare("SELECT id,post_id FROM b2s_posts WHERE id =%d AND publish_date = %s", (int) $v, "0000-00-00 00:00:00");
            $row = $wpdb->get_row($sql);
            if (isset($row->id) && (int) $row->id == $v) {
                $wpdb->update('b2s_posts', array('hook_action' => 3, 'hide' => 1), array('id' => $v));
                $resultPostIds[] = $v;
                $blogPostId = $row->post_id;
                $count++;
            }
        }
        if (!empty($resultPostIds) && is_array($resultPostIds)) {
            return array('result' => true, 'postId' => $resultPostIds, 'postCount' => $count, 'blogPostId' => $blogPostId);
        }

        return array('result' => false);
    }

    public static function deleteUserPublishPost($postIds = array()) {
        global $wpdb;
        $resultPostIds = array();
        $blogPostId = 0;
        $count = 0;
        foreach ($postIds as $v) {
            $sql = $wpdb->prepare("SELECT id,v2_id,post_id FROM b2s_posts WHERE id =%d", (int) $v);
            $row = $wpdb->get_row($sql);
            if (isset($row->id) && (int) $row->id == $v) {
                $hook_action = (isset($row->v2_id) && (int) $row->v2_id > 0) ? 0 : 4; //oldItems
                $wpdb->update('b2s_posts', array('hook_action' => $hook_action, 'hide' => 1), array('id' => $v));
                $resultPostIds[] = $v;
                $blogPostId = $row->post_id;
                $count++;
            }
        }
        if (!empty($resultPostIds) && is_array($resultPostIds)) {
            return array('result' => true, 'postId' => $resultPostIds, 'postCount' => $count, 'blogPostId' => $blogPostId);
        }
        return array('result' => false);
    }

}
