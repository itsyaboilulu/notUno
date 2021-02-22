<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class unoBot extends Model
{

    private $id;
    private $gid;
    function __construct($id,$gid)
    {
        $this->id = $id;
        $this->gid = $gid;
    }

    private $gameToMember;
    private function gameToMember(){
        if (!$this->gameToMember()){
            $this->gameToMember = new ckGameToMember($this->gid,$this->id);
        }
        return $this->gameToMember;
    }

    private $game;
    private function game(){
        if (!$this->game){
            $this->game = game::find($this->gid);
        }
        return $this->game;
    }

    /**
     * get unobot to play a card
     *
     * @return void
     */
    public function play(){

    }

    //----------------- STATIC

    /**
     * returns t/f if given member is a unoBot
     *
     * @return boolean
     */
    public static function isBot($id)
    {
        return in_array($id, array(1, 2, 3, 4, 5));
    }

    /**
     * returns list of bots id's
     *
     * @return array
     */
    public static function bots()
    {
        return array(1, 2, 3, 4, 5);
    }

    /**
     * add bots a game
     *
     * @param integer $gid gamedid
     * @param integer $bots number of bots to add (Max:5)
     * @return boolean success/fail
     */
    public static function addBotsToGame($gid, $bots=3){

        if ($bots > 5){ $bots = 5;}
        for($i=0;$i<$bots;$i++){
            gameToMember::addMember(unoBot::bots()[$i], $gid, 0);
            gameLeaderboard::addMember(unoBot::bots()[$i], $gid);
        }
        return TRUE;

    }

}
