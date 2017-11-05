<?php

class B2S_Post_Filter {

    public $type;
    protected $postFilter = '';
    protected $searchAuthorId;
    protected $searchPostStatus;
    protected $searchPostTitle;
    protected $searchPostCat;
    protected $searchPostType;
    protected $searchPublishDate;
    protected $searchSchedDate;
    protected $postAuthor;

    function __construct($type, $title = "", $authorId = "", $postStatus = "", $publishDate = "", $schedDate = "", $postCat = "", $postType = "") {  //type=all,publish,sched
        $this->type = $type;
        $this->searchPostTitle = $title;
        $this->searchAuthorId = $authorId;
        $this->searchPostStatus = $postStatus;
        $this->searchPublishDate = $publishDate;
        $this->searchSchedDate = $schedDate;
        $this->searchPostCat = $postCat;
        $this->searchPostType = $postType;
    }

    public function getAutorData() {
        global $wpdb;
        $sqlAuthors = "SELECT `ID`,`display_name` FROM `$wpdb->users`";
        $this->postAuthor = $wpdb->get_results($sqlAuthors);
    }

    private function getAutorHtml() {
        $autor = '<div class="form-group"><select id="b2sSortPostAuthor" name="b2sSortPostAuthor" class="form-control b2s-select"><option value="">' . __('all authors', 'blog2social') . '</option>';
        foreach ($this->postAuthor as $var) {
            $selected = (!empty($this->searchAuthorId) && $var->ID == (int) $this->searchAuthorId) ? 'selected' : '';
            $autorName = $var->display_name;
            //Bug: Converting json + PHP Extension
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                $autorName = mb_strlen($var->display_name,'UTF-8') > 27 ? mb_substr($var->display_name, 0, 27,'UTF-8') . '...' : $autorName;
            }
            $autor .= '<option ' . $selected . ' value="' . $var->ID . '">' . $autorName . '</option>';
        }
        $autor .= '</select></div>';
        return $autor;
    }

    private function getPostStatusHtml() {
        $typeData = array(array('key' => 'publish', 'value' => __('published', 'blog2social')), array('key' => 'future', 'value' => __('scheduled', 'blog2social')), array('key' => 'pending', 'value' => __('draft', 'blog2social')));
        $type = '<div class="form-group"><select id="b2sSortPostStatus" name="b2sSortPostStatus" class="form-control b2s-select"><option value="">' . __('all statuses', 'blog2social') . '</option>';
        foreach ($typeData as $var) {
            $var = (object) $var;
            $selected = (!empty($this->searchPostStatus) && $var->key == $this->searchPostStatus) ? 'selected' : '';
            $type .= '<option ' . $selected . ' value="' . $var->key . '">' . $var->value . '</option>';
        }
        $type .= '</select></div>';
        return $type;
    }

    private function getPublishDateHtml() {
        $typeData = array(array('key' => 'desc', 'value' => __('newest first', 'blog2social')), array('key' => 'asc', 'value' => __('oldest first', 'blog2social')));
        $type = '<div class="form-group"><select id="b2sSortPostPublishDate" name="b2sSortPostPublishDate" class="form-control b2s-select">';
        foreach ($typeData as $var) {
            $var = (object) $var;
            $selected = (!empty($this->searchPublishDate) && $var->key == $this->searchPublishDate) ? 'selected' : '';
            $type .= '<option ' . $selected . ' value="' . $var->key . '">' . $var->value . '</option>';
        }
        $type .= '</select></div>';
        return $type;
    }

    private function getSchedDateHtml() {
        $typeData = array(array('key' => 'desc', 'value' => __('newest first', 'blog2social')), array('key' => 'asc', 'value' => __('oldest first', 'blog2social')));
        $type = '<div class="form-group"><select id="b2sSortPostSchedDate" name="b2sSortPostSchedDate" class="form-control b2s-select">';
        foreach ($typeData as $var) {
            $var = (object) $var;
            $selected = (!empty($this->searchSchedDate) && $var->key == $this->searchSchedDate) ? 'selected' : '';
            $type .= '<option ' . $selected . ' value="' . $var->key . '">' . $var->value . '</option>';
        }
        $type .= '</select></div>';
        return $type;
    }

    private function getPostCatHtml() {
        $taxonomies = get_taxonomies(array('public' => true), "object", "and");
        $type = '<div class="form-group"><select id="b2sSortPostCat" name="b2sSortPostCat" class="form-control b2s-select"><option value="">' . __('all categories & tags', 'blog2social') . '</option>';
        foreach ($taxonomies as $tax => $taxValue) {
            $cat = get_categories(array('taxonomy' => $taxValue->name,'number' =>100)); //since 3.7.0 => all too much load
            if (!empty($cat)) {
                $type.='<optgroup label="' . $taxValue->labels->name . '">';
                foreach ($cat as $key => $categorie) {
                    $selected = (!empty($this->searchPostCat) && $categorie->term_id == $this->searchPostCat) ? 'selected' : '';
                    $catName = $categorie->name;
                    //Bug: Converting json + PHP Extension
                    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                        $catName = mb_strlen($categorie->name,'UTF-8') > 27 ? mb_substr($categorie->name, 0, 27,'UTF-8') . '...' : $catName;
                    }
                    $type .= '<option ' . $selected . ' value="' . $categorie->term_id . '">' . $catName . '</option>';
                }
                $type .='</optgroup>';
            }
        }
        $type .= '</select></div>';
        return $type;
    }

    private function getPostTypeHtml() {
        $type = '<div class="form-group"><select id="b2sSortPostType" name="b2sSortPostType" class="form-control b2s-select"><option value="">' . __('all post types', 'blog2social') . '</option>';
        $post_types = get_post_types(array('public' => true));
        if (is_array($post_types) && !empty($post_types)) {
            foreach ($post_types as $k => $v) {
                if ($v != 'attachment' && $v != 'nav_menu_item' && $v != 'revision') {
                    $selected = (!empty($this->searchPostType) && $v == $this->searchPostType) ? 'selected' : '';
                    //Bug: Converting json + PHP Extension
                      if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                            $v = mb_strlen($v,'UTF-8') > 27 ? mb_substr($v, 0, 27,'UTF-8') . '...' : $v;
                      }
                    $type .= '<option ' . $selected . ' value="' . $v . '">' . ucfirst($v) . '</option>';
                }
            }
        }
        $type .= '</select></div>';
        return $type;
    }

    public function getItemHtml() {
        $this->getAutorData();
        $this->postFilter .= '<div class="form-group">
                                    <input id="b2sSortPostTitle" name="b2sSortPostTitle" maxlength="30" class="form-control b2s-input input-sm" value="' . (empty($this->searchPostTitle) ? '' : $this->searchPostTitle) . '" placeholder="' . (empty($this->searchPostTitle) ? __('Search Title', 'blog2social') : '') . '" type="text">
                             </div>';
        if (B2S_PLUGIN_ADMIN) {
            $this->postFilter .= $this->getAutorHtml();
        }
        $this->postFilter .= $this->getPostCatHtml();
        $this->postFilter .= $this->getPostTypeHtml();

        if ($this->type == 'all') {
            $this->postFilter .= $this->getPostStatusHtml();
        }
        if ($this->type == 'publish') {
            $this->postFilter .= $this->getPublishDateHtml();
        }
        if ($this->type == 'sched') {
            $this->postFilter .= $this->getSchedDateHtml();
        }
        $this->postFilter .= '<div class="form-group">';

        /*if ($this->type == 'sched') {
            $this->postFilter .='<a href="#" class="btn btn-primary b2s-sched-calendar-btn btn-sm" data-hide-calendar-btn-title="' . __('hide calendar', 'blog2social') . '" data-show-calendar-btn-title="' . __('show calendar', 'blog2social') . '"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> <span id="b2s-sched-calendar-btn-text">' . __('show calendar', 'blog2social') . '<span></a> ';
        }*/

        $this->postFilter .='<a href="#" id="b2s-sort-submit-btn" class="btn btn-primary btn-sm">' . __('sort', 'blog2social') . '</a>
                                    <a id="b2s-sort-reset-btn" class="btn btn-primary btn-sm" href="#">' . __('reset', 'blog2social') . '</a>
                             </div>';

        if ($this->type == 'sched') {
            $this->postFilter .='<div id="b2s-sched-calendar-area"><br><div id="b2s-sched-datepicker-area"></div><br>';
            $this->postFilter .='<div class="pull-right"><small><span class="b2s-calendar-legend-active glyphicon glyphicon-stop"></span> ' . __('selected date', 'blog2social') . ' <span class="b2s-calendar-legend-event glyphicon glyphicon-stop"></span> ' . __('scheduled post(s)', 'blog2social') . '</small></div></div>';
        }

        return $this->postFilter;
    }

}
