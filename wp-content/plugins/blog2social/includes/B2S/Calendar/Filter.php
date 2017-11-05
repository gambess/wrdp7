<?php
require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/Item.php');
class B2S_Calendar_Filter{

    private $items = [];

    /**
     * @param $sql
     * @return B2S_Calendar_Filter
     */
    public static function getBySql($sql)
    {
        global $wpdb;

        $res = new B2S_Calendar_Filter();

        $items = $wpdb->get_results($sql);
        foreach($items as $item)
        {
            $res->items[] = new B2S_Calendar_Item($item);
        }

        return $res;
    }

    /**
     * @return B2S_Calendar_Filter|null
     */
    public static function getAll()
    {
        global $wpdb;
        $res = null;
        
        $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND b2s_posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';
   
        $sql = "SELECT b2s_posts.sched_date, "
                     ."b2s_posts.blog_user_id, "
                     ."b2s_posts.id as b2s_id, "
                     ."b2s_posts.user_timezone, "
                     ."b2s_posts.post_id, "
                     ."b2s_posts_network_details.network_id, "
                     ."b2s_posts_network_details.network_type, "
                     ."b2s_posts_network_details.network_display_name, "
                     ."b2s_posts_network_details.network_auth_id, "
                     ."post.post_title, "
                     ."b2s_posts_sched_details.sched_data, "
                     ."b2s_posts_sched_details.image_url, "
                     ."b2s_posts.sched_details_id "
              ."FROM b2s_posts "
              ."INNER JOIN b2s_posts_network_details ON b2s_posts.network_details_id = b2s_posts_network_details.id "
              ."INNER JOIN b2s_posts_sched_details ON b2s_posts.sched_details_id = b2s_posts_sched_details.id "
              ."INNER JOIN ".$wpdb->posts." post ON post.ID = b2s_posts.post_id "
              ."WHERE b2s_posts.publish_link = '' "
                 ."AND b2s_posts.hide = 0 ".$addNotAdminPosts." ORDER BY sched_date";
        
        
        $res = self::getBySql($sql);
    
        return $res;
    }

    /**
     * @return B2S_Calendar_Filter|null
     */
    public static function getByTimespam($start, $end)
    {
        global $wpdb;
        $res = null;

        $addNotAdminPosts = (B2S_PLUGIN_ADMIN == false) ? $wpdb->prepare(' AND b2s_posts.`blog_user_id` = %d', B2S_PLUGIN_BLOG_USER_ID) : '';

        $sql = "SELECT b2s_posts.sched_date, "
            ."b2s_posts.blog_user_id, "
            ."b2s_posts.id as b2s_id, "
            ."b2s_posts.user_timezone, "
            ."b2s_posts.post_id, "
            ."b2s_posts_network_details.network_id, "
            ."b2s_posts_network_details.network_type, "
            ."b2s_posts_network_details.network_display_name, "
            ."b2s_posts_network_details.network_auth_id, "
            ."post.post_title, "
            ."b2s_posts_sched_details.sched_data, "
            ."b2s_posts_sched_details.image_url, "
            ."b2s_posts.sched_details_id "
            ."FROM b2s_posts "
            ."INNER JOIN b2s_posts_network_details ON b2s_posts.network_details_id = b2s_posts_network_details.id "
            ."INNER JOIN b2s_posts_sched_details ON b2s_posts.sched_details_id = b2s_posts_sched_details.id "
            ."INNER JOIN ".$wpdb->posts." post ON post.ID = b2s_posts.post_id "
            ."WHERE b2s_posts.publish_link = '' "
                ."&& b2s_posts.sched_date BETWEEN '".date('Y-m-d H:i:s',strtotime($start))."' AND '".date('Y-m-d H:i:s',strtotime($end))."' "
            ."AND b2s_posts.hide = 0 ".$addNotAdminPosts." ORDER BY sched_date";


        $res = self::getBySql($sql);

        return $res;
    }

    /**
     * @param $id
     * @return B2S_Calendar_Item|null
     */
    public static function getById($id)
    {
        global $wpdb;

        if(!is_numeric($id))
        {
            return null;
        }

        $sql = "SELECT b2s_posts.sched_date, "
                     ."b2s_posts.blog_user_id, "
                     ."b2s_posts.id as b2s_id, "
                     ."b2s_posts.user_timezone, "
                     ."b2s_posts.post_id, "
                     ."b2s_posts_network_details.network_id, "
                     ."b2s_posts_network_details.network_type, "
                     ."b2s_posts_network_details.network_display_name, "
                     ."b2s_posts_network_details.network_auth_id, "
                     ."post.post_title, "
                     ."b2s_posts_sched_details.sched_data, "
                     ."b2s_posts_sched_details.image_url, "
                     ."b2s_posts.sched_details_id "
                ."FROM b2s_posts "
                ."INNER JOIN b2s_posts_network_details ON b2s_posts.network_details_id = b2s_posts_network_details.id "
                ."INNER JOIN b2s_posts_sched_details ON b2s_posts.sched_details_id = b2s_posts_sched_details.id "
                ."INNER JOIN ".$wpdb->posts." post ON post.ID = b2s_posts.post_id "
                ."WHERE b2s_posts.id = %d "
                   ."&& b2s_posts.publish_link = '' "
                   ."&& b2s_posts.hide = 0 "
                ."ORDER BY sched_date";

        $sql = $wpdb->prepare($sql, array($id));

        $rows = self::getBySql($sql)->getItems();

        if(count($rows) > 0)
        {
            return $rows[0];
        }

        return null;
    }

    /**
     * @param $id
     * @return B2S_Calendar_Filter|null
     */
    public static function getByPostId($id)
    {
        global $wpdb;

        if(!is_numeric($id))
        {
            return null;
        }

        $sql = "SELECT b2s_posts.sched_date, "
                     ."b2s_posts.blog_user_id, "
                     ."b2s_posts.id as b2s_id, "
                     ."b2s_posts.user_timezone, "
                     ."b2s_posts.post_id, "
                     ."b2s_posts_network_details.network_id, "
                     ."b2s_posts_network_details.network_type, "
                     ."b2s_posts_network_details.network_display_name, "
                     ."b2s_posts_network_details.network_auth_id, "
                     ."post.post_title, "
                     ."b2s_posts_sched_details.sched_data, "
                     ."b2s_posts_sched_details.image_url, "
                     ."b2s_posts.sched_details_id "
            ."FROM b2s_posts "
            ."INNER JOIN b2s_posts_network_details ON b2s_posts.network_details_id = b2s_posts_network_details.id "
            ."INNER JOIN b2s_posts_sched_details ON b2s_posts.sched_details_id = b2s_posts_sched_details.id "
            ."INNER JOIN ".$wpdb->posts." post ON post.ID = b2s_posts.post_id "
            ."WHERE b2s_posts.post_id = %d "
               ."&& b2s_posts.hide = 0 "
            ."ORDER BY sched_date";

        $sql = $wpdb->prepare($sql, array($id));

        return self::getBySql($sql);
    }

    /**
     * @return B2S_Calendar_Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function asCalendarArray() {
        $res = [];

        foreach($this->getItems() as $item)
        {
            $res[] = $item->asCalendarArray();
        }

        return $res;
    }
}
