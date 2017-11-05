<?php

class B2S_Settings_Save {

    private $data;

    public function __construct($data = array()) {
        $this->data = $data;
    }

    public function saveSchedTime() {

        if (!empty($this->data) && is_array($this->data)) {
            global $wpdb;
            foreach ($this->data as $k => $times) {
                foreach ($times as $t => $v) {
                    $settingsId = $wpdb->get_var($wpdb->prepare("SELECT id FROM b2s_post_sched_settings WHERE blog_user_id= %d AND network_id=%d AND network_type=%d", B2S_PLUGIN_BLOG_USER_ID, (int) $k, (int) $t));
                    $dateTime = date('Y-m-d') . ' ' . $v;
                    $schedTime = date('H:i', strtotime($dateTime));
                    if ((int) $settingsId > 0) {
                        $wpdb->update('b2s_post_sched_settings', array('sched_time' => $schedTime), array('id' => $settingsId), array('%s'), array('%d'));
                    } else {
                        $wpdb->insert('b2s_post_sched_settings', array('blog_user_id' => B2S_PLUGIN_BLOG_USER_ID, 'network_id' => (int) $k, 'network_type' => (int) $t, 'sched_time' => $schedTime), array('%d', '%d','%d', '%s'));
                    }
                }
            }
            return true;
        }
        return false;
    }

}
