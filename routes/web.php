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
*/ 
Route::post('user/login','Admin\Manager@login');
Route::post('user/info','Admin\Manager@info');

Route::post('user/logout','Admin\Manager@logout');
Route::get('/', function () {
    return view('c');
});

 