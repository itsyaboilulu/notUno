<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: sessions
 *
 *@param STRING id PRIMARY_KEY
 *@param INT user_id 
 *@param STRING ip_address 
 *@param STRING user_agent 
 *@param STRING payload 
 *@param INT last_activity 
 */
class sessions extends Model 
{
    public $timestamps = false;
    protected $table = 'sessions';
}