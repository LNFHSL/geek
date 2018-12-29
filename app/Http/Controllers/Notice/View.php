<?php 
/**
 * 公告查看类接口
 */
namespace App\Http\Controllers\Notice;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class View extends Controller{
    public $user=null;
	public function __construct () {
        $this->user=Auth::user();
     }
    // 数据库更新函数
    public function SQLUpdate($who,$where){

    }
     // 数据库删除函数
    public function SQLDelete($who,$where){

    }
     // 数据库插入
    public function SQLInsert($who,$where){

    }
     // 数据库查询
    public function SQLSelect($who,$where){
 // 注:还可写成echo DB::table($who)->where($where)->get();
    	switch ($who){
    		// 获取公告列表
    		case 'getNoticeList':
    			  $getNoticeList = DB::table('notice_list')->where($where)->get();
                  $getNoticeList['data']=DB::table('getnoticefiltercondition')->where($where)->get();
                  return $getNoticeList;
    		break;
    		 
    		// 公告筛选地区数据库获取
    		case 'getNoticeFilterPlace':
    			  $getNoticeFilterPlace = DB::table('getnoticeFilterplace')->where($where)->get();
                  echo $getNoticeFilterPlace;
    		break;
    		// 热门通告数据库获取
    		case 'geHotNotice':
    			  $geHotNotice = DB::table('notice_list')->get();
                  echo $geHotNotice;
    		break;
    		   
    		// 获取公告筛选条件
    		case 'getNoticeFilterCondition':
    			  $getNoticeFilterCondition = DB::table('notice_list')->where($where)->get(['id','placeTop','title','talk_pay','film','place','people','endtime','time']);
                  echo $getNoticeFilterCondition;
    		break;

    	}
    }

/**
 * 公告类各接口函数
 */
    // 获取已参加的萌娃
    public function getStarBaby(){
       $juese_l = DB::table('notice_juese')->where(['notice_id'=>request('noticeid')])->get();
       $rtn_a = [];
       foreach ($juese_l as $key => $value) {
              $trtn_a['starname']  = '角色名'.(++$key) ;
              $trtn_a['babys'] = DB::table('notice_baoming')
                     ->join("baby_info","notice_baoming.babyid","=","baby_info.uid")
                     ->where(['nstarid'=>$value->id,'noticeid'=>request('noticeid')])
                     ->get();
              $rtn_a[]=$trtn_a;
       }
      
       return $rtn_a;
    }
    // 获取公告列表
    public function getNoticeList(){
       $where_eq_a = $notice_list = [];
       $ida = [];
       $is_in = false;
       if (request('type')!='') {
            $where_eq_a['film'] = request('type');
       }elseif (request('equalpay')!='') {
             $where_eq_a['talk_pay'] = request('equalpay');
       }elseif (request('sex')!='') {
              $is_in = true;
              $juese_a = DB::table('notice_juese')
              ->where(["sex"=>request('sex')])
              ->get(["notice_id"]);
       }
       elseif (request('age')!='') {
              $is_in = true;
              list($ageStar,$ageEnd) = explode("-",str_replace("岁","",request('age'))); 
              $juese_a = DB::table('notice_juese')
              ->where('ageStar','<=',$ageStar)
              ->where('ageEnd','>=',$ageEnd)
              ->get(["notice_id"]);
       }
       elseif (request('height')!='') {
              $is_in = true;
              $heightEnd = str_replace("以下","",request('height')); 
              $juese_a = DB::table('notice_juese')
              ->where('heightEnd','>=',$heightEnd)
              ->get(["notice_id"]);
       }
       if ($is_in) {
              foreach ($juese_a as $key => $value) {
                     $ida[] = $value->notice_id;
              }  
              $notice_list = DB::table('notice_list')
              ->whereIn("id",$ida)
              ->get();
       }else{
              $notice_list = DB::table('notice_list')
              ->where($where_eq_a)
              ->get(); 
       }
             
      
    	return json_encode($notice_list);
    }
    // 获取公告筛选地区
    public function getNoticeFilterPlace(){
    	$this->SQLSelect('getNoticeFilterPlace',['city'=>request('city')]);
    }
    // 上传图片测试
    public function unloadImgTest(request $request){
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
    // 获取公告筛选条件
    public function getNoticeFilterCondition(request $request){
       $nav_list = DB::table('nav_cate')
       ->where("parent_id",1)
       ->get(["id","name"]);    
       $filter[] = [
              'id'=>19,
              'mapfield'=>'type',
              'name'=>'类型',
              'data'=> $nav_list
       ];
       $filter[] = [
              'id'=>19,
              'mapfield'=>'sex',
              'name'=>'性别',
              'data'=> [
                     [
                            'id'=>1,
                            'name'=>'男'
                     ],
                     [
                            'id'=>1,
                            'name'=>'女'
                     ]
              ]
       ];
       $filter[] = [
              'id'=>19,
              'mapfield'=>'age',
              'name'=>'年龄',
              'data'=> [
                     [
                            'id'=>1,
                            'name'=>'1-5岁'
                     ],
                     [
                            'id'=>1,
                            'name'=>'5-10岁'
                     ]
              ]
       ];
       $filter[] = [
              'id'=>19,
              'mapfield'=>'height',
              'name'=>'身高',
              'data'=> [
                     [
                            'id'=>1,
                            'name'=>'100以下'
                     ],
                     [
                            'id'=>1,
                            'name'=>'70以下'
                     ]
              ]
       ];
       $filter[] = [
              'id'=>19,
              'mapfield'=>'equalpay',
              'name'=>'类型',
              'data'=> [
                     [
                            'id'=>1,
                            'name'=>'面议'
                     ],
                     [
                            'id'=>1,
                            'name'=>'片酬'
                     ],
                     [
                            'id'=>1,
                            'name'=>'付费'
                     ],
                     [
                            'id'=>1,
                            'name'=>'免费'
                     ]
              ]
       ];
       return $filter;
    }
    // 获取热门公告
    public function geHotNotice(){
        $geHotNotice = DB::table('notice_list')->orderBy("id","desc")->get();
        return $geHotNotice;
    }
    // 获取通告详情
    public function getNoticeInfo(){
       $getNoticeDetail = DB::table('notice_list')->where(['id'=>request('id')])->first();
       $juese_list = DB::table('notice_juese')->where(['notice_id'=>request('id')])->get();

       foreach ($juese_list as $key => $value) {
              if ($value->allAge) {
                     $value->age = '不限';
              }else{
                     $value->age = $value->ageStar.'-'.$value->ageEnd.'岁';
              }

              if ( $value->allHeight) {
                     $value->height = '不限';
              }else{
                     $value->height = $value->heightStar.'-'.$value->heightEnd.'cm';
              }
              $value->equalpay =  $getNoticeDetail->talk_pay;
              $value->type =  $getNoticeDetail->type;
              $getNoticeDetail->job[]=$value;
       }

       $getNoticeDetail->babys = [];
       $getNoticeDetail->isPart = false;
       $getNoticeDetail->isCollection = false;
       
       $user = Auth::user();
        
       if ($user) {  
              $c = DB::table("collection")
              ->where("contentid",request('id'))
              ->where("uid",$user['id'])
              ->count();
              if ($c>0) {
                     $getNoticeDetail->isCollection = true;
              }
       }
      
       echo json_encode($getNoticeDetail);
    }
    // 获取报名时童星角色的价格类型
    public function getStarsForSignUp(){
      
       $res = DB::table("notice_juese")
              ->join("notice_list","notice_juese.notice_id","=","notice_list.id")
              ->where("notice_id",request('noticeid'))
              ->get(["title","talk_pay as equalpay","price"]);
          
        return $res;
    }
    // 报名
    public function signUp(){
       if (request('babyid')=='') {
              return ['msg'=>'请先选择萌娃！'];
       }else{
              $c = DB::table("notice_baoming")->where(
                            ['babyid'=>request('babyid'),'nstarid'=>request('nstarid')]
                     )->count();
              if ($c>0) {
                     return ['msg'=>'你已经报过名了！'];exit();
              }
              $user=Auth::user();
              $score = DB::table("notice_juese")
              ->where(['notice_id'=>request('noticeid')])
              ->where(['id'=>request('nstarid')])
              ->value("score"); 
              DB::table("notice_baoming")->insert(
                     ['babyid'=>request('babyid'),'type'=>request('type'),'noticeid'=>request('noticeid'),'nstarid'=>request('nstarid'),'contacts'=>request('contacts'),'contactmode'=>request('contactmode'),'uid'=> $user['id']]
              );

              DB::table("notice_list")
                     ->where(['id'=>request('noticeid')])
                     ->increment("people");
              // 赠送积分
              DB::table("users")
                     ->where(['id'=>$user['id']])
                     ->increment("score",$score);      
              return ['intergal'=>$score];
       }
       
    }


}





 ?>