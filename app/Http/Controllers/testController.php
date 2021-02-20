<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\playByPlay;
use App\Models\pushPad;
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
        foreach(chat::where('uid',0)->where('gid',4)->get() as $c){

            $pbp            = new playByPlay();
            $pbp->gid       = 4;
            $pbp->game_no   = 35;

            foreach(array(
                'lulu'          =>8,
                'lewis'         =>8,
                'ricky@ricky'   =>9,
                'Coconut'       =>9,
                'jash'          =>10,
                'M_Dawg'        =>11,
                'Kcaj'          =>12
            ) as $key=>$val){
                if (strpos($c->message, "<strong>$key</strong>") !== false) {
                    $pbp->uid = $val;
                    break;
                }
            }

            if ($pbp->uid) {

                $continue = 0;
                if (strpos($c->message, 'drew a card') !== false) {
                    $pbp->draw();
                    $continue = 1;
                } else if (strpos($c->message, 'played') !== false) {
                    $pbp->addCard(str_replace('</strong>', '', str_replace('<strong>', '', explode('played', $c->message)[1])));
                    $continue = 1;
                } else if (strpos($c->message, 'called <strong>uno!</strong>') !== false) {
                    $pbp->uno();
                    $continue = 1;
                } else if (strpos($c->message, 'took too long to play') !== false) {
                    $pbp->timeOut($pbp->uid);
                    $continue = 1;
                }

                if ($continue) {
                    $c = chat::find( $c->id );
                    $c->delete();
                }

            }


        };
    }
}
