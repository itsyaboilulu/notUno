<?php

namespace App\Models;

use App\Http\Controllers\settingsController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * model for quiz: game
 *
 *@param INT id PRIMARY_KEY
 *@param STRING name
 *@param STRING password
 *@param STRING current_card
 *@param STRING deck
 *@param STRING base_deck
 *@param INT turn
 *@param STRING order
 *@param STRING settings
 */
class game extends Model
{
    public $timestamps = false;
    protected $table = 'game';

    /**
     * returns game instance using password instead of id
     *
     * @param string $password
     * @return object game
     */
    public static function gameFromPassword($password)
    {
        return game::findOrFail( ( game::where('password', $password)->first() )->id);
    }

    /**
     * returns page data for loaded game
     *
     * @return array
     */
    public function gameData()
    {
        return array(
            'password'  => $this->password,
            'currcard'  => $this->current_card,
            'turn'      => users::getName($this->turn),
            'order'     => $this->order,
            'extreme7'  => (@unserialize($this->setting)['extreme7'])? unserialize($this->setting)['extreme7'] : 0,
        );
    }

    /**
     * refresh sessioned data
     */
    public static function refreshSession()
    {
        session(['game'=>game::find(session('game')->id)]);
    }

    /**
     * generates a unique password for each game
     *
     * @param string $str
     * @return string
     */
    public function generatePassword($str)
    {
        $this->password = base64_encode($str . time() . 'xX_UnoMadLadz_Xx');
        return $this->password;
    }

    /**
     * gets all members of the loaded game
     *
     * @return array
     */
    public function getMembers()
    {
        return gameToMember::getMembers($this->id);
    }

    /**
     * add a user to the game
     *
     * @param id $id user id
     * @return boolean
     */
    public function addMember($id)
    {
        return (gameToMember::addMember($id, $this->id)) ?
            gameLeaderboard::addMember($id, $this->id) :
            FALSE ;
    }

    /**
     * retruns t/f is sessioned user is the admin of loaded game
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return ( gameToMember::where('gid',$this->id)->where('uid',Auth::id())->first() )->admin;
    }

    /**
     * begin setup to start a new game
     *
     * @return boolean
     */
    public function startGame($settings = NULL)
    {

        $set = new gameSettings($this->id);
        $set->setSettings($settings);
        $set->save();

        $hand = new hand( NULL, useful::unserialize( $this->deck ) );

        foreach ($this->getMembers() as $m) {
            $mg = new ckModel('game_to_member', [
                'gid' => $this->id,
                'uid' => $m->id
            ]);
            $mg->hand = serialize($hand->newHand());
            $mg->save();
            $members[] = $m->id;
        }

        shuffle($members);

        $this->current_card = $hand->draw(TRUE);
        $this->turn         = $members[0];
        $this->order        = serialize($members);
        $this->started      = 1;

        return $this->save();
    }

    /**
     * commit stack data into game_data
     *
     * @param int $user
     * @param string $card
     * @param int $draw
     * @return boolean
     */
    public function addStack($user,$card,$draw)
    {
        $data = unserialize($this->game_data);
        if (!isset($data['stack'])){
            $data['stack'] = array(
                'user'=>0,
                'card'=>0,
                'draw'=>0
            );
        }
        $data['stack']['user'] = $user;
        $data['stack']['card'] = $card;
        $data['stack']['draw'] = $data['stack']['draw'] + $draw;

        $this->game_data = serialize($data);
        return $this->save();
    }

    /**
     * removes stack data from game_data
     *
     * @return boolean
     */
    public function clearStack()
    {
        $data            = unserialize($this->game_data);
        $data['stack']   = NULL;
        $this->game_data = serialize($data);
        return $this->save();
    }

    public static function newGame($name, $pass){
        $game = new game();
        $game->generatePassword($pass);
        $game->name = $name;
        return ($game->save()) ? $game : NULL;
    }


    /**
     * before deleteing game delete related memberdata
     *
     * @return void
     */
    public function delete()
    {
        //remove members
        foreach($this->getMembers() as $m){
            (new ckGameToMember($this->id,$m->id))->delete();
        }
        //keep play by play but delete chat
        chat::deleteAll($this->id);

        //delete
        return parent::delete();
    }

    /**
     * returns card model for current_card data
     *
     * @return object
     */
    public function card(){
        return new card($this->current_card);
    }

}
