<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\gameLeaderboard;
use App\Models\gameToMember;
use App\Models\playByPlay;
use App\Models\pushPad;
use App\Models\stats;
use App\Models\useful;
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
    }
}
