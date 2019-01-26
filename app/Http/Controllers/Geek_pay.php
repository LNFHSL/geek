<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

//设置
class Geek_pay extends Controller{
    public function weixin_notifys(){
        $list=db::table('weixin_notify')->get();
        return $list;
    }
	
	
	
	
	
}