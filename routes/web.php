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

//ssh root@106.75.230.69

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/weixinhome', 'WeixinController@home');
Route::get('/weixin', 'WeixinController@index');
Route::post('/weixin', 'WeixinController@handleMsg');
Route::any('/test', 'WeixinController@test');
Route::get('/check', 'HomeController@check');
Route::get('/order', 'OrderController@index')->name('orderHome');
Route::get('/getOrder', 'OrderController@getOrder')->name('getOrder');
Route::get('/store', 'OrderController@store')->name('store');
Route::get('/verify/getImgCode', 'VerifyCodeController@genImgCode');
