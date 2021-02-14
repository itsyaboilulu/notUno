<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: failed_jobs
 *
 *@param INT id PRIMARY_KEY
 *@param STRING uuid 
 *@param STRING connection 
 *@param STRING queue 
 *@param STRING payload 
 *@param STRING exception 
 *@param DATE failed_at 
 */
class failedJobs extends Model 
{
    public $timestamps = false;
    protected $table = 'failed_jobs';
}