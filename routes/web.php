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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/weixin', 'WeixinController@index');
Route::get('/check', 'HomeController@check');
Route::get('/order', 'OrderController@index')->name('orderHome');
Route::get('/getOrder', 'OrderController@getOrder')->name('getOrder');
Route::get('/store', 'OrderController@store')->name('store');
