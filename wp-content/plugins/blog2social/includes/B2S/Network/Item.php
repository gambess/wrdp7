<?php

class B2S_Network_Item {

    private $authurl;
    private $allowProfil;
    private $allowPage;
    private $allowGroup;
    private $oAuthPortal;
    private $mandantenId;

    public function __construct($load = true) {
        $this->mandantenId = array(-1, 0); //All,Default
        if ($load) {
            $this->authurl = B2S_PLUGIN_API_ENDPOINT_AUTH . '?b2s_token=' . B2S_PLUGIN_TOKEN . '&sprache=' . substr(B2S_LANGUAGE, 0, 2) . '&unset=true';
            $this->allowProfil = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PROFILE);
            $this->allowPage = unserialize(B2S_PLUGIN_NETWORK_ALLOW_PAGE);
            $this->allowGroup = unserialize(B2S_PLUGIN_NETWORK_ALLOW_GROUP);
            $this->oAuthPortal = unserialize(B2S_PLUGIN_NETWORK_OAUTH);
        }
    }

    public function getData() {
        $result = json_decode(B2S_Api_Post::post(B2S_PLUGIN_API_ENDPOINT, array('action' => 'getUserAuth', 'view_mode' => 'all', 'auth_count' => true, 'token' => B2S_PLUGIN_TOKEN, 'version' => B2S_PLUGIN_VERSION)));
        return array('mandanten' => isset($result->mandanten) ? $result->mandanten : '',
            'auth' => isset($result->auth) ? $result->auth : '',
            'auth_count' => isset($result->auth_count) ? $result->auth_count : false,
            'portale' => isset($result->portale) ? $result->portale : '');
    }

    public function getCountSchedPostsByUserAuth($networkAuthId = 0) {
        global $wpdb;
        $countSched = $wpdb->get_results($wpdb->prepare("SELECT COUNT(b.id) AS count FROM b2s_posts b LEFT JOIN b2s_posts_network_details d ON (d.id = b.network_details_id) WHERE d.network_auth_id= %d AND b.hide = %d AND b.sched_date !=%s", $networkAuthId, 0, '0000-00-00 00:00:00'));
        if (is_array($countSched) && !empty($countSched) && isset($countSched[0]->count)) {
            if ((int) $countSched[0]->count > 0) {
                return (int) $countSched[0]->count;
            }
        }
        return false;
    }

    public function getSelectMandantHtml($data) {
        $select = '<select class="form-control b2s-network-mandant-select b2s-select">';
        $select .= '<optgroup label="' . __("Default", "blog2social") . '"><option value="-1" selected="selected">' . __('Show all', 'blog2social') . '</option>';
        $select .= '<option value="0">' . __('My profile', 'blog2social') . '</option></optgroup>';
        if (!empty($data)) {
            $select .='<optgroup label="' . __("Your profiles:", "blog2social") . '">';
            foreach ($data as $id => $name) {
                $select .= '<option value="' . $id . '">' . stripslashes($name) . '</option>';
            }
            $select .='</optgroup>';
        }
        $select .= '</select>';
        return $select;
    }

    public function getPortale($mandanten, $auth, $portale, $auth_count) {
        $convertAuthData = $this->convertAuthData($auth);

        foreach ($mandanten as $k => $v) {
            $this->mandantenId[] = $k;
        }

        $html = '<div class="col-md-12 b2s-network-details-container">';

        foreach ($this->mandantenId as $k => $mandant) {
            $html .= $this->getItemHtml($mandant, $mandanten, $convertAuthData, $portale, $auth_count);
        }
        $html .= '</div>';
        return $html;
    }

    public function getItemHtml($mandant, $mandantenData, $convertAuthData, $portale, $auth_count) {

        $html = '<ul class="list-group b2s-network-details-container-list" data-mandant-id="' . $mandant . '" style="display:' . ($mandant > 0 ? "none" : "block" ) . '">';
        foreach ($portale as $k => $portal) {
            if (!isset($convertAuthData[$mandant][$portal->id]) || empty($convertAuthData[$mandant][$portal->id])) {
                $convertAuthData[$mandant][$portal->id] = array();
            }
            $maxNetworkAccount = ($auth_count !== false && is_array($auth_count)) ? ((isset($auth_count[$portal->id])) ? $auth_count[$portal->id] : $auth_count[0]) : false;

            if ($mandant == -1) { //all
                $html .= $this->getPortaleHtml($portal->id, $portal->name, $mandant, $mandantenData, $convertAuthData, $maxNetworkAccount, true);
            } else {
                $html .= $this->getPortaleHtml($portal->id, $portal->name, $mandant, $mandantenData, $convertAuthData[$mandant][$portal->id], $maxNetworkAccount);
            }
        }
        $html .= '</ul>';

        return $html;
    }

    private function getPortaleHtml($networkId, $networkName, $mandantId, $mandantenData, $networkData, $maxNetworkAccount = false, $showAllAuths = false) {
        $mandantId = ($mandantId == -1) ? 0 : $mandantId;
        $sprache = substr(B2S_LANGUAGE, 0, 2);
        $html = '<li class="list-group-item">';
        $html .='<div class="media">';
        $html .='<img class="pull-left hidden-xs b2s-img-network" alt="' . $networkName . '" src="' . plugins_url('/assets/images/portale/' . $networkId . '_flat.png', B2S_PLUGIN_FILE) . '">';
        $html .='<div class="media-body network">';
        $html .= '<h4>' . ucfirst($networkName);
        if ($maxNetworkAccount !== false) {
            $html .=' <span class="b2s-network-auth-count">(' . __("Connections", "blog2social") . ' <span class="b2s-network-auth-count-current" '.(($showAllAuths) ? 'data-network-count-trigger="true"' : '').'  data-network-id="' . $networkId . '"></span>/' . $maxNetworkAccount . ')</span>';
        }
        $html .= '<span class="pull-right">';

        $b2sAuthUrl = $this->authurl . '&portal_id=' . $networkId . '&transfer=' . (in_array($networkId, $this->oAuthPortal) ? 'oauth' : 'form' ) . '&mandant_id=' . $mandantId . '&version=3&affiliate_id=' . B2S_Tools::getAffiliateId();
        $html .= in_array($networkId, $this->allowProfil) ? '<a href="#" onclick="wop(\'' . $b2sAuthUrl . '&choose=profile\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-auth-btn">+ ' . __('Profile', 'blog2social') . '</a>' : '';

        if (in_array($networkId, $this->allowPage)) {
            $html .= (B2S_PLUGIN_USER_VERSION > 1 || (B2S_PLUGIN_USER_VERSION == 0 && $networkId == 1) || (B2S_PLUGIN_USER_VERSION == 1 && ($networkId == 1 || $networkId == 10))) ? '<button onclick="wop(\'' . $b2sAuthUrl . '&choose=page\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-auth-btn">+ ' . __('Page', 'blog2social') . '</button>' : '<a href="#" class="btn btn-primary btn-sm b2s-network-auth-btn b2s-btn-disabled" data-title="' . __('You want to connect a network page?', 'blog2social') . '" data-toggle="modal"  data-type="auth-network" data-target="#' . ((B2S_PLUGIN_USER_VERSION == 0) ? 'b2sPreFeatureModal' : 'b2sProFeatureModal') . '">+ ' . __('Page', 'blog2social') . ' <span class="label label-success">' . __("PREMIUM", "blog2social") . '</a>';
        }
        if (in_array($networkId, $this->allowGroup)) {
            $html .= (B2S_PLUGIN_USER_VERSION > 1 || (B2S_PLUGIN_USER_VERSION == 1 && $networkId != 8)) ? '<button  onclick="wop(\'' . $b2sAuthUrl . '&choose=group\', \'Blog2Social Network\'); return false;" class="btn btn-primary btn-sm b2s-network-auth-btn">+ ' . __('Group', 'blog2social') . '</button>' : '<a href="#" class="btn btn-primary btn-sm b2s-network-auth-btn b2s-btn-disabled" data-toggle="modal" data-title="' . __('You want to connect a social media group?', 'blog2social') . '" data-type="auth-network" data-target="#' . ((B2S_PLUGIN_USER_VERSION == 0) ? 'b2sPreFeatureModal' : 'b2sProFeatureModal') . '">+ ' . __('Group', 'blog2social') . ' <span class="label label-success">' . __("PREMIUM", "blog2social") . '</span></a>';
        }

        $html .= '</span></h4>';
        $html .= '<ul class="b2s-network-item-auth-list" data-network-mandant-id="' . $mandantId . '" data-network-id="' . $networkId . '" ' . (($showAllAuths) ? 'data-network-count="true"' : '') . '>';


        if ($showAllAuths) {
            foreach ($this->mandantenId as $ka => $mandantAll) {
                $mandantName = isset($mandantenData->{$mandantAll}) ? ($mandantenData->{$mandantAll}) : __("My profile", "blog2social");
                if (isset($networkData[$mandantAll][$networkId]) && !empty($networkData[$mandantAll][$networkId])) {
                    $html .= $this->getAuthItemHtml($networkData[$mandantAll][$networkId], $mandantAll, $mandantName, $networkId, $b2sAuthUrl, $sprache);
                }
            }
        } else {
            $html .= $this->getAuthItemHtml($networkData, $mandantId, "", $networkId, $b2sAuthUrl, $sprache);
        }

        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        return $html;
    }

    private function getAuthItemHtml($networkData = array(), $mandantId, $mandantName, $networkId, $b2sAuthUrl = '', $sprache = 'en') {
        $html = '';
        if (isset($networkData[0])) {
            foreach ($networkData[0] as $k => $v) {
                $html .= '<li class="b2s-network-item-auth-list-li" data-network-auth-id="' . $v['networkAuthId'] . '" data-network-mandant-id="' . $mandantId . '" data-network-id="' . $networkId . '" data-network-type="0">';

                if ($v['notAllow'] !== false) {
                    $html .='<span class="glyphicon glyphicon-remove-circle glyphicon-danger"></span> <span class="not-allow">' . __('Profile', 'blog2social') . ': ' . stripslashes($v['networkUserName']) . '</span> ';
                } else {
                    $html .= __('Profile', 'blog2social') . ': ' . stripslashes($v['networkUserName']) . '</span> ';
                }

                if (!empty($mandantName)) {
                    $html .='<span class="b2s-network-mandant-name">(' . $mandantName . ')</span> ';
                }

                if ($v['notAllow'] === false) {
                    $html .= '<a href="#" onclick="wop(\'' . $b2sAuthUrl . '&choose=profil&update=' . $v['networkAuthId'] . '\', \'Blog2Social Network\'); return false;" class="b2s-network-auth-btn b2s-network-auth-update-btn add-padding-left" data-network-auth-id="' . $v['networkAuthId'] . '"><span class="glyphicon  glyphicon-refresh glyphicon-grey"></span></a>';
                }
                $html .= '<a class="b2s-network-item-auth-list-btn-delete" data-network-id="'.$networkId.'" data-network-auth-id="' . $v['networkAuthId'] . '" href="#"><span class="glyphicon  glyphicon-trash glyphicon-grey"></span></a>';
                $html .= ($v['expiredDate'] != '0000-00-00' && $v['expiredDate'] <= date('Y-m-d')) ? ' <span class="label label-danger b2s-network-auth-update-label" data-network-auth-id="' . $v['networkAuthId'] . '">' . __('Authorization is interrupted since', 'blog2social') . ' ' . ($sprache == 'en' ? $v['expiredDate'] : date('d.m.Y', strtotime($v['expiredDate']))) . '</span>' : '';
                if ($v['notAllow'] !== false) {
                    $html .='<div class="no-allow-info-text">' . __('To reactivate this social media connection,', 'blog2social') . ' <a href="' . B2S_Tools::getSupportLink('affiliate') . '"target="_blank">' . __('please upgrade', 'blog2social') . '</a></div>';
                }

                $html .= '</li>';
            }
        }
        if (isset($networkData[1])) {
            foreach ($networkData[1] as $k => $v) {
                $html .= '<li class="b2s-network-item-auth-list-li" data-network-auth-id="' . $v['networkAuthId'] . '" data-network-mandant-id="' . $mandantId . '" data-network-id="' . $networkId . '" data-network-type="1">';

                if ($v['notAllow'] !== false) {
                    $html .='<span class="glyphicon glyphicon-remove-circle glyphicon-danger"></span> <span class="not-allow">' . __('Page', 'blog2social') . ': ' . stripslashes($v['networkUserName']) . '</span> ';
                } else {
                    $html .= __('Page', 'blog2social') . ': ' . stripslashes($v['networkUserName']) . '</span> ';
                }

                if (!empty($mandantName)) {
                    $html .='<span class="b2s-network-mandant-name">(' . $mandantName . ')</span> ';
                }

                if ($v['notAllow'] === false) {
                    $html .= '<a href="#" onclick="wop(\'' . $b2sAuthUrl . '&choose=page&update=' . $v['networkAuthId'] . '\', \'Blog2Social Network\'); return false;" class="b2s-network-auth-btn b2s-network-auth-update-btn add-padding-left" data-network-auth-id="' . $v['networkAuthId'] . '"><span class="glyphicon  glyphicon-refresh glyphicon-grey"></span></a>';
                }
                $html .= '<a class="b2s-network-item-auth-list-btn-delete" data-network-id="'.$networkId.'" data-network-auth-id="' . $v['networkAuthId'] . '" href="#"><span class="glyphicon  glyphicon-trash glyphicon-grey"></span></a>';
                $html .= ($v['expiredDate'] != '0000-00-00' && $v['expiredDate'] <= date('Y-m-d')) ? ' <span class="label label-danger b2s-network-auth-update-label" data-network-auth-id="' . $v['networkAuthId'] . '">' . __('Authorization is interrupted  since', 'blog2social') . ' ' . ($sprache == 'en' ? $v['expiredDate'] : date('d.m.Y', strtotime($v['expiredDate']))) . '</span>' : '';

                if ($v['notAllow'] !== false) {
                    $html .='<div class="no-allow-info-text">' . __('To reactivate this social media connection,', 'blog2social') . ' <a href="' . B2S_Tools::getSupportLink('affiliate') . '"target="_blank">' . __('please upgrade', 'blog2social') . '</a></div>';
                }
                $html .= '</li>';
            }
        }
        if (isset($networkData[2])) {
            foreach ($networkData[2] as $k => $v) {
                $html .= '<li class="b2s-network-item-auth-list-li" data-network-auth-id="' . $v['networkAuthId'] . '" data-network-mandant-id="' . $mandantId . '" data-network-id="' . $networkId . '" data-network-type="2">';

                if ($v['notAllow'] !== false) {
                    $html .='<span class="glyphicon glyphicon-remove-circle glyphicon-danger"></span> <span class="not-allow">' . __('Group', 'blog2social') . ': ' . stripslashes($v['networkUserName']) . '</span> ';
                } else {
                    $html .= __('Group', 'blog2social') . ': ' . stripslashes($v['networkUserName']) . '</span> ';
                }

                if (!empty($mandantName)) {
                    $html .='<span class="b2s-network-mandant-name">(' . $mandantName . ')</span> ';
                }

                if ($v['notAllow'] === false) {
                    $html .= '<a href="#" onclick="wop(\'' . $b2sAuthUrl . '&choose=group&update=' . $v['networkAuthId'] . '\', \'Blog2Social Network\'); return false;" class="b2s-network-auth-btn b2s-network-auth-update-btn add-padding-left" data-network-auth-id="' . $v['networkAuthId'] . '"><span class="glyphicon  glyphicon-refresh glyphicon-grey"></span></a>';
                }
                $html .= '<a class="b2s-network-item-auth-list-btn-delete" data-network-id="'.$networkId.'" data-network-auth-id="' . $v['networkAuthId'] . '" href="#"><span class="glyphicon  glyphicon-trash glyphicon-grey"></span></a>';
                $html .= ($v['expiredDate'] != '0000-00-00' && $v['expiredDate'] <= date('Y-m-d')) ? ' <span class="label label-danger b2s-network-auth-update-label" data-network-auth-id="' . $v['networkAuthId'] . '">' . __('Authorization is interrupted since', 'blog2social') . ' ' . ($sprache == 'en' ? $v['expiredDate'] : date('d.m.Y', strtotime($v['expiredDate']))) . '</span>' : '';

                if ($v['notAllow'] !== false) {
                    $html .='<div class="no-allow-info-text">' . __('To reactivate this social media connection,', 'blog2social') . ' <a href="' . B2S_Tools::getSupportLink('affiliate') . '"target="_blank">' . __('please upgrade', 'blog2social') . '</a></div>';
                }

                $html .= '</li>';
            }
        }
        return $html;
    }

    private function convertAuthData($auth) {
        $convertAuth = array();
        foreach ($auth as $k => $value) {
            $convertAuth[$value->mandantId][$value->networkId][$value->networkType][] = array(
                'networkAuthId' => $value->networkAuthId,
                'networkUserName' => $value->networkUserName,
                'expiredDate' => $value->expiredDate,
                'notAllow' => (isset($value->notAllow) ? $value->notAllow : false)
            );
        }
        return $convertAuth;
    }

}
