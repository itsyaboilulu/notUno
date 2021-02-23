<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * model for uno: game_leaderboard
 *
 *@param INT gid PRIMARY_KEY
 *@param INT uid PRIMARY_KEY
 *
 */
class ckGameLeaderboard extends ckModel
{
    /**
     * model for uno: game_leaderboard
     *
     *@param INT gid PRIMARY_KEY
     *@param INT uid PRIMARY_KEY
     *
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
    public function addWin()
    {
        $this->wins = ($this->wins) ? ($this->wins + 1) : 1;
        return $this->save();
    }
}
