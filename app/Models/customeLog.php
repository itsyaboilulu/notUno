<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: customeLog
 *
 *@param INT id PRIMARY_KEY
 *@param STRING message 
 *@param STRING context 
 *@param STRING level 
 *@param STRING level_name 
 *@param STRING channel 
 *@param STRING record_datetime 
 *@param STRING extra 
 *@param STRING formatted 
 *@param STRING remote_addr 
 *@param STRING user_agent 
 *@param DATE created_at 
 */
class customeLog extends Model 
{
    public $timestamps = false;
    protected $table = 'customeLog';
}