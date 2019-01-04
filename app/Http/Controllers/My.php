<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

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
		->first(["image as userpic","username","score as scroll","member","type"]);
		echo json_encode($res);
	}

	// 活动数量
	public function activity_tips()
	{
		$list=db::table('active_list')->where('uid',$this->user['id'])->get(["id"]);
		return $list;
	}

	public function getsellerInfo()
	{
		
	}
 }
?>