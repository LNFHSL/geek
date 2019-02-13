<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

//审核
class Geek_qt extends Controller{
	
	public function wait(){  //待审核   
	   $list=db::table('league')
	   ->where('state',0)->select('id','uid','shopname','contacter','contacttel','students','date','state','type')
	   ->get();
	   foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
	   return $list;
	}
	public function operation(){ //审核  
		$id=request('id');
		$uid=request('uid');
		$state=request('state');
		$type=request('type');
		$list_user=db::table('users')->where('id',$uid)->update(['type'=>$type]);
		if($list_user==1){
		$list=db::table('league')->where('id',$id)->update(['state'=>$state]);

		if($list==1){
			return 1;
		}else{
			return 2;
		}
		}else{
			return 2;
		}
	}
	public function league_details(){ //审核详情
		$id=request('id');
		$list=db::table('league')->where('id',$id)->first();
	  	$list->date = date('Y-m-d',$list->date);
	     
		return json_encode($list);
	}
	public function wait_complete(){  //审核完成   
	   $list=db::table('league')
	   ->where('state',">",0)->select('id','shopname','contacter','contacttel','students','date','state')
	   ->paginate(7);
	   foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
	   return $list;
	}
	
	public function send_info(){ //发送消息
		$input=request()->all();
		$input['createtime']=time();
		$input['isSystem']=1;
		$input['uid']=0;
		$list=db::table('info')->insert($input);
		return json_encode($list);
	}
	public function display_info(){  //系统消息
		$uid=0;
		$isSystem=1;
		$list=db::table('info')->where([['uid',$uid],['isSystem',$isSystem]])->orderBy('id', 'desc')->get();
		foreach($list as $key=>$value){
			$list[$key]->createtime=date('Y-m-d',$value->createtime);
		}
		return json_encode($list);
	}
	public function delete_info(){  //删除系统消息
		$list=db::table('info')->where('id',request('id'))->delete();
		if($list){
			return 1;
		}
	}
	public function feedback(){ //反馈
		$list=db::table('opinion')->orderBy('id','desc')->get();
		return $list;
	}
	public function sure_feedback(){ //确定反馈
		$list=db::table('opinion')->where('id',request('id'))->update(['read'=>1]);
	}
	public function meng_wa(){ //未认证
		$list=db::table('baby_info')->orderBy('id','desc')->where('isAuth',0)->get();
		return $list;
	}
	public function meng_wa_wc(){ //已认证或者未通过
		$list=db::table('baby_info')->orderBy('id','desc')->where('isAuth',">=",1)->paginate(5);
		return $list;
	}
	
	
	
	
	public function meng_wa_rz(){ //萌娃认证
		$id=request('id');
		$state=request('state');
		$list=db::table('baby_info')->where('id',$id)->update(['isAuth'=>$state]);
		if($list){
			return 1;
		}else{
			return 2;
		}
	}
	public function meng_wa_xq(){ //萌娃详情
	    $id=request('id');
		$list=db::table('baby_info')->where('id',$id)->first();
		$list_img=db::table('baby_uploadimage')->where('babyid',$id)->select('file')->get();
		return(['list'=>$list,'list_img'=>$list_img]);
		
	}
	public function meng_wa_search(){//萌娃搜索
		$list=db::table('baby_info')->where('name',request('name'))->get();
		return $list;
	}

	public function user_list(){  //用户管理
		$list=db::table('users')->leftJoin('vip','vip.id','=','users.member')
		->select("users.id","users.username","users.mobile","users.type","users.member","users.image","users.score","users.money",'vip.name')
		->orderBy('users.id', 'desc')->paginate(5);
		return $list;
	}

	public function query_user_list(){  //查询用户
		$classify=request('classify');
		$classify_two=request('classify_two');
		$input=request('input');

		if($classify == 1){         
			$list=db::table('users')->where('username','like',"%".$input."%")->leftJoin('vip','vip.id','=','users.member')
			->select("users.id","users.username","users.mobile","users.type","users.member","users.image","users.score","users.money",'vip.name')
			->orderBy('users.id', 'desc')->get();
			return $list;
		}else if($classify == 2){
			$list=db::table('users')->where('mobile','like',"%".$input."%")->leftJoin('vip','vip.id','=','users.member')
			->select("users.id","users.username","users.mobile","users.type","users.member","users.image","users.score","users.money",'vip.name')
			->orderBy('users.id', 'desc')->get();
			return $list;
		}else{
			$list=db::table('users')->where('type','like',"%".$classify_two."%")->leftJoin('vip','vip.id','=','users.member')
			->select("users.id","users.username","users.mobile","users.type","users.member","users.image","users.score","users.money",'vip.name')
			->orderBy('users.id', 'desc')->get();
			return $list;
		}
	}

	public function user_edit(){  //用户详情
		$id=request('id');
		$list=db::table('users')->where("users.id",$id)->leftJoin('vip','vip.id','=','users.member')
		->select("users.id","users.username","users.mobile","users.type","users.member","users.image","users.score","users.money",'vip.name')
		->first();
		return json_encode($list);
	}
	public function modify_edit(){  //用户详情
		$id=request("id");
		$input=request()->all();
		unset($input['id']);
		$list=db::table('users')->where('id',$id)->update($input);
		return $list;
	}
}	
?>