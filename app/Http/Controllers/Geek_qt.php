<?php
namespace App\http\Controllers;
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
	   ->where('state',0)->select('id','shopname','contacter','contacttel','students','date','state')
	   ->get();
	   foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
	   return $list;
	}
	public function operation(){ //
		$id=request('id');
		$state=request('state');
		$list=db::table('league')->where('id',$id)->update(['state'=>$state]);
		if($list){
			return 1;
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
	   ->get();
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
	public function send_slide(){ //上传幻灯片
		$time=time();
		$list=db::table('slide_img')->insert([
		'img'=>request('img'),
		'route'=>request('route'),
		 'date'=>$time
		]);
	}
	public function display_slide(){ //幻灯片
	    $list=db::table('slide_img')->orderBy('id','desc')->get();
		foreach($list as $key=>$value){
			$list[$key]->date=date('Y-m-d',$value->date);
		}
		return $list;
	}
	public function delete_slide(){ //删除幻灯片
		$id=request('id');
		$list_img=db::table('slide_img')->where('id',$id)->value('img');
		$path = public_path();
		$list=db::table('slide_img')->where('id',$id)->delete();
		unlink ($path.$list_img);
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
		$list=db::table('baby_info')->orderBy('id','desc')->where('isAuth',">=",1)->get();
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
		echo  json_encode($list);
		
	}
}	
?>