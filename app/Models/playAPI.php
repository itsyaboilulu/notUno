<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * extention for play to handle api needs
 */
class playAPI extends play
{

    /**
     * generric somthing went wronge api error
     *
     * @var array
     */
    private  $error_generic  = ['errorNO' => 0, 'error' => 'something went wronge'];

    /**
     * returns turn, hand, player data to api
     *
     * @return array
     */
    public function checkTurn()
    {
        $ret = ['winner' => $this->winner()];
        if (!$ret['winner']) {
            $this->checKBot();
            $this->checkTimeOut();
            $ret = [
                'yourturn'      => FALSE,
                'player'        => users::getName($this->game()->turn),
                'current_card'  => $this->game()->current_card,
                'hand'          => $this->getHand(Auth::id()),
                'stack'         => $this->checkStack(),
                'mhand'         => gameToMember::handCounts($this->id),
            ];
            if ($this->game()->turn == Auth::id()) {
                $ret['yourturn']    = TRUE;
            }
        }
        return $ret;
    }

    /**
     * draws a card and returns $this->checkTurn
     *
     * @return array
     */
    public function APIdrawCard()
    {
        if ($this->checkStack()) {
            $this->drawCard($this->game()->turn, (unserialize($this->game()->game_data)['stack']['draw']));
            $this->game()->clearStack();
        }
        return ($this->draw()) ? $this->checkTurn() : $this->error_generic;
    }

    /**
     * plays a card, returns true if done
     *
     * @param string $card
     * @param boolean $uno
     * @return array
     */
    public function playCard($card, $uno = NULL, $extra = NULL)
    {
        return ((new playPlayCard($this->id, $card))->play($uno, $extra)) ?
            ['complete' => TRUE] :
            $this->error_generic;
    }

    /**
     * checks if any user has won the game
     *
     * @return void
     */
    private function winner()
    {
        if ((game::find($this->game()->id))->started) {
            foreach (gameToMember::handCounts($this->game()->id) as $h) {
                if ($h['count'] == 0) {
                    (new play($this->game()->id, users::getID($h['member'])))->finnishGame();
                    return true;
                }
            }
        }
        return FALSE;
    }

    /**
     * check if player needs to stack a card
     *
     * @return boolean
     */
    private function checkStack()
    {
        if (isset(unserialize($this->game()->game_data)['stack'])) {
            return unserialize($this->game()->game_data)['stack'];
        }
        return 0;
    }

    /**
     * checks if its bots turn to play & plays the turn
     *
     * @return boolean
     */
    public function checkBot()
    {
        if (unoBot::isBot($this->game()->turn)) {
            return (new unoBot($this->game()->turn, $this->game()->id))->play();
        }
    }
}
