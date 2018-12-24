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
//
//Route::post('login', 'PassportController@login');
//Route::post('register', 'PassportController@register');



//登录
Route::post('user/login','Users@login');

//注册
Route::post('user/register','Users@register');

//手机验证
Route::post('user/getValidCode','Users@getValidCode');
Route::any('user/juhecurl','Users@juhecurl');

Route::post('baby/uploadheadpic', 'Baby@uploadheadpic');  //头像	
Route::post('baby/uploadimage', 'Baby@uploadimage'); //没头像	


Route::post('user/uploadJoinPic','My@uploadJoinPic');




Route::group(['middleware' => 'auth:api'], function(){
    Route::post('info', 'PassportController@getDetails');
	
	//收货地址
//	Route::post('user/addMyAddress', 'My@addMyAddress');  //添加地址
//	Route::post('user/delMyAddress', 'My@delMyAddress');  //删除地址
//	Route::post('user/editMyAddress', 'My@editMyAddress');//修改地址
//	Route::post('user/editMyAddressdz', 'My@editMyAddressdz');//修改默认地址
//	Route::post('user/getMyAddress', 'My@getMyAddress');  //查询地址
	
	Route::post('user/{action}', function(App\Http\Controllers\My $index, $action){
	return $index->$action();
	});
	
	Route::post('notice/{action}', function(App\Http\Controllers\Notice $index, $action){
	return $index->$action();
	});
	
	Route::post('baby/{action}', function(App\Http\Controllers\Baby $index, $action){
	return $index->$action();
	});
	
});