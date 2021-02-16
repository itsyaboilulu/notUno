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
        $from = ($from != 'undefined')?$from:0;
        $dir = ($from)
            ? 'ASC' : 'DESC';
            $chat =  DB::select("SELECT u.username, c.message, c.id
                FROM chat c
                    INNER JOIN users u
                        ON u.id = c.uid
                WHERE c.gid  = $gid
                    AND c.id > $from
                ORDER BY c.id $dir
                LIMIT 100
            ");

        return ($from) ? $chat : array_reverse($chat);
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

        if (strpos($message, '@alert') !== false) {
            new alert($gid, Auth::id());
        }

        return $c->save();
    }


}
