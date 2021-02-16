<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Pushpad\Pushpad as PushpadPushpad;
use Pushpad\Notification as Notification;

/**
 * extends play, used fo funtions related to playing a card and its effects
 */
class pushPad {

    private $auth = '7457b60b672ec508b58f719661d7e631';
    public $pid = 7581;

    private $tid;
    private $tuser;
    private $allow;

    function __construct($target = NULL)
    {
        PushpadPushpad::$auth_token = $this->auth;
        PushpadPushpad::$project_id = $this->pid;

        if ($target){
            $this->tid      = $target;
            $this->tuser    = users::getName($target);
            $this->allow    = $this->settings()->getNotifications('allow');
        }

    }

    private $settings;
    private function settings()
    {
        if (!$this->settings){
            $this->settings = userSettings::findOrMake($this->tid);
        }
        return $this->settings;
    }

    public function uidSignature($username=NULL)
    {

        $username = ($username) ?
            $username:
            Auth::user()->username;

        return PushpadPushpad::signature_for($username);

    }

    public function alert($by=NULL,$game_password=NULL)
    {
        if ($this->settings()->getNotifications('alert')) {
            return $this->sendNotification(NULL,
                ($by)            ? "$by Alerted you":"A player alerted you",
                ($game_password) ? "https://uno.yaboilulu.co.uk/lobby?game=$game_password" : "https://uno.yaboilulu.co.uk/" );
        }
        return;
    }

    private function sendNotification($title,$body, $url=NULL)
    {
        $n = new Notification(array('body' => $body, 'title' => $title, 'target_url' => $url));
        return ($this->allow) ? $n->deliver_to($this->tuser) : FALSE;
    }
}

