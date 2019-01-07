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
	

	public function noticeTypeShow(){   //查看通告活动类型
	    $id=request()->input('id');
	    if(empty($id)){
            $fType=Db::table('nav_cate')->where('parent_id',0)->get();
            return ['fType'=>$fType];
        }else{
	        $sType=Db::table('nav_cate')->where('parent_id',$id)->get();
            return ['sType'=>$sType];
        }
    }

    public function addNoticeType(){    //添加通告活动类型
        $id=request()->input('id');
        $input['name']=request()->input('name');
        if(empty($id)){
            $input['parent_id']=0;
            $res=Db::table('nav_cate')->insert($input);
        }else{
            $input['parent_id']=$id;
            $res=Db::table('nav_cate')->insert($input);
        }
        if($res){
            return ['state'=>1];
        }else{
            return ['state'=>0];
        }
    }

    public function delNoticeType(){    //删除通告活动类型
        $id=request()->input('id');
        $msg=Db::table('nav_cate')->where('id',$id)->first();
        if($msg->parent_id==0){
            if(!empty($msg->url)){  //判断图片路径是否为空，避免unlink()报错
                $path = base_path();
                $file=$path.'/public'.$msg->url;
                $img=str_replace('\\',"/",$file);
                unlink($img);
            }
            $res1=Db::table('nav_cate')->where('parent_id',$msg->id)->delete();
            $res=Db::table('nav_cate')->where('id',$id)->delete();
        }else{
            $res=Db::table('nav_cate')->where('id',$id)->delete();
        }
        if($res){
            return ['state'=>1];
        }else{
            return ['state'=>0];
        }
    }

	
	
	
}
	
?>