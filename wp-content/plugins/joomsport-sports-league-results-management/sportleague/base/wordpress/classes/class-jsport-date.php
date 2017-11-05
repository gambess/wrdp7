<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class classJsportDate
{
    protected static $_dateFormat;
    /**
     * @var JSPRO_Models A Model.
     */
    public $_model;

    protected static $_dateFormats = array(
        'd-m-Y H:M' => 'd-m-Y H:i',
    'd.m.Y H:M' => 'd.m.Y H:i',
        'Y.m.d H:M' => 'Y.m.d H:i',
        'm-d-Y I:M p' => 'm-d-Y g:i A',
        'm B, Y H:M' => 'j F, Y H:i',
        'm B, Y I:H p' => 'j F, Y g:i A',
        'd-m-Y' => 'd-m-Y',
        'A d B, Y H:M' => 'l d F, Y H:i',
    );

    public function __construct(&$model)
    {
        $this->_model = $model;
        parent::__construct();
    }

    public static function getDateFormat()
    {
        global $jsConfig;

        if (is_null(self::$_dateFormat)) {
            self::$_dateFormat = $jsConfig->get('dateFormat');
        }

        return self::$_dateFormat;
    }

    public static function getDate($date, $time, $format = null, $jsFormat = true)
    {
        if ($date == '' || $date == '0000-00-00') {
            return '';
        }
        
        $date = $date.' '.$time;
        
        if (is_null($format)) {
            if (!$format = self::getDateFormat()) {
                reset(self::$_dateFormats);
                $format = key(self::$_dateFormats);
            }
        }
        if ($jsFormat) {
            if (isset(self::$_dateFormats[$format])) {
                $format = self::$_dateFormats[$format];
            } else {
                $format = reset(self::$_dateFormats);
            }
        }

        if ($date instanceof DateTime) {
            $timestamp = $date->getTimestamp();
        } elseif (is_int($date)) {
            $timestamp = $date;
        } else {
            $timestamp = strtotime((string) $date);
        }

        if ($time == '00:00' || $time == '') {
            $format = str_replace('H:i', '', $format);
            $format = str_replace('g:i A', '', $format);

        }
        return date_i18n($format, $timestamp);
        //return date($format, $timestamp);
        $dt = new DateTime($date);

        return $dt->format($format);
    }

    /*public static function getDate($date, $time, $format = null){
        global $jsConfig;
        date_default_timezone_set('Europe/London');
        if($date !== '0000-00-00'){
            if(!$format){
                $format = $jsConfig->get('date_format'); 
                $format = str_replace('%', '', $format);
            }
            $timestamp = strtotime($date.' '.$time);

            $date = date($format, $timestamp);
        }else{
            return '';
        }    
        
        return $date;
        
    }*/
}
