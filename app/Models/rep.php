<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * calculates the users repuation score
 *
 * @todofull score isnt fully calulted yet
 */
class rep extends stats
{

    /**
     * userid
     *
     * @var int
     */
    private $id;

    /**
     * calculates the users repuation score
     *
     * @todofull score isnt fully calulted yet
     */
    function __construct($id = NULL)
    {
        $this->id = ($id) ? $id : Auth::id();
        parent::__construct($id);
    }

    /**
     * return users repulatation score
     *
     * @return int
     */
    public function rep()
    {
        return $this->plusRep() -  $this->minusRep();
    }

    /**
     * calculate posative repulatation score
     *
     * @return int
     */
    private function plusRep()
    {
        return array_sum(
            array(
                $this->gamesPlayed(),
                ($this->gamesWon() * 5),
                $this->calledUno()['called'],
                $this->golf()['plus'],
                $this->mirror(),
                ($this->perfectGame() * 5),
                $this->achievments(),
            )
        );
    }

    /**
     * calculate negaive repulatation score
     *
     * @return int
     */
    private function minusRep()
    {
        //add breaking chains
        return array_sum(
            array(
                ($this->timeOuts() * 5),
                $this->calledUno()['failed'],
                $this->golf()['lost'],
                $this->firstBlood(),
                $this->ignoredTimeouts(),
                $this->spamming(),
            )
        );
    }

    /**
     * returns the number of reverse battles
     *
     * @return int
     */
    private function golf()
    {
        $points         = 0;
        $minus          = 0;
        foreach ($this->cardsByGame() as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'play') {
                        if (substr($n->data, 1, 1) == 'R') {
                            if ($n->uid == $this->id || (isset($gn[($i + 1)]) && $gn[($i + 1)]->uid == $this->id)) {
                                $points++;
                                if ((isset($gn[($i + 1)]) && $gn[($i + 1)]->uid == $this->id)) {
                                    if (substr($gn[($i + 1)]->data, 1, 1) != 'R') {
                                        $minus++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return [
            'plus'  => $points,
            'lost'  => ($minus * 2),
        ];
    }

    /**
     * returns the number of rep points generated from achievments
     *
     * @return int
     */
    private function achievments()
    {
        $data = memberToAchievement::where('member_to_achievement.uid', '=', $this->id)
            ->join('achievements', 'achievements.id', '=', 'member_to_achievement.aid')
            ->select('achievements.rep')
            ->get();
        $ret = 0;
        foreach ($data as $rep) {
            $ret = $ret + $rep->rep;
        }
        return $ret;
    }

    /**
     * returns the number of times a user has been alerted and timed out
     *
     * @return integer
     */
    public function ignoredTimeouts()
    {
        $points = 0;
        foreach ($this->cardsByGame() as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'timeout' && $n->uid == $this->id) {
                        $time1 = $n->created_at;
                        $j = $i;
                        while (true) {
                            $j--;
                            if ($gn[$j]->action == 'play') {
                                $time2 = $gn[$j]->created_at;
                                break;
                            }
                        }
                        $count = count(DB::select("SELECT * FROM chat WHERE message like '%alerted%'
                            AND uid = 0 AND ( created_at < ? AND created_at > ? );", [$time1, $time2]));
                        $points = $points + (($count > 5)?5:$count);
                    }
                }
            }
        }
        return $points;
    }

    /**
     * penalise users for spamming the chat with alerts
     *
     * @return int
     */
    private function spamming()
    {
        $points = 0;
        $user = users::getName($this->id);
        $chat = DB::select("SELECT * FROM chat WHERE message like '$user alerted%' AND uid = 0 ORDER BY id DESC");

        if (count($chat)> 1){
            $t1 = $chat[0];
            foreach ($chat as $c) {
                if (useful::diffMins($c->created_at, $t1->created_at) < 5
                    && $t1->created_at != $c->created_at
                    && $t1->message == $c->message ) {
                    $points++;
                } else if ($t1->created_at != $c->created_at){
                    $t1 = $c;
                }
            }
        }
        return $points;
    }
}
