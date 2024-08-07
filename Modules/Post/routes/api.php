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
    Route::post('/add','Creator\PostController@addPost');
    Route::get('/','Creator\PostController@getPost');
    Route::get('/{id}/show','Creator\PostController@show');
    Route::post('/{id}/update','Creator\PostController@update');
    Route::post('/{id}/comment','Creator\PostController@addComment');
    Route::post('{id}/like','Creator\PostController@addLike');
    Route::post('{id}/unlike','Creator\PostController@UnLike');
    Route::post('images/{id}/delete','Creator\PostController@attachmentsDelete');
    Route::post('/{id}/delete', 'Creator\PostController@destroy');
});

Route::group([
    //'middleware' => ['cors','auth:api','user:active'],
    'prefix' => 'user'
], function ($router) {
    Route::get('/getPost','User\PostController@getPost');
});


Route::group([
    'prefix' => 'getPost',
    'middleware' => ['cors'],
], function ($router) {
    Route::post('/', 'Creator\PostController@getPostbyDomain');
});

