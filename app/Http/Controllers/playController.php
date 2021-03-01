<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\chatMessages;
use App\Models\deck;
use App\Models\game;
use App\Models\gameLeaderboard;
use App\Models\gameSettings;
use App\Models\gameToMember;
use App\Models\play;
use App\Models\playByPlay;
use App\Models\useful;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * collection for funtions related to game lobby and playing a game of uno
 */
class playController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * displays lobby page
     *
     * @todo clean up
     * @param Request $request ($game)
     * @return void
     */
    public function lobby(Request $request)
    {
        try {
            $game = game::gameFromPassword($request->get('game'));
            session(['game' => $game]);
            return ($game->started) ?
                redirect('play') :
                view(
                    'lobby',
                    [
                        'game'          => $game,
                        'settings'      => (new gameSettings($game->id))->settings(),
                        'deck'          => (new deck($game->deck))->deck(),
                        'chat'          => chat::chatlog(session('game')->id),
                        'playbyplay'    => playByPlay::plays(session('game')->id, (($game->game_no) ? ($game->game_no - 1) : 0)),
                        'leaderboard'   => gameLeaderboard::gameLeaderBoard($game->id)
                    ]
                );
        } catch (Exception $e) {
            return redirect('/');
        }
    }

    /**
     * allows user to join a game
     *
     * @todo clean up
     * @param Request $request ($join)
     * @return void
     */
    public function join(Request $request)
    {
        try {
            $game = game::gameFromPassword($request->get('join'));
            if (!$game->started) {
                $game->addMember(Auth::id());
                (new chatMessages($game->id))->joined();
                return redirect('lobby?game=' . $game->password);
            }
            return redirect('/');
        } catch (Exception $e) {
            return redirect('/');
        }
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        if (session()->has('game')) {

            game::refreshSession();

            $play = new play(session('game')->id);

            return (session('game')->started) ?
                view('play', array(
                    'play'          => $play,
                    'game'          => session('game')->gameData(),
                    'mhand'         => gameToMember::handCounts(session('game')->id),
                    'chat'          => chat::chatlog(session('game')->id),
                    'playbyplay'    => playByPlay::plays(session('game')->id, session('game')->game_no),
                    'settings'      => (new gameSettings(session('game')->id))->settings(),
                )) :
                redirect('lobby?game=' . session('game')->password);
        }

        return redirect('/');
    }

    /**
     * create and host a new game
     *
     * @return redirect
     */
    public function hostNew()
    {
        $game = new game();
        $game->generatePassword(Auth::id());
        $game->name = Auth::user()->username . ' lobby';
        $game->save();
        session(['game' => $game]);

        $gtm = new gameToMember();
        $gtm->gid   = $game->id;
        $gtm->uid   = Auth::id();
        $gtm->admin = 1;
        $gtm->save();

        gameLeaderboard::addMember(Auth::id(), $game->id);

        return redirect('lobby?game=' . $game->password);
    }

    /**
     * start a new game
     *
     * @param Request $request ($password, $name, $settings, $deck)
     * @return redirect
     */
    public function startGame(Request $request)
    {
        $game = game::gameFromPassword($request->get('password'));
        if ($game && !$game->started && $game->isAdmin()) {
            $game->name = $request->get('name', $game->name);
            $game->deck = serialize( ( new deck( useful::strToArray( $request->get( 'deck' ) ) ) )->deck());
            $game->startGame(useful::uriDecode($request->get('settings')));
        }

        return redirect('/lobby?game=' . $game->password);
    }

    /**
     * delete a lobby
     *
     * @param Request $request ($password)
     * @return redirect
     */
    public function removeLobby(Request $request)
    {
        $game = game::gameFromPassword($request->get('password'));
        if ($game && $game->isAdmin()) {
            $game->delete();
        }
        return redirect('/lobby?game=' . $game->password);
    }
}
