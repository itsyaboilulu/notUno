<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * display given players stats
 *
 * @param int $id userid
 */
class stats
{

    /**
     * user id
     *
     * @var int
     */
    private $id;

    /**
     * display given players stats
     *
     * @param int $id userid
     */
    function __construct($id = NULL)
    {
        $this->id = ($id) ? $id : Auth::id();
    }

    /**
     * @see cards()
     *
     * @var object
     */
    private $cards;

    /**
     * returns data from playbyplay for logged in user
     *
     * @return object
     */
    protected function cards()
    {
        if (!$this->cards) {
            $this->cards = playByPlay::where('uid', $this->id)->get();
        }
        return $this->cards;
    }

    /**
     * breakdown cards played into individul games
     */
    private $cardByGame;
    /**
     * breakdown cards played into individul games
     *
     * @return array
     */
    protected function cardsByGame($gid = NULL, $game_no = NULL)
    {
        if (!$this->cardByGame) {
            $games = [];
            foreach (playByPlay::select('gid', 'game_no')->where('uid', $this->id)->groupBy('gid', 'game_no')->get() as $p) {
                if (!isset($games[$p->gid])) {
                    $games[$p->gid] = array();
                }
                $games[$p->gid][] = ($p->game_no) ? $p->game_no : 0;
            }
            $ret = [];
            foreach ($games as $key => $g) {
                if (!isset($ret[$key])) {
                    $ret[$key] = [];
                }
                foreach ($g as $gn) {
                    $ret[$key][$gn] = playByPlay::where('gid', $key)->where('game_no', $gn)->get();
                }
            }
            $this->cardByGame = $ret;
        }
        return ($gid) ? $this->cardByGame[$gid] : $this->cardByGame;
    }

    /**
     * chat messages that include the user
     *
     * @var object
     */
    private $chat;

    /**
     * chat messages that include the user
     *
     * @var object
     */
    protected function chat()
    {
        if (!$this->chat) {
            $this->chat = DB::select('SELECT gid, uid, message, target FROM chat c WHERE uid = ? OR target = ? OR uid = 0', [$this->id, $this->id]);
        }
        return $this->chat;
    }

    /**
     * @see leaderboards()
     * @var object
     */
    private $leaderboards;

    /**
     * returns leaderboard stats for logged in user
     *
     * @return object
     */
    protected function leaderboards()
    {
        if (!$this->leaderboards) {
            $sql = "SELECT l.gid, sum(l.wins) as wins,
                    (
                        SELECT sum(g.wins)
                        FROM game_leaderboard g
                        WHERE g.gid = l.gid
                    ) as games
                FROM game_leaderboard l
                WHERE l.uid = ?
                GROUP BY l.gid;";
            $this->leaderboards =  DB::select($sql, [$this->id]);
        }
        return $this->leaderboards;
    }

    private $pageData;

    private $data;
    /**
     * package functions for esayer use in other models
     *
     * @return object
     */
    public function data()
    {
        if (!$this->data) {
            $d = $this->pageData();
            $d['cardsByGame'] = $this->cardsByGame();
            $this->data = (object)  $d;
        }
        return $this->data;
    }

    /**
     * package page data (esayer to pass data to vue)
     *
     * @return array
     */
    public function pageData()
    {
        if (!$this->pageData) {
            if ($this->cardsPlayed()) {
                $this->pageData = array(
                    'favCard'   => $this->favCard(),
                    'colors'    => $this->colorBreakdown(),
                    'wins'      => $this->gamesWon(),
                    'played'    => $this->gamesPlayed(),
                    'cards'     => [
                        'played'    => $this->cardsPlayedCount(),
                        'drawn'     => $this->cardsDrawn(),
                        'special'   => $this->specialCards()
                    ],
                    'uno'       => $this->calledUno(),
                    'timeout'   => $this->timeOuts(),
                    'chat'          => [
                        'alerts'    => $this->alerts(),
                        'sent'      => $this->messagesSent(),
                        'wisper'    => $this->wispers(),
                    ],
                    'playTime'      => [
                        'days'      => $this->activeDays(),
                        'hours'     => $this->activeHours(),
                    ],
                    'plays'         => [
                        'reverse'   => $this->longestGolf(),
                        'mirror'    => $this->mirror(),
                        'skip'      => $this->skips(),
                        'first'     => $this->firstBlood(),
                        'dom'       => $this->drawCause(),
                        'perfect'   => $this->perfectGame(),
                    ]

                );
            } else {
                $this->pageData = ['cards' => ['played' => 0]];
            }
        }
        return $this->pageData;
    }

    /**
     * returns a list of cards played
     *
     * @return array
     */
    protected function cardsPlayed()
    {
        $ret = [];
        foreach ($this->cards() as $c) {
            if ($c->action == 'play') {
                $ret[] = $c->data;
            }
        }
        return $ret;
    }


    /**
     * returns the users most played card
     *
     * @return string
     */
    protected function favCard()
    {
        $ret = array();
        foreach ($this->cardsPlayed() as $c) {
            $ret[$c] = (isset($ret[$c])) ?
                $ret[$c] + 1 :
                1;
        }
        asort($ret);
        return array_reverse(array_keys($ret))[0];
    }

    /**
     * returns the number of cards the logged in user has played
     *
     * @return int
     */
    protected function cardsPlayedCount()
    {
        return count($this->cardsPlayed());
    }

    /**
     * returns the number of cards the logged in user has drawn
     *
     * @return int
     */
    protected function cardsDrawn()
    {
        $count = 0;
        foreach ($this->cards() as $c) {
            if ($c->action == 'draw') {
                $count = $count + $c->data;
            }
        }
        return $count;
    }

    /**
     * returns array of the % of each color player has played
     *
     * @return array
     */
    protected function colorBreakdown()
    {
        $colors = array('R' => 0, 'G' => 0, 'B' => 0, 'Y' => 0);
        foreach ($this->cardsPlayed() as $c) {
            $colors[substr($c, 0, 1)]++;
        }
        foreach ($colors as $key => $value) {
            $ret[$key] = round(($value / $this->cardsPlayedCount()) * 100);
        }
        return $ret;
    }

    protected function specialCards()
    {
        $arr = array('D2' => 0, 'WD4' => 0, 'R' => 0, 'S' => 0, 'W' => 0);
        foreach ($this->cardsPlayed() as $c) {
            if (isset($arr[substr($c, 1)])) {
                $arr[substr($c, 1)]++;
            }
        }
        return $arr;
    }

    /**
     * returns the number of games user has played
     *
     * @return int
     */
    protected function gamesPlayed()
    {
        $p = 0;
        foreach ($this->leaderboards() as $l) {
            $p = $p + $l->games;
        }
        return $p;
    }

    /**
     * returns the number of games user has won
     *
     * @return int
     */
    protected function gamesWon()
    {
        $p = 0;
        foreach ($this->leaderboards() as $l) {
            $p = $p + $l->wins;
        }
        return $p;
    }

    /**
     * returns list of called and failed to call unos
     *
     * @return array
     */
    protected function calledUno()
    {
        $uno = array('called' => 0, 'failed' => 0);
        foreach ($this->cards() as $c) {
            if ($c->action == 'uno') {
                if ($c->data) {
                    $uno['called']++;
                } else {
                    $uno['failed']++;
                }
            }
        }
        return $uno;
    }

    /**
     * returns the number of times the user has ran out of time
     *
     * @return int
     */
    protected function timeOuts()
    {
        $ret = 0;
        foreach ($this->cards() as $c) {
            if ($c->action == 'timeout') {
                $ret++;
            }
        }
        return $ret;
    }

    /**
     * returns a count of how many alerts have been sent and recieved
     *
     * @return array
     */
    protected function alerts()
    {
        $alerts     = 0;
        $alerter    = 0;
        foreach ($this->chat() as $c) {
            if (strpos($c->message, "alerted " . users::getName($this->id)) !== false) {
                $alerts++;
            }
            if (strpos($c->message, users::getName($this->id) . " alerted") !== false) {
                $alerter++;
            }
        }
        return [
            'given'     => $alerter,
            'recived'   => $alerts
        ];
    }

    /**
     * returns a count of how many messages sent
     *
     * @return int
     */
    protected function messagesSent()
    {
        $s = 0;
        foreach ($this->chat() as $c) {
            if ($c->uid == $this->id) {
                $s++;
            }
        }
        return $s;
    }

    /**
     * returns a count of how many @wispers user has done
     *
     * @return int
     */
    protected function wispers()
    {
        $s = 0;
        foreach ($this->chat() as $c) {
            if ($c->uid == $this->id && ($c->target != 0 || $c->target != NULL)) {
                $s++;
            }
        }
        return $s;
    }

    /**
     * returns a count of what days cards where played
     *
     * @return array
     */
    protected function activeDays()
    {
        $ret = [];
        foreach ($this->cards() as $c) {
            $d = new DateTime($c->created_at);
            $ret[$d->format("l")] = (isset($ret[$d->format("l")])) ? ($ret[$d->format("l")] + 1) : 1;
        }
        return $ret;
    }

    /**
     * returns a count of what hours cards where played
     *
     * @return array
     */
    protected function activeHours()
    {
        $ret = [
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
        ];
        foreach ($this->cards() as $c) {
            $d = new DateTime($c->created_at);
            $ret[intval($d->format("H"))]++;
        }
        return $ret;
    }

    /**
     * returns the lobngest streak of reverse cards
     *
     * @return void
     */
    protected function longestGolf()
    {
        $maxstreak      = 1;
        $streak         = 0;
        $checkUsername  = FALSE;
        $with           = 0;
        foreach ($this->cardsByGame() as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'play') {
                        if (substr($n->data, 1, 1) == 'R') {
                            if ($n->uid == $this->id || (isset($gn[($i + 1)]) && $gn[($i + 1)]->uid == $this->id)) {
                                $streak++;
                            } else {
                                $streak = 0;
                            }
                        } else {
                            $streak = 0;
                        }
                    }
                    if ($maxstreak < $streak) {
                        $maxstreak  = $streak;
                        $with = ($n->uid != $this->id) ?
                            $n->uid : ((isset($gn[($i + 1)])) ?
                                $gn[($i + 1)]->uid :
                                $gn[($i - 1)]);
                    }
                }
                $streak = 0;
            }
        }
        return ['streak' => $maxstreak, 'with' => users::getName($with)];
    }

    /**
     * returns the number of skips the user has played
     *
     * @return int
     */
    protected function skips()
    {
        $skip = 0;
        foreach ($this->cardsPlayed() as $c) {
            if (substr($c, 1, 1) == 'S') {
                $skip++;
            }
        }
        return $skip;
    }

    /**
     * returns the number of mirror cards (playing same card) the user has played
     *
     * @return int
     */
    protected function mirror()
    {
        $mirror = 0;
        foreach ($this->cardsByGame() as $key => $p) {
            foreach ($p as $key => $c) {
                for ($i = 1; $i < count($c); $i++) {
                    if (($c[$i]->uid == $this->id)
                        && ($c[$i]->action == 'play' && $c[$i - 1]->action == 'play')
                        && ($c[$i]->data === $c[$i - 1]->data)
                    ) {
                        $mirror++;
                    }
                }
            }
        }
        return $mirror;
    }

    /**
     * returns the number of games user was first to play a damage card
     *
     * @return int
     */
    protected function firstBlood()
    {
        $fb = 0;
        foreach ($this->cardsByGame() as $key => $p) {
            foreach ($p as $key => $c) {
                for ($i = 1; $i < count($c); $i++) {
                    if ($c[$i]->action == 'play') {
                        if ((new card($c[$i]->data))->damage()) {
                            if ($c[$i]->uid == $this->id) {
                                $fb++;
                            }
                            break;
                        }
                    } else if ($c[$i]->action == 'draw') {
                        if ((new card($c[$i - 1]->data))->damage()) {
                            if ($c[$i - 1]->uid == $this->id) {
                                $fb++;
                            }
                            break;
                        }
                    }
                }
            }
        }
        return $fb;
    }

    /**
     * returns the most cards the user has managed to make another player draw
     *
     * @return integer
     */
    protected function drawCause()
    {
        $maxdraw    = 0;
        foreach ($this->cardsByGame() as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'draw') {
                        if (isset($gn[($i - 1)]) && ($gn[($i - 1)])->uid == $this->id) {
                            $maxdraw = ($n->data > $maxdraw) ? $n->data : $maxdraw;
                        }
                    }
                }
            }
        }
        return $maxdraw;
    }

    /**
     * returns number of games user has won without drawing a card
     * @return  int
     */
    protected function perfectGame()
    {
        $won = 0;
        $draw = FALSE;
        foreach ($this->cardsByGame() as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'draw' && $n->uid == $this->id) {
                        $draw = TRUE;
                    }
                    if ($n->action == 'winner' && $n->uid == $this->id) {
                        if (!$draw) {
                            $won++;
                        }
                    }
                }
                $draw = FALSE;
            }
        }
        return $won;
    }
}
