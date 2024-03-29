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

Auth::routes();



Route::get('/test', function () {
    return phpinfo();
});

Route::get('/', function () {
    return view('top');
})->name('top');


Route::get('/redirect', 'SocialAuthTwitterController@redirect');
Route::get('/callback', 'SocialAuthTwitterController@callback');


Route::get('/home', 'HomeController@index')->name('home');
