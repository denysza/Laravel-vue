<?php

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

Route::get('/test', function () {
    return phpinfo();
});

Route::get('/', function () {
    return view('top');
})->name('top');

Route::prefix('admin')->group(function(){
    Route::match(['get', 'post'], 'login', 'AdministratorController@login')->name('admin.login');
    Route::get('logout', 'AdministratorController@logout')->name('admin.logout');
});

Route::prefix('user')->group(function(){
    Route::match(['get', 'post'], 'login', 'UserController@login')->name('user.login');
    Route::get('top', 'UserController@top')->name('user.top');
    Route::get('logout', 'UserController@logout')->name('user.logout');
    Route::get('entry', 'UserController@create')->name('user.entry');
});

Route::prefix('painter')->group(function(){
    Route::match(['get', 'post'], 'login', 'PainterController@login')->name('painter.login');
    Route::get('top', 'PainterController@top')->name('painter.top');
    Route::get('logout', 'PainterController@logout')->name('painter.logout');
    Route::get('entry', 'PainterController@create')->name('painter.entry');
});

Route::prefix('example')->group(function(){
    Route::get('publish/{id}', 'ExampleController@publish')->name('example.publish');
    Route::get('list', 'ExampleController@list')->name('example.list');
});

Route::prefix('column')->group(function(){
    Route::get('list', 'ColumnController@list')->name('column.list');
    Route::get('create', 'ColumnController@create')->name('column.create');
});

Route::prefix('evaluation')->group(function(){
    Route::get('list', 'ExampleController@list')->name('evaluation.list');
});

Route::prefix('password')->group(function(){
    Route::get('reset/{arg}', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('reset/{arg}/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('reset', 'Auth\ResetPasswordController@reset');
});

Route::resources([
    'admin'      => 'AdministratorController',
    'column'     => 'ColumnController',
    'contract'   => 'ContractController',
    'evaluation' => 'EvaluationController',
    'example'    => 'ExampleController',
    'favorite'   => 'FavoriteController',
    'notice'     => 'NoticeController',
    'property'   => 'PropertyController',
    'proposal'   => 'ProposalController',
    'request'    => 'RequestController',
]);
