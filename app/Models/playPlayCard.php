<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * extends play, used fo funtions related to playing a card and its effects
 */
class playPlayCard extends play {

    /**
     * @see card()
     *
     * @var object
     */
    private $card;

    private $extra;

    /**
     * extends play, used fo funtions related to playing a card and its effects
     *
     * @param int $gid gameid
     * @param string $card card value
     */
    function __construct($gid,$card)
    {
        $this->card =  new card($card);
        parent::__construct($gid);
    }

    /**
     * play a card, also checks if user has called uno (only if 1 card left)
     *
     * @param boolean $uno
     * @return boolean
     */
    public function play($uno = null,$extra=NULL)
    {
        $this->extra = $extra;
        if ( $this->checkTurn() ) {
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
    private function checkUno($uno=NULL)
    {
        if ($this->gameMember()->isUno()){
            $this->chat()->uno($uno);
            $this->playByPlay()->uno($uno);
            return ($uno) ?
                $uno:
                $this->drawCard(Auth::id(), $this->settings('unoDrawPenalty'));
        }
    }

    /**
     * if card is speciel, proforms related effect
     *
     * @return boolean
     */
    private function special()
    {

        switch($this->card->isSpecial()){
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
     * reverse order of play
     *
     * @return boolean
     */
    private function reverseOrder()
    {
        $this->chat()->reverseOrder();
        $this->game()->order = serialize(array_reverse(unserialize($this->game()->order)));
        return $this->game()->save();
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
        foreach( ( new ckGameToMember($this->id, $player ) )->hand() as $h){
            if ( $this->card->stackable($h) ){
                $stackable = TRUE;
                break;
            }
        }
        if (!$stackable){
            return ($this->resolveStack()) ? TRUE : $this->drawCard($this->checkNextTurn(), $this->card->drawAmount()); ;
        }
        $this->chat()->canStack();
        $this->game()->addStack(Auth::id(),$this->card->Card(),$this->card->drawAmount() );
        return;
    }

    /**
     * resolve current stack
     *
     * @return boolean
     */
    private function resolveStack()
    {
        if (isset(unserialize($this->game()->game_data)['stack'])) {
            $this->drawCard(
                $this->checkNextTurn(),
                (unserialize($this->game()->game_data)['stack']['draw'] + $this->card->drawAmount())
            );
            return $this->game()->clearStack();
        }
        return;
    }

    /**
     * check and act upon the playing of special cards
     *
     * @return void
     */
    private function extremeCards(){
        switch ($this->card->basecard()){
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
            default:
                return;
        }
    }

    /**
     * handle the playing of a extreme 4
     *
     * @return void
     */
    private function extremeFour()
    {
        $deck = new deck(unserialize($this->game()->deck));
        while (true) {
            $d = $deck->draw();
            $draw[] = $d;
            if ( $this->card->canBePlayed($d) ){
                break;
            }
        }
        $mg = new ckGameToMember($this->game()->id, $this->checkNextTurn());
        foreach ($draw as $dr){
            $mg->addCard($dr);
        }
        $this->chat->extremeFour($this->game()->turn, $this->checkNextTurn(), count($draw));
        return;
    }

    /**
     * handle the playing of a extreme 7
     *
     * @return void
     */
    private function extremeSeven()
    {
        $target = new ckGameToMember($this->game()->id,users::getId($this->extra));
        $curr   = $this->gameMember();

        $thand = $target->hand;

        $target->hand   = $curr->hand;
        $curr->hand     = $thand;

        $curr   ->save();
        $target ->save();

        $this->chat->extremeSeven($this->extra);

        return;
    }

    /**
     * handle the playing of a extreme 0
     *
     * @return void
     */
    private function extremeZero(){
        $order  = unserialize($this->game()->order);
        foreach($order as $o){
            $ck     = new ckGameToMember($this->game()->id, $o);
            $d[]    = $ck;
            $h[]    = $ck->hand;
        }
        for($i=0;$i<count($d);$i++){
            $d[$i]->hand = $h[ (count($h) - ( $i + 1 ) ) ];
            $d[$i]->save();
        }
        return;
    }

}


