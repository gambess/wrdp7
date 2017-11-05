<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classExtrafieldDate
{
    public static function getValue($ef, $fieldObj)
    {
        $options = json_decode($fieldObj->options,true);
        
        $dateage = 0;
        if(isset($options['dateage'])){
            $dateage = intval($options['dateage']);
        }
                
        if($ef){
            switch ($dateage) {
                case '1':
                    return self::calcAge($ef);

                    break;
                case '2':
                    return classJsportDate::getDate($ef,'').' ('.self::calcAge($ef).')';

                    break;

                default:
                    return classJsportDate::getDate($ef,'');
                    break;
            }
            
        }
        return '';
    }
    public static function calcAge($ef){
        return intval(date('Y', time() - strtotime($ef))) - 1970;
    }
}
