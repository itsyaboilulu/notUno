<?php

namespace App\Models;

/**
 * store for deck data and deck releated functions
 *
 * @param mixed $deck array/string deck data to be loaded
 */
class deck extends cards {

    /**
     * loaded deck array
     *
     * @var array
     */
    private $deck;


    /**
     * store for deck data and deck releated functions
     *
     * @param mixed $deck array/string deck data to be loaded
     */
    function __construct($deck=NULL)
    {
        if ($deck){
            $this->setDeck($deck);
        }
    }

    /**
     * sets the loaded deck
     *
     * @param mixed $deck array/string deck data to be loaded
     */
    public function setDeck($deck)
    {
        $this->deck = (!is_array($deck)) ?
            useful::unserialize($deck)
            : $deck;
    }

    /**
     * returns loaded deck data, if no deck loaded creates one from standered list @see cards
     *
     * @return array $this->deck
     */
    public function deck()
    {
        if ($this->deck == NULL){
            $this->deck = array();
            foreach( parent::yellow() as $key=>$value ){
                for($i=0;$i<$value;$i++){
                    $this->deck[] = 'Y'.$key;
                }
            }
            foreach (parent::red() as $key => $value) {
                for ($i = 0; $i < $value; $i++) {
                    $this->deck[] = 'R'.$key;
                }
            }
            foreach (parent::blue() as $key => $value) {
                for ($i = 0; $i < $value; $i++) {
                    $this->deck[] = 'B'.$key;
                }
            }
            foreach (parent::green() as $key => $value) {
                for ($i = 0; $i < $value; $i++) {
                    $this->deck[] = 'G'.$key;
                }
            }
            for ($i = 0; $i < parent::wild(); $i++) {
                $this->deck[] = 'W';
            }
            for ($i = 0; $i < parent::wilddrawfour(); $i++) {
                $this->deck[] = 'WD4';
            }
        }
        return $this->deck;
    }

    /**
     * draes a single card from the loaded deck
     *
     * @param boolean $keepitclean stops special cards being returned @see card::isSpecial()
     * @return string cards value
     */
    public function draw($keepitclean=FALSE)
    {
        $deck = $this->deck();
        shuffle($deck);
        return ( $keepitclean && in_array($deck[0], array('W', 'WD4', 'RD2', 'RS', 'RR', 'BD2', 'BS', 'BR', 'GD2', 'GS', 'GR', 'YD2', 'YS', 'YR'))) ?
            $this->draw() :
            $deck[0];

    }



}
