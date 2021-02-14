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
class gameLeaderboard extends Model
{
    public $timestamps = false;
    protected $table = 'game_leaderboard';

    /**
     * add a new member to game leader board
     *
     * @param int $uid user id
     * @param int $gid game id
     * @return boolean
     */
    public static function addMember($uid,$gid)
    {
        $l = new gameLeaderboard();
        $l->gid     = $gid;
        $l->uid     = $uid;
        $l->wins    = 0;
        return $l->save();
    }

}
