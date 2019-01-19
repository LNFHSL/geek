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
    public function noticeShow(){   //通告列表
        $status=request()->input('status');
        if(!empty($status)){
            $list=Db::table('notice_list')->where('status',$status)->paginate(10);
            return $list;
        }
        $list=Db::table('notice_list')->paginate(10);
        return $list;
    }
    public function changeStatus(){ //修改通告审核状态
        $id=request()->input('id');
        $status=request()->input('status');
        $res=Db::table('notice_list')->where('id',$id)->update(['status'=>$status]);
        if($res){
            return ['state'=>1];
        }else{
            return ['state'=>0];
        }
    }
    public function showJoinBaby(){ //查看通告的报名详情
        $id=request()->input('id');
        $list=Db::table('notice_baoming')->where('noticeid',$id)->get();
        $n=0;
        foreach($list as $val){
            $baby=Db::table('baby_info')->where('id',$val->babyid)->first();
            $msg[$n]['id']=$baby->id;
            $msg[$n]['headpic']=$baby->headpic;
            $msg[$n]['sex']=$baby->sex;
            $msg[$n]['name']=$baby->name;
            $msg[$n]['contacts']=$val->contacts;
            $msg[$n]['contactmode']=$val->contactmode;
            $n++;
        }
        if(!empty($msg)){
            return $msg;
        }else{
            return '';
        }
    }
    public function delNotice(){    //删除通告
        $id=request()->input('id');
        $msg=DB::table('notice_list')->where('id',$id)->first();
        if(!empty($msg->thumb)){
            $imgUrl=str_replace('\\','/',base_path().'/public'.$msg->thumb);
            unlink($imgUrl);
        }
        $res=Db::table('notice_list')->where('id',$id)->delete();
        if($res){
            return ['state'=>1];
        }else{
            return ['state'=>0];
        }
    }
	public function report(){ //通告举报
		$input=request()->all();
		$uid=$this->user['id'];
		$input['report_id']=$uid;
		$input['time']=time();
		$input['title']=json_encode($input['title']);
		$list=db::table('report')->insert($input);
		if($list){
			return 1;
		}else{
			return 2;
		}
	}
	
}
	
?>