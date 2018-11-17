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

Route::get('/', 'PagesController@root')->name('root');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/email_verify_notice', 'PagesController@emailVerifiedNotice')->name('email_verify_notice');

    Route::group(['middleware' => 'email_verified'], function (){
        Route::get('/test', function() {
            return "Your email is verified";
        });
    });
});