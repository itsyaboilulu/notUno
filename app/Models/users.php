<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for uno: users
 *
 *@param INT id PRIMARY_KEY
 *@param STRING username
 *@param STRING password
 *@param STRING remember_token
 *@param DATE created_at
 *@param DATE updated_at
 */
class users extends Model
{
    public $timestamps = false;
    protected $table = 'users';

    /**
     * get username from given user id
     *
     * @param int $id
     * @return string
     */
    public static function getName($id)
    {
        return (users::find($id))->username;
    }

    /**
     * returns the id of given users username
     *
     * @param string $username
     * @return int
     */
    public static function getID($username)
    {
        $u = users::where('username', $username)->first();
        if ($u) {
            return $u->id;
        }
        return;
    }

    /**
     * returns t/f if username is availble
     *
     * @param string $name
     * @return void
     */
    public static function available($name)
    {
        return (count(users::where('username', $name)->get()) > 0) ?
            FALSE :
            TRUE;
    }

    /**
     * create a temp user account
     *
     * @return int $id
     */
    public static function temp()
    {

        $u = new users();
        $u->password = 123;
        $u->username = 'Temp#' . time();
        $u->save();

        return $u->id;
    }
}
