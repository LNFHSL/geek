<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use zgldh\QiniuStorage\QiniuStorage;
/**
 * 萌娃接口类
 */
class Baby extends Controller
{

    public $successStatus = 200;
    public $user = null;
    function __CONSTRUCT()
    {
        $this->user = Auth::user();
    }
    // 上传头像
    public function olduploadheadpic(Request $request)
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
            $path = '/uploads/' . $filename;
            return ['url' => $path];

        }
    }

    public function uploadheadpic(Request $request) {
        $path  = '没有文件';
         // 判断是否有文件上传
         if ($request->hasFile('file')) {
            // 获取文件,file对应的是前端表单上传input的name
            $file = $request->file('file');
            // Laravel5.3中多了一个写法
            // $file = $request->file;
 
            // 初始化
            $disk = QiniuStorage::disk('qiniu');
            // 重命名文件
            $fileName = md5($file->getClientOriginalName().time().rand()).'.'.$file->getClientOriginalExtension();
 
            // 上传到七牛
            $bool = $disk->put('iwanli/image_'.$fileName,file_get_contents($file->getRealPath()));
            // 判断是否上传成功
            if ($bool) {
                $path = $disk->downloadUrl('iwanli/image_'.$fileName);
            }else{
                return '上传失败';

            }
        }
        return ['url' => $path];
    	 
    	
    }
    

    // 萌娃卡片
    public function getBabyCard()
    {
        if (request("babyid")) {
            $info = DB::table('baby_card')->where("id", request("babyid"))->get();
            $this->user['id'] = request("babyid");
        }else{
            $info = DB::table('baby_card')->where("uid", $this->user['id'])->get();
        }
       
        $videos = DB::table('baby_video')->select("id", "url", "createtime")->where("babyid", $this->user['id'])->count();
        $shotexp = DB::table('baby_experience')->where("type", 'shot')->where("babyid", $this->user['id'])->value('content');
        $showexp = DB::table('baby_experience')->where("type", 'show')->where("babyid", $this->user['id'])->value('content');
        $awardexp = DB::table('baby_experience')->where("type", 'award')->where("babyid", $this->user['id'])->value('content');
        $rate = 100;
        if (empty($videos)) {
            $rate = $rate - 10;
        }
        if (empty($shotexp)) {
            $rate = $rate - 10;
        }
        if (empty($showexp)) {
            $rate = $rate - 10;
        }
        if (empty($awardexp)) {
            $rate = $rate - 10;
        }
        $info[0]->rate = $rate . '%';

        return response()->json($info);
    }

    // 萌娃详情
    public function getbabyinfo()
    {
        $info = DB::table('baby_info')->where("id", request('babyid'))->first();
        if ($info->lookstyle) {
         $info->lookstyle = explode(",", $info->lookstyle);
        }
        if ($info->speciality) {
        $info->speciality = explode(",", $info->speciality);
             
        }

        $info->videos = DB::table('baby_video')->select("id", "url", "createtime")->where("babyid", request('babyid'))->get();
        $info->shotexp = DB::table('baby_experience')->where("type", 'shot')->where("babyid", request('babyid'))->limit(4)->get();
        $info->showexp = DB::table('baby_experience')->where("type", 'show')->where("babyid", request('babyid'))->limit(4)->get();
        $info->awardexp = DB::table('baby_experience')->where("type", 'award')->where("babyid", request('babyid'))->limit(4)->get();

        return response()->json($info);
    }
    public function getimages()
    {

        $info = DB::table('baby_uploadimage')->select("id", "file as url")->where(request()->all())->get();

        return response()->json([
            'image' => $info
        ]);
    }
    public function delimages()  //三类经历和三类照片 多图删除
    {
        $ids = request('ids');
        if (empty($ids)) {
            return;
        }
        foreach ($ids as $key => $value) {
            $info = DB::table('baby_uploadimage')->where("id", $value)->delete();
        }
        
        DB::table('baby_card')->where("id", $this->user['id'])->decrement('images');

        return ['code' => $info];
    }
    public function delimage()
    {
        $id = request('id');
        $info = DB::table('baby_uploadimage')->where("id", $id)->delete();
        return ['code' => 66];
    }
    public function addVideo()
    {
        $input = request()->all();
        $input['createtime'] = time();
        $id = DB::table("baby_video")->insertGetId($input);
        DB::table('baby_card')->where("id", $this->user['id'])->increment('videos');
        return ['id' => $id];
    }
    public function addexperience()
    {
        $input = request()->all();
        unset($input['imgUrls']);
        $id = DB::table("baby_experience")->insertGetId($input);
        foreach (request('imgUrls') as $key => $value) {
            DB::table('baby_uploadimage')->where("file", $value)->update(['cid' => $id]);
        };
        return ['id' => $id];
    }
    public function editorexperience()
    {
        $input = request()->all();
        DB::table("baby_experience")->where(
            ['id'=>request('id')],
            ['babyid'=>request('babyid')],
            ['type'=>request('type')]
        )->update(['content' => request('content')]);
        foreach (request('imgUrls') as $key => $value) {
            DB::table('baby_uploadimage')->where("file", $value)->update(['cid' => request('id')]);
        };
        return response()->json(['msg' => '修改成功', 'code' => 200]);
    }
    public function getexperience()
    {
        if(request('type')==''){ //获取三类经历信息
            $shotexp=DB::table("baby_experience")->where('babyid',request('babyid'))->where('type','shot')->get(); //拍摄
            $showexp=DB::table("baby_experience")->where('babyid',request('babyid'))->where('type','show')->get(); //演出
            $awardexp=DB::table("baby_experience")->where('babyid',request('babyid'))->where('type','award')->get();//获奖
            return response()->json(['shotexp'=>$shotexp,'showexp'=>$showexp,'awardexp'=>$awardexp]);
        }else if(request('type')!='' && request('id')==''){  //获取某类经历信息
            $info = DB::table("baby_experience")->where('babyid',request('babyid'))->where('type',request('type'))->get();
            return response()->json($info);
        }else{  //获取某类经历的一条信息
            $input = request()->all();
            unset($input['id']);
            $info = DB::table("baby_experience")->where($input)->where('id',request('id'))->first();
            $info->image = DB::table("baby_uploadimage")->where('babyid',request('babyid'))->where('type',request('type'))->where('cid',request('id'))->select('id','file as url')->get();
            return response()->json($info);
        }
    }


    public function uploadimage(Request $request)
    {
        $file = $request->file('file');
        // 文件是否上传成功
        if ($file->isValid()) {
               // 获取文件,file对应的是前端表单上传input的name
               $file = $request->file('file');
               // Laravel5.3中多了一个写法
               // $file = $request->file;
    
               // 初始化
               $disk = QiniuStorage::disk('qiniu');
               // 重命名文件
               $fileName = md5($file->getClientOriginalName().time().rand()).'.'.$file->getClientOriginalExtension();
    
               // 上传到七牛
               $bool = $disk->put('iwanli/image_'.$fileName,file_get_contents($file->getRealPath()));
               // 判断是否上传成功
               if ($bool) {
                   $path = $disk->downloadUrl('iwanli/image_'.$fileName);
               }else{
                   return '上传失败';
   
               }
           
            if (request('type') != 'cardmod') {
                DB::table('baby_card')->where("id", request('babyid'))->increment('images');
            } else {
                DB::table('baby_card')->where("id", request('babyid'))->increment('cardmodels');
            }

        }
        $input = request()->all();
        $input['file'] = $path;
        $id = DB::table("baby_uploadimage")->insertGetId($input);
        return ['id' => $id, 'url' => $path];

    }

    // 添加萌娃
    public function addbaby()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'weight' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => '数据验证失败', 'code' => 401]);
        }


        $input = request()->all();
        $input['uid'] = $this->user['id'];
        unset($input['token']);
        $id = DB::table("baby_info")->insertGetId($input);


        DB::table("baby_card")->insert([
            'id' => $id,
            'uid' => $input['uid'],
            'name' => request('name'),
            'headpic' => request('headpic'),
        ]);
        return response()->json(['msg' => '添加成功', 'code' => 200]);

    }

    // 修改萌娃
    public function updatebaby()
    {
        $input = request()->all();

        $id = request('id');
        unset($input['token']);
        unset($input['id']);

        $id = DB::table("baby_info")->where("uid", $id)->update($input);
    }

    // 我的卡模
    public function getCardModel()
    {
        $babyid = request('babyid');
        $bianhao = request('bianhao');
        $rtn['bianhao'] = $bianhao;
        $rtn['txt'] = DB::table("baby_info")->where("id", $babyid)->first();
        $card_a = DB::table("baby_uploadimage")
            ->where("babyid", $babyid)
            ->where("cardmode", $bianhao)
            ->get();
            $rtn['image1']['url'] = $rtn['image2']['url']  = $rtn['image3']['url']  = $rtn['image4']['url']  = $rtn['image5']['url'] =  $rtn['image6']['url'] = '';    
        foreach ($card_a as $key => $value) {
            if ($value->index == 1) {
                $rtn['image1']['id'] = $value->id;
                $rtn['image1']['url'] = base64EncodeImage($value->file);
            } elseif ($value->index == 2) {
                $rtn['image2']['id'] = $value->id;
                $rtn['image2']['url'] = base64EncodeImage($value->file);
            } elseif ($value->index == 3) {
                $rtn['image3']['id'] = $value->id;
                $rtn['image3']['url'] = base64EncodeImage($value->file);
            } elseif ($value->index == 4) {
                $rtn['image4']['id'] = $value->id;
                $rtn['image4']['url'] = base64EncodeImage($value->file);
            } elseif ($value->index == 5) {
                $rtn['image5']['id'] = $value->id;
                $rtn['image5']['url'] = base64EncodeImage($value->file);
            } elseif ($value->index == 6) {
                $rtn['image6']['id'] = $value->id;
                $rtn['image6']['url'] = base64EncodeImage($value->file);
            }
        };
        return $rtn;
    }
    public function babyShow(){ //查看已认证萌娃
        $id=request()->input('id');
        if(empty($id)){
            $list=Db::table('baby_info')->where('isAuth',1)->paginate(10);
            return $list;
        }else{
            $list=Db::table('baby_info')->where('id',$id)->first();
            return response()->json($list);
        }
    }
    public function changeRcmd(){   //修改推荐    //mark
        $id=request()->input('id');
        $Recommend=request()->input('Recommend');
        $res=Db::table('baby_info')->where('id',$id)->update(['Recommend'=>$Recommend]);
        if($res){
            return ['state'=>1];
        }else{
            return ['state'=>0];
        }
    }
};


function base64EncodeImage ($image_file) {
    return $image_file;
    $base64_image = '';
    $imgPath = public_path().str_replace("/","\\",$image_file);
    $image_info = getimagesize( $imgPath );
    $image_data = fread(fopen($imgPath, 'r'), filesize($imgPath));
    $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    return $base64_image;
};