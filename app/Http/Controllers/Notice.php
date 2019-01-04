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
		$list=db::table('notice_baoming')
			->join("notice_list","notice_baoming.noticeid","=","notice_list.id")
			->join("notice_juese","notice_baoming.nstarid","=","notice_juese.id")
			->where([['notice_baoming.uid',$uid],['notice_baoming.type',$type]])->get();
		return $list;
	}
	
	public function getpartnotice_tips(){ //我参加的通告 提示     前端为添加
		$uid=$this->user['id'];
		if($this->user['type'] >1){
			$list=db::table('notice_list')->where('uid',$uid)->get(['uid']);
		}else{
			$list=db::table('partnotice')->where([['uid',$uid],['tips','0']])->get();
		}
		return $list;
	}
	

	
	
	
}
	
?>