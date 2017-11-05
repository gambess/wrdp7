<?php

class PRG_Api_Get {

    public static function get($url = '', $header = array("Content-Type:application/x-www-form-urlencoded")) {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Blog2Social/" . B2S_PLUGIN_VERSION . " (Wordpress/Plugin)");
        $result = curl_exec($ch);
        return $result;
    }

}
