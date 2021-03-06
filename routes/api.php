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
Route::post('baby/getCardModel', 'Baby@getCardModel');  
Route::any('headline/headlineLi', 'Home\View@showTt');  


Route::post('user/uploadJoinPic','My@uploadJoinPic');

Route::get('weixin/token','Weixin@token');
Route::get('weixin/menu','Weixin@menu');
Route::get('weixin/getopenid','Weixin@getopenid');
Route::any('weixin/pay_code','Weixin@pay_code');

Route::any('weixin/gethasopen','Weixin@gethasopen');
//后台管理系统 商品
Route::any('geek_ht/{action}', function(App\Http\Controllers\Geek_goods $index, $action){
	return $index->$action();
	});
	
//后台管理系统 审核加盟	
Route::any('notice/noticeTypeShow','Notice@noticeTypeShow');

Route::post('geek_qt/{action}', function(App\Http\Controllers\Geek_qt $index, $action){
	return $index->$action();
});
		
//管理头条
Route::post('headline/{action}', function(App\Http\Controllers\Headline $index, $action){
    return $index->$action();
});

//后台 设置
Route::post('geek_set/{action}', function(App\Http\Controllers\Geek_set $index, $action){
    return $index->$action();
});

//后台 通告
Route::post('geek_notice/{action}', function(App\Http\Controllers\Geek_notice $index, $action){
    return $index->$action();
});

//后台 首页推荐
Route::post('geek_home/{action}', function(App\Http\Controllers\Geek_home $index, $action){
    return $index->$action();
});

//后台 财务
Route::post('geek_pay/{action}', function(App\Http\Controllers\Geek_pay $index, $action){
	return $index->$action();
	});


//通告类型增加和删除
Route::post('notice/addNoticeType','Notice@addNoticeType');
Route::post('notice/delNoticeType','Notice@delNoticeType');
//通告查询
Route::post('notice/noticeShow','Notice@noticeShow');
//通告审核状态修改
Route::post('notice/changeStatus','Notice@changeStatus');
//查看通告的报名详情
Route::post('notice/showJoinBaby','Notice@showJoinBaby');
//删除通告
Route::post('notice/delNotice','Notice@delNotice');

//已验证萌娃查询
Route::post('baby/babyShow','Baby@babyShow');
//萌娃推荐
Route::post('baby/changeRcmd','Baby@changeRcmd');
// 吴同学start
/**
 * 首页界面接口
 */
// 获取导航
Route::get('nav/getNavCate','Home\View@getNavCate');
// 童星邀约
Route::post('index/inviteBaby','Home\View@inviteBaby');
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
// 获取分类详情
Route::post('home/getCateChild','Home\View@getCateChild');
// 获取活动列表
Route::post('index/getActiveList','Home\View@getActiveList');

/**
 * 公告类接口
 */
// 获取已参加的萌娃
Route::post('notice/getStarBaby','Notice\View@getStarBaby');
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
// 吴同学end
Route::post('active/view','Active@view');

Route::post('shop/goodsDetail','Shop@goodsDetail'); //商品详情
Route::post('shop/getFilterGoods','Shop@getFilterGoods'); //商品详情
Route::post('shop/getGoods','Shop@getGoods');  //查询商品 且查询我能买的商品

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('info', 'PassportController@getDetails');
	
	
	// 获取报名时童星角色的价格类型
	Route::post('notice/getStarsForSignUp','Notice\View@getStarsForSignUp');
	//报名
	Route::post('notice/signUp','Notice\View@signUp');
	Route::post('weixin/pay/ggg','Weixin@pay');

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
	
	Route::post('weixin/{action}', function(App\Http\Controllers\Weixin $index, $action){
		return $index->$action();
	});


});