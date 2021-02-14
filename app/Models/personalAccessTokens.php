<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**

 * model for quiz: personal_access_tokens
 *
 *@param INT id PRIMARY_KEY
 *@param STRING tokenable_type 
 *@param INT tokenable_id 
 *@param STRING name 
 *@param STRING token 
 *@param STRING abilities 
 *@param DATE last_used_at 
 *@param DATE created_at 
 *@param DATE updated_at 
 */
class personalAccessTokens extends Model 
{
    public $timestamps = false;
    protected $table = 'personal_access_tokens';
}