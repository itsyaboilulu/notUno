<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\gameLeaderboard;
use App\Models\gameToMember;
use App\Models\playAPI;
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
        $ret = array('R' => 3, 'G' => 0, 'B' => 2, 'Y' => 5);
        asort($ret);
        return array_keys(array_reverse($ret))[0];
    }
}
