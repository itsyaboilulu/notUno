<?php

namespace App\Models;


/**
 * model to handle hand data
 *
 * @param mixed $hand array/string hand data
 * @param array $deck deck data for parent
 */
class hand extends deck
{

    /**
     * hand card data
     *
     * @var array
     */
    private $hand;

    /**
     * number of cards in starting hand
     *
     * @var int
     *
     * @todo remove and use settings
     */
    private $starting_hand_count;

    /**
     * model to handle hand data
     *
     * @param mixed $hand array/string hand data
     * @param array $deck deck data for parent
     */
    function __construct($hand = NULL, $deck = NULL)
    {
        if ($deck) {
            parent::__construct($deck);
        }
        if ($hand) {
            $this->setHand($hand);
        }
    }

    /**
     * returns number of cards in a games starting hand
     *
     * @return int
     * @todo change for settings
     */
    private function startingHandCount()
    {
        if ($this->starting_hand_count == NULL) {
            $this->starting_hand_count = 7;
        }
        return $this->starting_hand_count;
    }

    /**
     * returns set hand data, if not set create a new hand
     *
     * @return array
     */
    public function hand()
    {
        if ($this->hand == NULL) {
            for ($i = 0; $i < $this->startingHandCount(); $i++) {
                $this->hand[] = parent::draw();
            }
        }
        return $this->hand;
    }

    /**
     * remove and reset hand data
     *
     * @return array
     */
    public function newHand()
    {
        $this->hand = NULL;
        return $this->hand();
    }

    /**
     * set current hand
     *
     * @param mixed $hand
     */
    public function setHand($hand)
    {
        if (!is_array($hand)) {
            $hand =  unserialize($hand);
        }
        $this->hand = $hand;
    }

    /**
     * check if passed card is in the users hand
     *
     * @param string $card
     * @return boolean
     */
    public function inHand($card)
    {
        return in_array($card, $this->hand());
    }

    /**
     * remove a card from loaded hand
     *
     * @param string $card
     * @return boolean
     */
    public function removeCard($card)
    {
        $card = (!strpos($card, 'W')) ? $card : substr($card, 1);
        if (!$this->inHand($card)) {
            return FALSE;
        }
        $hand = $this->hand();
        foreach ($hand as $n => $c) {
            if ($c == $card) {
                unset($this->hand[$n]);
                break;
            }
        }
        return ($this->hasEmptyHand()) ?
            TRUE :
            $this->sortHand();
    }

    /**
     * check if hand is empty
     *
     * @return boolean
     */
    public function hasEmptyHand()
    {
        return (@count($this->hand)) ? ((count($this->hand)) ?
            FALSE : TRUE) : TRUE;
    }

    /**
     * sort hand ( R, G, B, Y, W )
     *
     * @return array hand()
     */
    private function sortHand()
    {
        $ret = array('R' => array(), 'G' => array(), 'B' => array(), 'Y' => array(), 'W' => array());
        foreach ($this->hand() as $h) {
            $ret[$h[0]][] = $h;
        }
        foreach ($ret as $key => $re) {
            foreach ($re as $r) {
                $data[] = $r;
            }
        }
        $this->setHand($data);
        return $this->hand();
    }
}
