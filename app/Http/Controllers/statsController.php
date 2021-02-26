<?php

namespace App\Http\Controllers;

use App\Models\rep;
use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * collection for funtions related to user settings
 */
class statsController extends Controller
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
    public function index( Request $request)
    {
        $rep = new rep( ( ($request->get('user'))? users::getID($request->get('user')) : Auth::id() ) );
        return view('stats',
            array(
                'stats'=> $rep->pageData(),
                'rep'  => $rep->rep(),
            )
        );
    }
}
