<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * model for uno: game_to_member
 *
 * @var int $gid
 * @var int $uid
 *
 * @param int gid PRIMARY_KEY
 * @param int uid PRIMARY_KEY
 * @param string hand
 * @param boolean admin
 */
class ckGameToMember extends ckModel
{

    /**
     * model for uno: game_to_member
     *
     * @var int $gid
     * @var int $uid
     *
     * @param int gid PRIMARY_KEY
     * @param int uid PRIMARY_KEY
     * @param string hand
     * @param boolean admin
     */
    function __construct($gid, $uid)
    {
        parent::__construct('game_to_member', [
            'gid'   =>  $gid,
            'uid'   =>  $uid
        ]);
    }

    /**
     * add a card to players hand
     *
     * @param string $card cards value
     * @return boolean
     */
    public function addCard($card)
    {
        $hand = unserialize($this->hand);
        $hand[] = $card;
        $this->hand = serialize($hand);
        return $this->save();
    }

    /**
     * remove a card from hand
     *
     * @param string $card cards value
     * @return boolean
     */
    public function removeCard($card)
    {
        $h = new hand(unserialize($this->hand));
        if (!$h->removeCard($card)) {
            return False;
        };
        $this->hand = ($h->hasEmptyHand()) ?
            NULL : serialize($h->hand());
        return $this->save();
    }

    /**
     * return unserialize hand
     *
     * @return array
     */
    public function hand()
    {
        return useful::unserialize($this->hand);
    }

    /**
     * t/f if members hand is on uno
     *
     * @return boolean
     */
    public function isUno()
    {
        return ($this->hand()) ?
            ((count($this->hand()) == 1) ?
                TRUE :
                FALSE) :
            FALSE;
    }
}
