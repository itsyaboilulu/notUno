<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class play {

    protected   $id;
    private     $game;
    private     $settings;
    private     $gameMember;
    protected   $chat;

    function __construct($gid)
    {
        $this->id = $gid;
    }

    /**
     * returns the game settings, gets specific setting if specified
     *
     * @param string $s
     * @return mixed
     */
    protected function settings($s=NULL){
        if (!$this->settings){
            $this->settings = new gameSettings($this->id);
        }
        return ($s) ?
            $this->settings->{$s} :
            $this->settings;
    }

    /**
     * load chat data
     *
     * @return object
     */
    public function chat(){
        if (!$this->chat){
            $this->chat = new chatMessages($this->id);
        }
        return $this->chat;
    }

    /**
     * returns relevent data from game model
     *
     * @return object
     */
    protected function game(){
        if (!$this->game){
            $this->game = game::find($this->id);
        }
        return $this->game;
    }

    /**
     * returns relevent data from game_to_member model
     *
     * @return object
     */
    protected function gameMember(){
        if (!$this->gameMember){
            $this->gameMember =  new ckGameToMember($this->id,Auth::id());
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
        return ($this->game()->turn == Auth::id()) ? TRUE : FALSE;
    }

    /**
     * returns the current users hand
     */
    public function getHand()
    {
        return $this->gameMember()->hand();
    }

    /**
     * check if conditions are met for the game to be won
     *
     * @return boolean
     */
    public function checkWin()
    {
        return ( $this->getHand() ) ?
            FALSE :
            $this->finnishGame();
    }

    /**
     * set the game to be finnished and update leaderboards
     *
     * @return boolean
     */
    public function finnishGame()
    {
        (new ckGameLeaderboard($this->id, Auth::id()))->addWin();
        $this->game()->started = 0;
        return $this->game()->save();
    }

    /**
     * draw card for current user
     *
     * @return boolean
     */
    public function draw(){
        //check settings
        if (!$this->settings('drawUntilPlay')){
            $this->NextTurn();
        }
        $this->chat()->draw();
        return $this->drawCard(Auth::id());
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
        $deck   = new deck(unserialize($this->game()->deck));
        $gtm    = new ckGameToMember($this->id, $target);
        for ($i = 0; $i < ($no); $i++) {
            $gtm->addCard($deck->draw());
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
    protected function checkNextTurn($skip=NULL){
        $order  = unserialize($this->game()->order);
        return array_values(array_slice($order, ( ( array_search($this->game()->turn, $order) + (($skip) ? 2 : 1) ) - count($order) ) ) )[0];
    }

    /**
     * check if current player has ran out of time to play a card
     * @return boolean T/F
     */
    protected function checkTimeOut(){
        if ($this->settings('allowTimeouts')){
            if ( useful::diffMins( $this->game()->updated , date( "Y-m-d H:i:s" ) ) > $this->settings('timeoutsTime') ){
                $this->chat()->timeOut($this->game()->turn);
                $this->drawCard($this->game()->turn, $this->settings('timeoutsDraw'));
                return $this->nextTurn();
            }
        }
        return;
    }

}
