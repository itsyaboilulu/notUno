<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\game;
use App\Models\gameLeaderboard;
use App\Models\playAPI;
use App\Models\gameToMember;
use App\Models\users;
use App\Models\userSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APIController extends Controller
{
    private $error_notpost  = ['errorNO'       => 1, 'error' => 'Request not posted'];
    private $error_data     = ['errorNO'       => 2, 'error' => 'Incorrect data given'];


    public function gameAction(Request $request){
        if ($request->isMethod('post')) {
            if ($request->has('password')) {
                $game       = game::gameFromPassword($request->get('password'));
                if(!$game) { return $this->error_data; }
                $gameapi    = new playAPI($game->id);
                session(['game' => $game]);
                switch( $request->get('action') ){
                    case 'playCard':
                        return $gameapi->playCard($request->get('card'), $request->get('uno_call'));
                    case 'draw':
                        return $gameapi->APIdrawCard();
                    default:
                        return $gameapi->checkTurn();
                }
            }
            return $this->error_data;
        }
        return $this->error_notpost;
    }


    public function lobby(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->has('password')) {
                $game       = game::gameFromPassword($request->get('password'));
                if (!$game) {
                    return $this->error_data;
                }
                switch ($request->get('action')) {
                    case 'checkMembers':
                        return [
                            'members' => gameLeaderboard::gameLeaderBoard($game->id),
                            'started' => $game->started,
                        ];
                    default:
                        return $this->error_data;
                }
            }
            return $this->error_data;
        }
        return $this->error_notpost;
    }


    public function chat(Request $request){
        if ($request->isMethod('post')) {
            if ($request->has('password')) {
                $game       = game::gameFromPassword($request->get('password'));
                switch ($request->get('action')) {
                    case 'send':
                        return chat::send($game->id, $request->get('message'));
                    default:
                        return chat::chatlog($game->id, $request->get('lastUpdate'));
                }
            }
            return $this->error_data;
        }
        return $this->error_notpost;
    }

    public function register(Request $request){
        return [
            'available' => ( users::available( $request->get('name') ) ),
        ];
    }


    public function settings(Request $request){
        if ($request->isMethod('post')) {
            $settings = userSettings::findOrMake(Auth::id());
            switch ($request->get('action')) {
                case 'updateNotify' :
                    return $settings->updateNotify();
                case 'setSetting'   :
                    return $settings->setSetting($request->get('key'), $request->get('val'));
            }
        }
        return $this->error_notpost;
    }

}
