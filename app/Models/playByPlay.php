<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * model for uno: play_by_play
 *
 *@param INT id PRIMARY_KEY
 *@param INT gid
 *@param INT uid
 *@param STRING action
 *@param STRING data
 *@param DATE created_at
 */
class playByPlay extends Model
{
    public $timestamps = false;
    protected $table = 'play_by_play';

    /**
     * returns the plays for the given game
     *
     * @param int $gid game id
     * @param integer $game_no game number
     * @param integer $last_update id of the last playbyplay sent
     * @return collection
     */
    public static function plays($gid, $game_no = 0, $last_update = 0)
    {
        return playByPlay::join('users', 'users.id', '=', 'play_by_play.uid')
            ->select('users.username', 'play_by_play.action', 'play_by_play.data', 'play_by_play.id')
            ->where('play_by_play.gid', $gid)
            ->where('play_by_play.game_no', $game_no)
            ->where('play_by_play.id', '>', $last_update)
            ->get();
    }

    /**
     * log winner into system
     *
     * @return boolean
     */
    public function winner()
    {
        return $this->newPBP('winner', 1);
    }

    /**
     * log play card into system
     *
     * @return boolean
     */
    public function addCard($card)
    {
        return $this->newPBP('play', $card);
    }


    /**
     * log draw into the system
     *
     * @param integer $num
     * @param int $id
     * @return boolean
     */
    public function draw($num = 1, $id = NULL)
    {
        return $this->newPBP('draw', $num, $id);
    }

    /**
     * log timeout (player ran out of time) into the system
     *
     * @param int $id
     * @return boolean
     */
    public function timeOut($id)
    {
        return $this->newPBP('timeout', 1, $id);
    }

    /**
     * log uno call/fail into the system
     *
     * @param boolean $uno if uno was called
     * @return boolean
     */
    public function uno($uno = 1)
    {
        return $this->newPBP('uno', $uno);
    }

    /**
     * add a new line of play by play data
     *
     * @param string $action
     * @param string $data
     * @param int $uid
     * @return boolean save()
     */
    private function newPBP($action, $data, $uid = NULL)
    {
        $p          = new playByPlay();
        $p->gid     = $this->gid;
        $p->uid     = ($uid) ? $uid : $this->uid;
        $p->action  = $action;
        $p->data    = $data;
        $p->game_no = $this->game_no;
        return $p->save();
    }
}
