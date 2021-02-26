<?php

namespace App\Http\Controllers;

use App\Models\users;
use App\Models\gameLeaderboard;
use App\Models\gameToMember;
use App\Models\game;
use App\Models\unoBot;
use Illuminate\Support\Facades\Auth;

/**
 * collection for funtions related to creating a temparary user
 */
class tempUserController extends Controller
{

    /**
     * create a temp test account along with a bot game
     *
     * @return void
     */
    public function tempUser() {

        $id = users::temp();
        Auth::loginUsingId($id);

        $game = game::newGame('Temp Lobby',$id);

        gameToMember::addMember($id,$game->id,1);
        gameLeaderboard::addMember($id, $game->id);

        unoBot::addBotsToGame($game->id);

        return redirect('startgame?password='. $game->password);

    }

}
