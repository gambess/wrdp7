<?php

class B2S_Meta {

    static private $instance = null;
    public $print;
    public $post;
    public static $meta_prefix = '_b2s_post_meta';
    public $metaData = false;
    public $options;

    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function _run() {
        global $post;
        $this->post = $post;
        $this->print = true;
        $this->getMeta($this->post->ID);
        $this->options = get_option('B2S_PLUGIN_GENERAL_OPTIONS');

        //Check 3rd Plugin Yoast
        if (isset($this->options['og_active']) && (int) $this->options['og_active'] == 1) {  //on
            $yoast = get_option('wpseo_social');
            if (is_array($yoast) && isset($yoast['opengraph']) && $yoast['opengraph'] !== false && defined('WPSEO_VERSION')) { //plugin with settings is active
                $this->override3rdYoast();
            } else {
                $this->getOgMeta();
            }
        }

        if (isset($this->options['card_active']) && (int) $this->options['card_active'] == 1) {  //on
            $yoast = get_option('wpseo_social');
            if (is_array($yoast) && isset($yoast['twitter']) && $yoast['twitter'] !== false && defined('WPSEO_VERSION')) {//plugin with settings is active
                $this->override3rdYoast('card');
            } else {
                $this->getCardMeta();
            }
        }

        //SEO 
        if (!defined('WPSEO_VERSION') && (isset($this->options['og_active']) && (int) $this->options['og_active'] == 1) || isset($this->options['card_active']) && (int) $this->options['card_active'] == 1) {
            $this->getAuthor();
        }
    }

    public function getOgMeta() {
        $this->getTitle();
        $this->getDesc();
        $this->getUrl();
        $this->getImage();
    }

    public function getCardMeta() {
        echo '<meta name="twitter:card" content="summary">' . "\n";
        $this->getTitle('card');
        $this->getDesc('card');
        $this->getImage('card');
    }

    private function getTitle($type = 'og') {
        if (isset($this->metaData[$type . '_title']) && !empty($this->metaData[$type . '_title'])) {
            $title = $this->metaData[$type . '_title'];
        } else {
            $title = (is_home()) ? ((isset($this->options[$type . '_default_title']) && !empty($this->options[$type . '_default_title'])) ? $this->options[$type . '_default_title'] : get_bloginfo('name')) : get_the_title();
        }
        if ($this->print) {
            if ($type == 'og') {
                echo '<meta property="og:title" content="' . esc_attr(apply_filters('b2s_og_meta_title', $title)) . '"/>' . "\n";
            } else {
                echo '<meta property="twitter:title" content="' . esc_attr(apply_filters('b2s_card_meta_title', $title)) . '"/>' . "\n";
            }
        } else {
            return $title;
        }
    }

    private function getDesc($type = 'og') {
        if (is_singular()) {
            if (isset($this->metaData[$type . '_desc']) && !empty($this->metaData[$type . '_desc'])) {
                $desc = str_replace("\r\n", ' ', strip_tags(strip_shortcodes($this->metaData[$type . '_desc'])));
            } else {
                if (has_excerpt($this->post->ID)) {
                    $desc = strip_tags(get_the_excerpt());
                } else {
                    $desc = str_replace("\r\n", ' ', substr(strip_tags(strip_shortcodes($this->post->post_content)), 0, 160));
                }
            }
        } else {
            $desc = (isset($this->options[$type . '_default_desc']) && !empty($this->options[$type . '_default_desc'])) ? $this->options[$type . '_default_desc'] : get_bloginfo('description');
        }
        if ($this->print) {
            if ($type == 'og') {
                echo '<meta property="og:description" content="' . esc_attr(apply_filters('b2s_og_meta_desc', $desc)) . '"/>' . "\n";
            } else {
                echo '<meta property="twitter:description" content="' . esc_attr(apply_filters('b2s_card_meta_desc', $desc)) . '"/>' . "\n";
            }
        } else {
            return $desc;
        }
    }

    private function getUrl() {
        $url = (is_home()) ? home_url() : 'http' . ( is_ssl() ? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        echo '<meta property="og:url" content="' . esc_url(apply_filters('b2s_og_meta_url', $url)) . '"/>' . "\n";
    }

    private function getImage($type = 'og') {

        $image = '';
        if (!is_home()) {
            if (isset($this->metaData[$type . '_image']) && !empty($this->metaData[$type . '_image'])) {
                $image = $this->metaData[$type . '_image'];
            } else {
                if ($id_attachment = get_post_thumbnail_id($this->post->ID)) {
                    $image = wp_get_attachment_url($id_attachment, false);
                    if (!preg_match('/^https?:\/\//', $image)) {
                        // Remove any starting slash with ltrim() and add one to the end of site_url()
                        $image = site_url('/') . ltrim($image, '/');
                    }
                }
            }
        }
        if ((is_home() || empty($image)) && isset($this->options[$type . '_default_image']) && !empty($this->options[$type . '_default_image'])) {
            $image = $this->options[$type . '_default_image'];
        }


        if (!empty($image)) {
            if ($this->print) {
                if ($type == 'og') {
                    echo '<meta property="og:image" content="' . esc_url(apply_filters('b2s_og_meta_image', $image)) . '"/>' . "\n";
                } else {
                    echo '<meta property="twitter:image" content="' . esc_url(apply_filters('b2s_card_meta_image', $image)) . '"/>' . "\n";
                }
            }
        } else {
            return $image;
        }
    }

    public function getAuthor() {
        if ($this->post->post_author > 0 && is_singular()) {
            $author_meta = get_the_author_meta('display_name', $this->post->post_author);
            echo '<meta name="author" content="' . trim(esc_attr($author_meta)) . '"/>' . "\n";
        }
    }

    /* private function findImages() {
      if (!is_object($this->post)) {
      return array();
      }
      $content = $this->post->post_content;
      $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);

      if ($output === FALSE) {
      return false;
      }
      $images = array();
      foreach ($matches[1] as $match) {
      if (!preg_match('/^https?:\/\//', $match)) {
      $match = site_url('/') . ltrim($match, '/');
      }
      $images[] = $match;
      }
      return $images;
      } */

    public function getMeta($postId = 0) {
        $postId = ((int) $postId > 0) ? $postId : (isset($this->post->ID) && is_object($this->post->ID) ? $this->post->ID : 0);
        if ($postId > 0) {
            $this->metaData = get_post_meta($postId, self::$meta_prefix, true);
            return $this->metaData;
        } else {
            return false;
        }
    }

    public function setMeta($key, $value) {
        $update = false;
        if (!is_array($this->metaData) || $this->metaData === false) {
            $this->metaData = array($key => $value);
            $update = true;
        } else {
            foreach ($this->metaData as $k) {
                if (isset($this->metaData[$key])) {
                    $this->metaData[$key] = $value;
                    $update = true;
                }
            }
            if (!$update) {
                if (is_array($this->metaData)) {
                    $this->metaData[$key] = $value;
                }
            }
        }
    }

    public function updateMeta($postId) {
        if (!add_post_meta($postId, self::$meta_prefix, $this->metaData, true)) {
            update_post_meta($postId, self::$meta_prefix, $this->metaData);
        }
        return true;
    }

    public function deleteMeta($post_id) {
        return delete_post_meta($post_id, self::$meta_prefix);
    }

    public function override3rdYoast($type = 'og') {
        $this->print = true;
        if ($type == 'og' && ( (is_array($this->metaData)) || isset($this->options['og_active']) && (int) $this->options['og_active'] == 1)) {
            add_filter('wpseo_opengraph_title', '__return_false');
            $this->getTitle();
            add_filter('wpseo_opengraph_desc', '__return_false');
            $this->getDesc();
            add_filter('wpseo_opengraph_image', '__return_false');
            $this->getImage();
        }

        if ($type == 'card' && ( (is_array($this->metaData)) || isset($this->options['card_active']) && (int) $this->options['card_active'] == 1)) {
            add_filter('wpseo_twitter_title', '__return_false');
            $this->getTitle('card');
            add_filter('wpseo_twitter_description', '__return_false');
            $this->getDesc('card');
            add_filter('wpseo_twitter_image', '__return_false');
            $this->getImage('card');
        }
    }

    /* 3rd Party - Yoast SEO */

    public function is_yoast_seo_active() {
        if (defined('WPSEO_VERSION')) {
            $yoast = get_option('wpseo_social');
            $result = array();
            if (is_array($yoast) && ((isset($yoast['opengraph']) && $yoast['opengraph'] !== false ) || ( isset($yoast['twitter']) && $yoast['twitter'] !== false) )) {
                return true;
            }
        }
        return false;
    }

    /* 3rd Party - All in One SEO Pack */

    public function is_aioseop_active() {
        if (defined('AIOSEOP_VERSION')) {
            return true;
        }
        return false;
    }

    /* 3rd Party - Facebook Open Graph, Google+ and Twitter Card Tag */

    public function is_webdados_active() {
        if (defined('WEBDADOS_FB_VERSION')) {
            return true;
        }
        return false;
    }

    /* Own Social Meta Tags */

    public function is_b2s_active() {
        $this->options = get_option('B2S_PLUGIN_GENERAL_OPTIONS');
        if ((isset($this->options['og_active']) && (int) $this->options['og_active'] == 1 ) || (isset($this->options['card_active']) && (int) $this->options['card_active'] == 1)) {  //on 
            return true;
        }
        return false;
    }

}
