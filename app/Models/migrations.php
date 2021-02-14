<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: migrations
 *
 *@param INT id PRIMARY_KEY
 *@param STRING migration 
 *@param INT batch 
 */
class migrations extends Model 
{
    public $timestamps = false;
    protected $table = 'migrations';
}