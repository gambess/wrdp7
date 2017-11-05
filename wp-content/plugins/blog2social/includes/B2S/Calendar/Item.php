<?php

require_once (B2S_PLUGIN_DIR . 'includes/B2S/Calendar/ItemEdit.php');
require_once (B2S_PLUGIN_DIR . 'includes/Util.php');

class B2S_Calendar_Item {

    private $sched_date = null;
    private $network_id = null;
    private $post_title = null;
    private $blog_user_id = null;
    private $network_display_name = null;
    private $b2s_id = null;
    private $user_timezone = null;
    private $ship_item = null;
    private $network_type = null;
    private $network_auth_id = null;
    private $sched_data = null;
    private $image_url = null;
    private $post_format = null;
    private $sched_details_id = null;

    public function __construct(\StdClass $data = null) {
        if (isset($data)) {
            $this
                    ->setSchedData($data->sched_data)
                    ->setSchedDate($data->sched_date)
                    ->setNetworkId($data->network_id)
                    ->setPostTitle($data->post_title)
                    ->setBlogUserId($data->blog_user_id)
                    ->setNetworkDisplayName($data->network_display_name)
                    ->setUserTimezone($data->user_timezone)
                    ->setPostId($data->post_id)
                    ->setNetworkType($data->network_type)
                    ->setNetworkAuthId($data->network_auth_id)
                    ->setB2SId($data->b2s_id)
                    ->setSchedDetailsId($data->sched_details_id)
                    ->setImageUrl($data->image_url);

            if ($data->network_id == 1 || $data->network_id == 2) {
                $this->setPostFormat();
            }
        }
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setSchedDate($value) {
        if (is_numeric($value) || is_null($value)) {
            $this->sched_date = $value;
        } else if (is_string($value)) {
            $this->sched_date = strtotime($value);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getSchedDate() {
        return $this->sched_date;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkId($value) {
        if (is_numeric($value)) {
            $this->network_id = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getNetworkId() {
        return $this->network_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setSchedDetailsId($value) {
        if (is_numeric($value)) {
            $this->sched_details_id = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getSchedDetailsId() {
        return $this->sched_details_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkAuthId($value) {
        if (is_numeric($value)) {
            $this->network_auth_id = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getNetworkAuthId() {
        return $this->network_auth_id;
    }

    /**
     * @param string|array $value
     * @return $this
     */
    public function setSchedData($value) {
        if (is_string($value)) {
            $this->sched_data = unserialize($value);
            if (is_array($this->sched_data)) {
                //prepare Data                        
                foreach ($this->sched_data as $k => $v) {
                    if (!is_array($v)) {
                        $this->sched_data[$k] = stripslashes($v);
                    }
                }
            }
        } else if (is_array($value)) {
            $this->sched_data = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSchedData() {
        return $this->sched_data;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkType($value) {
        if (is_numeric($value)) {
            $this->network_type = (int) $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getNetworkType() {
        return $this->network_type;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setPostId($value) {
        if (is_numeric($value)) {
            $this->post_id = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getPostId() {
        return $this->post_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setB2SId($value) {
        if (is_numeric($value)) {
            $this->b2s_id = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getB2SId() {
        return $this->b2s_id;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPostTitle($value) {
        if (is_string($value)) {
            $this->post_title = B2S_Util::getTitleByLanguage($value, strtolower(substr(get_locale(), 0, 2)));
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPostTitle() {
        return $this->post_title;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setBlogUserId($value) {
        if (is_numeric($value)) {
            $this->blog_user_id = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getBlogUserId() {
        return $this->blog_user_id;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setUserTimezone($value) {
        if (is_numeric($value)) {
            $this->user_timezone = $value;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getUserTimezone() {
        return $this->user_timezone;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setNetworkDisplayName($value) {
        if (is_string($value)) {
            $this->network_display_name = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getNetworkDisplayName() {
        return $this->network_display_name;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setImageUrl($value) {
        if (is_string($value)) {
            $this->image_url = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl() {
        return $this->image_url;
    }

    /**
     * @param integer $value
     * @return $this
     */
    public function setPostFormat($value = null) {
        if ($value == null) {
            $sched_data = $this->getSchedData();
            if (is_array($sched_data)) {
                if (isset($sched_data['post_format'])) {
                    $this->post_format = (int) $sched_data['post_format'];
                }
            }
        } else {
            $this->post_format = $value;
        }
        return $this;
    }

    /**
     * @return integer
     */
    public function getPostFormat() {
        return $this->post_format;
    }

    /**
     * @return string
     */
    public function getAvatar() {
        $res = "";

        $user = get_user_by("id", $this->getBlogUserId());

        if ($user) {
            $res = get_avatar($user->user_email, 32);
        }

        return $res;
    }

    /**
     * @return string
     */
    public function getAuthor() {
        $res = "";

        $user = get_user_by("id", $this->getBlogUserId());

        if ($user) {
            $res = $user->display_name;
        }

        return $res;
    }

    private function getColor() {
        $colors = ["#983b3b", "#79B232", "#983b7d", "#3b3b98", "#3b8e98", "#65983b", "#6b3b98", "#93983b", "#987d3b", "#985c3b"];
        $id = $this->getBlogUserId() % count($colors);
        return $colors[$id];
    }

    private function getNetworkName() {
        $names = unserialize(B2S_PLUGIN_NETWORK);
        if ($names[$this->getNetworkId()]) {
            return $names[$this->getNetworkId()];
        }

        return null;
    }

    /**
     * @return array
     */
    public function asCalendarArray() {
        return ["title" => $this->getPostTitle(),
            "avatar" => $this->getAvatar(),
            "author" => $this->getAuthor(),
            "start" => date("Y-m-d H:i:s", $this->getSchedDate()),
            "color" => $this->getColor(),
            "network_name" => $this->getNetworkName(),
            "network_id" => $this->getNetworkId(),
            "network_type" => $this->getNetworkType(),
            "network_auth_id" => $this->getNetworkAuthId(),
            "post_format" => $this->getPostFormat(),
            "b2s_id" => $this->getB2SId(),
            "post_id" => $this->getPostId(),
            "user_timezone" => $this->getUserTimezone(),
            "profile" => $this->getNetworkDisplayName()];
    }

    /**
     * @return B2S_Ship_Item
     */
    public function ship_item() {
        if (is_null($this->ship_item)) {
            $this->ship_item = new B2S_Calendar_ItemEdit($this->getPostId());

            $sched_data = $this->getSchedData();
            if (is_array($sched_data)) {
                if (!empty($sched_data['url'])) {
                    $this->ship_item->setPostUrl($sched_data['url']);
                }
                if (!empty($sched_data['custom_title'])) {
                    $this->ship_item->setTitle($sched_data['custom_title']);
                }
            }


            $this->ship_item->setB2SId($this->getB2SId());
        }

        return $this->ship_item;
    }

    public function getEditHtml($view = 'modal') {
        $itemData = array('networkAuthId' => $this->getNetworkAuthId(),
            'networkId' => $this->getNetworkId(),
            'network_display_name' => $this->getNetworkDisplayName(),
            'networkType' => $this->getNetworkType(),
            'image_url' => $this->getImageUrl(),
            'view' => $view);

        return $this->ship_item()->getItemHtml((object) $itemData, false);
    }

}
