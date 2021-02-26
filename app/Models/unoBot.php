<?php

namespace App\Models;

/**
 * model for controlling the automated users (unoBots)
 *
 * @param int $id
 * @param int $gid
 */
class unoBot
{
    /**
     * user id of bot
     *
     * @var int
     */
    private $id;

    /**
     * game id
     *
     * @var int
     */
    private $gid;

    /**
     * model for controlling the automated users (unoBots)
     *
     * @param int $id
     * @param int $gid
     */
    function __construct($id, $gid)
    {
        $this->id = $id;
        $this->gid = $gid;
    }

    /**
     * game_to_member model
     *
     * @var object
     */
    private $gameToMember;

    /**
     * returns game_to_member model
     *
     * @var object
     */
    private function gameToMember()
    {
        if (!$this->gameToMember) {
            $this->gameToMember = new ckGameToMember($this->gid, $this->id);
        }
        return $this->gameToMember;
    }

    /**
     * game model
     *
     * @var object
     */
    private $game;

    /**
     * returns game model
     *
     * @var object
     */
    private function game()
    {
        if (!$this->game) {
            $this->game = game::find($this->gid);
        }
        return $this->game;
    }

    /**
     * game settings
     *
     * @return object
     */
    private $settings;

    /**
     * returns the game settings, gets specific setting if specified
     *
     * @param string $s
     * @return mixed
     */
    protected function settings($s = NULL)
    {
        if (!$this->settings) {
            $this->settings = new gameSettings($this->gid);
        }
        return ($s) ?
            $this->settings->{$s} :
            $this->settings;
    }


    /**
     * get unobot to play a card
     *
     * @return boolean
     */
    public function play()
    {

        $play = $this->findCard();

        if (!$play) {
            return $this->draw();
        }

        $extra = NULL;
        if ($play->isSpecial()) {
            if ($play->isWild()) {
                $play = new card($this->wild() . $play->card());
            }
        }
        if ($this->game()->card()->baseCard() == '7') {
            if ($this->settings('extreme7')) {
                foreach ($this->game()->getMembers() as $gm) {
                    $m[] = $gm->username;
                }
                $extra = useful::getRandom($m);
            }
        }

        return (new playPlayCard($this->gid, $play->card(), $this->id))->play(1, $extra);
    }

    /**
     * find a playable card in the bots hand
     *
     * @return mixed card object or NULL on fail
     */
    public function findCard()
    {

        $card = $this->game()->card();

        if ($card->drawAmount()) {
            if (($this->settings('stack'))) {

                foreach ($this->gameToMember()->hand() as $h) {
                    $c = new card($h);
                    if ($c->canBePlayed($card->card()) && $card->stackable($h)) {
                        return $c;
                    }
                }
            }
        }

        foreach ($this->gameToMember()->hand() as $h) {
            $c = new card($h);
            if ($c->canBePlayed($card->card())) {
                return new card($h);
            }
        }

        return NULL;
    }


    /**
     * returns the best color for the bot to pick
     *
     * @return string
     */
    private function wild()
    {
        $ret = array('R' => 0, 'G' => 0, 'B' => 0, 'Y' => 0,);
        foreach ($this->gameToMember()->hand() as $h) {
            $c = new card($h);
            if ($c->color()) {
                $ret[$c->color()]++;
            }
        }
        asort($ret);
        return array_keys(array_reverse($ret))[0];
    }


    /**
     * allow unobot to draw new cards
     *
     * @return boolean
     */
    private function draw()
    {

        if ($this->settings('drawUntilPlay')) {

            $deck = new deck(unserialize($this->game()->deck));

            while (true) {
                $d = $deck->draw();
                $draw[] = $d;
                if ($this->game()->card()->canBePlayed($d)) {
                    break;
                }
            }
            foreach ($draw as $dr) {
                $this->gameToMember()->addCard($dr);
            }

            return $this->play();
        }

        return (new play($this->gid, $this->id))->draw();
    }


    /**
     * returns t/f if given member is a unoBot
     *
     * @return boolean
     */
    public static function isBot($id)
    {
        return in_array($id, array(1, 2, 3, 4, 5));
    }

    /**
     * returns list of bots id's
     *
     * @return array
     */
    public static function bots()
    {
        return array(1, 2, 3, 4, 5);
    }

    /**
     * add bots a game
     *
     * @param integer $gid gamedid
     * @param integer $bots number of bots to add (Max:5)
     * @return boolean success/fail
     */
    public static function addBotsToGame($gid, $bots = 3)
    {

        if ($bots > 5) {
            $bots = 5;
        }
        for ($i = 0; $i < $bots; $i++) {
            gameToMember::addMember(unoBot::bots()[$i], $gid, 0);
            gameLeaderboard::addMember(unoBot::bots()[$i], $gid);
        }
        return TRUE;
    }
}
