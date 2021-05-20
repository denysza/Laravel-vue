<?php

use Illuminate\Http\Request;

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



Route::prefix('stub')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('show', 'Stub\UserController@show');
        Route::post('update', 'Stub\UserController@update');
        Route::post('upload_image', 'Stub\UserController@uploadImage');
        Route::post('delete_image', 'Stub\UserController@deleteImage');
        Route::get('properties', 'Stub\UserController@properties');
        Route::get('search', 'Stub\UserController@search');
        Route::get('examplelist', 'Stub\UserController@examplelist');
    });

    Route::prefix('property')->group(function () {
        Route::post('store', 'Stub\PropertyController@store');
        Route::post('update/{id}', 'Stub\PropertyController@update');
        Route::post('destroy/{id}', 'Stub\PropertyController@destroy');
    });

    Route::prefix('painter')->group(function () {
        Route::post('show', 'Stub\PainterController@show');
        Route::post('update', 'Stub\PainterController@update');
        Route::post('upload_image', 'Stub\PainterController@uploadImage');
        Route::post('delete_image', 'Stub\PainterController@deleteImage');
        Route::post('store', 'Stub\PainterController@store');
        Route::get('images', 'Stub\PainterController@images');
        Route::post('exampleentry', 'Stub\PainterController@exampleentry');
    });
});
