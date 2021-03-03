<?php

namespace App\Http\Controllers;

use App\Models\achievement;
use App\Models\users;
use Exception;
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
        try {
            $achievement = new achievement(
            (($request->has('user'))? users::getID($request->get('user')) :Auth::id())
            );
            $achievement->check();
            return view('achievments',array(
                'all'       => $achievement->all(),
                'achieved'  => $achievement->achieved(),
            ));
        } catch (Exception $e){
            return redirect('/achievements');
        }
    }
}
