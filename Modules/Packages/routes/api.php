<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/



Route::group([
    'middleware' => ['cors','auth:api','user:active'],
    'prefix' => 'packages'
], function ($router) {
    Route::get('/','Creator\PackagesController@getPackage');
    Route::post('/add','Creator\PackagesController@addPackage');
    Route::post('/{id}/update','Creator\PackagesController@update');
});

Route::group([
    'middleware' => ['cors','auth:api','user:active'],
    'prefix' => 'user'
], function ($router) {
    Route::get('/package/purchase','User\PackagesController@getPackage');
});