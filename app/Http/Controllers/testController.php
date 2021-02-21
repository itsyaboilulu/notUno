<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\playByPlay;
use App\Models\pushPad;
use App\Models\stats;
use App\Models\useful;
use App\Models\userSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class testController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function test()
    {

        $arr = array('t1','t2','t3');

        for($i=0;$i<count($arr);$i++){
            echo $arr[$i];
            echo "-";
            if (($i+1) == count($arr)){
                echo $arr[0];
            } else {
                echo $arr[$i + 1];
            }

            echo "<br>";
        }

        exit;
        $s = new stats();
        echo $s->cardsPlayedCount();
        echo "<hr>";
        echo $s->cardsDrawn();
        echo '<hr>';
        echo $s->gamesWon().'/'.$s->gamesPlayed();
        echo "<hr>";
        echo print_r($s->colorBreakdown());
        echo "<hr>";
        echo print_r($s->specialCards());
        echo "<hr>";
        print_r($s->calledUno());
        echo "<hr>";
        echo $s->timeOuts();
        echo "<hr>";
        echo $s->favCard();
    }
}
