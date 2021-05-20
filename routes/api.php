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
    Route::prefix('top')->group(function () {
        Route::post('painters', 'Stub\TopController@painters');
        Route::post('exsamples', 'Stub\TopController@exsamples');
        Route::post('columns', 'Stub\TopController@columns');
    });

    Route::prefix('user')->group(function(){
        Route::post('/', 'Stub\UserController@entry');
        Route::post('login', 'Stub\UserController@login');
    });

    Route::prefix('painter')->group(function(){
        Route::post('/', 'Stub\PainterController@entry');
        Route::post('login', 'Stub\PainterController@login');
    });

    Route::prefix('config')->group(function(){
        Route::post('select', 'Stub\ConfigController@select');
    });

    Route::prefix('column')->group(function(){
        Route::post('store', 'Stub\ColumnController@store');
    });
});

Route::match(['get', 'post'], 'contact', 'ContactController@contact');

Route::post('/message', 'MessageController@get')->name('message.get');
Route::put('/message', 'MessageController@store')->name('message.send');
Route::get('/pdf/{name}', 'MessageController@pdf')->name('message.pdf');
Route::get('/message/clear', 'MessageController@clear')->name('message.clear');

Route::match(['get', 'post', 'put'], 'estimate', 'WorkflowController@estimate');
Route::match(['get', 'post'], 'proposal', 'WorkflowController@proposal');
Route::get('/properties/{user_id}', 'WorkflowController@get_properties');
Route::get('/favorites/{user_id}', 'WorkflowController@get_favorites');
Route::get('/messages/{id}/{kbn}', 'WorkflowController@get_messages');

Route::get('/workflow/user', 'WorkflowController@workflow_u');
Route::get('/workflow/painter', 'WorkflowController@workflow_p');
Route::get('/negotiation/{id}', 'WorkflowController@negotiation');
Route::get('/contract/{id}', 'WorkflowController@contract');
Route::get('/finish/{id}', 'WorkflowController@finish');
Route::post('/complete', 'WorkflowController@complete');
Route::get('/test/{id}/{status}', 'WorkflowController@test');
