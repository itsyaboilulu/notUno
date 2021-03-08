<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * model to check for complete achievments
 *
 * @param int $uid
 */
class achievement
{

    /**
     * user id
     *
     * @var [type]
     */
    private $uid;

    /**
     * model to check for complete achievments
     *
     * @param int $uid
     */
    function __construct($uid)
    {
        $this->uid = $uid;
    }

    /**
     * returns all achievments
     *
     * @return array
     */
    public function all()
    {
        return DB::SELECT('SELECT
                a.*,
                (
                    SELECT count(*)
                    FROM member_to_achievement m
                    WHERE m.aid = a.id
                ) as achieved,
                (
                    SELECT count(*)
                    FROM users
                ) as users
            FROM achievements a;');
    }

    /**
     * returns list of achievments
     *
     * @return object
     */
    private $achievements;

    /**
     * returns list of achievments
     *
     * @return object
     */
    private function achievements()
    {
        if (!$this->achievements) {
            $this->achievements = achievements::all();
        }
        return $this->achievements;
    }

    /**
     * returns a list of achivemnets the user has
     *
     * @return array
     */
    private $achieved;

    /**
     * returns a list of achivemnets the user has
     *
     * @return array
     */
    public function achieved()
    {
        if (!$this->achieved) {
            $this->achieved = memberToAchievement::where('uid', $this->uid)->get();
        }
        return $this->achieved;
    }

    /**
     * returns a list of achievments the user has not got yet
     *
     * @param string $state
     * @return array
     */
    private function unAchieved()
    {
        $ret = [];
        foreach ($this->achievements() as $a) {
            $stop = FALSE;
            foreach ($this->achieved() as $ac) {
                if ($ac->aid == $a->id) {
                    $stop = TRUE;
                }
            }
            if (!$stop) {
                $ret[] = $a;
            }
        }
        return $ret;
    }

    /**
     * returns rep model
     *
     * @var object
     */
    private $rep;

    /**
     * returns rep model
     *
     * @var object
     */
    private function rep()
    {
        if (!$this->rep) {
            $this->rep = new rep($this->uid);
        }
        return $this->rep;
    }

    /**
     * returns stats data
     *
     * @var object
     */
    private $stats;

    /**
     * returns stats data
     *
     * @var object
     */
    private function stats()
    {
        if (!$this->stats) {
            $this->stats = $this->rep()->data();
        }
        return $this->stats;
    }

    /**
     * add a achivement as complete
     *
     * @param int $aid achievment id
     *
     * @var boolean
     */
    private function addAchievment($aid)
    {
        $m = new memberToAchievement();
        $m->aid = $aid;
        $m->uid = $this->uid;
        return $m->save();
    }

    /**
     * here so i dont have to wright this funcion 20 times
     *
     * @param int $data
     * @param int $limit
     * @param int $aid
     * @return boolean
     */
    private function basicCheck($data, $limit, $aid)
    {
        if ($data >= $limit) {
            return $this->addAchievment($aid);
        }
    }

    /**
     * check if user has get any achievments
     *
     * @param string $state
     * @return void
     */
    public function check()
    {
        foreach ($this->unAchieved() as $a) {
            if ($a->action == 'stat') {
                $this->{'check' . str_replace(' ', '', $a->name)}();
            }
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkMirrorMaster()
    {
        return $this->basicCheck($this->stats()->plays['mirror'], 100, 1);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkDecimated()
    {
        return $this->basicCheck($this->stats()->plays['dom'], 10, 2);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkPerfectGame()
    {
        return $this->basicCheck($this->stats()->plays['perfect'], 1, 3);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    public function checkANiceSuprise()
    {
        foreach ($this->unAchieved() as $a) {
            if ($a->id == 4) {
                return $this->addAchievment(4);
            }
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    public function checkJustWhy($setting)
    {
        $t1 = $setting->settings();
        $t2 = $setting->maxSettings();
        $t1['timeoutsTime'] = 60;
        $t2['timeoutsTime'] = 60;
        if ($t1 == $t2) {
            return $this->addAchievment(5);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkHurryUpAndWait()
    {
        return $this->basicCheck($this->stats()->chat['alerts']['given'], 100, 6);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkSavourTheVicotory()
    {
        $won = 0;
        $t = FALSE;
        foreach ($this->stats()->cardsByGame as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'timeout' && $n->uid == $this->uid) {
                        $t = TRUE;
                    }
                    if ($n->action == 'winner' && $n->uid == $this->uid) {
                        if ($t) {
                            $won++;
                        }
                    }
                }
                $t = FALSE;
            }
        }
        if ($won) {
            return $this->addAchievment(7);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkThirdTimesTheCharm()
    {
        $won = 0;
        $uno = FALSE;
        foreach ($this->stats()->cardsByGame as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'uno' && $n->data == 1 && $n->uid == $this->uid) {
                        $uno++;
                    }
                    if ($n->action == 'winner' && $n->uid == $this->uid) {
                        if ($uno >= 3) {
                            $won++;
                        }
                    }
                }
                $uno = 0;
            }
        }
        if ($won) {
            return $this->addAchievment(8);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkFallMeOnce()
    {
        $won = 0;
        $uno = FALSE;
        foreach ($this->stats()->cardsByGame as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'uno' && $n->data == 0 && $n->uid == $this->uid) {
                        $uno = TRUE;
                    }
                    if ($n->action == 'winner' && $n->uid == $this->uid) {
                        if ($uno) {
                            $won++;
                        }
                    }
                }
                $uno = FALSE;
            }
        }
        if ($won) {
            return $this->addAchievment(9);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkExploreTheSevenSeas($hand)
    {
        $seven = 0;
        foreach ($hand as $h) {
            if ((new card($h))->Card() == 'B7') {
                $seven++;
            }
        }
        if ($seven >= 7) {
            return $this->addAchievment(10);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    public function checkOneInaThousand()
    {
        foreach ($this->unAchieved() as $a) {
            if ($a->id == 11) {
                return $this->addAchievment(11);
            }
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkPerfectlyBalanced()
    {
        $colors = array('R' => 0, 'G' => 0, 'B' => 0, 'Y' => 0);
        foreach ($this->stats()->colors as $key => $value) {
            $colors[$key] = round(($value / $this->stats()->cards['played']) * 100);
        }
        if ( $colors['R'] == 25 && $colors['G'] == 25 && $colors['B'] == 25 ) {
            return $this->addAchievment(12);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkRambo()
    {
        return $this->basicCheck($this->stats()->plays['first'], 5, 13);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkGameOfGolf()
    {
        return $this->basicCheck($this->stats()->plays['reverse']['streak'], 18, 14);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkStreetCred()
    {
        return $this->basicCheck($this->rep()->rep(), 100, 17);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkEliteGamer()
    {
        return $this->basicCheck($this->rep()->rep(), 1337, 18);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkGoOutside()
    {
        return $this->basicCheck($this->stats()->played, 100, 19);
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkKeepItClean()
    {
        $won = 0;
        $damage = FALSE;
        foreach ($this->stats()->cardsByGame as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'play') {
                        $card = new card($n->data);
                        if ($card->damage()) {
                            $damage = TRUE;
                        }
                    }
                    if ($n->action == 'winner' && $n->uid == $this->uid) {
                        if (!$damage) {
                            $won++;
                        }
                    }
                }
                $damage = FALSE;
            }
        }
        if ($won) {
            return $this->addAchievment(20);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkGetBaited()
    {
        $play = 0;
        foreach ($this->stats()->cardsByGame as $key => $g) {
            foreach ($g as $key => $gn) {
                for ($i = 0; $i < count($gn); $i++) {
                    $n = $gn[$i];
                    if ($n->action == 'draw' && $n->uid == $this->uid) {
                        $j = 1;
                        while (TRUE) {
                            try {
                                if ($gn[$i - $j]->action == 'play'){
                                    if ((new card($gn[$i - $j]->data))->drawAmount() > 0){
                                        $play++;
                                    }
                                }
                                if ($gn[$i - $j]->action == 'draw') {
                                    break;
                                }
                                $j++;
                            } catch (Exception $e){
                                break;
                            }
                        }
                        if ($play > 1){
                            if ($play == count(gameToMember::getMembers($n->gid))) {
                                return $this->addAchievment(21);
                            }
                        }
                        $play = 0;
                    }
                }
            }
        }
    }

    /**
     * check if achievement is complete
     *
     * @param array $hand
     * @return boolean
     */
    private function checkCentury($hand)
    {
        return $this->basicCheck(count($hand), 100, 22);
    }

    /**
     * check if achievement is complete
     *
     * @param array $hand
     * @return boolean
     */
    private function checkWeFiving($hand)
    {
        $five = 0;
        foreach ($hand as $h) {
            if ((new card($h))->baseCard() == 5) {
                $five++;
            }
        }
        if ($five >= 5) {
            return $this->addAchievment(23);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkTwentyFourSeven()
    {
        $d = 0;
        $h = 0;
        foreach ($this->stats()->playTime['days'] as $day => $cards) {
            $d++;
        }
        foreach ($this->stats()->playTime['hours'] as $hour => $cards) {
            if ($cards > 0) {
                $h++;
            }
        }
        if ($d == 7 && $h == 24) {
            return $this->addAchievment(24);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    private function checkFourElements($hand){
        $r=0;$g=0;$b=0;$y=0;
        foreach ($hand as $h) {
            switch($h){
                case 'R4':
                    $r++;
                    break;
                case 'G4':
                    $g++;
                    break;
                case 'B4':
                    $b++;
                    break;
                case 'Y4':
                    $y++;
                    break;
            }
        }
        if ($r>0 && $b>0 && $g>0 && $y>0){
            return $this->addAchievment(25);
        }
    }

    /**
     * check if achievement is complete
     *
     * @return boolean
     */
    public function checkUnoSquared($card){
        if ((new card($card))->baseCard() == 1){
            return $this->addAchievment(26);
        }
    }

    /**
     * chech hand based achivements
     *
     * @param array $hand
     * @return boolean
     */
    public function checkHand($hand)
    {
        foreach ($this->unAchieved() as $u) {
            switch ($u->id) {
                case 10:
                    $this->checkExploreTheSevenSeas($hand);
                    break;
                case 22:
                    $this->checkCentury($hand);
                    break;
                case 23:
                    $this->checkWeFiving($hand);
                    break;
                case 25:
                    $this->checkFourElements($hand);
                    break;
            }
        }
    }
}
