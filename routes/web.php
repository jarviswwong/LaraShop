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
    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
    Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');

    Route::group(['middleware' => 'email_verified'], function () {
        // UserAddresses Routers
        Route::get('/user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
        Route::get('/user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
        Route::get('/user_addresses/edit/{userAddress}', 'UserAddressesController@edit')->name('user_addresses.edit');
        Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
        Route::put('/user_addresses/update/{userAddress}', 'UserAddressesController@update')->name('user_addresses.update');
        Route::delete('/user_addresses/destroy/{userAddress}', 'UserAddressesController@destroy')->name('user_addresses.destroy');
    });
});