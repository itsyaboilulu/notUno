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
        foreach(explode('&', $data) as $dat){
            $d = explode('=',$dat);
            $ret[$d[0]] = ($d[1] == 'false')? 0 : (($d[1] == 'true')? 1 : $d[1]) ;
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
        return explode(',',$data);
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

        $datetime1 = new DateTime("$start");
        $datetime2 = new DateTime("$finnish");
        $interval = $datetime1->diff($datetime2);
        return $interval->format('%i');
    }

}
