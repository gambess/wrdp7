<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportgetmdays
{
    public static function getMdays($options)
    {
        global $jsDatabase;
        $result_array = array();
        if ($options) {
            extract($options);
        }

        if (!isset($ordering)) {
            $ordering = 'md.m_name, md.id';
        }
        
        
        if (isset($season_id) && $season_id) {
            $mdays = array();
            if(get_bloginfo('version') < '4.5.0'){
                $tx = get_terms('joomsport_matchday',array(
                    "hide_empty" => false
                ));
            }else{
                $tx = get_terms(array(
                    "taxonomy" => "joomsport_matchday",
                    "hide_empty" => false
                ));
            }

                for($intA=0;$intA<count($tx);$intA++){
                    $term_meta = get_option( "taxonomy_".$tx[$intA]->term_id."_metas");

                    if($term_meta['season_id'] == $season_id){
                        if((isset($mday_type) && $term_meta['matchday_type'] == $mday_type)
                                || !isset($mday_type)){
                            $tmp = new stdClass();
                            $tmp->id = $tx[$intA]->term_id;
                            $tmp->m_name = $tx[$intA]->name;
                            $mdays[] = $tmp;
                        }
                    }
                }
            
            
            

            return $mdays;
        }
    }
}
