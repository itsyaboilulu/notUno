<?php

namespace App\Http\Controllers;

use App\Models\stats;

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
    public function index()
    {
        return view('stats',
            array(
                'stats'=> new stats(),
            )
        );
    }
}
