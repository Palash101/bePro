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
    'prefix' => 'pro/brands',
    'middleware' => ['cors','auth:api','user:active'],
], function ($router) {
    Route::get('/', 'Creator\BrandController@index');
    Route::get('/active', 'Creator\BrandController@activeBrand');
    Route::post('/store', 'Creator\BrandController@store');
    Route::get('/{id}/show', 'Creator\BrandController@show');
    Route::post('/{id}/update', 'Creator\BrandController@update');
    Route::get('/{id}/status', 'Creator\BrandController@changeStatus');
    Route::post('/{id}/delete', 'Creator\BrandController@destroy');
});

Route::group([
    'prefix' => 'pro/category',
    'middleware' => ['cors','auth:api','user:active'],
], function ($router) {
    Route::get('/', 'Creator\CategoryController@index');
    Route::get('/active', 'Creator\CategoryController@activeCategory');
    Route::post('/store', 'Creator\CategoryController@store');
    Route::get('/{id}/show', 'Creator\CategoryController@show');
    Route::post('/{id}/update', 'Creator\CategoryController@update');
    Route::get('/{id}/status', 'Creator\CategoryController@changeStatus');
    Route::post('/{id}/delete', 'Creator\CategoryController@destroy');
});

Route::group([
    'prefix' => 'product',
    'middleware' => ['cors','auth:api','user:active'],
], function ($router) {
    Route::get('/', 'Creator\ProductController@index');
    Route::get('/active', 'Creator\ProductController@active');
    Route::post('/store', 'Creator\ProductController@store');   
    Route::get('/{id}/show', 'Creator\ProductController@show');
    Route::post('/{id}/update', 'Creator\ProductController@update');
    Route::post('/{id}/status', 'Creator\ProductController@changeStatus');
    Route::post('/{id}/delete','Creator\ProductController@destroy');
});