<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => ['cors'],
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'App\Http\Controllers\AppController@login');
    Route::post('register', 'App\Http\Controllers\AppController@register');
});

