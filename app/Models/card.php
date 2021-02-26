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
     * list of standered uno colors
     * @var array
     */
    private $colors = array('R' => 'red', 'G' => 'green', 'B' => 'blue', 'Y' => 'yellow');

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
        return (in_array(substr($this->card(), 0, 1), array('R', 'G', 'B', 'Y'))) ?
            substr($this->card(), 1) :
            $this->card();
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
        return (in_array($this->baseCard(), [
            'W', 'R', 'D2', 'S', 'WD4'
        ])) ? $this->baseCard() : NULL;
    }

    /**
     * checks if the loaded card is wild type
     *
     * @return boolean
     */
    public function isWild()
    {
        return (substr($this->baseCard(), 0, 1) == 'W') ? 1 : 0;
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
        return (substr($this->baseCard(), 0, 1) == 'W') ? TRUE : FALSE;
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
        $ret = ($this->basecard() == substr($card, 1))   ? TRUE : $ret;
        return ($card == 'WD4')                         ? TRUE : $ret;
    }

    /**
     * gets the draw amount if loaded card is played
     *
     * @return int
     */
    public function drawAmount()
    {
        switch ($this->baseCard()) {
            case 'D2':
                return 2;
            case 'WD4':
            case 'D4':
                return 4;
        }
        return 0;
    }

    /**
     * returns the color of the loaded card (e.g R/red)
     *
     * @param boolean $full return colors full name or abreviated
     * @return void
     */
    public function color($full = FALSE)
    {
        if (isset($this->colors[substr($this->card(), 0, 1)])) {
            return ($full) ? $this->colors[substr($this->card(), 0, 1)] : substr($this->card(), 0, 1);
        }
        return NULL;
    }

    /**
     * returns t/f if card is a damage card
     *
     * @return boolean
     */
    public function damage()
    {
        return in_array($this->baseCard(),array('D2','WD4','S'));
    }
}
