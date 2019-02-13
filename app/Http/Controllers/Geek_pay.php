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
class Geek_pay extends Controller{
    public function weixin_notifys(){
        $list=db::table('notice_list')->where("notice_list.is_pay",1)->leftJoin('users','notice_list.uid','users.id')
        ->select('notice_list.title','notice_list.film','notice_list.createtime','notice_list.money','notice_list.uid',
        'users.username','users.id')->paginate(10);
        foreach($list as $key=>$value){
            $list[$key]->createtime = date('Y-m-d',$value->createtime);
        }
        return $list;
    }

    public function query_notifys(){
        $username=request('content');
        if($username!="系统发布"){
            $list=db::table('users')->join('notice_list','users.id','notice_list.uid')
            ->where([['users.username','like',"%".$username."%"],["notice_list.is_pay",1]])
            ->select('notice_list.title','notice_list.film','notice_list.createtime','notice_list.money','notice_list.uid',
            'users.username','users.id')->get();
            foreach($list as $key=>$value){
                $list[$key]->createtime = date('Y-m-d',$value->createtime);
            }
            return $list;
        }else{
            $list=db::table('notice_list')->where('uid',0) ->select('title','film','createtime','money','uid')
            ->get();
            foreach($list as $key=>$value){
                $list[$key]->createtime = date('Y-m-d',$value->createtime);
            }
            return $list;
        }
    }
	
	
	
	
	
}