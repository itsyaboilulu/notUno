<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Pushpad\Pushpad as PushpadPushpad;
use Pushpad\Notification as Notification;

/**
 * extends pushPad librarys to send push notifiactions to enabled users
 *
 * @param int $target userid
 */
class pushPad
{

    /**
     * pushpad auth token
     * @var string
     */
    private $auth = '7457b60b672ec508b58f719661d7e631';

    /**
     * pushpad's customer id
     * @var integer
     */
    public $pid = 7581;

    /**
     * target users id
     * @var int
     */
    private $tid;

    /**
     * target users name
     * @var string
     */
    private $tuser;

    /**
     * t/f if notifications have been enabled
     * @var boolean
     */
    private $allow;

    /**
     * extends pushPad librarys to send push notifiactions to enabled users
     *
     * @param int $target userid
     */
    function __construct($target = NULL)
    {
        PushpadPushpad::$auth_token = $this->auth;
        PushpadPushpad::$project_id = $this->pid;

        if ($target) {
            $this->tid      = $target;
            $this->tuser    = users::getName($target);
            $this->allow    = $this->settings()->getNotifications('allow');
        }
    }

    /**
     * settings model for user
     *
     * @var object
     */
    private $settings;

    /**
     * settings model for user
     *
     * @var object
     */
    private function settings()
    {
        if (!$this->settings) {
            $this->settings = userSettings::findOrMake($this->tid);
        }
        return $this->settings;
    }

    /**
     * generates the pushpad signiture for the given username
     *
     * @param string $username
     * @return string
     */
    public function uidSignature($username = NULL)
    {

        $username = ($username) ?
            $username :
            Auth::user()->username;

        return PushpadPushpad::signature_for($username);
    }

    /**
     * send alert notifiaction
     *
     * @param string $by username of sender
     * @param string $game_password password of game alert sent from
     * @return void
     */
    public function alert($by = NULL, $game_password = NULL)
    {
        if ($this->settings()->getNotifications('alert')) {
            return $this->sendNotification(
                NULL,
                ($by)            ? "$by Alerted you" : "A player alerted you",
                ($game_password) ? "https://uno.yaboilulu.co.uk/lobby?game=$game_password" : "https://uno.yaboilulu.co.uk/"
            );
        }
        return;
    }

    /**
     * sends a notifiction through pushPad
     *
     * @param string $title
     * @param string $body
     * @param string $url
     * @return boolean
     */
    private function sendNotification($title, $body, $url = NULL)
    {
        $n = new Notification(array('body' => $body, 'title' => $title, 'target_url' => $url));
        return ($this->allow) ? $n->deliver_to($this->tuser) : FALSE;
    }
}
