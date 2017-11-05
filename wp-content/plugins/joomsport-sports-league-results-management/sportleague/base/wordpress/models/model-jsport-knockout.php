<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class modelJsportKnockout
{
    public $matchday_id = null;
    public $lists = null;
    private $row = null;

    public function __construct($id)
    {
        $this->matchday_id = $id;

        if (!$this->matchday_id) {
            die('ERROR! Matchday ID not DEFINED');
        }
    }

    public function getMatches($t_single)
    {
        global $jsDatabase;
        if ($t_single) {
            $query = 'SELECT m.*, t1.id as hm_id, t2.id as aw_id,'
                            ." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
                            .' IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,'
                            .' IF(m.score1>m.score2,t1.id,t2.id) as winnerid,'
                            ." IF(m.score1<m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as looser,"
                            .' IF(m.score1<m.score2,t1.nick, t2.nick) as looser_nick,'
                            .' IF(m.score1<m.score2,t1.id,t2.id) as looserid'
                            .' FROM '.DB_TBL_MATCHDAY.' as md,'
                            .' '.DB_TBL_MATCH.' as m'
                            .' LEFT JOIN '.DB_TBL_PLAYERS.' as t1 ON m.team1_id = t1.id'
                            .' LEFT JOIN '.DB_TBL_PLAYERS.' as t2 ON m.team2_id = t2.id'
                            .' WHERE m.m_id = md.id AND m.published = 1'
                            ." AND m.k_type = '0' AND md.id=".$this->matchday_id
                            .'  ORDER BY m.k_stage,m.k_ordering';
        } else {
            $query = 'SELECT m.*, t1.id as hm_id, t2.id as aw_id,'
                            .' IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,'
                            .' IF(m.score1>m.score2,t1.id,t2.id) as winnerid,'
                            .' IF(m.score1<m.score2,t1.t_name,t2.t_name) as looser,'
                            .' IF(m.score1<m.score2,t1.id,t2.id) as looserid'
                            .' FROM '.DB_TBL_MATCHDAY.' as md,'
                            .' '.DB_TBL_MATCH.' as m'
                            .' LEFT JOIN  '.DB_TBL_TEAMS.' as t1 ON m.team1_id = t1.id'
                            .' LEFT JOIN '.DB_TBL_TEAMS.' as t2 ON m.team2_id = t2.id'
                            .' WHERE m.m_id = md.id AND m.published = 1'
                            ." AND m.k_type = '0' AND md.id=".$this->matchday_id
                            .' ORDER BY m.k_stage,m.k_ordering';
        }

        $matchs = $jsDatabase->select($query);

        return $matchs;
    }

    public function getMatchesDE($t_single)
    {
        global $jsDatabase;
        if ($t_single) {
            $query = 'SELECT m.*, t1.id as hm_id, t2.id as aw_id,'
                ." IF(m.score1>m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as winner,"
                .' IF(m.score1>m.score2,t1.nick, t2.nick) as winner_nick,'
                .' IF(m.score1>m.score2,t1.id,t2.id) as winnerid,'
                ." IF(m.score1<m.score2,CONCAT(t1.first_name,' ',t1.last_name), CONCAT(t2.first_name,' ',t2.last_name)) as looser,"
                .' IF(m.score1<m.score2,t1.nick, t2.nick) as looser_nick,'
                .' IF(m.score1<m.score2,t1.id,t2.id) as looserid'
                .' FROM '.DB_TBL_MATCHDAY.' as md,'
                .' '.DB_TBL_MATCH.' as m'
                .' LEFT JOIN '.DB_TBL_PLAYERS.' as t1 ON m.team1_id = t1.id'
                .' LEFT JOIN '.DB_TBL_PLAYERS.' as t2 ON m.team2_id = t2.id'
                .' WHERE m.m_id = md.id AND m.published = 1'
                ." AND m.k_type = '1' AND md.id=".$this->matchday_id
                .'  ORDER BY m.k_stage,m.k_ordering';
        } else {
            $query = 'SELECT m.*, t1.id as hm_id, t2.id as aw_id,'
                    .'IF(m.score1>m.score2,t1.t_name,t2.t_name) as winner,'
                    .'IF(m.score1>m.score2,t1.id,t2.id) as winnerid,'
                    .'IF(m.score1<m.score2,t1.t_name,t2.t_name) as looser,'
                    .'IF(m.score1<m.score2,t1.id,t2.id) as looserid'
                    .' FROM '.DB_TBL_MATCHDAY.' as md,'
                    .' '.DB_TBL_MATCH.' as m'
                    .' LEFT JOIN  '.DB_TBL_TEAMS.' as t1 ON m.team1_id = t1.id'
                    .' LEFT JOIN '.DB_TBL_TEAMS.' as t2 ON m.team2_id = t2.id'
                    .' WHERE m.m_id = md.id AND m.published = 1'
                    ." AND m.k_type = '1' AND md.id=".$this->matchday_id
                    .' ORDER BY m.k_stage,m.k_ordering';
        }

        $matchs = $jsDatabase->select($query);

        return $matchs;
    }
}
