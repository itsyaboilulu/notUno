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
use App\Models\stats;
use App\Models\users;
use App\Models\achievement;
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

    }
}
