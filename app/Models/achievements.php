<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for uno: achievements
 *
 *@param INT id PRIMARY_KEY
 *@param STRING name
 *@param STRING desc
 *@param STRING action
 *@param INT rep
 */
class achievements extends Model
{
    public $timestamps = false;
    protected $table = 'achievements';
}
