<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * extends play, used fo funtions related to playing a card and its effects
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

    private function plusRep(){
        //revrse battles, mirror cards
        return array_sum(
            array(
                $this->stats()->gamesPlayed(),
                $this->stats()->gamesWon(),
                $this->stats()->calledUno()['called'],
            )
        );
    }

    private function minusRep(){
        //reverse battle loss, breaking chains, first to play damage card
         return array_sum(
            array(
                $this->stats()->timeOuts(),
                $this->stats()->calledUno()['failed'],
            )
        );
    }

}
