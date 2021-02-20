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

    }
}
