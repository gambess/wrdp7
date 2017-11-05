<?php

class B2S_Ship_Navbar {

    private $neworkName;
    private $networkTypeName;
    private $authUrl;
    private $allowProfil;
    private $allowPage;
    private $allowGroup;
    private $oAuthPortal;

    public function __construct() {
        $this->neworkName = unserialize(B2S_PLUGIN_NETWORK);
        $this->networkTypeName = unserialize(B2S_PLUGIN_NETWORK_TYPE);
        $this->authUrl = B2S_PLUGIN_API_ENDPOINT_AUTH . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2) . '&unset=true';
        $this->allowProfil = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PROFILE);
        $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $this->oAuthPortal = unserialize(B2S_PLUGIN_NETWORK_OAUTH);
    }

    public function getData() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getUserAuth', 'token' => B2S_PLUGIN_TOKEN,'version'=>B2S_PLUGIN_VERSION)));
        return array('mandanten' => isset($result->mandanten) ? $result->mandanten : '',
            'auth' => isset($result->auth) ? $result->auth : '',
            'portale' => isset($result->portale) ? $result->portale : '');
    }

    public function getSelectMandantHtml($data) {
        $select = '<select class="form-control b2s-network-details-mandant-select b2s-select">';
        $select .= '<option value="0" selected="selected">' . __('Default', 'blog2social') . '</option>';
        if (!empty($data)) {
            foreach ($data as $id => $name) {
                $select .= '<option value="' . $id . '">' . $name . '</option>';
            }
        }
        $select .= '</select>';
        return $select;
    }

    public function getItemHtml($data) {
        $username = stripslashes($data->networkUserName);
        $b2sAuthUrl = $this->authUrl . '&portal_id=' . $data->networkId . '&transfer=' . (in_array($data->networkId, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&mandant_id=' . $data->mandantId . '&version=3&affiliate_id='.B2S_Tools::getAffiliateId();
        $onclick = ($data->expiredDate != '0000-00-00' && $data->expiredDate <= date('Y-m-d')) ? ' onclick="wop(\'' . $b2sAuthUrl . '&choose=profil&update=' . $data->networkAuthId . '\', \'Blog2Social Network\'); return false;"' : '';

        global $wpdb;
        $mandantCount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(mandant_id)FROM b2s_user_network_settings  WHERE mandant_id =%d AND blog_user_id=%d ", $data->mandantId, B2S_PLUGIN_BLOG_USER_ID));
        $userSelected = $wpdb->get_results($wpdb->prepare("SELECT mandant_id FROM b2s_user_network_settings WHERE blog_user_id =%d AND network_auth_id = %d", B2S_PLUGIN_BLOG_USER_ID, $data->networkAuthId));

        $mandantIds = array();
        foreach ($userSelected as $key => $value) {
            $mandantIds[] = $value->mandant_id;
        }

        if ($mandantCount == 0) {
            $mandantIds[] = $data->mandantId;
        }
        //Bug: Converting json + PHP Extension
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            $username = (mb_strlen($username,'UTF-8') >= 19 ? (mb_substr($username, 0, 16,'UTF-8') . '...') : $username);
        }

        $content = '<li class="b2s-sidbar-wrapper-nav-li i" data-mandant-id=\'' . json_encode($mandantIds) . '\' data-mandant-default-id="' . $data->mandantId . '">';
        $content .= '<div class="b2s-network-select-btn ' . (($data->expiredDate != '0000-00-00' && $data->expiredDate <= date('Y-m-d')) ? 'b2s-network-select-btn-deactivate" ' . $onclick : '"') . ' data-network-auth-id="' . $data->networkAuthId . '" data-network-type="' . $data->networkType . '" data-network-id = "' . $data->networkId . '" data-network-display-name="' . strtolower($data->networkUserName) . '">'; 
        $content .= '<div class="b2s-network-list">';
        $content .= '<div class="b2s-network-thumb">';
        $content .= '<img alt="" src="' . plugins_url('/assets/images/portale/' . $data->networkId . '_flat.png', B2S_PLUGIN_FILE) . '">';
        $content .= '</div>';
        $content .= '<div class="b2s-network-details">';
        $content .= '<h4>' . $username . '</h4>';
        $content .= '<p>' . $this->networkTypeName[$data->networkType] . ' | ' . $this->neworkName[$data->networkId] . '</p>';
        $content .= '</div>';
        $content .= '<div class="b2s-network-status" data-network-auth-id="' . $data->networkAuthId . '">';
        $content .= '<span class="b2s-network-hide b2s-network-status-img glyphicon glyphicon-ok glyphicon-success"></span>';
        $content .= '<span class="b2s-network-status-no-img glyphicon glyphicon-danger glyphicon-ban-circle" data-network-auth-id="' . $data->networkAuthId . '" data-network-id="' . $data->networkId . '" style="display:none"></span>';
        $content .= ($data->expiredDate != '0000-00-00' && $data->expiredDate <= date('Y-m-d')) ? '<span class="b2s-network-status-expiredDate glyphicon glyphicon-danger glyphicon-refresh" data-network-auth-id="' . $data->networkAuthId . '"></span>' : '';
        $content .= '<div style="display:none;" class="b2s-network-status-img-loading b2s-loader-impulse b2s-loader-impulse-sm" data-network-auth-id="' . $data->networkAuthId . '"></div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '</li>';

        return $content;
    }

}
