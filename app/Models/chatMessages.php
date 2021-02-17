<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;

/**
 * store for pre deturmed chat messages
 */
class chatMessages
{
    private $gid;

    /**
    * store for pre deturmed chat messages
    *
    * @param int $gid game id
    */
    function __construct($gid)
    {
        $this->gid = $gid;
    }

    /**
     * commits a message into the game chat
     *
     * @param string $message
     * @param integer $id game id
     * @return boolean
     */
    private function newMessage($message,$id=0)
    {
        $c          = new chat();
        $c->gid     = $this->gid;
        $c->uid     = $id;
        $c->message = $message;

        return $c->save();
    }

    /**
     * returns formatted username
     *
     * @var $id id of username to get
     * @return string
     */
    private function username($id=NULL)
    {
        return "<strong>" . ( ($id) ? users::getName($id) : Auth::user()->username ) . "</strong>";
    }

    /**
     * generic $user joined game message
     *
     * @return boolean
     */
    public function joined()
    {
        return $this->newMessage( $this->username() . "has joined the game" );
    }

    /**
     * generic $user played card message
     *
     * @var string $card card value of card played
     * @return boolean
     */
    public function cardPlayed($card)
    {
        return $this->newMessage($this->username() . " played <strong>$card</strong>" );
    }

    /**
     * generic $user drew a card message
     *
     * @var int $cards number of cards drawn
     * @return boolean
     */
    public function draw($cards=1)
    {
        return $this->newMessage( $this->username() . ( ( $cards !== 1 )?" drew $cards card's ":" drew a card" ));
    }

    /**
     * generic $user called/din't uno message
     *
     * @param boolean $called if uno was called
     * @return boolean
     */
    public function uno($called)
    {
        return ($called) ?
            $this->calledUno() :
            $this->forgotUno();
    }

    /**
     * generic $user called uno message
     *
     * @return boolean
     */
    public function calledUno()
    {
        return $this->newMessage( $this->username() . " called <strong>uno!</strong>" );
    }

    /**
     * generic $user didnt call uno message
     *
     * @return boolean
     */
    public function forgotUno()
    {
        return $this->newMessage( $this->username() . " forgot to call <strong>uno!</strong>" );
    }

    /**
     * generic $user can stack message
     *
     * @return boolean
     */
    public function canStack()
    {
        return $this->newMessage($this->username() . " can Stack");
    }

    /**
     * generic $user timed out message
     *
     * @return boolean
     */
    public function timeOut($user=NULL)
    {
        return $this->newMessage( $this->username($user) . " took too long to play ");
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function extremeFour($user,$target,$cards)
    {
        $this->newMessage($this->username($user) . " played an extreme 4 ");
        return $this->newMessage($this->username($target) . " had to draw $cards cards ");
    }

    public function extremeSeven($target)
    {
        $this->newMessage($this->username() . " played an extreme 7 ");
        return $this->newMessage($this->username() . " swapped hands with " . $this->username($target) );
    }

    public function extremeZero()
    {
        $this->newMessage($this->username() . " played an extreme 0 ");
        return $this->newMessage("All players hands have beem swapped");
    }

}
