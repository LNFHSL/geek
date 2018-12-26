<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Db;
use App\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
/**
 * 活动接口类
 */
class Active extends Controller
{

    public $successStatus = 200;
    public $user = null;
    function __CONSTRUCT(){
        $this->user = Auth::user();
    }
    public function save()
    {
        $input = request()->all();
        $input['uid'] = $this->user['id'];
        unset($input['token']);

        $input['create_time'] = time();
        
        $id = DB::table("active_list")->insert($input);

        
    
        return response()->json(['msg'=>'添加成功','code'=>200]);      
    }
    
}