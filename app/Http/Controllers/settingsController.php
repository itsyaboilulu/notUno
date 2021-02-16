<?php

namespace App\Http\Controllers;

use App\Models\pushPad;
use App\Models\userSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class settingsController extends Controller
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
    public function index()
    {
        return view('settings', array(
            'pushpad'   => new pushPad(),
            'settings'  => (userSettings::findOrMake(Auth::id()))->pageData(),
        ));
    }


}
