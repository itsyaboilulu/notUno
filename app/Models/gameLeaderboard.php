<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * returns list of the top 10 player by numbe of wins
     *
     * @return collection
     */
    public static function globalLeaderBoard()
    {
        $sql = "SELECT  u.username,
                (
                    SELECT sum(n.wins)
                    FROM game_leaderboard n
                    WHERE n.uid = l.uid
                ) as wins
            FROM game_leaderboard l
                INNER JOIN users u
                    ON u.id = l.uid
            WHERE wins != 0
            GROUP BY l.uid
            ORDER BY wins DESC
            LIMIT 10;";
        return DB::select($sql);
    }

    /**
     * returns list of the winners for the given game
     *
     * @return collection
     */
    public static function gameLeaderBoard($gid)
    {
        $sql = "SELECT u.username, l.wins
            FROM game_leaderboard l
                INNER JOIN users u
                    ON u.id = l.uid
            WHERE l.gid = $gid
            ORDER BY l.wins DESC;";
        return DB::select($sql);
    }
}
