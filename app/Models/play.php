<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * model to handle functions for playing the game
 *
 * @param int $gid game id
 * @param int $uid user id
 */
class play
{

    /**
     * game id
     * @var int
     */
    protected $id;

    /**
     * game Model
     * @var object
     */
    private $game;

    /**
     * game settings model
     * @var object
     */
    private $settings;

    /**
     * game to member model
     * @var object
     */
    private $gameMember;

    /**
     * chat model
     * @var object
     */
    protected $chat;

    /**
     * play_by_play model
     * @var object
     */
    protected $playByPlay;

    /**
     * user id
     * @var int
     */
    protected $uid;

    /**
     * model to handle functions for playing the game
     *
     * @param int $gid game id
     * @param int $uid user id
     */
    function __construct($gid, $uid = NULL)
    {
        $this->id = $gid;
        $this->uid = ($uid) ? $uid : Auth::id();
    }

    /**
     * returns related deck object
     *
     * @var object
     */
    protected $deck;

    /**
     * returns related deck object
     *
     * @var object
     */
    protected function deck(){
        if (!$this->deck){
            $this->deck = new deck(unserialize($this->game()->deck));
        }
        return $this->deck;
    }

    /**
     * returns the game settings, gets specific setting if specified
     *
     * @param string $s
     * @return mixed
     */
    protected function settings($s = NULL)
    {
        if (!$this->settings) {
            $this->settings = new gameSettings($this->id);
        }
        return ($s) ?
            $this->settings->{$s} :
            $this->settings;
    }


    /**
     * return a new instance of playByPlay
     *
     * @return object
     */
    protected function playByPlay()
    {
        if (!$this->playByPlay) {
            $this->playByPlay           = new playByPlay();
            $this->playByPlay->gid      = $this->game()->id;
            $this->playByPlay->uid      = $this->uid;
            $this->playByPlay->game_no  = $this->game()->game_no;
        }
        return $this->playByPlay;
    }

    /**
     * load chat data
     *
     * @return object
     */
    public function chat()
    {
        if (!$this->chat) {
            $this->chat = new chatMessages($this->id,$this->uid);
        }
        return $this->chat;
    }

    /**
     * returns relevent data from game model
     *
     * @return object
     */
    protected function game()
    {
        if (!$this->game) {
            $this->game = game::find($this->id);
        }
        return $this->game;
    }

    /**
     * returns relevent data from game_to_member model
     *
     * @return object
     */
    protected function gameMember()
    {
        if (!$this->gameMember) {
            $this->gameMember =  new ckGameToMember($this->id, $this->uid);
        }
        return $this->gameMember;
    }

    /**
     * checks if its the current users turn to play
     *
     * @return boolean
     */
    public function checkTurn()
    {
        return ($this->game()->turn == $this->uid) ? TRUE : FALSE;
    }

    /**
     * returns the current users hand
     */
    public function getHand()
    {
        return $this->gameMember()->hand();
    }


    /**
     * set the game to be finnished and update leaderboards
     *
     * @return boolean
     */
    public function finnishGame()
    {
        (new ckGameLeaderboard($this->id, $this->uid))->addWin();
        $this->game()->started = 0;
        $this->game()->game_no = ($this->game()->game_no) ? $this->game()->game_no + 1 : 1;
        $this->playByPlay()->winner();
        return $this->game()->save();
    }

    /**
     * draw card for current user
     *
     * @return boolean
     */
    public function draw()
    {
        //check settings
        if (!$this->settings('drawUntilPlay')) {
            $this->NextTurn();
        }
        return $this->drawCard($this->uid);
    }

    /**
     * given $target draws given $no amount of cards
     *
     * @param int $target user.id
     * @param integer $no
     * @return boolean
     */
    public function drawCard($target, $no = 1)
    {
        $gtm    = new ckGameToMember($this->id, $target);
        $this->playByPlay()->draw($no, $target);
        for ($i = 0; $i < ($no); $i++) {
            $gtm->addCard($this->deck()->draw());
        }
        return TRUE;
    }

    /**
     * set the next turn into the DB
     *
     * @param boolean $skip has next player been skipped
     * @return void
     */
    public function nextTurn($skip = NULL)
    {
        $this->game()->turn = $this->checkNextTurn($skip);
        $this->game()->save();
    }

    /**
     * returns whos turn is next
     *
     * @param boolean $skip has next player been skipped
     * @return void
     */
    protected function checkNextTurn($skip = NULL)
    {
        $order  = unserialize($this->game()->order);
        return array_values(array_slice($order, ((array_search($this->game()->turn, $order) + (($skip) ? 2 : 1)) - count($order))))[0];
    }

    /**
     * check if current player has ran out of time to play a card
     * @return boolean T/F
     */
    protected function checkTimeOut()
    {
        if ($this->settings('allowTimeouts')) {
            if (useful::diffMins($this->game()->updated, date("Y-m-d H:i:s")) > $this->settings('timeoutsTime')) {
                $this->chat()->timeOut($this->game()->turn);
                $this->playByPlay()->timeOut($this->game()->turn);
                $this->drawCard($this->game()->turn, $this->settings('timeoutsDraw'));
                return $this->nextTurn();
            }
        }
        return;
    }
}
