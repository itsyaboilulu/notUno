<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: logs
 *
 *@param INT id PRIMARY_KEY
 *@param STRING env 
 *@param STRING message 
 *@param UNASSIGNED level 
 *@param STRING context 
 *@param STRING extra 
 *@param DATE created_at 
 *@param DATE updated_at 
 *@param DATE deleted_at 
 */
class logs extends Model 
{
    public $timestamps = false;
    protected $table = 'logs';
}