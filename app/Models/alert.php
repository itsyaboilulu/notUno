<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class alert
{

    private $response;
    private $game;
    private $from;

    function __construct($gid,$from)
    {
        $this->game = game::find($gid);
        $this->from = $from;

        $this->response = ($this->timeOut())?
            FALSE :
            (new pushPad($this->game->turn))->alert(Auth::user()->username, $this->game->password);
    }

    public function getResponse(){
        return $this->response;
    }

    /**
     * check if user has alerted within the last 5 mins
     *
     * @return void
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


