<?php

class PRG_Api_Post {

    public static function post($url = '', $post = array()) {
        if (empty($url) || empty($post)) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_USERAGENT, "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)");
        $result = curl_exec($ch);
        return $result;
    }

}
