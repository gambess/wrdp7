<?php

class B2S_Options {

    protected $optionData;
    protected $name;
    protected $blog_user_id;

    public function __construct($blog_user_id = 0, $name = 'B2S_PLUGIN_OPTIONS') {  //since V4.0.0
        $this->name = $name;
        $this->blog_user_id = $blog_user_id;
        $this->optionData = ($this->blog_user_id == 0) ? get_option($name) : get_option($name . '_' . $blog_user_id);
    }

    public function _getOption($key) {
        if (is_array($this->optionData)) {
            foreach ($this->optionData as $k) {
                if (isset($this->optionData[$key])) {
                    return $this->optionData[$key];
                }
            }
        }
        return false;
    }

    public function _setOption($key, $value) {
        $update = false;
        if (!is_array($this->optionData) || $this->optionData === false) {
            $this->optionData = array($key => $value);
            $update = true;
        } else {
            foreach ($this->optionData as $k) {
                if (isset($this->optionData[$key])) {
                    $this->optionData[$key] = $value;
                    $update = true;
                }
            }
            if (!$update) {
                if (is_array($this->optionData)) {
                    $this->optionData[$key] = $value;
                }
            }
        }
        if ($this->blog_user_id == 0) {
            update_option($this->name, $this->optionData);
        } else {
            update_option($this->name . '_' . $this->blog_user_id, $this->optionData);
        }
        return true;
    }

}
