<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\Auth;

class stats {

    private $cards;
    private function cards(){
        if (!$this->cards){
            $this->cards = playByPlay::where('uid',Auth::id())->get();
        }
        return $this->cards;
    }



}
