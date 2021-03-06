<?php

namespace App\Http\Controllers;

use App\Models\game;
use App\Models\gameLeaderboard;
use App\Models\gameToMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * dashboard
 */
class homeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::guest()){
            return view('auth/login');
        }

        return view('home', array(
                'games'         => gameToMember::gamesFromMemberId(Auth::id()),
                'leaderboard'   => gameLeaderboard::globalLeaderBoard(),
            )
        );
    }
}
