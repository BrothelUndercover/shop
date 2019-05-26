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

Route::get('/','PagesController@root')->name('root');

Auth::routes();

Route::middleware('auth')->group(function(){

	Route::get('/email_verify_notice','PagesController@emailVerifyNotice')->name('email_verify_notice'); //邮箱验证通知
	Route::get('/email_verification/verify','EmailVerificationController@verify')->name('email_verification.verify'); //邮箱认证逻辑
	Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send'); //手动发送邮件(邮箱认知)
	Route::middleware('email_verified')->group(function(){ //邮箱是否已验证
		Route::get('user_addresses','UserAddressesController@index')->name('user_addresses.index'); //收货地址列表
		Route::get('user_addresses/create','UserAddressesController@create')->name('user_addresses.create'); //收货地址添加
		Route::post('user_addresses','UserAddressesController@store')->name('user_addresses.store'); //收货地址添加
	});

});