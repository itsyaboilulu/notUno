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
    private  $error_generic  = [ 'errorNO' => 0, 'error' => 'something went wronge' ];

    /**
     * returns turn, hand, player data to api
     *
     * @return array
     */
    public function checkTurn()
    {
        parent::checkTimeOut();
        $ret = [
                'yourturn'      => FALSE,
                'player'        => users::getName($this->game()->turn),
                'current_card'  => $this->game()->current_card,
                'hand'          => $this->getHand(Auth::id()),
                'stack'         => $this->checkStack(),
                'winner'        => $this->winner(),
                'mhand'         => gameToMember::handCounts($this->id),
            ];
        if (parent::checkTurn()) {
            $ret['yourturn']    = TRUE;
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
        return ($this->draw()) ? $this->checkTurn() : $this->error_generic;
    }

    /**
     * plays a card, returns true if done
     *
     * @param string $card
     * @param boolean $uno
     * @return array
     */
    public function playCard($card,$uno=NULL)
    {
        return ( (new playPlayCard($this->id, $card))->play($uno) ) ?
            ['complete'=>TRUE] :
            $this->error_generic;
    }

    /**
     * checks if any user has won the game
     *
     * @return void
     */
    private function winner()
    {
        return ( $this->checkWin() ) ?
            TRUE :
            FALSE ;
    }

    /**
     * check if player needs to stack a card
     *
     * @return boolean
     */
    private function checkStack()
    {
        return (isset(unserialize($this->game()->game_data)['stack'])) ?
            1 :
            0 ;
    }

}
