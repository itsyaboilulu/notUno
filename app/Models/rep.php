<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

/**
 * calculates the users repuation score
 *
 * @todofull score isnt fully calulted yet
 */
class rep extends stats
{

    /**
     * userid
     *
     * @var int
     */
    private $id;

    /**
     * calculates the users repuation score
     *
     * @todofull score isnt fully calulted yet
     */
    function __construct($id = NULL)
    {
        $this->id = ($id) ? $id : Auth::id();
        parent::__construct($id);
    }

    /**
     * return users repulatation score
     *
     * @return int
     */
    public function rep()
    {
        return $this->plusRep() -  $this->minusRep();
    }

    /**
     * calculate posative repulatation score
     *
     * @return int
     */
    private function plusRep()
    {
        return array_sum(
            array(
                $this->gamesPlayed(),
                ($this->gamesWon() * 5),
                $this->calledUno()['called'],
                $this->golf()['plus'],
                $this->mirror(),
            )
        );
    }

    /**
     * calculate negaive repulatation score
     *
     * @return int
     */
    private function minusRep()
    {
        //add breaking chains
        return array_sum(
            array(
                ($this->timeOuts() * 5),
                $this->calledUno()['failed'],
                $this->golf()['lost'],
                $this->firstBlood(),
            )
        );
    }

    /**
     * returns the number of reverse battles
     *
     * @return int
     */
    private function golf()
    {
        $points         = 0;
        $minus          = 0;
        foreach ($this->cardsByGame() as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'play') {
                        if (substr($n->data, 1, 1) == 'R') {
                            if ($n->uid == $this->id || (isset($gn[($i + 1)]) && $gn[($i + 1)]->uid == $this->id)) {
                                $points++;
                                if ((isset($gn[($i + 1)]) && $gn[($i + 1)]->uid == $this->id)) {
                                    if (substr($gn[($i + 1)]->data, 1, 1) != 'R') {
                                        $minus++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return [
            'plus'  => $points,
            'lost'  => ($minus * 2),
        ];
    }
}
