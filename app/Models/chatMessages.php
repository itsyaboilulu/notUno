<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;

/**
 * store for pre-deturmed chat messages
 */
class chatMessages
{
    private $gid;
    private $uid;
    /**
     * store for pre-deturmed chat messages
     *
     * @param int $gid game id
     */
    function __construct($gid,$uid=NUll)
    {
        $this->gid = $gid;
        $this->uid = ($uid)?$uid:Auth::id();
    }

    /**
     * game model
     *
     * @var object
     */
    private $game;

    /**
     * returns the game model releated to $this->gid
     *
     * @return object
     */
    private function game()
    {
        if (!$this->game) {
            $this->game =  game::find($this->gid);
        }
        return $this->game;
    }

    /**
     * returns the username of whoes turn it is in loaded game()
     *
     * @return string
     */
    private function turn()
    {
        return $this->username($this->game()->turn);
    }

    /**
     * returns formatted username
     *
     * @var $id id of username to get
     * @return string
     */
    private function username($id = NULL)
    {
        return ($id) ? users::getName($id) : users::getName($this->uid);
    }

    /**
     * commits a message into the game chat
     *
     * @param string $message
     * @param integer $id game id
     * @return boolean
     */
    private function newMessage($message, $id = 0, $target = NULL)
    {
        $c          = new chat();
        $c->gid     = $this->gid;
        $c->uid     = $id;
        $c->message = $message;
        $c->target  = $target;

        return $c->save();
    }

    /**
     * commits a message into the game chat
     *
     * @param string $message
     * @return boolean
     */
    public function send($message)
    {
        return ($this->checkCommands($message)) ?
            TRUE :
            $this->newMessage($message, Auth::id());
    }

    /**
     * check the message for any special commands
     *
     * @param string $message
     * @return boolean
     */
    private function checkCommands($message)
    {
        if (strpos($message, '@alert') !== false) {
            new alert($this->gid, Auth::id());
            return $this->alert();
        }

        if (strpos($message, '@wisper') !== false) {
            return $this->wisper(ltrim($message));
        }
    }

    /**
     * sends alert message
     *
     * @return boolean
     */
    private function alert()
    {
        return $this->newMessage($this->username() . " alerted " . $this->turn());
    }

    /**
     * sends a private message to another user
     *
     * @param string $message
     * @return boolean
     */
    private function wisper($message)
    {
        $wisper  = explode(' ', $message);
        if ($wisper[0] == '@wisper') {
            $target = users::getID($wisper[1]);
            if ($target) {
                $m = " (wispered) ";
                for ($i = 2; $i < count($wisper); $i++) {
                    $m = $m . ' ' . $wisper[$i];
                }
                return $this->newMessage($m, Auth::id(), $target);
            }
        }
        return FALSE;
    }


    /**
     * generic $user joined game message
     *
     * @return boolean
     */
    public function joined()
    {
        return $this->newMessage($this->username() . "has joined the game");
    }

    /**
     * generic $user played card message
     *
     * @var string $card card value of card played
     * @return boolean
     */
    public function cardPlayed($card)
    {
        return $this->newMessage($this->username() . " played <strong>$card</strong>");
    }

    /**
     * generic $user drew a card message
     *
     * @var int $cards number of cards drawn
     * @return boolean
     */
    public function draw($cards = 1)
    {
        return $this->newMessage($this->username() . (($cards !== 1) ? " drew $cards card's " : " drew a card"));
    }

    /**
     * generic $user drew a card message
     *
     * @var int $cards number of cards drawn
     * @return boolean
     */
    public function toDraw($target, $cards = 1)
    {
        return $this->newMessage($this->username($target) . " had to draw $cards cards ");
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
        return $this->newMessage($this->username() . " called <strong>uno!</strong>");
    }

    /**
     * generic $user didnt call uno message
     *
     * @return boolean
     */
    public function forgotUno()
    {
        return $this->newMessage($this->username() . " forgot to call <strong>uno!</strong>");
    }

    /**
     * generic $user can stack message
     *
     * @return boolean
     */
    public function canStack($target)
    {
        return $this->newMessage($this->username($target) . " can Stack");
    }

    /**
     * generic $user timed out message
     *
     * @return boolean
     */
    public function timeOut($user = NULL)
    {
        return $this->newMessage($this->username($user) . " took too long to play ", 0, $user);
    }

    /**
     * chat message for playing extreme 4
     *
     * @return boolean
     */
    public function extremeFour($user, $target, $cards, $showchat = TRUE)
    {
        if ($showchat){
            $this->newMessage($this->username($user) . " played an extreme 4 ");
        }
        return $this->toDraw($target, $cards);
    }

    /**
     * chat message for playing extreme 7
     *
     * @return boolean
     */
    public function extremeSeven($target)
    {
        $this->newMessage($this->username() . " played an extreme 7 ");
        return $this->newMessage($this->username() . " swapped hands with " . $this->username($target));
    }

    /**
     * chat message for playing extreme 0
     *
     * @return boolean
     */
    public function extremeZero()
    {
        $this->newMessage($this->username() . " played an extreme 0 ");
        return $this->newMessage("All players hands have been swapped");
    }

    /**
     * chat message for playing extreme 0
     *
     * @return boolean
     */
    public function extremeOne()
    {
        $this->newMessage($this->username() . " played an extreme 1 ");
        return $this->newMessage("All players have drawn a card");
    }

    /**
     * chat message for playing extreme 0
     *
     * @return boolean
     */
    public function extremeNine()
    {
        $this->newMessage($this->username() . " played an extreme 9");
        return $this->newMessage("The order has been randomised");
    }

    /**
     * chat message for reverseing the game order
     *
     * @return boolean
     */
    public function reverseOrder()
    {
        return $this->newMessage("Order has been reversed");
    }

    /**
     * chat message for playing extreme 0
     *
     * @return boolean
     */
    public function extremeTwo($user)
    {
        $this->newMessage($this->username() . " played an extreme 2");
        return $this->newMessage($this->username( $user ) . " lost a card");
    }

    /**
     * chat message for playing extreme 0
     *
     * @return boolean
     */
    public function extremeSix($rand,$data=NULL)
    {
        $this->newMessage($this->username() . " played an extreme 6");
        switch ($rand){
            case 1000:
                return $this->newMessage($this->username() . " has 1 card left");
            case 0:
                return $this->newMessage("Played card has changed");
            case 1:
                return $this->newMessage("The order has been randomised");
            case 2:
                return $this->newMessage("$data players have been skipped");
            case 3:
                $this->reverseOrder();
            case 4:
                return $this->draw($data);
            case 5:
                return $this->newMessage($this->username() . " lost $data cards");
            case 6:
                return $this->newMessage($this->username() . " hand was randomised");
            case 7:
                return $this->newMessage($this->username() . " swapped hands with " . $this->username($data));
            case 8:
                return $this->newMessage($this->username() . " drew a new hand");
        }
    }

}
