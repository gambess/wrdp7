<?php

class B2S_Util {

    public static function getUTCForDate($date, $userTimezone) {
        $utcTime = strtotime($date) + ($userTimezone * 3600);
        return date('Y-m-d H:i:s', $utcTime);
    }

    public static function getLocalDate($userTimezone, $lang = 'en') {
        $ident = ($lang == 'de') ? 'd.m.Y H:i' : 'Y/m/d g:i a';
        $localTime = strtotime(gmdate('Y-m-d H:i:s')) + ($userTimezone * 3600);
        return date($ident, $localTime);
    }

    public static function getVersion($version = 000) {
        return substr(chunk_split($version, 1, '.'), 0, -1);
    }

    public static function getCustomDateFormat($dateTime = '0000-00-00 00:00:00', $lang = 'en') {
        if ($lang == 'de') {
            $ident = 'd.m.Y H:i';
            return date($ident, strtotime($dateTime)) . ' Uhr';
        } else {
            $ident = 'Y/m/d g:i a';
            return date($ident, strtotime($dateTime));
        }
    }

    public static function getTrialRemainingDays($trialEndDate = '', $timeZone = 'Europe/Berlin') {
        if (!empty($trialEndDate)) {
            $trailDateUtc = new DateTime($trialEndDate);
            $timeZone = empty($timeZone) ? 'Europe/Berlin' : $timeZone;
            $trailDateUtc->setTimezone(new DateTimeZone($timeZone));
            $isTrial = $trailDateUtc->format('Y-m-d H:i:s');

            $differTime = strtotime($isTrial) - time();
            if ((int) $differTime >= 0) {
                return (int) ($differTime / 86400);
            }
            return 0;
        }
        return false;
    }

    public static function getMetaTags($postId = 0, $postUrl = '', $network = 1) {

        //GETSTOREEDDATA
        if ((int) $postId != 0) {
            $metaData = get_option('B2S_PLUGIN_POST_META_TAGES_' . $postId);
            if ($metaData !== false && is_array($metaData)) {
                return $metaData;
            }
        }

        //GETDATA
        $getTags = array('title', 'description', 'image');
        $param = array();
        libxml_use_internal_errors(true); // Yeah if you are so worried about using @ with warnings
        $html = self::b2sFileGetContents($postUrl);
        if (!empty($html) && $html !== false) {
            //if ($network == 1) { //FB
            //Serach frist OG Parameter
            $temp = self::b2sGetAllTags($html, 'og');
            foreach ($getTags as $k => $v) {
                if (isset($temp[$v]) && !empty($temp[$v])) {
                    $param[$v] = $temp[$v];
                } else {
                    if ($v == 'title') {
                        if (function_exists('mb_convert_encoding')) {
                            $param[$v] = htmlspecialchars(self::b2sGetMetaTitle($html));
                        } else {
                            $param[$v] = self::b2sGetMetaTitle($html);
                        }
                    }
                    if ($v == 'description') {
                        if (function_exists('mb_convert_encoding')) {
                            $param[$v] = htmlspecialchars(self::b2sGetMetaDescription($html));
                        } else {
                            $param[$v] = self::b2sGetMetaDescription($html);
                        }
                    }
                }
                //}
            }
            //STOREDATA
            if ((int) $postId != 0) {
                update_option('B2S_PLUGIN_POST_META_TAGES_' . $postId, $param);
            }
            return $param;
        }
        return false;
    }

    private static function b2sFileGetContents($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = self::b2sCurlExecFollow($ch);
        curl_close($ch);
        return $data;
    }

    private static function b2sCurlExecFollow($ch, $maxredirect = 5) {
        $b2sSafeMode = ini_get('safe_mode');
        $b2sOpenBaseDir = ini_get('open_basedir');
        if ((empty($b2sOpenBaseDir) || $b2sOpenBaseDir == " ") && (filter_var($b2sSafeMode, FILTER_VALIDATE_BOOLEAN) === false) || strtolower($b2sSafeMode) == "off") {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            if ($maxredirect > 0) {
                $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $newurl = $original_url;
                $rch = curl_copy_handle($ch);
                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                do {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    curl_setopt($rch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0');
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ((int) $code == 301) {
                            preg_match('/Location:(.*?)\n/i', $header, $matches);
                            $newurl = trim(array_pop($matches));
                            if (!preg_match("/^https?:/i", $newurl)) {
                                $newurl = $original_url . $newurl;
                            }
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$maxredirect);

                curl_close($rch);

                if (!$maxredirect) {
                    if ($maxredirect === null) {
                        return false; //Too many redirects
                    } else {
                        $maxredirect = 0;
                    }
                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0');
        return curl_exec($ch);
    }

    private static function b2sGetMetaDescription($html) {
        //$res = get_meta_tags($url);
        //return (isset($res['description']) ? self::cleanContent(strip_shortcodes($res['description'])) : '');
        $res = preg_match('#<meta +name *=[\"\']?description[\"\']?[^>]*content=[\"\']?(.*?)[\"\']? */?>#i', $html, $matches);
        return (isset($matches[1]) && !empty($matches[1])) ? trim(preg_replace('/\s+/', ' ', $matches[1])) : '';
    }

    private static function b2sGetMetaTitle($html) {
        $res = preg_match("/<title>(.*)<\/title>/siU", $html, $matches);
        return (isset($matches[1]) && !empty($matches[1])) ? trim(preg_replace('/\s+/', ' ', $matches[1])) : '';
    }

    private static function b2sGetAllTags($html, $type = 'og') {
        $list = array();
        @libxml_use_internal_errors(true);
        $dom = new DomDocument();
        if (function_exists('mb_convert_encoding')) {
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        } else {
            $dom->loadHTML($html);
        }
        $xpath = new DOMXPath($dom);
        $query = '//*/meta[starts-with(@property, \'' . $type . ':\')]';
        $result = $xpath->query($query);
        foreach ($result as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            $property = str_replace($type . ':', '', $property);
            if ($property == 'description') {
                $content = self::cleanContent(strip_shortcodes($content));
            }
            $list[$property] = (function_exists('mb_convert_encoding') ? htmlspecialchars($content) : $content );
        }
        return $list;
    }

    public static function getImagesByPostId($postId = 0, $postContent = '', $postUrl = '', $network = false, $postLang = 'en') {
        $homeUrl = get_site_url();
        $scheme = parse_url($homeUrl, PHP_URL_SCHEME);
        $featuredImage = wp_get_attachment_url(get_post_thumbnail_id($postId));
        $content = self::getFullContent($postId, $postContent, $postUrl, $postLang);

        $matches = array();
        if (!preg_match_all('%<img.*?src=[\"\'](.*?)[\"\'].*?>%', $content, $matches) && !$featuredImage) {
            return false;
        }
        array_unshift($matches[1], $featuredImage);
        $rtrnArray = array();
        foreach ($matches[1] as $key => $imgUrl) {
            if ($imgUrl == false) {
                continue;
            }

            //AllowedExtensions?
            if (!$network && !in_array(substr($imgUrl, strrpos($imgUrl, '.')), array('.jpg', '.png'))) {
                continue;
            }

            //isRelativ?
            if (!preg_match('/((http|https):\/\/|(www.))/', $imgUrl)) {
                //StartWith //
                if ((substr($imgUrl, 0, 2) == '//')) {
                    $imgUrl = (($scheme != NULL) ? $scheme : 'http') . ':' . $imgUrl;
                } else {
                    //StartWith /
                    $imgUrl = (substr($imgUrl, 0, 1) != '/') ? '/' . $imgUrl : $imgUrl;
                    $imgUrl = str_replace('//', '/', $imgUrl);
                    $imgUrl = $homeUrl . $imgUrl;
                    if (strpos($imgUrl, 'http://') === false && strpos($imgUrl, 'https://') === false) {
                        $imgUrl = (($scheme != NULL) ? $scheme : 'http') . '://' . $imgUrl;
                    }
                }
            }

            /* $file_headers = @get_headers($imgUrl);
              if ((!is_array($file_headers)) || (is_array($file_headers) && !preg_match('/200/', $file_headers[0]))) {
              continue;
              } */

            $rtrnArray[$key][0] = urldecode($imgUrl);
        }
        return $rtrnArray;
    }

    public static function prepareContent($postId = 0, $postContent = '', $postUrl = '', $allowHtml = '<p><h1><h2><br><i><b><a><img>', $allowEmoji = true, $postLang = 'en') {
        $homeUrl = get_site_url();
        $scheme = parse_url($homeUrl, PHP_URL_SCHEME);
        $postContent = html_entity_decode($postContent, ENT_COMPAT, 'UTF-8');
        $postContent = self::getFullContent($postId, $postContent, $postUrl, $postLang);
        $prepareContent = ($allowHtml !== false) ? self::cleanContent(self::cleanHtmlAttr(strip_shortcodes(self::cleanShortCodeByCaption($postContent)))) : self::cleanContent(strip_shortcodes($postContent));
        $prepareContent = ($allowEmoji !== false) ? $prepareContent : self::remove4byte($prepareContent);
        $prepareContent = preg_replace('/(?:[ \t]*(?:\n|\r\n?)){3,}/', "\n\n", $prepareContent);

        if ($allowHtml !== false) {
            $tempContent = nl2br(trim(strip_tags($prepareContent, $allowHtml)));
            if (preg_match_all('%<img.*?src=[\"\'](.*?)[\"\'].*?/>%', $tempContent, $matches)) {
                foreach ($matches[1] as $key => $imgUrl) {
                    if ($imgUrl == false) {
                        continue;
                    }
                    //isRelativ?
                    if (!preg_match('/((http|https):\/\/|(www.))/', $imgUrl)) {
                        //StartWith //
                        if ((substr($imgUrl, 0, 2) == '//')) {
                            $tempImgUrl = (($scheme != NULL) ? $scheme : 'http') . ':' . $imgUrl;
                        } else {
                            //StartWith /
                            $tempImgUrl = (substr($imgUrl, 0, 1) != '/') ? '/' . $imgUrl : $imgUrl;
                            $tempImgUrl = str_replace('//', '/', $tempImgUrl);
                            $tempImgUrl = $homeUrl . $tempImgUrl;
                            if (strpos($tempImgUrl, 'http://') === false && strpos($imgUrl, 'https://') === false) {
                                $tempImgUrl = (($scheme != NULL) ? $scheme : 'http') . '://' . $tempImgUrl;
                            }
                        }
                        $tempContent = str_replace(trim($imgUrl), $tempImgUrl, $tempContent);
                    }
                }
            }
            return $tempContent;
        }
        return trim(strip_tags($prepareContent));
    }

    public static function cleanHtmlAttr($postContent) {
        $postContent = preg_replace('/(<[^>]+) style=[\"\'].*?[\"\']/i', '$1', $postContent);
        $postContent = preg_replace('/(<[^>]+) class=[\"\'].*?[\"\']/i', '$1', $postContent);
        $postContent = preg_replace('/(<[^>]+) height=[\"\'].*?[\"\']/i', '$1', $postContent);
        $postContent = preg_replace('/(<[^>]+) width=[\"\'].*?[\"\']/i', '$1', $postContent);
        return preg_replace('/(<[^>]+) id=[\"\'].*?[\"\']/i', '$1', $postContent);
    }

    public static function cleanContent($postContent) {
        return preg_replace('/\[.*?(?=\])\]/s', '', $postContent);
    }

    public static function getFullContent($postId = 0, $postContent = '', $postUrl = '', $postLang = 'en') {
        $postLang = ($postLang === false) ? 'en' : trim(strtolower($postLang));
        //isset settings allow shortcode
        if (get_option('B2S_PLUGIN_USER_ALLOW_SHORTCODE_' . B2S_PLUGIN_BLOG_USER_ID) !== false) {
            //check is shortcode in content
            if (preg_match('/\[(.*?)\]/s', $postContent)) {
                //check has crawled content from frontend
                $dbContent = get_option('B2S_PLUGIN_POST_CONTENT_' . $postId);
                if ($dbContent !== false) {
                    return $dbContent;
                } else {
                    //crawl content from frontend
                    $postUrl = add_query_arg(array('b2s_get_full_content' => 1, 'lang' => $postLang), $postUrl);
                    $wpB2sGetFullContent = wp_remote_get($postUrl, array('timeout' => 11)); //slot 11 seconds         
                    if (is_array($wpB2sGetFullContent) && !is_wp_error($wpB2sGetFullContent)) {
                        //get crwaled content from db - hide cache by get_options
                        global $wpdb;
                        $dbContent = $wpdb->get_var($wpdb->prepare("SELECT option_value FROM " . $wpdb->options . " WHERE option_name =%s ", 'B2S_PLUGIN_POST_CONTENT_' . $postId));
                        if ($dbContent !== NULL) {
                            return $dbContent;
                        }
                    }
                }
            }
        }
        return $postContent;
    }

    //Emoji by Schedule + AllowNoNetwork
    public static function remove4byte($content) {
        if (function_exists('iconv')) {
            $content = iconv("utf-8", "utf-8//ignore", $content);
        }
        return trim(preg_replace('%(?:
         \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
        | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
    )%xs', '', $content));
    }

    public static function cleanShortCodeByCaption($postContent) {
        preg_match_all('#\s*\[caption[^]]*\].*?\[/caption\]\s*#is', $postContent, $matches);
        if (isset($matches[0]) && !empty($matches[0]) && is_array($matches[0])) {
            $temp = '';
            foreach ($matches[0] as $k => $v) {
                $temp = $v;
                if (preg_match('/< *img[^>]+\>/i', $v, $match)) {
                    $v = (isset($match[0])) ? str_replace($match[0], $match[0] . "\n\n", $v) : $v;
                    $t = preg_replace('#\s*\[/caption\]\s*#is', "\n\n", $v);
                    $new = preg_replace('#\s*\[caption[^]]*\]\s*#is', '', $t);
                    $postContent = str_replace($temp, "\n" . $new, $postContent);
                }
            }
        }
        return $postContent;
    }

    public static function getRandomTime($start, $ende) {
        $startparts = explode(':', $start);
        $startH = $startparts[0];
        $startMin = strlen($startparts[1]) == 1 ? '0' . $startparts[1] : $startparts[1];
        $endparts = explode(':', $ende);
        $endH = $endparts[0];
        $endMin = strlen($endparts[1]) == 1 ? '0' . $endparts[1] : $endparts[1];

        $rand = rand((int) ($startH . $startMin), (int) ($endH . $endMin));
        if ($rand == NULL) {
            return date('H:00');
        }
        if (strlen($rand) == 3) {
            $rand = '0' . $rand;
        }
        $hour = substr($rand, 0, 2);
        $miunte = substr($rand, 2, 2);
        $minute = $miunte > 50 ? '30' : '00';

        return $hour . ':' . $minute;
    }

    public static function getTimeByLang($time, $lang = 'de') {
        $time = substr('0' . $time, -2);
        $slug = ($lang == 'en') ? 'h:i a' : 'H:i';
        return date($slug, strtotime(date('Y-m-d ' . $time . ':00:00')));
    }

    public static function getExcerpt($text, $count = 400, $max = false, $add = false) {
        //Bug: Converting json + PHP Extension
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($text, 'UTF-8') < $count) {
                return trim($text);
            }
            $stops = array('.', ':');
            $min = (int) $count / 2;
            $max = ($max !== false) ? ($max - $min) : ($min - 1);
            $sub = mb_substr($text, $min, $max, 'UTF-8');
            for ($i = 0; $i < count($stops); $i++) {
                if (count($subArray = explode($stops[$i], $sub)) > 1) {
                    $subArray[count($subArray) - 1] = ' ';
                    $sub = implode($stops[$i], $subArray);
                    $add = false;
                    break;
                }
            }
            $text = trim(mb_substr($text, 0, $min, 'UTF-8') . $sub);
            return ($add) ? $text . "..." : $text;
        }
        return trim($text);
    }

//Plugin qTranslate [:en]Content[:de]Text[:]
    public static function getTitleByLanguage($title, $postLang = 'en') {
        $postLang = ($postLang === false) ? 'en' : trim(strtolower($postLang));
        $regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
        $blocks = preg_split($regex, $title, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (count($blocks) <= 1) {//no language is encoded in the $text, the most frequent case
            return $title;
        }
        $result = array();
        $current_lang = false;
        foreach ($blocks as $block) {
            // detect c-tags
            if (preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_lang = $matches[1];
                continue;
                // detect b-tags
            } elseif (preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_lang = $matches[1];
                continue;
                // detect s-tags @since 3.3.6 swirly bracket encoding added
            } elseif (preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_lang = $matches[1];
                continue;
            }
            switch ($block) {
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_lang = false;
                    break;
                default:
                    // correctly categorize text block
                    if ($current_lang) {
                        if (!isset($result[$current_lang])) {
                            $result[$current_lang] = '';
                        }
                        $result[$current_lang] .= $block;
                        $found[$current_lang] = true;
                        $current_lang = false;
                    }
                    break;
            }
        }
        foreach ($result as $l => $text) {
            $result[$l] = trim($text);
        }

        if (!isset($found[$postLang])) {
            $locale = (!get_locale()) ? get_locale() : B2S_LANGUAGE;
            $postLang = substr($locale, 0, 2);
            if (!isset($found[$postLang])) {
                $postLang = current(array_keys($found));
            }
        }

        return $result[$postLang];
    }

    public static function createTimezoneList($selected = '', $region = 2047) { //DateTimeZone::ALL == 2047  >=PHP 5.5.3 constant not set
        $timezones = timezone_identifiers_list($region);
        if (!$timezones) {
            return false;
        }
        $optionHtmlList = '';
        $timezoneData = array();
        foreach ($timezones as $timezone) {
            $timezoneData[$timezone] = self::getOffsetToUtcByTimeZone($timezone);
            self::humanReadableOffset($timezoneData[$timezone]);
            $utcStr = '(UTC ' . self::humanReadableOffset($timezoneData[$timezone]) . ')';
            $timeZoneEntry = trim($utcStr) . ' ' . trim(preg_replace("/\_/", ' ', $timezone));
            $isSelected = ($timezone == $selected) ? 'selected' : '';
            $optionHtmlList .= '<option value="' . $timezone . '" data-offset="' . $timezoneData[$timezone] . '" ' . $isSelected . '>' . $timeZoneEntry . '</option>';
        }
        return $optionHtmlList;
    }

    public static function humanReadableOffset($floatnbr = 0) {
        $result = '';
        $floatnbr = number_format($floatnbr, 2, '.', ' ');
        $sign = '';
        switch ($floatnbr) {

            case $floatnbr > 0.00:
                $sign = '+';
                break;

            case $floatnbr < 0.00:
                $sign = '-';
                break;

            case $floatnbr == 0.00:
                break;
        }

        $nbrSplit = explode('.', $floatnbr);
        $first = $nbrSplit[0];
        if ($first < 0) {
            $first = preg_replace('/-/', '', $first);
        }

        $first = str_pad($first, 2, '0', STR_PAD_LEFT);

        $second = $nbrSplit[1];
        if ($second > 0) {
            $second = $second / 100 * 60;
        }

        if ($floatnbr < 0.00) {
            $first = '-' . $first;
        } elseif ($floatnbr > 0.00) {
            $first = '+' . $first;
        } else {
            $first = ' ' . $first;
        }

        return $first . ':' . $second;
    }

    public static function getOffsetToUtcByTimeZone($userTimeZone = '', $firstDateTime = 'now') {

        if (empty($userTimeZone)) {
            $userTimeZone = date_default_timezone_get();
        }

        $this_tz = new DateTimeZone($userTimeZone);
        $now = new DateTime($firstDateTime, $this_tz);
        $offset = $this_tz->getOffset($now);

        return (float) $offset / 3600;
    }
}
