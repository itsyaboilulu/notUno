<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\game;
use App\Models\gameLeaderboard;
use App\Models\gameToMember;
use App\Models\playByPlay;
use App\Models\pushPad;
use App\Models\rep;
use App\Models\stats;
use App\Models\useful;
use App\Models\users;
use App\Models\userSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * live testing stuff
 */
class testController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function test()
    {
        foreach((game::find(4))->getMembers() as $m){
            echo users::getName($m->id).' -> <br>'. (new stats($m->id))->timeOuts().'<br>'.( new rep($m->id) )->rep();
            echo '<hr>';
        }
    }
}
