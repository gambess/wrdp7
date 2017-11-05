<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class jsHelper
{
    public static function getADF($ef, $suff = '')
    {
        $return = '';
        if (count($ef)) {
            foreach ($ef as $key => $value) {
                if ($value != null) {
                    $return .=  '<div class="jstable-row">';
                    $return .=  '<div class="jstable-cell"><strong>'.$key.':</strong></div>';
                    $return .=  '<div class="jstable-cell">'.$value.'</div>';
                    $return .=  '</div>';
                }
            }
        }
        if ($return) {
            $return = '<div class="jstable">'.$return.'</div>';
        }
        //$return .= '</div>';
        return $return;
    }

    public static function getMatches($matches, $lists = null, $mdname = true)
    {
        $html = '';
        global $jsConfig;
        $pagination = isset($lists['pagination'])?$lists['pagination']:null;
        if (count($matches)) {
            $html .= '<div class="table-responsive">';
            if (self::isMobile()) {
                $html .= '<div class="jstable jsMatchDivMainMobile">';
            } else {
                $html .= '<div class="jstable jsMatchDivMain">';
            }

            $md_id = 0;
            for ($intA = 0; $intA < count($matches); ++$intA) {
                $match = $matches[$intA];

                if (JSCONF_ENBL_MATCH_TOOLTIP && isset($match->lists['m_events_home']) && (count($match->lists['m_events_home']) || count($match->lists['m_events_away']))) {
                    $tooltip = '<div style="overflow:hidden;" class="tooltipInnerHtml"><div class="jstable jsInline" '.(count($match->lists['m_events_home']) >= count($match->lists['m_events_away']) ? 'style="border-right:1px solid #ccc;"' : '').'>';

                    for ($intP = 0; $intP < count($match->lists['m_events_home']); ++$intP) {
                        $tooltip .= '<div class="jstable-row">
                                <div class="jstable-cell">
                                    <div style="min-height:35px;vertical-align:middle;margin-top:12px;min-width:30px;">'.$match->lists['m_events_home'][$intP]->objEvent->getEmblem().'</div>
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_home'][$intP]->obj->getName().'
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_home'][$intP]->ecount.'
                                </div>
                                <div class="jstable-cell">
                                    '.($match->lists['m_events_home'][$intP]->minutes ? $match->lists['m_events_home'][$intP]->minutes."'" : '').'
                                </div>
                            </div>';
                    }
                    if (!count($match->lists['m_events_home'])) {
                        $tooltip .= '&nbsp';
                    }

                    $tooltip .= '</div>';
                    $tooltip .= '<div class="jstable jsInline" '.(count($match->lists['m_events_home']) < count($match->lists['m_events_away']) ? 'style="border-right:1px solid #ccc;"' : '').'>';

                    for ($intP = 0; $intP < count($match->lists['m_events_away']); ++$intP) {
                        $tooltip .= '<div class="jstable-row">
                                <div class="jstable-cell">
                                    <div style="min-height:35px;vertical-align:middle;margin-top:12px;min-width:30px;">'.$match->lists['m_events_away'][$intP]->objEvent->getEmblem().'</div>
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_away'][$intP]->obj->getName().'
                                </div>
                                <div class="jstable-cell">
                                    '.$match->lists['m_events_away'][$intP]->ecount.'
                                </div>
                                <div class="jstable-cell">
                                    '.($match->lists['m_events_away'][$intP]->minutes ? $match->lists['m_events_away'][$intP]->minutes."'" : '').'
                                </div>
                            </div>';
                    }

                    $tooltip .= '</div>';
                } else {
                    $tooltip = '';
                }
                $m_date = get_post_meta($match->id,'_joomsport_match_date',true);
                $m_time = get_post_meta($match->id,'_joomsport_match_time',true);
                    $partic_home = $match->getParticipantHome();
                    $partic_away = $match->getParticipantAway();
                $match_date = classJsportDate::getDate($m_date, $m_time);

                if (self::isMobile()) {
                    $html .= '<div class="jsMobileMatchCont">';
                    if ($jsConfig->get('enbl_mdnameoncalendar',1) == '1' && $mdname) {
                        $html .= '<div class="jsDivMobileMdayName">';
                        $html .= $match->getMdayName().'</div>';
                    }
                    $html .= '<div>'.$match_date;
                    if ($jsConfig->get('cal_venue',1)) {
                        $html .= '<div class="jsMatchDivVenue">'
                                    .$match->getLocation()
                                .'</div>';
                    }
                    $html .= '</div>';
                    $html .= '<div class="jsDivCenter">'
                                .'<div class="jsDivLineEmbl">'
                                
                                .self::nameHTML($partic_home->getName(true))
                            .'</div></div>';
                    $html .= '<div class="jsMatchDivScore">'
                                .($partic_home->getEmblem()).
                                    '<div class="jsScoreBonusB">'.self::getScore($match, '').'</div>'
                                .($partic_away->getEmblem()).
                                '</div>';
                    $html .= '<div  class="jsDivCenter">'
                                .'<div class="jsDivLineEmbl">'
                            
                                .self::nameHTML($partic_away->getName(true))
                            .'</div></div>';
                            

                   

                    $html .= '</div>';
                } else {
                    if ($md_id != $match->getMdayID() && $jsConfig->get('enbl_mdnameoncalendar',1) == '1' && $mdname) {
                        $html .= '<div class="jstable-row js-mdname"><div class="jsrow-matchday-name">'.$match->getMdayName().'</div></div>';
                        $md_id = $match->getMdayID();
                    }

                    $html .= '<div class="jstable-row">
                            <div class="jstable-cell jsMatchDivTime">
                                <div class="jsDivLineEmbl">'

                                    .$match_date
                                .'</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHome">
                                <div class="jsDivLineEmbl">';
                                if(is_object($partic_home)){    
                                    $html .= self::nameHTML($partic_home->getName(true));
                                }    
                    $html .=    '</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivHomeEmbl">'
                                .'<div class="jsDivLineEmbl pull-right">';
                                if(is_object($partic_home)){ 
                                    $html .= ($partic_home->getEmblem());
                                }            
                    $html .=    '</div>

                            </div>
                            <div class="jstable-cell jsMatchDivScore">
                                '.self::getScore($match, '', $tooltip).'
                            </div>
                            <div class="jstable-cell jsMatchDivAwayEmbl">
                                <div class="jsDivLineEmbl">';
                                    if(is_object($partic_away)){ 

                                        $html .= ($partic_away->getEmblem());
                                    }    
                    $html .=    '</div>'
                            .'</div>'
                            .'<div class="jstable-cell jsMatchDivAway">'
                                .'<div class="jsDivLineEmbl">';
                                if(is_object($partic_away)){    
                                    $html .= self::nameHTML($partic_away->getName(true), 0);
                                }    
                    $html .=    '</div>'   
                            .'</div>';
                    if ($jsConfig->get('cal_venue',1)) {
                        $html .= '<div class="jstable-cell jsMatchDivVenue">'
                                        .$match->getLocation()
                                    .'</div>';
                    }
                    if (isset($lists['ef_table']) && count($lists['ef_table'])) {
                        foreach ($lists['ef_table'] as $ef) {
                            $efid = 'ef_'.$ef->id;
                            $html .= '<div class="jstable-cell jsNoWrap">'
                                    .$match->{$efid}
                            
                                .'</div>';
                            

                        }
                    }
                    $html .= '</div>';
                }
            }

            $tooltip .= '</div></div>';
            $html .= '</div></div>';
            if ($pagination) {
                require_once JOOMSPORT_PATH_VIEWS.'elements'.DIRECTORY_SEPARATOR.'pagination.php';
                $html .= paginationView($pagination);
            }

            return $html;
        }
    }
    public static function getScore($match, $class = '', $tooltip = '', $itemid = 0)
    {
        $html = '';
        global $jsConfig;
        if($jsConfig->get('partdisplay_awayfirst',0) == 1){
            $away_score = get_post_meta( $match->id, '_joomsport_home_score', true );
            $home_score = get_post_meta( $match->id, '_joomsport_away_score', true );
        }else{
            $home_score = get_post_meta( $match->id, '_joomsport_home_score', true );
            $away_score = get_post_meta( $match->id, '_joomsport_away_score', true );
        }
        $m_played = get_post_meta( $match->id, '_joomsport_match_played', true );
        
        if ($m_played == '1') {
            $text = $home_score.JSCONF_SCORE_SEPARATOR.$away_score;
            $html .= classJsportLink::match($text, $match->id, false, '', $itemid);
        } elseif ($m_played == '0' || $m_played == '') {
            $html .= classJsportLink::match(JSCONF_SCORE_SEPARATOR_VS, $match->id, false, '', $itemid);
        } else {
            if ($match->lists['mStatuses'] && isset($match->lists['mStatuses']->id)) {
                $tooltip = $match->lists['mStatuses']->stName;
                $html .= $match->lists['mStatuses']->stShort;
            } else {
                $html .= JSCONF_SCORE_SEPARATOR_VS;
            }
        }
        $partic_home = $match->getParticipantHome();
        $partic_away = $match->getParticipantAway();
        if(!is_object($partic_home) && !is_object($partic_away)){
            $html = classJsportLink::match(get_the_title($match->id), $match->id, false, '', $itemid);;
        }
        
        //$tooltip = '<table><tr><td style="width:200px;background-color:blue; vertical-align:top;"><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div></td><td style="background-color:red;vertical-align:top; width:50%;"><div>Player 1 goal 55min</div></td></tr></table>';
        return '<div class="jsScoreDiv '.$class.'" data-toggle2="tooltip" data-placement="bottom" title="" data-original-title="'.htmlspecialchars(($tooltip)).'">'.$html.$match->getETLabel().'</div>'.$match->getBonusLabel();
    }
    public static function getScoreBigM($match)
    {
        $html = '';
        global $jsConfig;
        if($jsConfig->get('partdisplay_awayfirst',0) == 1){
            $away_score = get_post_meta( $match->id, '_joomsport_home_score', true );
            $home_score = get_post_meta( $match->id, '_joomsport_away_score', true );
        }else{
            $home_score = get_post_meta( $match->id, '_joomsport_home_score', true );
            $away_score = get_post_meta( $match->id, '_joomsport_away_score', true );
        }
        $m_played = get_post_meta( $match->id, '_joomsport_match_played', true );
        $jmscore = get_post_meta($match->id, '_joomsport_match_jmscore',true);
        if ($m_played == '1') {
            $bonus1 = '';
            $bonus2 = '';
            $sep = JSCONF_SCORE_SEPARATOR;
            if(isset($jmscore['bonus1'])){
                if ($jmscore['bonus1'] != '' || $jmscore['bonus2'] != '') {
                    $bonus1 = '<div class="jsHmBonus">'.floatval($jmscore['bonus1']).'</div>';
                    $bonus2 = '<div class="jsAwBonus">'.floatval($jmscore['bonus2']).'</div>';
                }
            }
            $html .= "<div class='BigMScore1'>".$home_score.'</div>';
            $html .= "<div class='BigMScore2'>".$away_score.'</div>';
        } elseif ($m_played == '0') {
            $sep = JSCONF_SCORE_SEPARATOR_VS;
        } else {
            if ($match->lists['mStatuses'] && isset($match->lists['mStatuses']->id)) {
                $tooltip = $match->lists['mStatuses']->stName;
                $sep = $match->lists['mStatuses']->stShort;
            } else {
                $sep = JSCONF_SCORE_SEPARATOR_VS;
            }
        }

        //$html .= '<div class="matchSeparator">'.$sep.'</div>';

        //$tooltip = '<table><tr><td style="width:200px;background-color:blue; vertical-align:top;"><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div><div>Player 1 goal 55min</div></td><td style="background-color:red;vertical-align:top; width:50%;"><div>Player 1 goal 55min</div></td></tr></table>';
        return '<div class="jsScoreDivM">'.$html.$match->getETLabel(false).'</div>';
    }
    public static function getMap($maps, $class = '')
    {
        global $wpdb;
        global $jsConfig;
        
        $html = '<div class="jsDivCenter">';
        if(count($maps)){
            
            foreach ($maps as $key => $value) {
                
                if($value[0] !== '' || $value[1] !== ''){
                    
                $sql = "SELECT id FROM {$wpdb->joomsport_maps} WHERE id=".intval($key);
                if($wpdb->get_var($sql)){
                    if($jsConfig->get('partdisplay_awayfirst',0) == 1){
                        $home_map = $value[1];
                        $away_map = $value[0];
                    }else{
                        $home_map = $value[0];
                        $away_map = $value[1];
                    }
                    $html .= '<div class="jsScoreDivMap '.$class.'">'.$home_map.JSCONF_SCORE_SEPARATOR.$away_map.'</div>';
            
                }
                }
            }
        }
        

        $html .= '</div>';

        return $html;
    }
    public static function nameHTML($name, $home = 1, $class = '')
    {
        return '<div class="js_div_particName">'.$name.'</div>';
    }

    public static function JsHeader($options)
    {
        global $jsConfig;
        $kl = '';
        if (classJsportRequest::get('tmpl') != 'component') {
            $kl .= '<div class="">';
            $kl .= '<nav class="navbar navbar-default navbar-static-top" role="navigation">';
            $kl .= '<div class="navbar-header navHeadFull">';

            $img = $jsConfig->get('jsbrand_epanel_image');
            $brand = $jsConfig->get('jsbrand_on',1) ? 'JoomSport' : '';

            if ($img && is_file(JSPLW_PATH_MAINCOMP.$img)) {
                $kl .= '<a class="module-logo" href="'.classJsportLink::seasonlist().'" title="'.$brand.'"><img src="'.JOOMSPORT_LIVE_URL.$img.'" style="height:38px;" alt="'.$brand.'"></a>';
            }

            $kl .= '<ul class="nav navbar-nav pull-right navSingle">';
                //calendar
            if (isset($options['calendar']) && $options['calendar']) {
                $link = classJsportLink::calendar('', $options['calendar'], true);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title=""><i class="date pull-left"></i>'.__('Calendar','joomsport-sports-league-results-management').'</a>';
            }
                //table
            if (isset($options['standings']) && $options['standings']) {
                $link = classJsportLink::season('', $options['standings'], true);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title=""><i class="tableS pull-left"></i>'.__('Standings','joomsport-sports-league-results-management').'</a>';
            }
                //join season
            if (isset($options['joinseason']) && $options['joinseason']) {
                $link = classJsportLink::joinseason($options['joinseason']);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title="">'.__('Register','joomsport-sports-league-results-management').'<i class="fa fa-hand-o-right"></i></a>';
            }
                //join team
            if (isset($options['jointeam']) && $options['jointeam']) {
                $link = classJsportLink::jointeam($options['jointeam']['seasonid'], $options['jointeam']['teamid']);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title="">'.__('Join team','joomsport-sports-league-results-management').'<i class="fa fa-sign-in"></i></a>';
            }

            if (isset($options['playerlist']) && $options['playerlist']) {
                $link = classJsportLink::playerlist($options['playerlist']);
                $kl .= '<a class="btn btn-default" href="'.$link.'" title=""><i class="fa fa-user"></i>'.__('Player list','joomsport-sports-league-results-management').'</a>';
            }
            $kl .= classJsportPlugins::get('addHeaderButton', null);
            $kl .= '</ul></div></nav></div>';
        }
        //$kl .= self::JsHistoryBox($options);
        $kl .= self::JsTitleBox($options);
        $kl .= "<div class='jsClear'></div>";

        return $kl;
    }

    public static function JsTitleBox($options)
    {
        $kl = '';
        $kl .= '<div class="heading col-xs-12 col-lg-12">
                    <div class="heading col-xs-6 col-lg-6">
                        <!--h2>
                           
                        </h2-->
                    </div>
                    <div class="selection col-xs-6 col-lg-6 pull-right">
                        <form method="post">
                            <div class="data">
                                '.(isset($options['tourn']) ? $options['tourn'] : '').'
                                <input type="hidden" name="jscurtab" value="" />    
                            </div>
                        </form>
                    </div>
                </div>';

        return $kl;
    }

    public static function JsHistoryBox($options)
    {
        $kl = '<div class="history col-xs-12 col-lg-12">
          <ol class="breadcrumb">
            <li><a href="javascript:void(0);" onclick="history.back(-1);" title="[Back]">
                <i class="fa fa-long-arrow-left"></i>[Back]
            </a></li>
          </ol>
          <div class="div_for_socbut">'.(isset($options['print']) ? '' : '').'<div class="jsClear"></div></div>
        </div>';

        return $kl;
    }

    public static function JsFormViewElement($match, $partic_id)
    {
        $from_str = '';
        if (isset($match) && $match) {
            if (isset($match->object)) {
                $match_object = $match;
                $match = $match->object;

                $home_score = get_post_meta( $match->ID, '_joomsport_home_score', true );
                $away_score = get_post_meta( $match->ID, '_joomsport_away_score', true );
                $home_team = get_post_meta( $match->ID, '_joomsport_home_team', true );
                $away_team = get_post_meta( $match->ID, '_joomsport_away_team', true );
            }
            if ($home_score == $away_score) {
                $class = 'match_draw';
                $alpha = __( 'D', 'joomsport-sports-league-results-management' );
            } else {
                if (($home_score > $away_score && $home_team == $partic_id)
                     ||
                   ($home_score < $away_score && $away_team == $partic_id)
                        ) {
                    $class = 'match_win';
                    $alpha = __( 'W', 'joomsport-sports-league-results-management' );
                } else {
                    $class = 'match_loose';
                    $alpha = __( 'L', 'joomsport-sports-league-results-management' );
                }
            }
            if (!isset($match->home)) {
                $partic_home = $match_object->getParticipantHome();
                $partic_away = $match_object->getParticipantAway();
                if(is_object($partic_home)){
                    $home = $partic_home->getName(false);
                }else{
                    $home = '';
                }
                if(is_object($partic_away)){
                    $away = $partic_away->getName(false);
                }else{
                    $away = '';
                }
            } else {
                $home = $match->home;
                $away = $match->away;
            }

            $title = $home_score.':'.$away_score.' ('.$home.' - '.$away.')'."\n".$match->m_date.' '.$match->m_time;
            $link = classJsportLink::match('', $match->ID, true);
            $from_str .= '<a href="'.$link.'" title="'.$title.'" class="jstooltip"><span class="jsform_none '.$class.'">'.$alpha.'</span></a>';
        } else {
            $from_str = '<span class="jsform_none match_quest">?</span>'.$from_str;
        }

        return $from_str;
    }
    
    public static function getBoxValue($box_id, $row){
        global $wpdb;
        $boxfield = 'boxfield_'.$box_id;
        
        $cBox = $wpdb->get_row('SELECT * FROM '.$wpdb->joomsport_box.' WHERE id='.$box_id, 'OBJECT') ;
        $options = json_decode($cBox->options, true);

        if($cBox->ftype == '1' && isset($options['calc'])){
            $boxfield1 = 'boxfield_'.$options['depend1'];
            $boxfield2 = 'boxfield_'.$options['depend2'];
            if(isset($row->{$boxfield1}) && $row->{$boxfield1} != NULL && isset($row->{$boxfield2}) && $row->{$boxfield2} != NULL){

                switch ($options['calc']) {
                    
                    case '0':
                        if($row->{$boxfield2}){
                            $res =  $row->{$boxfield1} / $row->{$boxfield2};
                        }else{
                            $res = 0;
                        }
                        return ($res !== NULL?round($res,2):'');
                        break;
                    case '1':
                        $res =  $row->{$boxfield1} * $row->{$boxfield2};
                        return ($res !== NULL?round($res,2):'');
                        break;
                    case '2':
                        $res =  $row->{$boxfield1} + $row->{$boxfield2};
                        return ($res !== NULL?round($res,2):'');

                        break;
                    case '3':
                        $res =  $row->{$boxfield1} - $row->{$boxfield2};
                        return ($res !== NULL?round($res,2):'');

                        break;
                    case '4':
                        return $row->{$boxfield1}.'/'.$row->{$boxfield2};

                        break;
                    default:
                        break;
                }
                
                
            }
            
        }
        
        $res = isset($row->{$boxfield})?$row->{$boxfield}:NULL;
        
        return ($res !== NULL?round($res,2):'');
    }

    public static function isMobile()
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER['HTTP_USER_AGENT']);
    }
}
