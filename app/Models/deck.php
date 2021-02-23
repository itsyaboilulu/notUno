<?php

namespace App\Models;

/**
 * store for deck data and deck releated functions
 *
 * @param mixed $deck array/string deck data to be loaded
 */
class deck extends card {

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
            $this->deck = unserialize('a:108:{i:0;s:2:"Y0";i:1;s:2:"Y1";i:2;s:2:"Y1";i:3;s:2:"Y2";i:4;s:2:"Y2";i:5;s:2:"Y3";i:6;s:2:"Y3";i:7;s:2:"Y4";i:8;s:2:"Y4";i:9;s:2:"Y5";i:10;s:2:"Y5";i:11;s:2:"Y6";i:12;s:2:"Y6";i:13;s:2:"Y7";i:14;s:2:"Y7";i:15;s:2:"Y8";i:16;s:2:"Y8";i:17;s:2:"Y9";i:18;s:2:"Y9";i:19;s:3:"YD2";i:20;s:3:"YD2";i:21;s:2:"YS";i:22;s:2:"YS";i:23;s:2:"YR";i:24;s:2:"YR";i:25;s:2:"R0";i:26;s:2:"R1";i:27;s:2:"R1";i:28;s:2:"R2";i:29;s:2:"R2";i:30;s:2:"R3";i:31;s:2:"R3";i:32;s:2:"R4";i:33;s:2:"R4";i:34;s:2:"R5";i:35;s:2:"R5";i:36;s:2:"R6";i:37;s:2:"R6";i:38;s:2:"R7";i:39;s:2:"R7";i:40;s:2:"R8";i:41;s:2:"R8";i:42;s:2:"R9";i:43;s:2:"R9";i:44;s:3:"RD2";i:45;s:3:"RD2";i:46;s:2:"RS";i:47;s:2:"RS";i:48;s:2:"RR";i:49;s:2:"RR";i:50;s:2:"B0";i:51;s:2:"B1";i:52;s:2:"B1";i:53;s:2:"B2";i:54;s:2:"B2";i:55;s:2:"B3";i:56;s:2:"B3";i:57;s:2:"B4";i:58;s:2:"B4";i:59;s:2:"B5";i:60;s:2:"B5";i:61;s:2:"B6";i:62;s:2:"B6";i:63;s:2:"B7";i:64;s:2:"B7";i:65;s:2:"B8";i:66;s:2:"B8";i:67;s:2:"B9";i:68;s:2:"B9";i:69;s:3:"BD2";i:70;s:3:"BD2";i:71;s:2:"BS";i:72;s:2:"BS";i:73;s:2:"BR";i:74;s:2:"BR";i:75;s:2:"G0";i:76;s:2:"G1";i:77;s:2:"G1";i:78;s:2:"G2";i:79;s:2:"G2";i:80;s:2:"G3";i:81;s:2:"G3";i:82;s:2:"G4";i:83;s:2:"G4";i:84;s:2:"G5";i:85;s:2:"G5";i:86;s:2:"G6";i:87;s:2:"G6";i:88;s:2:"G7";i:89;s:2:"G7";i:90;s:2:"G8";i:91;s:2:"G8";i:92;s:2:"G9";i:93;s:2:"G9";i:94;s:3:"GD2";i:95;s:3:"GD2";i:96;s:2:"GS";i:97;s:2:"GS";i:98;s:2:"GR";i:99;s:2:"GR";i:100;s:1:"W";i:101;s:1:"W";i:102;s:1:"W";i:103;s:1:"W";i:104;s:3:"WD4";i:105;s:3:"WD4";i:106;s:3:"WD4";i:107;s:3:"WD4";}');
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
