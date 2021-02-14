<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: users
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
     * returns t/f if username is availble
     *
     * @param string $name
     * @return void
     */
    public static function available($name)
    {
        return (count(users::where('username',$name)->get()) > 0)?
            FALSE:
            TRUE;
    }

}
