<?php

class B2S_Ship_Item {

    private $allowTitleProfile = array(7, 9, 13, 15, 16);
    private $allowTitlePage = array();
    private $allowTitleGroup = array();
    private $setPostFormat = array(1, 2);
    private $isCommentProfile = array(1, 3, 8, 10);
    private $isCommentPage = array(1);
    private $isCommentGroup = array(1, 8);
    private $allowTag = array(4, 9, 16);
    private $allowHtml = array(4, 11, 14);
    private $showTitleProfile = array(4, 9, 11, 14, 16);
    private $showTitlePage = array(8);
    private $showTitleGroup = array(8);
    private $onlyImage = array(6, 7, 12);
    private $allowNoImageProfile = array(5, 9);
    private $allowNoCustomImageProfile = array(8, 15);
    private $allowNoEmoji = array(3, 9, 13, 14, 15, 16);
    private $allowNoImagePage = array(8);
    private $allowEditUrl = array(1, 2, 3, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16);
    private $showBoards = array(6);
    private $showGroups = array(8, 15);
    private $hideGroupName = array(8);
    private $setShortTextProfile = array(1 => 239, 2 => 116, 3 => 239, 6 => 300, 8 => 239, 10 => 442, 12 => 240, 9 => 200, 16 => 250);
    private $setShortTextProfileLimit = array(1 => 400, 2 => 116, 3 => 400, 6 => 400, 8 => 400, 10 => 500, 12 => 400, 9 => 200);
    private $setShortTextPage = array(1 => 239, 3 => 239, 8 => 1200, 10 => 442);
    private $setShortTextPageLimit = array(1 => 400, 3 => 400, 8 => 1200, 10 => 500);
    private $setShortTextGroup = array(1 => 239, 8 => 239, 10 => 442);
    private $setShortTextGroupLimit = array(1 => 400, 8 => 400, 10 => 500);
    private $allowHashTags = array(6, 10, 12);
    private $limitCharacterProfile = array(2 => 140, 3 => 600, 6 => 500, 8 => 420, 9 => 250, 15 => 300, 12 => 2000);
    private $showImageArea = array(6, 7, 10, 12, 16);
    private $limitCharacterPage = array(3 => 600, 8 => 1200);
    private $requiredUrl = array(1, 3, 8, 9, 10, 15);
    private $getText = array(1, 7, 10, 12, 16);
    private $maxWeekTimeSelect = 52;
    private $maxSchedCount = 3;
    private $noScheduleRegularly = array(4, 11, 14, 15);
    private $defaultImage;
    private $postData;
    private $postUrl;
    private $postStatus;
    private $websiteName;
    private $postId;
    private $userLang;
    private $viewMode;

    public function __construct($postId, $userLang = 'en') {
        $this->postId = $postId;
        $this->postData = get_post($this->postId);
        $this->postStatus = $this->postData->post_status;
        $this->websiteName = get_option('blogname');
        $this->postUrl = (get_permalink($this->postData->ID) !== false ? get_permalink($this->postData->ID) : $this->postData->guid);
        $this->userLang = $userLang;
    }

    protected function getPostId() {
        return $this->postId;
    }

    public function getItemHtml($data, $show_time = true) {

        $this->viewMode = (isset($data->view) && !empty($data->view)) ? $data->view : null;  //normal or modal(Kalendar)

        $neworkName = unserialize(B2S_PLUGIN_NETWORK);
        $networkTypeName = unserialize(B2S_PLUGIN_NETWORK_TYPE);
        $limit = false;
        $limitValue = 0;
        $textareaLimitInfo = "";
        $textareaOnKeyUp = "";
        $this->defaultImage = plugins_url('/assets/images/no-image.png', B2S_PLUGIN_FILE);

        //Settings
        switch ($data->networkType) {
            case '0':
                //profil
                if (isset($this->limitCharacterProfile[$data->networkId]) && (int) $this->limitCharacterProfile[$data->networkId] > 0) {
                    $limitValue = $this->limitCharacterProfile[$data->networkId];
                    $limit = true;
                }
                $infoImage = (in_array($data->networkId, $this->allowNoImageProfile)) ? __('Network does not support image for profiles', 'blog2social') . '!' : '';
                $infoImage .= (in_array($data->networkId, $this->allowNoCustomImageProfile)) ? (!empty($infoImage) ? ' | ' : '') . __('Network defines image by link', 'blog2social') . '!' : '';
                $htmlTags = highlight_string("<p><br><i><b><a><img>", true);
                $infoImage .= (in_array($data->networkId, $this->allowHtml)) ? (!empty($infoImage) ? ' | ' : '') . __('Supported HTML tags', 'blog2social') . ': ' . $htmlTags : '';
                $infoImage .= (in_array($data->networkId, $this->allowNoEmoji)) ? (!empty($infoImage) ? ' | ' : '') . __('Network does not support emojis', 'blog2social') . '!' : '';

                $network_display_name = $data->network_display_name;
                $isRequiredTextarea = (in_array($data->networkId, $this->isCommentProfile)) ? '' : 'required="required"';

                //ShortText
                if (isset($this->setShortTextProfile[$data->networkId]) && (int) $this->setShortTextProfile[$data->networkId] > 0) {
                    $preContent = ($data->networkId == 2) ? B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang) : B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang);
                    $message = B2S_Util::getExcerpt($preContent, (int) $this->setShortTextProfile[$data->networkId], (int) $this->setShortTextProfileLimit[$data->networkId]);
                } else {
                    $message = (in_array($data->networkId, $this->allowTitleProfile) ? (in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, (in_array($data->networkId, $this->allowHtml) ? '<p><h1><h2><br><i><b><a><img>' : false), (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang));
                }

                //Feature Image Html-Network
                if (in_array($data->networkId, $this->allowHtml)) {
                    $featuredImage = wp_get_attachment_url(get_post_thumbnail_id($this->postId));
                    if ($featuredImage !== false) {
                        $title = in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang);
                        $message = '<img class="b2s-post-item-details-image-html-src" src="' . $featuredImage . '" alt="' . $title . '"/><br />' . $message;
                    }
                }

                //Hashtags
                if (in_array($data->networkId, $this->allowHashTags)) {
                    $message .= $this->getHashTagsString();
                }

                $message = $this->hook_message($message);

                $countCharacter = 0;
                if ($limit !== false) {
                    $infoCharacterCount = ($data->networkId != 2) ? ' (' . __('Text only', 'blog2social') . ')' : '';
                    $textareaLimitInfo .= '<span class="b2s-post-item-countChar" data-network-auth-id="' . $data->networkAuthId . '">' . (int) $countCharacter . '</span>/' . $limitValue . ' ' . __('characters', 'blog2social') . $infoCharacterCount . '</span>';
                    $textareaOnKeyUp = 'onkeyup="networkLimitAll(\'' . $data->networkAuthId . '\',\'' . $data->networkId . '\',\'' . $limitValue . '\');"';
                } else {
                    $textareaOnKeyUp = 'onkeyup="networkCount(\'' . $data->networkAuthId . '\');"';
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-auth-id="' . $data->networkAuthId . '">' . (int) $countCharacter . '</span> ' . __('characters', 'blog2social') . '</span>';
                }

                break;
            case '1':
                //page
                if (isset($this->limitCharacterPage[$data->networkId]) && (int) $this->limitCharacterPage[$data->networkId] > 0) {
                    $limitValue = $this->limitCharacterPage[$data->networkId];
                    $limit = true;
                }
                $infoImage = (in_array($data->networkId, $this->allowNoImagePage)) ? __('Network does not support image for pages', 'blog2social') . '!' : '';
                $infoImage .= (in_array($data->networkId, $this->allowNoEmoji)) ? (!empty($infoImage) ? ' | ' : '') . __('Network does not support emojis', 'blog2social') . '!' : '';

                //ShortText
                if (isset($this->setShortTextPage[$data->networkId]) && (int) $this->setShortTextPage[$data->networkId] > 0) {
                    if ($data->networkId == 8) { //Xing -1 Leerzeichen
                        $this->setShortTextPage[$data->networkId] = (int) $this->setShortTextPage[$data->networkId] - mb_strlen($this->postUrl, 'UTF-8') - 1;
                    }
                    $message = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang), (int) $this->setShortTextPage[$data->networkId], (int) $this->setShortTextPageLimit[$data->networkId]);
                } else {
                    $message = (in_array($data->networkId, $this->allowTitlePage) ? (in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, (in_array($data->networkId, $this->allowHtml) ? '<p><h1><h2><br><i><b><a><img>' : false), (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang));
                }

                //Hashtags
                if (in_array($data->networkId, $this->allowHashTags)) {
                    $message .= $this->getHashTagsString();
                }

                $message = $this->hook_message($message);

                $network_display_name = $data->network_display_name;
                $isRequiredTextarea = (in_array($data->networkId, $this->isCommentPage)) ? '' : 'required="required"';

                $countCharacter = 0;
                if ($limit !== false) {
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-auth-id="' . $data->networkAuthId . '">' . (int) $countCharacter . '</span>/' . $limitValue . ' ' . __('characters', 'blog2social') . '</span>';
                    $textareaOnKeyUp = 'onkeyup="networkLimitAll(\'' . $data->networkAuthId . '\',\'' . $data->networkId . '\',\'' . $limitValue . '\');"';
                } else {
                    $textareaOnKeyUp = 'onkeyup="networkCount(\'' . $data->networkAuthId . '\');"';
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-auth-id="' . $data->networkAuthId . '">' . (int) $countCharacter . '</span> ' . __('characters', 'blog2social') . '</span>';
                }
                break;
            case'2':
                //group
                //ShortText
                if (isset($this->setShortTextGroup[$data->networkId]) && (int) $this->setShortTextGroup[$data->networkId] > 0) {
                    $message = B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang), (int) $this->setShortTextGroup[$data->networkId], (int) $this->setShortTextGroupLimit[$data->networkId]);
                } else {
                    $message = (in_array($data->networkId, $this->allowTitleGroup) ? (in_array($data->networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang)) : B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, (in_array($data->networkId, $this->allowHtml) ? '<p><h1><h2><br><i><b><a><img>' : false), (in_array($data->networkId, $this->allowNoEmoji) ? false : true), $this->userLang));
                }
                //Hashtags
                if (in_array($data->networkId, $this->allowHashTags)) {
                    $message .= $this->getHashTagsString();
                }

                $message = $this->hook_message($message);
                $network_display_name = in_array($data->networkId, $this->hideGroupName) ? '' : $data->network_display_name;
                $isRequiredTextarea = (in_array($data->networkId, $this->isCommentGroup)) ? '' : 'required="required"';

                $countCharacter = 0;
                if ($limit !== false) {
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-auth-id="' . $data->networkAuthId . '">' . (int) $countCharacter . '</span>/' . $limitValue . ' ' . __('characters', 'blog2social') . '</span>';
                    $textareaOnKeyUp = 'onkeyup="networkLimitAll(\'' . $data->networkAuthId . '\',\'' . $data->networkId . '\',\'' . $limitValue . '\');"';
                } else {
                    $textareaOnKeyUp = 'onkeyup="networkCount(\'' . $data->networkAuthId . '\');"';
                    $textareaLimitInfo = '<span class="b2s-post-item-countChar" data-network-auth-id="' . $data->networkAuthId . '">' . (int) $countCharacter . '</span> ' . __('characters', 'blog2social') . '</span>';
                }
                break;
        }


        //Infotexte
        $messageInfo = (!empty($infoImage)) ? '<p class="b2s-post-item-message-info pull-left hidden-sm hidden-xs">' . $infoImage . '</p>' : '';
        $onlyimage = in_array($data->networkId, $this->onlyImage) ? 'b2sOnlyWithImage' : '';

        $content = '<div class="b2s-post-item ' . $onlyimage . '" data-network-auth-id="' . $data->networkAuthId . '" data-network-id="' . $data->networkId . '">';
        $content .= '<div class="panel panel-group">';
        $content .= '<div class="panel-body ' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'del-padding-left del-padding-right' : '') . ' ">';
        $content .= '<div class="b2s-post-item-area">';
        $content .= '<div class="b2s-post-item-thumb hidden-xs">';
        $content .= '<img alt="" data-network-auth-id="' . $data->networkAuthId . '" class="img-responsive b2s-post-item-network-image" src="' . plugins_url('/assets/images/portale/' . $data->networkId . '_flat.png', B2S_PLUGIN_FILE) . '">';
        $content .= '</div>';
        $content .= '<div class="b2s-post-item-details">';
        $content .= '<h4 class="pull-left b2s-post-item-details-network-display-name" data-network-auth-id="' . $data->networkAuthId . '">' . stripslashes($network_display_name) . '</h4>';
        $content .= '<div class="clearfix"></div>';
        $content .= '<p class="pull-left">' . $networkTypeName[$data->networkType] . ' | ' . $neworkName[$data->networkId];
        $content .= '<div class="b2s-post-item-details-message-result" data-network-auth-id="' . $data->networkAuthId . '" style="display:none;"></div>';
        $content .= '<span class="hidden-xs b2s-post-item-details-message-info" data-network-auth-id="' . $data->networkAuthId . '">' . $messageInfo . '</span></span>';

        $content .= '<div class="pull-right hidden-xs b2s-post-item-info-area">';

        if (in_array($data->networkId, $this->setPostFormat)) {
            $addCSS = (B2S_PLUGIN_USER_VERSION == 0) ? 'b2s-btn-disabled' : '';
            $content .= '<button class="btn btn-xs btn-link b2s-post-ship-item-post-format ' . $addCSS . '" data-network-auth-id="' . $data->networkAuthId . '" data-network-type="' . $data->networkType . '" data-network-id="' . $data->networkId . '" >' . __('post format', 'blog2social') . ': <span class="b2s-post-ship-item-post-format-text" data-network-auth-id="' . $data->networkAuthId . '" data-network-type="' . $data->networkType . '"  data-network-id="' . $data->networkId . '" ></span></button>';
            if (B2S_PLUGIN_USER_VERSION > 0) {
                $content .= '<input type="hidden" class="b2s-post-item-details-post-format" name="b2s[' . $data->networkAuthId . '][post_format]" data-network-auth-id="' . $data->networkAuthId . '" data-network-id="' . $data->networkId . '" data-network-type="' . $data->networkType . '" value="0" />';
            } else {
                $content .= '<span class="label label-success"><a target="_blank" class="btn-label-premium b2s-btn-trigger-post-ship-item-post-format" data-network-auth-id="' . $data->networkAuthId . '" href="#">PREMIUM</a></span>';
            }
            $content .='  | ';
        }
        if (in_array($data->networkId, $this->getText)) {
            $content .= '<button class="btn btn-xs btn-link b2s-post-ship-item-full-text" data-network-auth-id="' . $data->networkAuthId . '" >' . __('Insert full-text', 'blog2social') . '</button> | ';
        }
        $content .= '<button class="btn btn-xs btn-link b2s-post-ship-item-message-delete" data-network-auth-id="' . $data->networkAuthId . '">' . __('Delete text', 'blog2social') . '</button> | ';
        $content .= $textareaLimitInfo . '</div>';

        $content .= '</p>';

        $content .= '<div class="b2s-post-item-details-edit-area" data-network-auth-id="' . $data->networkAuthId . '">';
        $content .= in_array($data->networkId, $this->showBoards) ? $this->getBoardHtml($data->networkAuthId, $data->networkId) : '';
        $content .= (in_array($data->networkId, $this->showGroups) && ($data->networkType == 2 || $data->networkId == 15)) ? $this->getGroupsHtml($data->networkAuthId, $data->networkId) : '';
        $content .= ((in_array($data->networkId, $this->showTitleProfile) && $data->networkType == 0) || (in_array($data->networkId, $this->showTitlePage) && $data->networkType == 1) || (in_array($data->networkId, $this->showTitleGroup) && $data->networkType == 2)) ? $this->getTitleHtml($data->networkId, $data->networkAuthId, $this->postData->post_title) : '';
        $content .= $this->getCustomEditArea($data->networkId, $data->networkAuthId, $data->networkType, $message, $isRequiredTextarea, $textareaOnKeyUp, $limit, $limitValue, isset($data->image_url) ? $data->image_url : null);
        $content .= (in_array($data->networkId, $this->allowTag)) ? $this->getTagsHtml($data->networkAuthId) : '';
        if ($show_time) {
            $content .= $this->getShippingTimeHtml($data->networkAuthId, $data->networkType, $data->networkId);
        }
        $content .= '</div>';

        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<input type="hidden" class="form-control" name="b2s[' . $data->networkAuthId . '][network_id]" value="' . $data->networkId . '">';
        $content .= '<input type="hidden" class="form-control" name="b2s[' . $data->networkAuthId . '][network_type]" value="' . $data->networkType . '">';
        $content .= '<input type="hidden" class="form-control" name="b2s[' . $data->networkAuthId . '][network_display_name]" value="' . $data->network_display_name . '">';

        $content .= '</div>';
        return $content;
    }

    public function getCustomEditArea($networkId, $networkAuthId, $networkType, $message, $isRequiredTextarea, $textareaOnKeyUp, $limit, $limitValue, $imageUrl = null) {
        $meta = array();
        if ($networkId == 1 || ($networkId == 8 && $networkType == 0) || $networkId == 3 || $networkId == 2) {
            if (trim(strtolower($this->postStatus)) == 'publish') {
               //is calendar edit => scrape post url and not custom post url by override from edit function for meta tags!
               $editPostUrl = ($this->viewMode == 'modal') ? (get_permalink($this->postData->ID) !== false ? get_permalink($this->postData->ID) : $this->postData->guid) : $this->postUrl;
               $meta = B2S_Util::getMetaTags($this->postId, $editPostUrl);
            } else {
                $meta = array('title' => B2S_Util::getExcerpt(B2S_Util::getTitleByLanguage($this->postData->post_title, $this->userLang), 50) . ' - ' . $this->websiteName, 'description' => B2S_Util::getExcerpt(B2S_Util::prepareContent($this->postId, $this->postData->post_content, $this->postUrl, false, (in_array($networkId, $this->allowNoEmoji) ? false : true), $this->userLang), 150));
            }
            //EDIT Function - Calendar
            $meta = $this->hook_meta($meta);
            $imageUrl = $imageUrl ? $imageUrl : (isset($meta['image']) ? $meta['image'] : null);

            if ($networkId == 1) {
                $edit = '<textarea class="form-control fb-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-text-limit="' . $limitValue . '" data-network-auth-id="' . $networkAuthId . '" placeholder="' . __('Write something about your post...', 'blog2social') . '" name="b2s[' . $networkAuthId . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . $message . '</textarea>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                $edit .= '<button class="btn btn-danger btn-circle b2s-image-remove-btn" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-remove"></i></button>';
                $edit .= '<img src="' . (isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage) . '" class="fb-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-id="' . $networkId . '" data-network-image-change="1" data-network-auth-id="' . $networkAuthId . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" value="' . ($imageUrl ? $imageUrl : "") . '" name="b2s[' . $networkAuthId . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="og" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" data-post-id="' . $this->postId . '" data-image-url="' . esc_attr($imageUrl) . '">' . __('Change image for this network', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-7 col-lg-9') . '">';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $edit .= '<button data-network-auth-id="' . $networkAuthId . '" data-meta-type="og" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . __('Info: Change Open Graph Meta tags image, title and description for this network', 'blog2social') . '</button>';
                } else {
                    $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship" href="#"><span class="label label-success">PREMIUM</span></a>';
                    $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="og" data-meta-origin="ship">' . __('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                }
                $edit .= '<input type="text" readonly class="form-control fb-url-title b2s-post-item-details-preview-title change-meta-tag og_title" placeholder="' . __('OG Meta title', 'blog2social') . '" name="b2s[' . $networkAuthId . '][og_title]"  data-meta="og_title" data-meta-type="og" data-network-auth-id="' . $networkAuthId . '" value="' . (isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '') . '" />';
                $edit .= '<input type="text" readonly class="form-control fb-url-desc b2s-post-item-details-preview-desc change-meta-tag og_desc" placeholder="' . __('OG Meta description', 'blog2social') . '" name="b2s[' . $networkAuthId . '][og_desc]" data-meta="og_desc"  data-meta-type="og" data-network-auth-id="' . $networkAuthId . '" value="' . (isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '' ) . '" />';
                $edit .= $this->getUrlHtml($networkId, $networkAuthId, $limit, $limitValue, true, 'fb-url-input', true, $imageUrl);
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 2) {
                $edit = '<textarea class="form-control tw-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-text-limit="' . $limitValue . '" data-network-auth-id="' . $networkAuthId . '" placeholder="' . __('Write something about your post...', 'blog2social') . '" name="b2s[' . $networkAuthId . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . $message . '</textarea>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                $edit .= '<button class="btn btn-danger btn-circle b2s-image-remove-btn" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-remove"></i></button>';
                $edit .= '<img src="' . (isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage) . '" class="tw-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-id="' . $networkId . '" data-network-image-change="1" data-network-auth-id="' . $networkAuthId . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" value="' . ($imageUrl ? $imageUrl : "") . '" name="b2s[' . $networkAuthId . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-meta-type="card" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" data-post-id="' . $this->postId . '" data-image-url="' . esc_attr($imageUrl) . '">' . __('Change image for this network', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-7 col-lg-9') . '">';
                if (B2S_PLUGIN_USER_VERSION > 0) {
                    $edit .= '<button data-network-auth-id="' . $networkAuthId . '" data-meta-type="card" data-meta-origin="ship" class=" btn btn-xs hidden-xs btn-link b2s-load-info-meta-tag-modal">' . __('Info: Change Card Meta tags image, title and description for this network', 'blog2social') . '</button>';
                } else {
                    $edit .= '<a target="_blank" class="btn-label-premium btn-label-premium-xs b2s-load-info-meta-tag-modal" data-meta-type="card" data-meta-origin="ship" href="#"><span class="label label-success">PREMIUM</span></a>';
                    $edit .= '<a href="#" class="btn btn-link btn-xs b2s-load-info-meta-tag-modal" data-meta-type="card" data-meta-origin="ship">' . __('You want to change your link image, link title and link description for this network? Click here.', 'blog2social') . '</a> ';
                }
                $edit .= '<input type="text" readonly class="form-control tw-url-title b2s-post-item-details-preview-title change-meta-tag card_title"  placeholder="' . __('Card Meta title', 'blog2social') . '" name="b2s[' . $networkAuthId . '][card_title]"  data-meta="card_title" data-meta-type="card" data-network-auth-id="' . $networkAuthId . '" value="' . (isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '') . '" />';
                $edit .= '<input type="text" readonly class="form-control tw-url-desc b2s-post-item-details-preview-desc change-meta-tag card_desc"  placeholder="' . __('Card Meta description', 'blog2social') . '" name="b2s[' . $networkAuthId . '][card_desc]"  data-meta="card_desc" data-meta-type="card" data-network-auth-id="' . $networkAuthId . '" value="' . (isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '' ) . '" />';
                $edit .= $this->getUrlHtml($networkId, $networkAuthId, $limit, $limitValue, true, 'tw-url-input', true);
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 3) {
                $edit = '<textarea class="form-control linkedin-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-text-limit="' . $limitValue . '" data-network-auth-id="' . $networkAuthId . '" placeholder="' . __('Write something about your post...', 'blog2social') . '" name="b2s[' . $networkAuthId . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . $message . '</textarea>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '" >';
                $edit .= '<button class="btn btn-danger btn-circle b2s-image-remove-btn" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-remove"></i></button>';
                $edit .= '<img src="' . (isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage) . '" class="linkedin-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-id="' . $networkId . '" data-network-image-change="1" data-network-auth-id="' . $networkAuthId . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" value="' . ($imageUrl ? $imageUrl : "") . '" name="b2s[' . $networkAuthId . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" data-post-id="' . $this->postId . '" data-image-url="' . esc_attr($imageUrl) . '">' . __('Change image for this network', 'blog2social') . '</button>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-7 col-lg-9') . '">';
                $edit .= '<p class="linkedin-url-title b2s-post-item-details-preview-title hidden-xs" data-network-auth-id="' . $networkAuthId . '">' . (isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '') . '</p>';
                $edit .= $this->getUrlHtml($networkId, $networkAuthId, $limit, $limitValue, true, 'linkedin-url-input', true);
                $edit .= '<p class="linkedin-url-desc b2s-post-item-details-preview-desc hidden-xs" data-network-auth-id="' . $networkAuthId . '">' . (isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '' ) . '</p>';
                $edit .= '</div>';
                $edit .= '</div>';
            }

            if ($networkId == 8 && $networkType == 0) {
                $edit = '<textarea class="form-control xing-textarea-input b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-text-limit="' . $limitValue . '" data-network-auth-id="' . $networkAuthId . '" placeholder="' . __('Write something about your post...', 'blog2social') . '" name="b2s[' . $networkAuthId . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . $message . '</textarea>';
                $edit .= '<div class="row">';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                $edit .= '<img src="' . (isset($meta['image']) && !empty($meta['image']) ? $meta['image'] : $this->defaultImage) . '" class="xing-url-image b2s-post-item-details-url-image center-block img-responsive" data-network-id="' . $networkId . '" data-network-image-change="0" data-network-auth-id="' . $networkAuthId . '">';
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" value="' . ($imageUrl ? $imageUrl : "") . '" name="b2s[' . $networkAuthId . '][image_url]">';
                $edit .= '<div class="clearfix"></div>';
                $edit .= '</div>';
                $edit .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-7 col-lg-9') . '">';
                $edit .= '<p class="xing-url-title b2s-post-item-details-preview-title hidden-xs" data-network-auth-id="' . $networkAuthId . '">' . (isset($meta['title']) && !empty($meta['title']) ? $meta['title'] : '') . '</p>';
                $edit .= '<span class="xing-url-desc b2s-post-item-details-preview-desc hidden-xs" data-network-auth-id="' . $networkAuthId . '">' . (isset($meta['description']) && !empty($meta['description']) ? $meta['description'] : '' ) . '</span>';
                $edit .= $this->getUrlHtml($networkId, $networkAuthId, $limit, $limitValue, true, 'xing-url-input', true);
                $edit .= '</div>';
                $edit .= '</div>';
            }
        } else {
            $edit = '<textarea class="form-control b2s-post-item-details-item-message-input ' . (in_array($networkId, $this->allowHtml) ? 'b2s-post-item-details-item-message-input-allow-html' : '') . '" data-network-text-limit="' . $limitValue . '" data-network-auth-id="' . $networkAuthId . '" placeholder="' . __('Write something about your post...', 'blog2social') . '" name="b2s[' . $networkAuthId . '][content]" ' . $isRequiredTextarea . ' ' . $textareaOnKeyUp . '>' . $message . '</textarea>';

            //EDIT Function - Calendar
            $meta = $this->hook_meta(array());
            $imageUrl = $imageUrl ? $imageUrl : (isset($meta['image']) ? $meta['image'] : null);
            $edit .= $this->getUrlHtml($networkId, $networkAuthId, $limit, $limitValue, false, '', false, $imageUrl);
            if ($networkId == 14) {  //FeatureImage Network Torial (Portfolio)
                $edit .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" value="' . ($imageUrl ? $imageUrl : "") . '" name="b2s[' . $networkAuthId . '][image_url]">';
            }
        }
        return $edit;
    }

    private function getUrlHtml($networkId, $networkAuthId, $limit, $limitValue, $hideInfo = false, $class = '', $refeshBtn = false, $imageUrl = null) {
        if (in_array($networkId, $this->allowEditUrl)) {
            $urlLimit = ($limit !== false) ? ' onkeyup="networkLimitAll(\'' . $networkAuthId . '\',\'' . $networkId . '\',\'' . $limitValue . '\');"' : 'onkeyup="networkCount(\'' . $networkAuthId . '\');"';
            $isRequiredClass = (in_array($networkId, $this->requiredUrl)) ? 'required_network_url' : '';
            $isRequiredText = (!empty($isRequiredClass)) ? '<small>(' . __('required', 'blog2social') . ')</small>' : '';

            $url = '';
            if (in_array($networkId, $this->showImageArea)) {
                $url .= '<br>';
                $url .= '<div class="row">';
                $url .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-5 col-lg-3') . '">';
                $url .= '<div>';
                $url .= '<button class="btn btn-danger btn-circle b2s-image-remove-btn" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" ' . ($imageUrl ? '' : 'style="display:none"') . '><i class="glyphicon glyphicon-remove"></i></button>';
                $url .= '<img src="' . (($imageUrl != null) ? $imageUrl : $this->defaultImage) . '" class="b2s-post-item-details-url-image center-block img-responsive b2s-image-border" data-network-id="' . $networkId . '" data-network-image-change="1" data-network-auth-id="' . $networkAuthId . '">';
                $url .= '<input type="hidden" class="b2s-image-url-hidden-field form-control" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" value="' . (($imageUrl != null) ? $imageUrl : "") . '" name="b2s[' . $networkAuthId . '][image_url]">';
                $url .= '</div>';
                $url .= '<div class="clearfix"></div>';
                $url .= '<button class="btn btn-link btn-xs center-block b2s-select-image-modal-open" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" data-post-id="' . $this->postId . '" data-image-url="' . esc_attr($imageUrl) . '">' . __('Change image for this network', 'blog2social') . '</button></div>';
                $url .= '<div class="' . ((isset($this->viewMode) && $this->viewMode == 'modal') ? 'col-xs-12' : 'col-xs-12 col-sm-7 col-lg-9') . '">';
            }

            $url .= (!$hideInfo) ? '<div class="b2s-post-item-details-url-title hidden-xs">Link ' . $isRequiredText . '</div>' : '';

            if ($refeshBtn) {
                $url .= '<div class="input-group"><input class="form-control ' . $class . ' b2s-post-item-details-item-url-input ' . $isRequiredClass . ' complete_network_url" name="b2s[' . $networkAuthId . '][url]" ' . $urlLimit . ' placeholder="' . __('Link', 'blog2social') . '" data-network-auth-id="' . $networkAuthId . '" value="' . $this->postUrl . '" name="b2s[' . $networkAuthId . '][url]"/><span class="input-group-addon"><span class="glyphicon glyphicon-refresh b2s-post-item-details-preview-url-reload" data-network-auth-id="' . $networkAuthId . '" data-network-id="' . $networkId . '" aria-hidden="true"></span></span></div>';
            } else {
                $url .= '<input class="form-control ' . $class . ' b2s-post-item-details-item-url-input ' . $isRequiredClass . ' complete_network_url" name="b2s[' . $networkAuthId . '][url]" ' . $urlLimit . ' placeholder="' . __('Link', 'blog2social') . '" data-network-auth-id="' . $networkAuthId . '" value="' . $this->postUrl . '" name="b2s[' . $networkAuthId . '][url]"/>';
            }
            if (in_array($networkId, $this->showImageArea)) {
                $url .= '</div>';
                $url .= '</div>';
                $url .= '<div class="col-xs-12"><br></div>';
            }
        } else {
            $url = '<input type="hidden" name="b2s[' . $networkAuthId . '][url]" value="' . $this->postUrl . '">';
        }


        return $url;
    }

    protected function hook_message($message) {
        return $message;
    }

    protected function hook_meta(array $meta) {
        return $meta;
    }

    private function getHashTagsString($add = "\n\n") {
        $hashTagsData = get_the_tags($this->postId);
        $hashTags = '';
        if (is_array($hashTagsData) && !empty($hashTagsData)) {
            foreach ($hashTagsData as $tag) {
                $hashTags .= ' #' . trim($tag->name);
            }
        }
        return (!empty($hashTags) ? (!empty($add) ? $add . $hashTags : $hashTags) : '');
    }

    private function getBoardHtml($networkAuthId, $networkId) {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getBoards', 'token' => B2S_PLUGIN_TOKEN, 'networkAuthId' => $networkAuthId, 'networkId' => $networkId)));
        $board = '<select class="form-control b2s-select" name="b2s[' . $networkAuthId . '][board]">';
        if (is_object($result) && !empty($result) && isset($result->data) && !empty($result->data) && isset($result->result) && (int) $result->result == 1) {
            $board .= $result->data;
        }
        $board .= '</select>';
        return $board;
    }

    private function getGroupsHtml($networkAuthId, $networkId) {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getGroups', 'token' => B2S_PLUGIN_TOKEN, 'networkAuthId' => $networkAuthId, 'networkId' => $networkId, 'lang' => B2S_LANGUAGE)));
        $group = '<select class="form-control b2s-select b2s-post-item-details-item-group-select" name="b2s[' . $networkAuthId . '][group]">';
        if (is_object($result) && !empty($result) && isset($result->data) && !empty($result->data) && isset($result->result) && (int) $result->result == 1) {
            $group .= $result->data;
        }
        $group .= '</select>';
        return $group;
    }

    private function getTitleHtml($networkId, $networkdAutId, $title) {
        $title = in_array($networkId, $this->allowNoEmoji) ? B2S_Util::remove4byte(B2S_Util::getTitleByLanguage($title, $this->userLang)) : B2S_Util::getTitleByLanguage($title, $this->userLang);
        return '<input type="text" name="b2s[' . $networkdAutId . '][custom_title]" class="form-control b2s-post-item-details-item-title-input" data-network-auth-id="' . $networkdAutId . '" placeholder="' . __('The Headline...', 'blog2social') . '" required="required" maxlength="254" value="' . $title . '" />';
    }

    private function getTagsHtml($networkAuthId) {
        $tags = '<div class="b2s-post-item-details-tag-area">';
        $tags .= '<div class="b2s-post-item-details-tag-title"> ' . __('Hashtags', 'blog2social') . ' </div>';
        $tags .= '<div class="b2s-post-item-details-tag-input form-inline">';
        $posttags = get_the_tags($this->postId);
        $countTags = 0;
        if ($posttags) {
            foreach ($posttags as $tag) {
                $countTags += 1;
                $tags .= '<input class="form-control b2s-post-item-details-tag-input-elem" name="b2s[' . $networkAuthId . '][tags][]" data-network-auth-id="' . $networkAuthId . '" value="' . $tag->name . '">';
            }
        } else {
            $tags .= '<input class="form-control b2s-post-item-details-tag-input-elem" name="b2s[' . $networkAuthId . '][tags][]" data-network-auth-id="' . $networkAuthId . '" value="">';
        }
        $showRemoveTagBtn = ($countTags >= 2) ? '' : 'display:none;';
        $tags .= '<div class="form-control b2s-post-item-details-tag-add-div">';
        $tags .= '<span class="remove-tag-btn glyphicon glyphicon-minus" data-network-auth-id="' . $networkAuthId . '" style="' . $showRemoveTagBtn . '" onclick="removeTag(\'' . $networkAuthId . '\');" ></span>';
        $tags .= '<span class="ad-tag-btn glyphicon glyphicon-plus" data-network-auth-id="' . $networkAuthId . '" onclick="addTag(\'' . $networkAuthId . '\');" ></span>';
        $tags .= '</div>';
        $tags .= '</div>';
        $tags .= '</div>';

        return $tags;
    }

    private function getShippingTimeHtml($networkAuthId, $networkTyp, $networkId) {

        $isSelectedSched = (B2S_PLUGIN_USER_VERSION > 0 && trim(strtolower($this->postStatus)) == 'future') ? 'selected="selected"' : '';
        $isSelectedNow = (empty($isSelectedSched)) ? 'selected="selected"' : '';

        $shipping = '<br>';
        $shipping .= '<select name="b2s[' . $networkAuthId . '][releaseSelect]" data-user-version="' . B2S_PLUGIN_USER_VERSION . '" data-network-type="' . $networkTyp . '" data-network-id="' . $networkId . '" data-network-auth-id="' . $networkAuthId . '" class="form-control b2s-select b2s-post-item-details-release-input-date-select ' . (B2S_PLUGIN_USER_VERSION == 0 ? 'b2s-post-item-details-release-input-date-select-reset' : '') . '" >';
        $shipping .= '<option value="0" ' . $isSelectedNow . '>' . __('Share Now', 'blog2social') . '</option>';

        $isPremium = (B2S_PLUGIN_USER_VERSION == 0) ? ' [' . __("PREMIUM", "blog2social") . ']' : '';
        $shipping .= '<option value="1" ' . $isSelectedSched . '>' . __('Schedule post once', 'blog2social') . $isPremium . '</option>';
        if ($networkTyp != 2 && !in_array($networkId, $this->noScheduleRegularly)) {
            $shipping .= '<option value="2">' . __('Schedule Recurrent Post', 'blog2social') . $isPremium . '</option>';
        }

        $shipping .= '</select>';

        if (B2S_PLUGIN_USER_VERSION > 0) {
            $shipping .= '<div class="b2s-post-item-details-release-area-details">';
            $shipping .= '<ul class="list-group b2s-post-item-details-release-area-details-ul" data-network-auth-id="' . $networkAuthId . '" style="display:none;">';
            $shipping .= '<li class="list-group-item">';

            //Sched post
            $time = time();
            if (trim(strtolower($this->postStatus)) == 'future') {
                $time = strtotime($this->postData->post_date);
            }
            if (date('H') == '23' && date('i') >= 30) {
                $time = strtotime('+ 1 days');
            }
            $currentDate = (substr(B2S_LANGUAGE, 0, 2) == 'de') ? date('d.m.Y', $time) : date('Y-m-d', $time);

            for ($schedcount = 0; $schedcount < $this->maxSchedCount; $schedcount++) {
                $shipping .= '<div class="form-group b2s-post-item-details-releas-area-details-row" data-network-count="' . $schedcount . '"  data-network-auth-id="' . $networkAuthId . '" style="display:none">';

                $shipping .= $schedcount != 0 ? '<div class="clearfix"></div><hr class="b2s-hr-small">' : '';

                $shipping .= '<label class="col-xs-2 del-padding-left b2s-post-item-details-release-area-label-duration" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '">' . __('Duration', 'blog2social') . '</label>';
                $shipping .= '<label class="col-xs-2 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '">' . __('Date', 'blog2social') . '</label>';
                $shipping .= '<label class="col-xs-2 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '">' . __('Time', 'blog2social') . '</label>';
                $shipping .= '<label class="col-xs-6 del-padding-left b2s-post-item-details-release-area-label-day" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '">' . __('Days', 'blog2social') . '</label>';

                $shipping .= '<div class="clearfix"></div>';

                if ($networkTyp != 2 && !in_array($networkId, $this->noScheduleRegularly)) {
                    $shipping .= '<div class="col-xs-2 del-padding-left b2s-post-item-details-release-area-div-duration" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '"><select name="b2s[' . $networkAuthId . '][weeks][' . $schedcount . ']" class="form-control b2s-select b2s-post-item-details-release-input-weeks" data-network-count="' . $schedcount . '"  data-network-auth-id="' . $networkAuthId . '" style="display:none;" disabled="disabled">';
                    $defaultWeek = isset($this->defaultScheduleTime[$networkId][$schedcount]['weeks']) ? $this->defaultScheduleTime[$networkId][$schedcount]['weeks'] : 1;
                    for ($i = 1; $i <= $this->maxWeekTimeSelect; $i++) {
                        $weekName = ($i == 1) ? __('Week', 'blog2social') : __('Weeks', 'blog2social');
                        $shipping .= '<option value="' . $i . '" ' . ($defaultWeek == $i ? 'selected="selected"' : '') . '>' . $i . ' ' . $weekName . '</option>';
                    }
                    $shipping .= '</select></div>';
                }
                $shipping .= '<div class="col-xs-2 del-padding-left b2s-post-item-details-release-area-label-date" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '"><input type="text" placeholder="' . __('Date', 'blog2social') . '" name="b2s[' . $networkAuthId . '][date][' . $schedcount . ']" data-network-id="' . $networkId . '" data-network-type="' . $networkTyp . '" data-network-count="' . $schedcount . '" data-network-auth-id="' . $networkAuthId . '"  class="b2s-post-item-details-release-input-date form-control" style="display:none;"  disabled="disabled" readonly value="' . $currentDate . '"></div>';
                $shipping .= '<div class="col-xs-2 del-padding-left b2s-post-item-details-release-area-label-time" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '"><input type="text" placeholder="' . __('Time', 'blog2social') . '" name="b2s[' . $networkAuthId . '][time][' . $schedcount . ']" data-network-id="' . $networkId . '" data-network-type="' . $networkTyp . '" data-network-count="' . $schedcount . '"  data-network-auth-id="' . $networkAuthId . '"  class="b2s-post-item-details-release-input-time form-control" style="display:none;" disabled="disabled" readonly value=""></div>';
                $shipping .= '<div class="col-xs-5 del-padding-left b2s-post-item-details-release-area-label-day" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '">';
                if ($networkTyp != 2 && !in_array($networkId, $this->noScheduleRegularly)) {
                    $shipping .= '<div class="b2s-post-item-details-release-input-daySelect" data-network-count="' . $schedcount . '"  data-network-auth-id="' . $networkAuthId . '"  style="display:none;">';
                    $shipping .= '<input id="b2s-' . $networkAuthId . '-' . $schedcount . '-mo" type="checkbox" name="b2s[' . $networkAuthId . '][mo][' . $schedcount . ']" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-mo" value="1" disabled="disabled"><label for="b2s-' . $networkAuthId . '-' . $schedcount . '-mo" class="b2s-post-item-details-release-input-lable-day">' . __('Mon', 'blog2social') . '</label>'; //MO
                    $shipping .= '<input id="b2s-' . $networkAuthId . '-' . $schedcount . '-di" type="checkbox" name="b2s[' . $networkAuthId . '][di][' . $schedcount . ']" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-di" value="1" disabled="disabled"><label for="b2s-' . $networkAuthId . '-' . $schedcount . '-di" class="b2s-post-item-details-release-input-lable-day">' . __('Tue', 'blog2social') . '</label>'; //Di
                    $shipping .= '<input id="b2s-' . $networkAuthId . '-' . $schedcount . '-mi" type="checkbox" name="b2s[' . $networkAuthId . '][mi][' . $schedcount . ']" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-mi" value="1" disabled="disabled"><label for="b2s-' . $networkAuthId . '-' . $schedcount . '-mi" class="b2s-post-item-details-release-input-lable-day">' . __('Wed', 'blog2social') . '</label>'; //Mi
                    $shipping .= '<input id="b2s-' . $networkAuthId . '-' . $schedcount . '-do" type="checkbox" name="b2s[' . $networkAuthId . '][do][' . $schedcount . ']" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-do" value="1" disabled="disabled"><label for="b2s-' . $networkAuthId . '-' . $schedcount . '-do" class="b2s-post-item-details-release-input-lable-day">' . __('Thu', 'blog2social') . '</label>'; //Do
                    $shipping .= '<input id="b2s-' . $networkAuthId . '-' . $schedcount . '-fr" type="checkbox" name="b2s[' . $networkAuthId . '][fr][' . $schedcount . ']" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-fr" value="1" disabled="disabled"><label for="b2s-' . $networkAuthId . '-' . $schedcount . '-fr" class="b2s-post-item-details-release-input-lable-day">' . __('Fri', 'blog2social') . '</label>'; //Fr
                    $shipping .= '<input id="b2s-' . $networkAuthId . '-' . $schedcount . '-sa" type="checkbox" name="b2s[' . $networkAuthId . '][sa][' . $schedcount . ']" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-sa" value="1" disabled="disabled"><label for="b2s-' . $networkAuthId . '-' . $schedcount . '-sa" class="b2s-post-item-details-release-input-lable-day">' . __('Sat', 'blog2social') . '</label>'; //Sa
                    $shipping .= '<input id="b2s-' . $networkAuthId . '-' . $schedcount . '-so" type="checkbox" name="b2s[' . $networkAuthId . '][so][' . $schedcount . ']" data-network-auth-id="' . $networkAuthId . '" data-network-count="' . $schedcount . '" class="form-control b2s-post-item-details-release-input-days b2s-post-item-details-release-input-lable-day-so" value="1" disabled="disabled"><label for="b2s-' . $networkAuthId . '-' . $schedcount . '-so" class="b2s-post-item-details-release-input-lable-day">' . __('Sun', 'blog2social') . '</label>'; //So
                    $shipping .= '</div>';
                }
                $shipping .= '</div>';
                $shipping .= '<div class="col-xs-2 del-padding-left">';
                $shipping .= ( $schedcount >= 1) ? '<button class="btn btn-link b2s-post-item-details-release-input-hide"  data-network-count="' . $schedcount . '"  data-network-auth-id="' . $networkAuthId . '" data-network-count="1" style="display:none;">-' . __('delete', 'blog2social') . '</button>' : '';
                $shipping .= $schedcount < $this->maxSchedCount - 1 ? '<button class="btn btn-link b2s-post-item-details-release-input-add"  data-network-count="' . $schedcount . '"  data-network-auth-id="' . $networkAuthId . '" data-network-count="1" style="display:none;">+' . __('Add Posting Time', 'blog2social') . '</button>' : '';
                $shipping .= '</div>';
                $shipping .= '</div>';
            }
            $shipping .= '<div class="col-xs-12 del-padding-left">';
            $shipping .= '<label class="b2s-settings-time-zone-text"></label>';
            $shipping .= '<button class="btn btn-sm btn-link pull-right b2s-post-item-details-release-area-sched-for-all" data-network-auth-id="' . $networkAuthId . '">' . __('Apply Settings To All Networks', 'blog2social') . '</button>';
            $shipping .= '<label class="pull-right btn btn-link btn-sm b2s-post-item-details-release-save-settings-label" data-network-auth-id="' . $networkAuthId . '"><input class="b2s-post-item-details-release-save-settings" data-network-auth-id="' . $networkAuthId . '" type="checkbox" name="b2s[' . $networkAuthId . '][saveSchedSetting]" value="1" disabled="disabled">' . __('Save Settings As Default', 'blog2social') . '</label>';
            $shipping .= '</div><div class="clearfix"></div>';
            $shipping .= '</li>';
            $shipping .= '</ul>';
            $shipping .= '</div>';
        }
        return $shipping;
    }

    public function setPostUrl($value) {
        $this->postUrl = $value;
    }

    public function setTitle($value) {
        if ($this->postData) {
            $this->postData->post_title = $value;
        }
    }

}
