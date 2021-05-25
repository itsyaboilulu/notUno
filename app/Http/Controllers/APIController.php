<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\game;
use App\Models\gameLeaderboard;
use App\Models\playAPI;
use App\Models\gameToMember;
use App\Models\playByPlay;
use App\Models\users;
use App\Models\userSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * collection to store all api related interactions
 */
class APIController extends Controller
{
    /**
     * generic error response for not posting data
     */
    private $error_notpost  = ['errorNO'       => 1, 'error' => 'Request not posted'];
    /**
     * generic error response for incorrect data given
     */
    private $error_data     = ['errorNO'       => 2, 'error' => 'Incorrect data given'];

    /**
     * api call related to in game funcions
     *
     * @param Request $request ($password, $action, $card, $uno_call, $extra)
     * @return array
     */
    public function gameAction(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->has('password')) {
                $game       = game::gameFromPassword($request->get('password'));
                if (!$game) {
                    return $this->error_data;
                }
                $gameapi    = new playAPI($game->id);
                session(['game' => $game]);
                switch ($request->get('action')) {
                    case 'playCard':
                        return $gameapi->playCard(
                            $request->get('card'),
                            $request->get('uno_call'),
                            $request->get('extra', NULL)
                        );
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

    /**
     * api call related to lobby funcions
     *
     * @param Request $request ($password, $action)
     * @return array
     */
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

    /**
     * api call related to chat funcions
     *
     * @param Request $request ($password, $message, $clastUpdate, $plastUpdate, $action)
     * @return array
     */
    public function chat(Request $request)
    {
        if ($request->isMethod('post')) {
            if ($request->has('password')) {
                $game       = game::gameFromPassword($request->get('password'));
                switch ($request->get('action')) {
                    case 'send':
                        return chat::send($game->id, $request->get('message'));
                    default:
                        return [
                            'chat' => chat::chatlog($game->id, $request->get('clastUpdate')),
                            'pbp' => playByPlay::plays($game->id, $game->game_no, $request->get('plastUpdate'))
                        ];
                }
            }
            return $this->error_data;
        }
        return $this->error_notpost;
    }

    /**
     * checks if username is availible
     *
     * @param Request $request ( $name )
     * @return array
     */
    public function register(Request $request)
    {
        return [
            'available' => (users::available($request->get('name'))),
        ];
    }

    /**
     * update user settings
     *
     * @param Request $request ($action, $key, $val)
     * @return void
     */
    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $settings = userSettings::findOrMake(Auth::id());
            switch ($request->get('action')) {
                case 'updateNotify':
                    return $settings->updateNotify();
                case 'setSetting':
                    return $settings->setSetting($request->get('key'), $request->get('val'));
            }
        }
        return $this->error_notpost;
    }

    /**
     * one way api for bb app
     *
     * @return array
     */
    public function bb()
    {
        $game = game::find(4);
        return [
            'currCard'  => $game->current_card,
            'turn'      => users::getName($game->turn),
            'leader'    => gameLeaderboard::gameLeaderBoard(4),
        ];
    }
}
