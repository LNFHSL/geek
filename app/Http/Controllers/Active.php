<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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

    // 保存
    public function save()
    {
        $input = request()->all();
        $input['uid'] = $this->user['id'];

        $input['create_time'] = time();
        $input['allAge'] = $input['age']['allAge'];
        $input['ageStar'] = $input['age']['ageStar'];
        $input['ageEnd'] = $input['age']['ageEnd'];
        unset($input['age']);
      
        // 计算截止还有多少天
        $input['time'] = round(( strtotime($input['end_time']) - $input['start_time']) / 86400);
        
        $id = DB::table("active_list")->insert($input);

        
    
        return response()->json(['msg'=>'添加成功','code'=>200]);      
    }
<<<<<<< HEAD
    // 获取活动列表
    public function getActiveList(){
        $activeArr=DB::table("active_list")->where('time','>','0')->select('id','thumb','title','money','place','time')->get();
        return $activeArr;
    }
    //获取活动详情
    public function show(){
        $id=request("id");
        $activeObj=DB::table("active_list")->where('id',$id)->get();
        return $activeObj;
    }
=======

    public function lists()
    {
        $uid=$this->user['id'];
        $w_str = [];  
		$list=db::table('active_list')
            ->where(['uid'=>$uid]) 
            ->get();
        $rtn_a =  [];
        foreach ($list as $key => $value) {
            $tmp['id'] = $value->id;
            $tmp['activeUrl'] = $value->thumb;
            $tmp['activeTitle'] = $value->title;
            $tmp['activeSprice'] = $value->money;
            $tmp['activeDiqu'] = $value->place;
            $tmp['activeTime'] = $value->time;
            $tmp['activePeople'] = $value->people;
            $rtn_a[] = $tmp; 
        }   
        return $rtn_a;
         
    }

    // 查看详情
    public function view()
    {
       $activeDetail = DB::table('active_list')->where(['id'=>request('id')])->first();
       echo  json_encode($activeDetail);
    }
    
>>>>>>> 793987f411b85175ca8b6b14917fa008684312db
}