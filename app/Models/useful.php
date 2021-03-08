<?php

namespace App\Models;

use DateTime;

/**
 * store of useful static functions i dont have a place for
 */
class useful
{

    /**
     * remove a specific item from an array
     *
     * @param array $arr
     * @param mixed $item
     * @return array
     */
    public static function removeFromArray($arr,$item){
        $ret = [];
        $i = 0;
        foreach($arr as $a){
            if ($a != $item || $i == 1){
                $ret[] = $a;
            }
            if ($a == $item){
                $i = 1;
            }
        }
        return $ret;
    }

    /**
     * convert uriEncoded data into array
     *
     * @var string $data uriEncoded data
     * @return array
     */
    public static function uriDecode($data)
    {
        $ret = NULL;
        if ($data) {
            foreach (explode('&', $data) as $dat) {
                $d = explode('=', $dat);
                $ret[$d[0]] = ($d[1] == 'false') ?
                    0 : (($d[1] == 'true')
                        ? 1 : $d[1]);
            }
        }
        return $ret;
    }

    /**
     * convert sting array ( "$foo, $bar, $etc" ) and convert it into array
     *
     * @param string $data
     * @return array
     */
    public static function strToArray($data)
    {
        return (!is_array($data) && $data != NULL) ? explode(',', $data) : NULL;
    }


    /**
     * returns unserialize $data, check if $data can be unserialized, if not return NULL
     *
     * @param string $str
     * @return void
     */
    public static function unserialize($str)
    {
        return (@unserialize($str)) ? unserialize($str) : NULL;
    }

    /**
     * get mins between 2 dates
     */
    public static function diffMins($start, $finnish)
    {
        return (useful::diffSeconds($start, $finnish) / 60);
    }

    /**
     * get seconds between 2 dates
     */
    public static function diffSeconds($start, $finnish)
    {
        $datetime1 = strtotime("$start");
        $datetime2 = strtotime("$finnish");
        return abs($datetime1 - $datetime2);
    }

    /**
     * gets the date from past time using seconds
     */
    public static function getPast($secs)
    {
        return date("Y-m-d h:i:s", strtotime("-$secs seconds"));
    }

    /**
     * returns the update time of the css file, used to update page cache when css is changed
     *
     * @return time()
     */
    public static function cssUpdateTime()
    {
        $path_parts = filemtime(dirname(__FILE__) . '/../../resources/css/main.min.css');
        return $path_parts;
    }

    /**
     * return random value from array
     *
     * @param array $arr
     * @return mixed
     */
    public static function getRandom($arr)
    {
        shuffle($arr);
        return $arr[0];
    }
}
