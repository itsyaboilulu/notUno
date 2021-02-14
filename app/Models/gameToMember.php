<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * model for quiz: game_to_member
 * @param int gid PRIMARY_KEY
 * @param int uid PRIMARY_KEY
 * @param string hand
 * @param boolean admin
 */
class gameToMember extends Model
{
    public $timestamps = false;
    protected $table = 'game_to_member';

    /**
     * add member to game
     *
     * @param int $id user id
     * @param int $gid game id
     * @return boolean
     */
    public static function addMember($id,$gid)
    {
        $g = new gameToMember();
        $g->gid = $gid;
        $g->uid = $id;
        return $g->save();
    }

    /**
     * returns all games passed user is a part of
     *
     * @param int $id
     * @return void
     */
    public static function gamesFromMemberId($id)
    {
        return DB::select('SELECT g.*
            FROM game g
            INNER JOIN game_to_member m
                ON m.gid = g.id
            WHERE m.uid = ?', [$id]);
    }

    /**
     * returns list of members for given game
     *
     * @param int $gid game id
     * @return collection (id,username,admin)
     */
    public static function getMembers($gid)
    {
        return DB::select('SELECT m.id, m.username, g.admin
            FROM game_to_member g
                INNER JOIN users m
                ON m.id = g.uid
            WHERE g.gid = ?', [$gid]);
    }

    /**
     * return number of cards each member has in there hand
     *
     * @param int $gid
     * @return array
     */
    public static function handCounts($gid)
    {
        foreach( gameToMember::where('gid',$gid)->get() as $g ){
            if ( Auth::id() != $g->uid ){
                $ret[] = array(
                    'member'=> users::getName($g->uid),
                    'count' => (useful::unserialize($g->hand)) ? count(useful::unserialize($g->hand)) : 0
                ) ;
            }
        }
        return $ret;
    }

}

