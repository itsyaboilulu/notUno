<?php

namespace App\Models;

use DateTime;

class useful {

    /**
     * convert uriEncoded data into array
     *
     * @var string $data uriEncoded data
     * @return array
     */
    public static function uriDecode($data){
        $ret = NULL;
        if ($data){
            foreach(explode('&', $data) as $dat){
                $d = explode('=',$dat);
                $ret[$d[0]] = ( $d[1] == 'false')?
                    0 : ( ( $d[1] == 'true')
                            ? 1 : $d[1] ) ;
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
    public static function strToArray($data) {
        return (is_array($data))? explode(',',$data) : NULL;
    }


    /**
     * returns unserialize $data, check if $data can be unserialized, if not return NULL
     *
     * @param string $str
     * @return void
     */
    public static function unserialize($str){
        return (@unserialize($str))? unserialize($str) : NULL;
    }

    /**
     * get mins between 2 dates
     */
    public static function diffMins($start,$finnish){
        return ( useful::diffSeconds($start, $finnish) / 60 );
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
    public static function getPast($secs){
        return date("Y-m-d h:i:s", strtotime("-$secs seconds"));
    }

}
