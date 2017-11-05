<?php

class B2S_Ship_Portale {

    private $authurl;
    private $allowProfil;
    private $allowPage;
    private $allowGroup;
    private $oAuthPortal;

    public function __construct() {
        $this->authurl = B2S_PLUGIN_API_ENDPOINT_AUTH . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2);
        $this->allowProfil = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PROFILE);
        $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
        $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
        $this->oAuthPortal = unserialize(B2S_PLUGIN_NETWORK_OAUTH);
    }

    public function getItemHtml($portale) {
        $html = '<ul>';
        foreach ($portale as $k => $portal) {
            $html .= '<li>';
            $html .= '<img class="b2s-network-list-add-thumb" alt="' . $portal->name . '" src="' . plugins_url('/assets/images/portale/' . $portal->id . '_flat.png', B2S_PLUGIN_FILE) . '">';
            $html .= '<span class="b2s-network-list-add-details">' . $portal->name . '</span>';

            $b2sAuthUrl = $this->authurl . '&portal_id=' . $portal->id . '&transfer=' . (in_array($portal->id, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&version=3&affiliate_id=' . B2S_Tools::getAffiliateId();
            if (in_array($portal->id, $this->allowGroup)) {
                $html .= (B2S_PLUGIN_USER_VERSION > 1 || (B2S_PLUGIN_USER_VERSION == 1 && $portal->id != 8)) ? ('<button onclick="wop(\'' . $b2sAuthUrl . '&choose=group\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-list-add-btn">+ ' . __('Group', 'blog2social').'</button>') : '<button type="button" class="btn btn-primary btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled" data-type="auth-network" data-title="' . __('You want to connect a social media group?', 'blog2social') . '" data-toggle="modal" data-target="#'.((B2S_PLUGIN_USER_VERSION ==0) ? 'b2sPreFeatureModal' :'b2sProFeatureModal').'">+ ' . __('Group', 'blog2social').' <span class="label label-success">' . __("PREMIUM", "blog2social") . '</a></button>';
            }
            if (in_array($portal->id, $this->allowPage)) {
                $html .= (B2S_PLUGIN_USER_VERSION > 1 || (B2S_PLUGIN_USER_VERSION == 0 && $portal->id == 1) || (B2S_PLUGIN_USER_VERSION == 1 && ($portal->id == 1 || $portal->id == 10))) ? ('<button onclick="wop(\'' . $b2sAuthUrl . '&choose=page\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-list-add-btn">+ ' . __('Page', 'blog2social') . '</button>') : '<button type="button" class="btn btn-primary btn-sm b2s-network-list-add-btn b2s-network-list-add-btn-profeature b2s-btn-disabled" data-title="' . __('You want to connect a network page?', 'blog2social') . '" data-type="auth-network" data-toggle="modal" data-target="#'.((B2S_PLUGIN_USER_VERSION ==0) ? 'b2sPreFeatureModal' :'b2sProFeatureModal').'">+ ' . __('Page', 'blog2social') . ' <span class="label label-success">' . __("PREMIUM", "blog2social") . '</a></button>';
                
            }
            $html .= in_array($portal->id, $this->allowProfil) ? ('<a href="#" onclick="wop(\'' . $b2sAuthUrl . '&choose=profile\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-list-add-btn">+ ' . __('Profile', 'blog2social') . '</a>') : '';

            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

}
