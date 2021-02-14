<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: password_resets
 *
 *@param STRING email 
 *@param STRING token 
 *@param DATE created_at 
 */
class passwordResets extends Model 
{
    public $timestamps = false;
    protected $table = 'password_resets';
}