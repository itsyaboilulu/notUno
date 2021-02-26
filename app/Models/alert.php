<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * model to handle alert notifications from game chat
 *
 * @param int $gid game id
 * @param int $from user id
 */
class alert
{
    /**
     * notification response data
     * @var mixed
     */
    private $response;

    /**
     * game data
     *
     * @var object
     */
    private $game;

    /**
     * user id of sender
     *
     * @var int
     */
    private $from;

    /**
     * model to handle alert notifications from game chat
     *
     * @param int $gid game id
     * @param int $from user id
     */
    function __construct($gid,$from)
    {
        $this->game = game::find($gid);
        $this->from = $from;

        $this->response = ($this->timeOut())?
            FALSE :
            (new pushPad($this->game->turn))->alert(Auth::user()->username, $this->game->password);
    }

    /**
     * get the notification response data
     *
     * @return mixed
     */
    public function getResponse(){
        return $this->response;
    }

    /**
     * check if user has alerted within the last 5 mins to stop spamming
     *
     * @return boolean
     */
    private function timeOut(){
        //get prev messages
        $p = useful::getPast((5 * 60));
        foreach(chat::where('updated_at','>',$p)->where('gid',$this->game->id)->where('uid',$this->from)->get() as $c ){
            if (strpos($c->message, '@alert') !== false) {
                return TRUE;
            }
        }
        return FALSE;
    }

}


