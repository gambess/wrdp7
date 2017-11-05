<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportLink
{
    public static function season($text, $season_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        
        $link = get_permalink($season_id);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function calendar($text, $season_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($season_id);
        $link = add_query_arg( 'action', 'calendar', $link );
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function tournament($text, $tournament_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function team($text, $team_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($team_id);
        if($season_id){
            $link = add_query_arg( 'sid', $season_id, $link );
        }
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function match($text, $match_id, $onlylink = false, $class = '', $Itemid = '', $linkable = true)
    {
        if($match_id){
            if (!$Itemid) {
                $Itemid = self::getItemId();
            }
            $pp = get_post($match_id);
            if (isset($pp->post_status) && $pp->post_status != 'publish' || get_post_status($match_id) == 'private') {
                return $text;
            }
            $link = get_permalink($match_id);
            if ($onlylink) {
                return $link;
            }

            return '<a class="'.$class.'" href="'.$link.'">'.$text.'</a>';
        }
    }
    public static function player($text, $player_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($player_id);
        if($season_id){
            $link = add_query_arg( 'sid', $season_id, $link );
        }
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function person($text, $player_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($player_id);
        if($season_id){
            $link = add_query_arg( 'sid', $season_id, $link );
        }
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function matchday($text, $matchday_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function venue($text, $venue_id, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($venue_id);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function club($text, $club_id, $season_id = 0, $onlylink = false, $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

        $link =  get_term_link($club_id);
        if ($onlylink) {
            return $link;
        }

        return '<a href="'.$link.'">'.$text.'</a>';
    }
    public static function playerlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
        $link = get_permalink($season_id);
        $link = add_query_arg( 'action', 'playerlist', $link );
        $link .= $params;
        return $link;
    }
    public static function teamlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
    }
    public static function seasonlist($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function joinseason($season_id = 0, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }

    }
    public static function jointeam($season_id, $team_id, $params = '', $Itemid = '', $linkable = true)
    {
        if (!$Itemid) {
            $Itemid = self::getItemId();
        }
    }
    public static function getItemId()
    {

        return 0;
    }
}
