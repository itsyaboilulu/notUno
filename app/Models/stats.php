<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class stats {

    private $id;

    function __construct($id=NULL)
    {
        $this->id = ($id) ? $id : Auth::id();
    }

    /**
     * @see cards()
     *
     * @var object
     */
    private $cards;

    /**
     * returns data from playbyplay for logged in user
     *
     * @return object
     */
    private function cards()
    {
        if (!$this->cards){
            $this->cards = playByPlay::where('uid', $this->id)->get();
        }
        return $this->cards;
    }

    /**
     * @see leaderboards()
     * @var object
     */
    private $leaderboards;

    /**
     * returns leaderboard stats for logged in user
     *
     * @return object
     */
    private function leaderboards(){
        if (!$this->leaderboards){
            $sql = "SELECT l.gid, sum(l.wins) as wins,
                    (
                        SELECT sum(g.wins)
                        FROM game_leaderboard g
                        WHERE g.gid = l.gid
                    ) as games
                FROM game_leaderboard l
                WHERE l.uid = ?
                GROUP BY l.gid;";
            $this->leaderboards =  DB::select($sql, [$this->id]);
        }
        return $this->leaderboards;
    }

    /**
     * returns a list of cards played
     *
     * @return array
     */
    private function cardsPlayed(){
        foreach ($this->cards() as $c) {
            if ($c->action == 'play') {
                $ret[] = $c->data;
            }
        }
        return $ret;
    }

    /**
     * returns the users most played card
     *
     * @return string
     */
    public function favCard()
    {
        $ret = array();
        foreach($this->cardsPlayed() as $c){
            $ret[$c] = ( isset($ret[$c]) )?
                $ret[$c] + 1:
                1;
        }
        asort($ret);
        return array_reverse(array_keys($ret))[0];
    }

    /**
     * returns the number of cards the logged in user has played
     *
     * @return int
     */
    public function cardsPlayedCount(){
        return count($this->cardsPlayed());
    }

    /**
     * returns the number of cards the logged in user has drawn
     *
     * @return int
     */
    public function cardsDrawn()
    {
        $count = 0;
        foreach ($this->cards() as $c) {
            if ($c->action == 'draw') {
                $count = $count + $c->data;
            }
        }
        return $count;
    }

    /**
     * returns array of the % of each color player has played
     *
     * @return array
     */
    public function colorBreakdown()
    {
        $colors = array( 'R'=>0, 'G'=>0, 'B'=>0,'Y'=>0);
        foreach ($this->cardsPlayed() as $c){
            $colors[substr($c, 0, 1)]++;
        }
        foreach($colors as $key=>$value){
            $ret[$key] = round (( $value / $this->cardsPlayedCount() )*100);
        }
        return $ret;
    }

    public function specialCards(){
        $arr = array('D2'=>0,'WD4'=>0,'R'=>0,'S'=>0);
        foreach ($this->cardsPlayed() as $c) {
            if (isset($arr[substr($c, 1)])){
                $arr[substr($c, 1)]++;
            }
        }
        return $arr;
    }

    /**
     * returns the number of games user has played
     *
     * @return int
     */
    public function gamesPlayed()
    {
        $p=0;
        foreach($this->leaderboards() as $l){
            $p = $p + $l->games;
        }
        return $p;
    }

    /**
     * returns the number of games user has won
     *
     * @return int
     */
    public function gamesWon()
    {
        $p = 0;
        foreach ($this->leaderboards() as $l) {
            $p = $p + $l->wins;
        }
        return $p;
    }

    /**
     * returns list of called and failed to call unos
     *
     * @return array
     */
    public function calledUno(){
        $uno = array( 'called'=>0,'failed'=>0);
        foreach($this->cards() as $c){
            if ($c->action == 'uno') {
                if ($c->data){
                    $uno['called']++;
                } else {
                    $uno['failed']++;
                }
            }
        }
        return $uno;
    }

    /**
     * returns the number of times the user has ran out of time
     *
     * @return int
     */
    public function timeOuts(){
        $ret = 0;
        foreach ($this->cards() as $c) {
            if ($c->action == 'timeout') {
                $ret++;
            }
        }
        return $ret;
    }

}
