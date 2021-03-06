<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

use zgldh\QiniuStorage\QiniuStorage;
class My extends Controller{
	   public $user = null;
	    function __CONSTRUCT(){
	        $this->user = Auth::user();
	    }
	  public function addMyAddress(){  //添加地址
	    
		$uid=$this->user['id'];
	  	$name=request('name');
		$tel=request('tel');
		$province=request('province');
		$city=request('city');
		$area=request('area');
		$address=request('address');
		$isdefault=request('isdefault');
		
	  $list=db::table('address')->insert([
	  'name'=>$name,'tel'=>$tel,'province'=>$province,'city'=>$city,
	  'area'=>$area,'address'=>$address,'isdefault'=>$isdefault,'uid'=>$uid]);
	  }
	  
	 public function delMyAddress(){  //删除地址
	      $list=db::table('address')->where('id',request('id'))->delete();

      }
	 
	 public function editMyAddress(){  //更新地址
	
	    $uid=$this->user['id'];
	    $id=request('id');
	  	$name=request('name');
		$tel=request('tel');
		$province=request('province');
		$city=request('city');
		$area=request('area');
		$address=request('address');
		$isdefault=request('isdefault');
      if($isdefault >=1){
	  	  db::table('address')->where('uid',$uid)->update(['isdefault'=>'0']);
	  	  db::table('address')->where('id',$id)->update(['isdefault'=>'1']);
	 
	  }
       db::table('address')->where('id',$id)->update([
	  'name'=>$name,'tel'=>$tel,'province'=>$province,'city'=>$city,
	  'area'=>$area,'address'=>$address]);
	  
	  
      }
	 public function editMyAddressdz(){  //更新默认地址
	  
	    $uid=$this->user['id'];
	    $id=request('id');
		$isdefault='1';
		 if($isdefault >=1){
		  	  db::table('address')->where('uid',$uid)->update(['isdefault'=>'0']);
		  	  db::table('address')->where('id',$id)->update(['isdefault'=>'1']);
		 
		  }
	 
	 }
	 public function getMyAddress(){  //查询地址
	      $user = Auth::user();
		  $list=db::table('address')->where('uid',$user['id'])->get();
		  return $list;
      }
	 
	 public function getMessage(){   //我的消息
	 	 $uid=$this->user['id'];
		 $list=db::table('info')->where('uid',$uid)->get();
		 return $list;
	 }
	 public function getMessage_unread(){ //未读
		$list=db::table('info')->where([['uid',$this->user['id']],['isread','0']])->get();
		return $list;
	 }
	 public function getMessage_read(){   //设置添加已读
	  $list_one=db::table('info')->where('id',request('id'))->value('isread');
		  if($list_one == 0){
		  	db::table('info')->where('id',request('id'))->increment('isread');
		  }  
	 }
	 
	 
	 public function getMyBalance(){  //我的余额
	 	$uid=$this->user['id'];
		$money_list=db::table('users')->where('id',$uid)->value('money');
		$record_list=db::table('pay_record')->where('uid',$uid)->get();
		return ['balance'=>$money_list,'payrecord'=>$record_list];

	 }
	  
	 public function opinion(){  //反馈
	 	$time=time();	
		db::table('opinion')->insert(['uid'=>$this->user['id'],'opinion'=>request('opinion'),
		'time'=>$time]);
	 }
	 
	 
	
	 public function uploadJoinPic(Request $request){   //上传图片  通用
	    
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
	  public function myJoin(){  //加盟
	      $uid=$this->user['id'];
		  $shopname=request('shopname');
	      $contacter=request('contacter');
		  $contacttel=request('contacttel');
		  $province=request('province');
		  $city=request('city');
		  $area=request('area');
		  $address=request('address');
		  $students=request('students');
		  $subjects=request('subjects');
		  $type=request('type');
		  $imgs= json_encode(request('imgs'));
 
	 	$lsit=db::table('league')->insert([
	 	    'uid'=>$uid,'shopname'=>$shopname,
	 	    'contacter'=>$contacter,'contacttel'=>$contacttel,
	 	    'province'=>$province,'city'=>$city,
	 	    'area'=>$area,'address'=>$address,
	 	    'students'=>$students,'subjects'=>$subjects,
	 	    'type'=>$type,'imgs'=>$imgs
	 	]);
	 }
	 public function getsellerInfo(){   //获取商家或合伙人资料
		$uid=$this->user['id'];
		$info=DB::table('league')->where('uid',$uid)->get();
		// 
		return $info;
	 }
	  //------------------------------前端未写----------------------
	  
	public function balance_tips(){  //余额支付提示  
	    $uid=$this->user['id'];
	    $list=db::table('pay_record')->where([['uid',$uid],['tips','0']])->get();
		return $list;  
		
	}  
    public function balance_modify(){  //余额通知 改成已通知
    	$uid=$this->user['id'];
		db::table('pay_record')->where('uid',$uid)->update(['tips'=>'1']);
    }
	
	public function open_member(){     //开通vip
		$vip_type=request('vip_type'); 
		$uid=$this->user['id'];
		$date=date();
		$list=db::table('users')->where('uid',$uid)->update(['member'=>$vip_type]);
		
		if($vip_type == 1){
			db::table('pay_record')->insert([
		   'uid'=>$uid,'type'=>'4','price'=>'1000','title'=>'开通vip','datetime'=>$date
		]);
		}else{
			db::table('pay_record')->insert([
		   'uid'=>$uid,'type'=>'4','price'=>'2000','title'=>'开通svip','datetime'=>$date
		]);
		}
		
		return $list;
		
	}

	// 添加收藏
	// notice为通告，baby为童星,activity为活动，product为产品,传值为英文
	public function addCollection()
	{  
		$input = request()->all();
		$input['add_time'] = time();
		$input['uid'] = $this->user['id'];
		unset($input['token']);
        $id = DB::table("collection")->insertGetId($input);
        return ['id'=>$id];
	}

	// 删除收藏
	public function delCollection()
	{  
		$res = DB::table("collection")
			->where("contentid",request('contentid'))
			->where("uid",$this->user['id'])
			->delete();
        return ['res'=>$res];
	}

	// 修改头像
	public function changeUinfo()
	{
		$w_str = [];
		if(request('imgurl')){
			$w_str['image'] = request('imgurl');
		}
		if(request('username')){
			$w_str['username'] = request('username');
		}
		
		if(request('password')){
			$w_str['password'] =  bcrypt(request('password'));
		}
		$res = DB::table("users")
			->where("id",$this->user['id'])
			->update($w_str);
	}
	public function getUserInfo()
	{ 
		$res = DB::table("users")->where("id",$this->user['id'])
		->first(["image as userpic","username","score as scroll","type","unreads","openid"]);
		
		$res->token =$this->user->createToken('MyApp')->accessToken;
		
		echo json_encode($res);
	}

	public function activity_tips()
	{
		
	}
	
	public function collection()
	{
		$res = [];
		if (request('type') == 'notice') {
			$res = DB::table("collection")
			->where("collection.type","notice")
			->where("collection.uid",$this->user['id'])
			->join("notice_list","collection.contentid","=","notice_list.id")
			->get();
			foreach ($res as $key => $value) {
				$res[$key]->people=DB::table('notice_baoming')->where(['noticeid'=>$value->id])->count();
		  }       
		}elseif (request('type') == 'active') {
			$tres = DB::table("collection")
			->where("collection.type","active")
			->where("collection.uid",$this->user['id'])
			->join("active_list","collection.contentid","=","active_list.id")
			->get();
			$res = [];
			foreach ($tres as $key => $value) {
				$value->activeLink = "";
				$value->activeUrl = $value->thumb;
				$value->activeTitle = $value->title;
				$value->activeSprice = $value->money;
				$value->activeDiqu = $value->place;
				$value->activeTime = $value->time;
				$res[] =$value;
			}
		}elseif (request('type') == 'baby') {
			$tres = DB::table("collection")
			->where("collection.type","baby")
			->where("collection.uid",$this->user['id'])
			->join("baby_info","collection.contentid","=","baby_info.id")
			->get();
			$res = [];
			foreach ($tres as $key => $value) {
				$value->isCollect = 1;
				$res[] =$value;
			}
			 
		}elseif (request('type') == 'product') {
			$tres = DB::table("collection")
			->where("collection.type","product")
			->where("collection.uid",$this->user['id'])
			->join("goods","collection.contentid","=","goods.id")
			->get();
			$res = [];
			foreach ($tres as $key => $value) {
				$value->pic = $value->image;
				$value->title = $value->name;
				$value->score = $value->integral;
				$res[] =$value;
			}
			 
		}	
		return $res;
		 
	}
    public function vip(){
		$list=db::table('vip')->where('state',1)->get();
		return $list;
	}
	public function user_type(){
		$uid=$this->user['id'];
		$list=db::table('users')->where('id',$uid)->value('type');
		return $list;

	}
 }
?>