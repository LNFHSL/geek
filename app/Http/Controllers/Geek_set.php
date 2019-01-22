<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

//设置
class Geek_set extends Controller{
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
//		$list_img=db::table('slide_img')->where('id',$id)->value('img');
//		$path = public_path();
		$list=db::table('slide_img')->where('id',$id)->delete();
//		unlink ($path.$list_img);
	}
	
	
	
	public function vip(){ //添加vip
		$input=request()->all();	
		$list=db::table('vip')->insert($input);
	}
	public function display_vip(){ //查询显示vip
		$list=db::table('vip')->get();
		return $list;
	}
	public function delete_vip(){ //删除vip
	    $list_user=db::table('users')->where('member', request('id'))->first();
		if($list_user){
			return 3;
		}else{
			 $list=db::table('vip')->where('id', request('id'))->delete();
			if($list){
				return 1;
			}else{
				return 2;
			}
		}
		
	}
	public function Lower_shelf(){ //下架vip
	    $list=db::table('vip')->where('id', request('id'))->update(['state'=>2]);
		if($list){
			return 1;
		}else{
			return 2;
		}
		
	}
	public function display_user(){  //显示购买vip的会员
		$list=db::table('users' )->where('member','>',0)->join('vip','vip.id','users.member')
		->join('vip_time','users.id','vip_time.uid')
		->select('users.username','vip.name','vip_time.time')->paginate(8);
		foreach($list as $key=>$value){
	  	 $list[$key]->time = date('Y-m-d',$value->time);
	     }
		return $list;
	}
	public function vip_user_query(){  //查询vip用户
	    if(request('classify')==1){
		    $list=db::table('users' )->where('member','>',0)->join('vip','vip.id','users.member')
				->join('vip_time','users.id','vip_time.uid')->where('username',request('input'))
				->select('users.username','vip.name','vip_time.time')->paginate(8);
			foreach($list as $key=>$value){
		  	 	$list[$key]->time = date('Y-m-d',$value->time);
		     }
			return $list;
	    }
		else if(request('classify')==2){
			$list_vip=db::table('vip')->where('name',strtoupper(request('input')))->value('id');
			
			$list=db::table('users' )->where('member','>',0)->join('vip','vip.id','users.member')
				->join('vip_time','users.id','vip_time.uid')->where('users.member',$list_vip)
				->select('users.username','vip.name','vip_time.time')->paginate(8);
			foreach($list as $key=>$value){
		  	 	$list[$key]->time = date('Y-m-d',$value->time);
		     }
			return $list;
		}
	    
	}
	
	
	
	
	
}