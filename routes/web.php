<?php

use App\Models\pushPad;
use App\Models\useful;
use App\Models\userSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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

Route::get( '/settings',    [App\Http\Controllers\settingsController::class, 'index']);
Route::post('/setSettings', [App\Http\Controllers\settingsController::class, 'setSettings']);


Route::post('api/game',     [App\Http\Controllers\APIController::class, 'gameAction'] );
Route::post('api/lobby',    [App\Http\Controllers\APIController::class, 'lobby']);
Route::post('api/chat',     [App\Http\Controllers\APIController::class, 'chat']);
Route::post('api/register', [App\Http\Controllers\APIController::class, 'register']);
Route::post('api/settings', [App\Http\Controllers\APIController::class, 'settings']);

Auth::routes();

Route::get('/test', function(){
    echo app()->environment();
});
