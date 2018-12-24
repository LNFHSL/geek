<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;


class Notice extends Controller{
	public $user = null;
	function __CONSTRUCT(){
	        $this->user = Auth::user();
	    }
	
	public function getpartnotice(){ //我参加的通告
		$uid=$this->user['id'];
		$type=request('type');
		$list=db::table('partnotice')->where([['uid',$uid],['type',$type]])->get();
		db::table('partnotice')->where([['uid',$uid],['type',$type]])->update(["tips"=>'1']);
		return $list;
	}
	
	public function getpartnotice_tips(){ //我参加的通告 提示     前端为添加
		$uid=$this->user['id'];
		$list=db::table('partnotice')->where([['uid',$uid],['tips','0']])->get();
		return $list;
	}
	

	
	
	
}
	
?>