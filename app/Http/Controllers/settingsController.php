<?php

namespace App\Http\Controllers;

use App\Models\pushPad;
use App\Models\userSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * collection for funtions related to user settings
 */
class settingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * show page for user settings
    *
    * @return view()
    */
    public function index()
    {
        return view('settings', array(
            'pushpad'   => new pushPad(),
            'settings'  => (userSettings::findOrMake(Auth::id()))->pageData(),
        ));
    }


}
