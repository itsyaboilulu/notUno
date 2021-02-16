<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * model for quiz: user_settings
 *
 * @var int $id
 *
 *@param INT uid PRIMARY_KEY
 *@param STRING notifications
 *@param UNASSIGNED globalleaderboard
 *@param DATE created_at
 *@param DATE updated_at
 */
class userSettings extends Model
{
    public $timestamps = false;
    protected $table = 'user_settings';

    public static function findOrMake($id){
        $ret = userSettings::find($id);
        if (!$ret){
            $ret =  new userSettings();
            $ret->id = $id;
            $ret->save();
        }
        return $ret;
    }

    private $pushNots;

    public function setNotifications($key,$value, $save=1){
        $this->getNotifications();
        $this->pushNots[$key] = $value;

        $this->notifications = serialize($this->pushNots);
        return ($save) ? $this->save() : $this->pushNots[$key];
    }

    public function getNotifications($name=NULL)
    {
        if (!$this->pushNots){
            $this->pushNots = useful::unserialize($this->notifications);
            $this->pushNots = ($this->pushNots)? $this->pushNots : array();
        }

        if ($name && !isset( $this->pushNots[$name] ) ){
            $this->pushNots[$name] = NULL;
        }

        return ($name) ?
            $this->pushNots[$name] :
            $this->pushNots;
    }

    public function updateNotify(){
        return $this->setNotifications( 'allow', ( ( $this->getNotifications('allow') ) ? 0 : 1 ), 1 );
    }

    public function pageData(){
        return array(
            'globalleaderboard' => $this->globalleaderboard,
            'notifications'     => $this->getNotifications()
        );
    }

    public function setSetting($key, $val){
        switch($key){
            case 'alert':
                return $this->setNotifications($key,$val,1);
        }
    }

}
