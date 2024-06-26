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
    'prefix' => 'post'
], function ($router) {
    Route::post('/add','PostController@addPost');
    Route::get('/','PostController@getPost');
    Route::post('/{id}/update','PostController@update');
    Route::post('/{id}/comment','PostController@addComment');
    Route::post('{id}/like','PostController@addLike');
    Route::post('{id}/unlike','PostController@UnLike');
});

