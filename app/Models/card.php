<?php

namespace App\Models;

use phpDocumentor\Reflection\PseudoTypes\False_;

/**
 * model to store and handle single card data
 *
 * @var string card value (e.g. R6)
 */
class card
{

    /**
     * model to store and handle single card data
     *
     * @var string $card cardvalue (e.g. R6)
     */
    function __construct($card = NULL)
    {
        if ($card) {
            $this->setCard($card);
        }
    }

    /**
     * set card value
     *
     * @var string
     */
    private $card;


    /**
     * return the stored card value
     *
     * @return void
     */
    public function card()
    {
        return $this->card;
    }

    /**
     * returns the card value stripped of modifiers (e.g. R6 -> 6)
     *
     * @return void
     */
    public function baseCard()
    {
        return substr($this->card(), 1);
    }

    /**
     * set the card value
     *
     * @param string $card
     */
    public function setCard($card)
    {
        $this->card = $card;
    }

    /**
     * returns t/f if loaded card is considered a special/effect card
     *
     * @return boolean
     */
    public function isSpecial()
    {
        return ( in_array( substr( $this->card, 1,1 ), [
                'W','R','D','S'
         ]) ) ? $this->baseCard() : NULL;
    }

    /**
     * returns t/f if given $set can be played against loaded current card
     *
     * @param string $set cardvalue of set card
     * @return boolean
     */
    public function canBePlayed($set)
    {
        $play = ($set == $this->card) ? TRUE : FALSE;
        if (!$play) {
            $play = $this->canBePlayedWild();
        }
        if (!$play) {
            $play = $this->canBePlayedColor($set);
        }
        if (!$play) {
            $play = $this->canBePlayedNumber($set);
        }
        return $play;
    }

    /**
     * returns t/f if loaded card is 'wild' (can be played on any color)
     *
     * @return boolean
     */
    private function canBePlayedWild()
    {
        return (substr($this->card, 1, 1) == 'W') ? TRUE : FALSE;
    }

    /**
     * checks if $set card's color matches the loaded card
     *
     * @param string $set card value
     * @return boolean
     */
    private function canBePlayedColor($set)
    {
        return (substr($this->card, 0, 1) == substr($set, 0, 1)) ? TRUE : FALSE;
    }

    /**
     * checks if $set cards number matches the loaded cards number
     *
     * @param string $set card value
     * @return boolean
     */
    private function canBePlayedNumber($set)
    {
        return (substr($this->card, 1, 1) == substr($set, 1, 1)) ? TRUE : FALSE;
    }

    /**
     * returns t/f if loaded card can be stacked (e.g. d2 + d2 = d4) against another card
     *
     * @param string $card card value
     * @return boolean
     */
    public function stackable($card)
    {
        $ret = ($this->basecard() == $card)             ? TRUE : FALSE;
        $ret = ($this->basecard() == substr($card,1))   ? TRUE : $ret;
        return ($card == 'WD4')                         ? TRUE : $ret;
    }

    /**
     * gets the draw amount if loaded card is played
     *
     * @return int
     */
    public function drawAmount()
    {
        switch($this->baseCard()){
            case 'D2':
                return 2;
            case 'WD4':
            case 'D4':
                return 4;
        }
        return 0;
    }

}
