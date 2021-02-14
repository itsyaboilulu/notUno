<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**

 * model for quiz: chat
 *
 *@param INT id PRIMARY_KEY
 *@param INT gid
 *@param INT uid
 *@param STRING message
 *@param DATE created_at
 *@param DATE updated_at
 */
class chat extends Model
{
    public $timestamps = false;
    protected $table = 'chat';

    /**
     * retuns the chat log for given game
     *
     * @param int $gid game id
     * @param integer $from last id, allows only chanegs to be loaded
     * @return array collection ( 'username', 'message', 'id' )
     */
    public static function chatlog($gid,$from=0)
    {
        return DB::table('chat')
            ->select('users.username', 'chat.message','chat.id')
            ->join('users','users.id','=','chat.uid')
            ->where('chat.gid', $gid)
            ->where('chat.id','>',$from)
            ->orderBy('chat.id', 'desc')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * send a message into the given game
     *
     * @param int $gid
     * @param string $message
     * @return void
     */
    public static function send($gid,$message)
    {
        $c          = new chat();
        $c->gid     = $gid;
        $c->uid     = Auth::id();
        $c->message = $message;

        return $c->save();
    }


}
