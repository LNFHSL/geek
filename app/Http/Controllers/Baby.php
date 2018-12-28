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
 * 萌娃接口类
 */
class Baby extends Controller
{

    public $successStatus = 200;
    public $user = null;
    function __CONSTRUCT(){
        $this->user = Auth::user();
    }
    // 上传头像
    public function uploadheadpic(Request $request){
        $file = $request->file('file');
        // 文件是否上传成功
        if ($file->isValid()) {

            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg

            // 上传文件
            $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            //这里的uploads是配置文件的名称
            $bool = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
            $path ='/uploads/'.$filename;
            return ['url'=>$path];

        }
    }

    public function getBabyCard()
    {
       $info = DB::table('baby_card')->where("uid",$this->user['id'])->get();
	   if($info->count()){
       $videos = DB::table('baby_video')->select("id","url","createtime")->where("babyid", $this->user['id'])->count();
       $shotexp = DB::table('baby_experience')->where("type",'shot')->where("babyid", $this->user['id'])->value('content');
       $showexp = DB::table('baby_experience')->where("type",'show')->where("babyid", $this->user['id'])->value('content');
       $awardexp = DB::table('baby_experience')->where("type",'award')->where("babyid", $this->user['id'])->value('content');
       $rate = 100;
       if (empty($videos)) {
           $rate = $rate-10;
       }
       if (empty($shotexp)) {
           $rate = $rate-10;
       }
       if (empty($showexp)) {
           $rate = $rate-10;
       }
       if (empty($awardexp)) {
           $rate = $rate-10;
       }
       $info[0]->rate = $rate.'%';

       return response()->json( $info);
	    }else{
	   	 return (['code'=>'-1','msg'=>'没有找到萌娃的信息']);;
	   }
    }

    public function  getbabyinfo()
    {
        $info = DB::table('baby_info')->where("uid",$this->user['id'])->first();
        $info->lookstyle = explode(",", $info->lookstyle);
        $info->speciality = explode(",", $info->speciality);

        $info->videos = DB::table('baby_video')->select("id","url","createtime")->where("babyid", $info->uid)->get();
        $info->shotexp = DB::table('baby_experience')->where("type",'shot')->where("babyid",$this->user['id'])->value('content');
        $info->showexp = DB::table('baby_experience')->where("type",'show')->where("babyid",$this->user['id'])->value('content');
        $info->awardexp = DB::table('baby_experience')->where("type",'award')->where("babyid",$this->user['id'])->value('content');
     
        return response()->json( $info);
    }
    public function getimages()
    {
        
        $info = DB::table('baby_uploadimage')->select("id","file as url")->where(request()->all())->get();
         
        return  response()->json( [
            'image'=>$info
        ]);
    }
    public function delimages()
    {
        $id = request('id');
        $info = DB::table('baby_uploadimage')->where("id",$id)->delete();
        DB::table('baby_card')->where("id",$this->user['id'])->decrement('images');

        return ['code'=>$info];
    }
    public function delimage()
    {
        

        return ['code'=>66];
    }
    public function addVideo()
    {
        $input = request()->all();
        $input['createtime'] = time();
        $id = DB::table("baby_video")->insertGetId($input);
        DB::table('baby_card')->where("id",$this->user['id'])->increment('videos');
        return ['id'=>$id];
    }
    public function addexperience()
    {
        $input = request()->all(); 
        $id = DB::table("baby_experience")->insertGetId($input);
        return ['id'=>$id];
    }
    public function getexperience()
    {
        $input = request()->all(); 
        $info = DB::table("baby_experience")->where($input)->first();
        return  response()->json($info);   
    }

    
    public function uploadimage(Request $request)
    {
        $file = $request->file('file');
        // 文件是否上传成功
        if ($file->isValid()) {

            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg

            // 上传文件
            $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;
            // 使用我们新建的uploads本地存储空间（目录）
            //这里的uploads是配置文件的名称
            $bool = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
            $path ='/uploads/'.$filename;
            if (request('type')!='cardmod') {
                 DB::table('baby_card')->where("id",request('babyid'))->increment('images');
            }else{
                DB::table('baby_card')->where("id",request('babyid'))->increment('cardmodels');
            }

        }
        $input = request()->all();
        $input['file'] = $path; 
        $id = DB::table("baby_uploadimage")->insertGetId($input);
        return ['id'=>$id,'url'=>$path];

    }
    public function addbaby()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'weight' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg'=>'数据验证失败','code'=>401]);          
        }

        
        $input = request()->all();
        $input['uid'] = $this->user['id'];
        unset($input['token']);
        $id = DB::table("baby_info")->insert($input);

        
        DB::table("baby_card")->update([
            'id'=>$input['uid'],
            'name'=>request('name')
            ]);
        return response()->json(['msg'=>'添加成功','code'=>200]);          

    }

    public function updatebaby()
    {
        $input = request()->all();
        
        $id = request('id');
        unset($input['token']);
        unset($input['id']);

        $id = DB::table("baby_info")->where("id",$id)->update($input);
    }
    
}