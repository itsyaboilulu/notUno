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
        $order  = unserialize('a:3:{i:0;i:3;i:1;i:1;i:2;i:8;}');
        shuffle($order);
        echo serialize($order);
    }
}
