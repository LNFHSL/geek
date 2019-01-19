<?php

use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
<<<<<<< HEAD
*/

//登录
Route::any('login/index','Login@login_ht');
Route::any('user/info','Login@info');
=======
*/ 
Route::post('user/login','Admin\Manager@login');
Route::post('user/info','Admin\Manager@info');
Route::post('user/logout','Admin\Manager@logout');
>>>>>>> 3797334cccb83e82ee13088571ff1b16c0d7cb1f
Route::get('/', function () {
    return view('c');
});

 