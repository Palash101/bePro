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
    'prefix' => 'course/category',
    'middleware' => ['cors','auth:api','user:active'],
], function ($router) {
    Route::get('/', 'Creator\CategoryController@index');
    Route::get('/active', 'Creator\CategoryController@activeCategory');
    Route::post('/store', 'Creator\CategoryController@store');
    Route::get('/{id}/show', 'Creator\CategoryController@show');
    Route::post('/{id}/update', 'Creator\CategoryController@update');
    Route::post('/{id}/status', 'Creator\CategoryController@changeStatus');
    Route::post('/{id}/delete', 'Creator\CategoryController@destroy');
});

Route::group([
    'prefix' => 'course',
    'middleware' => ['cors','auth:api','user:active'],
], function ($router) {
    Route::get('/', 'Creator\CourseController@index');
    Route::get('/active', 'Creator\CourseController@active');
    Route::post('/store', 'Creator\CourseController@store');   
    Route::get('/{id}/show', 'Creator\CourseController@show');
    Route::post('/{id}/update', 'Creator\CourseController@update');
    Route::post('/{id}/status', 'Creator\CourseController@changeStatus');
    Route::post('/{id}/delete','Creator\CourseController@destroy');
});

Route::group([
    'prefix' => 'course/chapter',
    'middleware' => ['cors','auth:api','user:active'],
], function ($router) {
    Route::get('/{id}/', 'Creator\ChapterController@index');
    Route::post('/store', 'Creator\ChapterController@store');   
    Route::get('/{id}/show', 'Creator\ChapterController@show');
    Route::post('/{id}/update', 'Creator\ChapterController@update');
    Route::post('/{id}/delete','Creator\ChapterController@destroy');
});


Route::group([
    'prefix' => 'getCourse',
    'middleware' => ['cors'],
], function ($router) {
    Route::post('/', 'Creator\CourseController@getCoursebyDomain');
});
