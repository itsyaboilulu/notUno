<?php

namespace App\Http\Controllers;

use App\Models\chat;
use App\Models\chatMessages;
use App\Models\deck;
use App\Models\game;
use App\Models\gameSettings;
use App\Models\gameToMember;
use App\Models\play;
use App\Models\useful;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class playController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function lobby(Request $request){
        try {
            $game = game::gameFromPassword( $request->get('game'));
            session(['game'=>$game]);
            return ($game->started) ?
                redirect('play') :
                view( 'lobby',[
                    'game'      => $game,
                    'settings'  => (new gameSettings($game->id))->settings(),
                    'members'   => $game->getMembers(),
                    'deck'      => (new deck($game->deck))->deck(),
                    'admin'     => $game->isAdmin(),
                    'chat'      => chat::chatlog(session('game')->id),
                    ]
                );
        } catch (Exception $e){
            return redirect('/');
        }
    }

    public function join(Request $request)
    {
        try {
            $game = game::gameFromPassword($request->get('join'));
            $game->addMember(Auth::id());
            ( new chatMessages($game->id) )->joined();
            return redirect('lobby?game='.$game->password);
        } catch (Exception $e) {
            return redirect('/');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (session()->has('game')){

            game::refreshSession();

            $play = new play(session('game')->id);

            return (session('game')->started)?
                view('play', array(
                    'hand'      => $play->getHand(),
                    'game'      => session('game')->gameData(),
                    'mhand'     => gameToMember::handCounts( session('game')->id ),
                    'chat'      => chat::chatlog(session('game')->id),
                    //vue cries if we use TRUE/FALSE
                    'yourturn'  => ($play->checkTurn())?1:0 ,
                )) :
                redirect('lobby?game='.session('game')->password);
        }

        return redirect('/');

    }

    /**
     * create and host a new game
     *
     * @return void
     */
    public function hostNew(){
        $game = new game();
        $game->generatePassword(Auth::id());
        $game->name = Auth::user()->username.' lobby';
        $game->save();
        session(['game'=>$game]);

        $gtm = new gameToMember();
        $gtm->gid   = $game->id;
        $gtm->uid   = Auth::id();
        $gtm->admin = 1;
        $gtm->save();

        return redirect('lobby?game='.$game->password);
    }


    public function startGame(Request $request){
        $game = game::gameFromPassword($request->get('password'));
        if ($game && !$game->started){
            $game->name = $request->get('name');
            $game->deck = serialize( ( new deck(useful::strToArray($request->get('deck') ) ))->deck() );
            $game->startGame(useful::uriDecode($request->get('settings')));
        }

        return redirect('/lobby?game='.$game->password);

    }

}
