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

Route::prefix('admin')->group(function() {
    Route::get('/', 'VpanelController@index')->name('dashboard');

    Route::get('/login', 'UserController@index')->name('login.show');
    Route::post('/login', 'UserController@login')->name('login.perform');

    Route::get('/restore-password', 'RestorePasswordController@index')->name('restore.show');
    Route::post('/restore-password', 'RestorePasswordController@restore')->name('restore.perform');

    Route::get('/reset-password/{token}', 'ResetPasswordController@index')->name('reset.show');
    Route::post('/reset-password', 'ResetPasswordController@reset')->name('reset.perform');

    Route::get('/logout', 'UserController@logout');

    Route::get('/{any}', 'VpanelController@index')->where('any', '.*');
});
