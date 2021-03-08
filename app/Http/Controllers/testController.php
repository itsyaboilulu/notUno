<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\ckGameToMember;
use App\Models\game;
use App\Models\gameLeaderboard;
use App\Models\gameToMember;
use App\Models\playByPlay;
use App\Models\pushPad;
use App\Models\rep;
use App\Models\deck;
use App\Models\hand;
use App\Models\card;
use App\Models\users;
use App\Models\achievement;
use App\Models\gameSettings;
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
        $setting = new gameSettings(22);
        $t1 = $setting->settings();
        $t2 = $setting->maxSettings();
        $t1['timeoutsTime'] = 60;
        $t2['timeoutsTime'] = 60;
        if ($t1 == $t2){
            echo 1;
        }
    }
}
