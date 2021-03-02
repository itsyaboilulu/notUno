<?php

namespace App\Http\Controllers;

use App\Models\achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * collection for funtions related to achievments
 */
class achievmentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * achievments index page
     *
     * @param Request $request
     * @return view
     */
    public function index(Request $request){
        $achievement = new achievement(Auth::id());
        $achievement->check();
        return view('achievments',array(
            'all'       => $achievement->all(),
            'achieved'  => $achievement->achieved(),
        ));
    }
}
