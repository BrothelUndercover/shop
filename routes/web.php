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

// Route::get('/','PagesController@root')->name('root');

Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');
Route::get('products/{product}', 'ProductsController@show')->name('products.show');
Auth::routes();

Route::middleware('auth')->group(function(){

	Route::get('/email_verify_notice','PagesController@emailVerifyNotice')->name('email_verify_notice'); //邮箱验证通知
	Route::get('/email_verification/verify','EmailVerificationController@verify')->name('email_verification.verify'); //邮箱认证逻辑
	Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send'); //手动发送邮件(邮箱认知)
	Route::middleware('email_verified')->group(function(){ //邮箱是否已验证
		Route::get('user_addresses','UserAddressesController@index')->name('user_addresses.index'); //收货地址列表
		Route::get('user_addresses/create','UserAddressesController@create')->name('user_addresses.create'); //收货地址添加
		Route::post('user_addresses','UserAddressesController@store')->name('user_addresses.store'); //收货地址添加
		Route::get('user_addresses/{user_address}','UserAddressesController@edit')->name('user_addresses.edit'); //收货地址白编辑
		Route::put('user_addresses/{user_address}','UserAddressesController@update')->name('user_addresses.update'); //put编辑收货地址
		Route::delete('user_addresses/{user_address}','UserAddressesController@destroy')->name('user_addresses.destroy'); //删除收货地址
		//收藏
		Route::post('products/{product}/favorite','ProductsController@favor')->name('products.favor');

		//取消收藏
		Route::delete('products/{product}/favorite','ProductsController@disfavor')->name('products.disfavor');
	});

});