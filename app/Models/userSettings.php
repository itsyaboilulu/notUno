<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * model for uno: user_settings
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

    /**
     * returns user settings, if not makes a new record and returns that
     *
     * @param int $id
     * @return object
     */
    public static function findOrMake($id)
    {
        $ret = userSettings::find($id);
        if (!$ret) {
            $ret =  new userSettings();
            $ret->id = $id;
            $ret->save();
        }
        return $ret;
    }

    /**
     * settings related to push notifiactions
     *
     * @var mixed
     */
    private $pushNots;

    /**
     * set push notifation settings into DB
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $save
     * @return mixed
     */
    public function setNotifications($key, $value, $save = 1)
    {
        $this->getNotifications();
        $this->pushNots[$key] = $value;

        $this->notifications = serialize($this->pushNots);
        return ($save) ? $this->save() : $this->pushNots[$key];
    }

    /**
     * returns push notifiaction related settings
     *
     * @param string $name setting name
     * @return void
     */
    public function getNotifications($name = NULL)
    {
        if (!$this->pushNots) {
            $this->pushNots = useful::unserialize($this->notifications);
            $this->pushNots = ($this->pushNots) ? $this->pushNots : array();
        }

        if ($name && !isset($this->pushNots[$name])) {
            $this->pushNots[$name] = NULL;
        }

        return ($name) ?
            $this->pushNots[$name] :
            $this->pushNots;
    }

    /**
     * toggles allow_notifiactions setting
     *
     * @return boolean
     */
    public function updateNotify()
    {
        return $this->setNotifications('allow', (($this->getNotifications('allow')) ? 0 : 1), 1);
    }

    /**
     * packages data to pass to page
     *
     * @return array
     */
    public function pageData()
    {
        return array(
            'globalleaderboard' => $this->globalleaderboard,
            'notifications'     => $this->getNotifications()
        );
    }

    /**
     * set a setting into DB
     *
     * @param string $key
     * @param string $val
     * @return boolean
     */
    public function setSetting($key, $val)
    {
        switch ($key) {
            case 'alert':
                return $this->setNotifications($key, $val, 1);
        }
    }
}
