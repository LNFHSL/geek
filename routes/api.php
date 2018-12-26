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


// 吴同学start
/**
 * 首页界面接口
 */
// 获取导航
Route::get('nav/getNavCate','Home\View@getNavCate');
// 童星邀约
Route::post('index/inviteBaby','Home\View@childInvite');
// 获取轮播
Route::get('Index/getSwriper','Home\View@getBanner');
 // 获取童星萌娃详情
Route::post('index/getbabydetial','Home\View@getChildDetail');
 // 获取筛选童星
Route::post('index/getFilterbaby','Home\View@getFilterChild');
 // 获取头条
Route::get('Index/getTops','Home\View@getHeadTiao');
 // 获取其他推荐童星
Route::post('index/gettuijianbaby2','Home\View@getOhterRecommendChild');
 // 获取推荐童星
Route::post('index/gettuijianbaby','Home\View@getRecommendChild');


/**
 * 公告类接口
 */
// 获取已参加的萌娃
Route::get('notice/getStarBaby','Notice\View@alreadyJoinChild');
// 获取公告列表
Route::post('notice/getNotice','Notice\View@getNoticeList');
// 获取公告筛选地区
Route::get('notice/getArea','Notice\View@getNoticeFilterPlace');
// 上传图片测试
Route::post('notice/testUpLoad','Notice\View@unloadImgTest');
// 获取公告筛选条件
Route::get('notice/getFilter','Notice\View@getNoticeFilterCondition');
// 获取热门公告
Route::get('notice/getHotNotice','Notice\View@geHotNotice');
 
// 获取通告详情    
Route::post('notice/getNoticeInfo','Notice\View@getNoticeInfo');
// 获取报名时童星角色的价格类型
Route::post('notice/getStarsForSignUp','Notice\View@getStarsForSignUp');
//报名
Route::post('notice/signUp','Notice\View@enroll');

// 吴同学end


Route::group(['middleware' => 'auth:api'], function(){
    Route::post('info', 'PassportController@getDetails');
	 
	
	Route::post('user/{action}', function(App\Http\Controllers\My $index, $action){
		return $index->$action();
	});
	
	Route::post('notice/{action}', function(App\Http\Controllers\Notice $index, $action){
		return $index->$action();
	});
	
	Route::post('baby/{action}', function(App\Http\Controllers\Baby $index, $action){
		return $index->$action();
	});
	Route::post('manager_notice/{action}', function(App\Http\Controllers\Notice\Manager $index, $action){
		return $index->$action();
	});
	Route::post('active/{action}', function(App\Http\Controllers\Active $index, $action){
		return $index->$action();
	});
	
});