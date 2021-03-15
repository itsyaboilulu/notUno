<?php

/**
 * simplified and reduced code snippet from uno project
 * https://uno.yaboilulu.co.uk/
 * https://github.com/itsyaboilulu/notUno
 * ( https://github.com/itsyaboilulu/notUno/blob/main/app/Models/playPlayCard.php && https://github.com/itsyaboilulu/notUno/blob/main/app/Models/play.php )
 */

use App\Models\card;
use App\Models\game;
use App\Models\gameToMember;
use Illuminate\Support\Facades\Auth;

/**
 * extends play, used fo funtions related to playing a card and its effects
 */
class playCard extends play
{
    /**
     * @see App\Models\card
     * @var object
     */
    private $card;

    /**
     * play a card, also checks if user has called uno (only if 1 card left)
     *
     * @param boolean $uno
     * @return boolean
     */
    public function play($card, $uno = False)
    {
        if ($this->checkTurn()) {
            $this->card = new card($card);

            if (!$this->card->canBePlayed($this->game()->current_card)) {
                return FALSE;
            }

            $this->game()->current_card = $this->card->card();
            $this->gameMember()->removeCard($this->card->card());

            $this->checkUno($uno);

            return $this->nextTurn($this->special());
        }
        return FALSE;
    }

    /**
     * if card is speciel, proforms related effect
     *
     * @return boolean t if next player needs to be skipped
     */
    private function special()
    {
        switch ($this->card->isSpecial()) {
            case 'S':
                return TRUE;
            case 'D2':
            case 'WD4':
                return $this->drawCard($this->checkNextTurn(), $this->card->drawAmount());
            case 'R':
                return $this->reverseOrder();
            default:
                return FALSE;
        }
    }

    /**
     * check if user has called uno ( on 1 card ), if not draws penalty cards
     *
     * @param boolean $uno
     * @return boolean
     */
    private function checkUno($uno)
    {
        if ($this->gameMember()->isUno()) {
            if (!$uno) {
                $this->drawCard(Auth::id(), 2);
                return FALSE;
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * reverse order of play
     *
     * @return boolean
     */
    private function reverseOrder()
    {
        $this->game()->order = serialize(array_reverse(unserialize($this->game()->order)));
        $this->game()->save();
        return;
    }
}

/**
 * class to handle functions for playing the game
 *
 * @param int $gid game id
 */
class play
{
    /**
     * class to handle functions for playing the game
     *
     * @param int $gid game id
     */
    function __construct($gid)
    {
        $this->gid = $gid;
    }

    /**
     * game id
     *
     * @var int
     */
    private $gid;


    private $game;

    /**
     * returns relevent data from game DB table
     *
     * @return object
     */
    protected function game()
    {
        if (!$this->game) {
            $this->game = game::find($this->gid);
        }
        return $this->game;
    }

    private $gameMember;

    /**
     * returns relevent data from game_to_member DB table
     *
     * @return object
     */
    protected function gameMember()
    {
        if (!$this->gameMember) {
            $this->gameMember = new GameToMember($this->game()->id, Auth::id());
        }
        return $this->gameMember;
    }

    /**
     * checks if its the current users turn to play
     *
     * @return boolean
     */
    protected function checkTurn()
    {
        return ($this->game()->turn == Auth::id()) ? TRUE : FALSE;
    }

    /**
     * given $target draws given $draw amount of cards
     *
     * @param int $target user.id
     * @param int $draw
     * @return boolean
     */
    protected function drawCard($target, $draw)
    {
        $gtm = new gameToMember($this->game()->id, $target);
        for ($i = 0; $i < ($draw); $i++) {
            $gtm->addCard();
        }
        return TRUE;
    }

    /**
     * set the next turn into the DB
     *
     * @param boolean $skip has next player been skipped
     * @return void
     */
    protected function nextTurn($skip = FALSE)
    {
        $this->game()->turn = $this->checkNextTurn($skip);
        return $this->game()->save();
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
}
