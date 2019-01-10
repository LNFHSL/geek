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
        $input['allAge'] = $input['age']['allAge'];
        $input['ageStar'] = $input['age']['ageStar'];
        $input['ageEnd'] = $input['age']['ageEnd'];
        unset($input['age']);
      
        // 计算截止还有多少天
        $input['time'] = round(( strtotime($input['end_time']) -  strtotime($input['start_time'])) / 86400);
        
        $id = DB::table("active_list")->insert($input);

        
    
        return response()->json(['msg'=>'添加成功','code'=>200]);      
    }
    // 获取活动列表
    public function getActiveList(){
        $type=request("type");
        if($type==""){
            $activeArr=DB::table("active_list")
            ->where('time','>','0')
            ->select('id','thumb','title','money','place','time')
            ->orderby("id","desc")
            ->get();
        }else{
            $activeArr=DB::table("active_list")
            ->where(['film'=>$type])
            ->select('id','thumb','title','money','place','time')
            ->orderby("id","desc")
            ->get();
        };
        return $activeArr;
    }
    //获取活动详情
    public function show(){
        $activeObj=DB::table("active_list")->where(['id'=>request('id')])->get();
        $activeObj->isPart = false;
        $activeObj->isCollection = false;
        
        $user = Auth::user();
         
        if ($user) {  
               $c = DB::table("collection")
               ->where("contentid",request('id'))
               ->where("uid",$user['id'])
               ->count();
               if ($c>0) {
                      $activeObj->isCollection = true;
               }
        }
        return $activeObj;
    }
    //获取点击活动报名后的信息
    public function activeReportInfo(){
        //根据活动id和用户id获得该用户在该活动已报名的萌娃
        $babyArr=DB::table("active_baoming")->where([ ['uid',request('uid')],['activeid',request('id')] ])->select('babyid')->get();
        //活动信息及用户所有萌娃
        $info=DB::table("active_list")->where('id',request('id'))->select('start_time','end_time','place','money as sprice')->first();
        
        $info->starArr=DB::table("baby_info")->where('uid',request('uid'))->select('id','headpic','name')->get();
        foreach($info->starArr as $key => $value){  //[ {id:1},{id:2} ]  所有
            foreach($babyArr as $k => $val){        //[ {babyid:1} ]     已报名
                if($value->id == $val->babyid){
                    unset( $info->starArr[$key] );   
                };
            };
        };
        return response()->json($info);
    }
    //保存活动已报名的萌娃
    public function addActiveBaby(){
        foreach (request('babyid') as $key => $value) {
            DB::table('active_baoming')->insert([
                'babyid'=>$value['id'],'babyname'=>$value['name'],'uid'=>request('uid'),'activeid'=>request('activeid'),'connactName'=>request('connactName'),'connact'=>request('connact')
            ]);
        };
    }

    public function lists()
    {
        $uid=$this->user['id'];
        $w_str = [];  
		$list=db::table('active_list')
            ->where(['uid'=>$uid]) 
            ->orderby("id","desc")
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
    
}