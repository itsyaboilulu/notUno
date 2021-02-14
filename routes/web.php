<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\gameController;
use App\Models\card;
use App\Models\chat;
use App\Models\ckGameToMember;
use App\Models\ckModel;
use App\Models\deck;
use App\Models\game;
use App\Models\gameSettings;
use App\Models\hand;
use App\Models\gamePlayCard;
use App\Models\gameToMember;
use App\Models\playAPI;
use App\Models\useful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Expr\Print_;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/dashboard', '/', 301);

Route::get('/',         [App\Http\Controllers\homeController::class, 'index']);

Route::get('/hostnew',      [App\Http\Controllers\playController::class, 'hostNew']);
Route::get('/join',         [App\Http\Controllers\playController::class, 'join']);
Route::get('/lobby',        [App\Http\Controllers\playController::class, 'lobby']);
Route::get('/startgame',    [App\Http\Controllers\playController::class, 'startGame']);
Route::get('/play',         [App\Http\Controllers\playController::class, 'index']);


Route::post('api/game',     [App\Http\Controllers\APIController::class, 'gameAction'] );
Route::post('api/lobby',    [App\Http\Controllers\APIController::class, 'lobby']);
Route::post('api/chat',     [App\Http\Controllers\APIController::class, 'chat']);
Route::post('api/register', [App\Http\Controllers\APIController::class, 'register']);



Auth::routes();

Route::get('/test', function(){
    print_r(useful::uriDecode(NULL));


});
