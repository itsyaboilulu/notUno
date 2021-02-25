<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * extends play, used fo funtions related to playing a card and its effects
 *
 * NOT FINNISHED YET
 *
 */
class rep {

    private $id;

    function __construct($id = NULL)
    {
        $this->id = ($id) ? $id : Auth::id();
    }

    private $stats;
    private function stats(){
        if (!$this->stats){
            $this->stats = new stats($this->id);
        }
        return $this->stats;
    }

    private $cards;
    private function cards(){
        if(!$this->cards){
            $this->cards = new playByPlay();
        }
        return $this->cards;
    }

    public function rep(){
       return $this->plusRep() -  $this->minusRep();
    }

    public function plusRep(){
        //revrse battles, mirror cards
        return array_sum(
            array(
                $this->stats()->gamesPlayed(),
                $this->stats()->gamesWon(),
                $this->stats()->calledUno()['called'],
                $this->golf()['plus'],
            )
        );
    }

    public function minusRep(){
        //reverse battle loss, breaking chains, first to play damage card
         return array_sum(
            array(
                ($this->stats()->timeOuts()*5),
                $this->stats()->calledUno()['failed'],
                $this->golf()['lost'],
            )
        );
    }


    /**
     * returns the number of reverse battles
     *
     * @return int
     */
    private function golf() {
        $points         = 0;
        $minus          = 0;
        foreach ($this->stats()->cardsByGame() as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'play') {
                        if (substr($n->data, 1, 1) == 'R') {
                            if ($n->uid == $this->id || (isset($gn[($i + 1)]) && $gn[($i + 1)]->uid == $this->id)) {
                                $points++;
                                if ((isset($gn[($i + 1)]) && $gn[($i + 1)]->uid == $this->id)){
                                    if (substr($gn[($i + 1)]->data, 1, 1) != 'R'){
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
            'lost'  => ($minus*2),
        ];
    }


    /**
     * checks each time user was last to play in a day
     *
     * @return int
     */
    private function lastPlay(){

    }


}
