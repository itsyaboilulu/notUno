<?php

namespace App\Models;

use phpDocumentor\Reflection\PseudoTypes\False_;

/**
 * store for standered deck of cards
 *
 * @todo move elsewhere
 */
class cards extends card
{

    protected $wild;
    protected $wilddrawfour;

    protected $yellow;
    protected $green;
    protected $blue;
    protected $red;

    protected $fixed_list;

    protected function yellow()
    {
        if ($this->yellow == NULL) {
            $this->yellow = $this->standeredList();
        }
        return $this->yellow;
    }

    protected function green()
    {
        if ($this->green == NULL) {
            $this->green = $this->standeredList();
        }
        return $this->green;
    }

    protected function blue()
    {
        if ($this->blue == NULL) {
            $this->blue = $this->standeredList();
        }
        return $this->blue;
    }

    protected function red()
    {
        if ($this->red == NULL){
            $this->red = $this->standeredList();
        }
        return $this->red;
    }

    protected function wild(){
        if ($this->wild == NULL){
            $this->wild = 4;
        }
        return $this->wild;
    }

    protected function wilddrawfour()
    {
        if ($this->wilddrawfour == NULL) {
            $this->wilddrawfour = 4;
        }
        return $this->wilddrawfour;
    }


    private function standeredList(){
        if ($this->fixed_list){
            return $this->fixed_list;
        }
        return array(
            '0' => 1,
            '1' => 2,
            '2' => 2,
            '3' => 2,
            '4' => 2,
            '5' => 2,
            '6' => 2,
            '7' => 2,
            '8' => 2,
            '9' => 2,
            'D2' => 2,
            'S' => 2,
            'R' => 2,
        );
    }



}
