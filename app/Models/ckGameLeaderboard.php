<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * model for quiz: game_leaderboard
 *
 *@param INT gid PRIMARY_KEY
 *@param INT uid PRIMARY_KEY
 *@param INT wins
 */
class ckGameLeaderboard extends ckModel
{
    /**
     * model for quiz: game_leaderboard
     *
     *@param INT gid PRIMARY_KEY
     *@param INT uid PRIMARY_KEY
     *@param INT wins
     */
    function __construct($gid, $uid)
    {
        parent::__construct('game_leaderboard', [
            'gid'   =>  $gid,
            'uid'   =>  $uid
        ]);
    }


    /**
     * add a win to the loaded leaderboard data and save
     *
     * @return boolean
     */
    public function addWin(){
        $this->win = ($this->win + 1);
        return $this->save();
    }

}
