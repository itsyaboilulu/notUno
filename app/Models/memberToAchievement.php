<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: member_to_achievement
 *
 *@param INT aid PRIMARY_KEY
 *@param INT uid PRIMARY_KEY
 */
class memberToAchievement extends Model 
{
    public $timestamps = false;
    protected $table = 'member_to_achievement';
}