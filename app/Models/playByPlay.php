<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * model for quiz: customeLog
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

    public static function plays($gid,$game_no=0,$last_update=0){
        return playByPlay::join('users','users.id','=', 'play_by_play.uid')
            ->select('users.username', 'play_by_play.action','play_by_play.data', 'play_by_play.id')
            ->where('play_by_play.gid',$gid)
            ->where('play_by_play.game_no', $game_no)
            ->where('play_by_play.id','>',$last_update)
            ->get();
    }

    public function winner(){
        return $this->newPBP('winner', 1);
    }

    public function addCard($card){
        return $this->newPBP('play', $card);
    }

    public function draw($num=1,$id=NULL){
        return $this->newPBP('draw',$num,$id);
    }

    public function timeOut($id){
        return $this->newPBP('timeout', 1, $id);
    }

    public function uno($uno=1){
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
    private function newPBP($action,$data,$uid=NULL){
        $p          = new playByPlay();
        $p->gid     = $this->gid;
        $p->uid     = ($uid) ? $uid : $this->uid;
        $p->action  = $action;
        $p->data    = $data;
        $p->game_no = $this->game_no;
        return $p->save();
    }

}
