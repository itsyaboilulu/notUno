<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * extends play, used fo funtions related to playing a card and its effects
 *
 * @param int $gid gameid
 * @param string $card card value
 * @param int $uid user id
 */
class playPlayCard extends play
{

    /**
     * @see card()
     *
     * @var object
     */
    private $card;

    /**
     * store for any passed extra data
     *
     * @var mixed
     */
    private $extra;

    /**
     * extends play, used fo funtions related to playing a card and its effects
     *
     * @param int $gid gameid
     * @param string $card card value
     * @param int $uid user id
     */
    function __construct($gid, $card, $uid = NULL)
    {
        $this->card =  new card($card);
        parent::__construct($gid, $uid);
    }

    /**
     * play a card, also checks if user has called uno (only if 1 card left)
     *
     * @param boolean $uno
     * @return boolean
     */
    public function play($uno = null, $extra = NULL)
    {
        $this->extra = $extra;
        if ($this->checkTurn()) {
            if (!$this->card->canBePlayed($this->game()->current_card)) {
                return FALSE;
            }

            $this->gameMember()->removeCard($this->card->card());

            $this->checkUno($uno);

            $this->game()->current_card = $this->card->card();

            $this->playByPlay()->addCard($this->card->card());

            return $this->nextTurn($this->special());
        }
        return FALSE;
    }

    /**
     * check if user has called uno ( on 1 card ), if not draws penalty cards
     *
     * @param boolean $uno
     * @return void
     */
    private function checkUno($uno = NULL)
    {
        if ($this->gameMember()->isUno()) {
            $this->chat()->uno($uno);
            $this->playByPlay()->uno($uno);
            if (!$uno){
                return $this->drawCard($this->uid, $this->settings('unoDrawPenalty'));
            }
            (new achievement($this->uid))->checkUnoSquared($this->card->Card());
            return $uno;
        }
    }

    /**
     * if card is speciel, proforms related effect
     *
     * @return boolean
     */
    private function special()
    {
        $this->checkStack();
        switch ($this->card->isSpecial()) {
            case 'S':
                return TRUE;
            case 'D2':
            case 'WD4':
                return ($this->settings('stack')) ?
                    $this->stack() :
                    $this->drawCard($this->checkNextTurn(), $this->card->drawAmount());
            case 'R':
                return $this->reverseOrder();
            default:
                return $this->extremeCards();
        }
    }

    /**
     * check if active stack, and resolve if stack is ignored
     *
     * @return boolean
     */
    private function checkStack()
    {
        if ($this->settings('stack')) {
            if (!in_array($this->card->isSpecial(), array('D2', 'WD4'))) {
                return $this->resolveStack($this->game()->turn);
            }
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
        $this->chat()->reverseOrder();
        $this->game()->order = serialize(array_reverse(unserialize($this->game()->order)));
        $this->game()->save();
        return;
    }

    /**
     * check if player (and next player) can stack, if not resolves current stack
     *
     * @return boolean
     */
    private function stack()
    {
        //check if next player can stack
        $player = $this->checkNextTurn();
        $stackable = FALSE;
        foreach ((new ckGameToMember($this->id, $player))->hand() as $h) {
            if ($this->card->stackable($h)) {
                $stackable = TRUE;
                break;
            }
        }
        if (!$stackable) {
            return ($this->resolveStack()) ? TRUE : $this->drawCard($this->checkNextTurn(), $this->card->drawAmount());;
        }
        $this->chat()->canStack($this->checkNextTurn());
        $this->game()->addStack($this->uid, $this->card->Card(), $this->card->drawAmount());
        return;
    }

    /**
     * resolve current stack
     *
     * @return boolean
     */
    private function resolveStack($turn = NULL)
    {
        if (isset(unserialize($this->game()->game_data)['stack'])) {
            $this->drawCard(
                (($turn) ? $turn : $this->checkNextTurn()),
                (unserialize($this->game()->game_data)['stack']['draw'] + $this->card->drawAmount())
            );
            $this->chat()->toDraw(
                (($turn) ? $turn : $this->checkNextTurn()),
                (unserialize($this->game()->game_data)['stack']['draw'] + $this->card->drawAmount())
            );
            return $this->game()->clearStack();
        }
        return;
    }

    /**
     * extreme cards
     * @todo getting to big move to another model
     */

    /**
     * check and act upon the playing of special cards
     *
     * @return void
     */
    private function extremeCards()
    {
        switch ($this->card->basecard()) {
            case '4':
                return ($this->settings('extreme4')) ?
                    $this->extremeFour() :
                    NULL;
            case '7':
                return ($this->settings('extreme7')) ?
                    $this->extremeSeven() :
                    NULL;
            case '0':
                return ($this->settings('extreme0')) ?
                    $this->extremeZero() :
                    NULL;
            case '1':
                return ($this->settings('extreme1')) ?
                    $this->extremeOne() :
                    NULL;
            case '9':
                return ($this->settings('extreme9')) ?
                    $this->extremeNine() :
                    NULL;
            case '2':
                return ($this->settings('extreme2')) ?
                    $this->extremeTwo() :
                    NULL;
            case '6':
                return ($this->settings('extreme6')) ?
                    $this->extremeSix() :
                    NULL;
            default:
                return;
        }
    }

    /**
     * handle the playing of a extreme 6 (random card effect applied)
     *
     * @return boolean
     */
    private function extremeSix()
    {
        $data = NULL;
        if (rand(0, 1001) == 1000){
            $rand = 1000;
            //set hand to uno
            $this->gameMember()->hand = serialize(unserialize($this->gameMember()->hand)[array_rand($this->gameMember()->hand())]);
            $this->gameMember()->save();
            (new achievement($this->uid))->checkOneInaThousand();
            $this->chat()->extremeSix($rand, $data);
            return FALSE;
        } else {
            $arr = array(
                function (){
                    //change current card
                    $data = $this->deck()->draw(TRUE);
                    $this->game()->current_card = $data;
                    return $data;
                },
                $this->extremeNine(FALSE) ,
                function (){
                    //skip random players
                    $data = rand(0, 6);
                    for ($i = 1; $i < $data; $i++) {
                        $this->nextTurn();
                    }
                    return $data;
                },
                $this->reverseOrder() ,
                function () {
                    //draw random number of cards
                    $data = rand(1, 11);
                    $this->drawCard($this->uid, $data);
                    return $data;
                },
                function () {
                    //remove random cards
                    $hand = $this->gameMember()->hand();
                    $data = rand(1, (count($hand) - 2));
                    for ($i = 1; $i < $data; $i++) {
                        $hand = useful::removeFromArray($hand, $hand[array_rand($hand, 1)]);
                    }
                    $this->gameMember()->hand = serialize($hand);
                    $this->gameMember()->save();
                    return $data;
                },
                function () {
                    //randomise hand
                    for ($i = 0; $i < count($this->gameMember()->hand()); $i++) {
                        $new[]  = $this->deck()->draw();
                    }
                    $this->gameMember()->hand = serialize($new);
                    $this->gameMember()->save();
                    return NULL;
                },
                function (){
                    //chnage hands with a random player
                    $data = unserialize($this->game()->order)[array_rand(unserialize($this->game()->order), 1)];
                    $this->extra = users::getName($data);
                    $this->extremeSeven(FALSE);
                    return $data;
                },
                function () {
                    //reset hand
                    $this->gameMember()->hand = serialize((new hand(NULL, $this->deck()->deck()))->newHand());
                    $this->gameMember()->save();
                    return NULL;
                },
                $this->extremeFour(FALSE),
            );
            $rand = array_rand($arr);
            $data = call_user_func($arr[$rand]);
        }
        $this->chat()->extremeSix($rand, $data);
        return FALSE;
    }

    /**
     * handle the playing of a extreme 2
     *
     * @return boolean
     */
    private function extremeTwo()
    {
        $target = new ckGameToMember($this->game()->id, $this->checkNextTurn());
        $target->hand = serialize(useful::removeFromArray(unserialize($target->hand), unserialize($target->hand)[array_rand(unserialize($target->hand))]));
        $target->save();
        $this->chat()->extremeTwo( $this->checkNextTurn() );
        if (count( unserialize($target->hand)) == 0){
            (new achievement($target->uid))->checkANiceSuprise();
        }
        return FALSE;
    }

    /**
     * handle the playing of a extreme 4
     *
     * @return void
     */
    private function extremeFour($showchat = TRUE)
    {
        while (true) {
            $d = $this->deck()->draw();
            $draw[] = $d;
            if ($this->card->canBePlayed($d)) {
                break;
            }
        }
        $mg = new ckGameToMember($this->game()->id, $this->checkNextTurn());
        foreach ($draw as $dr) {
            $mg->addCard($dr);
        }
        $this->chat()->extremeFour($this->game()->turn, $this->checkNextTurn(), count($draw), $showchat);
        $this->playByPlay()->draw(count($draw), $this->checkNextTurn());
        return FALSE;
    }

    /**
     * handle the playing of a extreme 7
     *
     * @return void
     */
    private function extremeSeven($chat = true)
    {
        $target = new ckGameToMember($this->game()->id, users::getId($this->extra));
        $curr   = $this->gameMember();

        $thand = $target->hand;

        $target->hand   = $curr->hand;
        $curr->hand     = $thand;

        $curr->save();
        $target->save();

        if (count(unserialize($target->hand)) == 0) {
            (new achievement($target->uid))->checkANiceSuprise();
        }
        if ($chat) {
            $this->chat()->extremeSeven($target->uid);
        }
        return FALSE;
    }

    /**
     * handle the playing of a extreme 0
     *
     * @return void
     */
    private function extremeZero()
    {
        $order  = unserialize($this->game()->order);
        foreach ($order as $o) {
            $ck     = new ckGameToMember($this->game()->id, $o);
            $d[]    = $ck;
            $h[]    = $ck->hand;
        }
        for ($i = 0; $i < count($d); $i++) {
            if (($i + 1) == count($h)) {
                $d[$i]->hand = $h[0];
            } else {
                $d[$i]->hand = $h[($i + 1)];
            }
            $d[$i]->save();
            if ( count(unserialize($h[$i])) == 0){
                (new achievement($d[$i]->uid))->checkANiceSuprise();
            }
        }
        $this->chat()->extremeZero($this->extra);
        return FALSE;
    }

    /**
     * handle the playing of a extreme 1
     *
     * @return void
     */
    private function extremeOne()
    {
        foreach (unserialize($this->game()->order) as $u) {
            if ($u != $this->uid) {
                $this->drawCard($u, 1);
            }
        }
        $this->chat()->extremeOne();
        return False;
    }

    /**
     * handle the playing of a extreme 9
     *
     * @return void
     */
    private function extremeNine($chat = true)
    {
        $order = unserialize($this->game()->order);
        shuffle($order);
        $this->game()->order = serialize($order);
        $this->game()->save();
        if ($chat) {
            $this->chat()->extremeNine();
        }
        return FALSE;
    }
}
