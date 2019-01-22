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
class Geek_home extends Controller{
    public function Announcements(){  //查询通告
        // print_r(12312);exit();
        $list=db::table('notice_list')->where([["status",'>','1'],['placeTop','2']])->get();
        foreach($list as $key=>$value){
            $list[$key]->createtime = date('Y-m-d',$value->createtime);
          }
        return $list;
    }
    public function Recommends(){  //推荐
        
        $list=db::table('notice_list')->where('id',request('id'))->update(['placeTop'=>request('state')]);
        return $list;
    }
    public function details_r(){//查询推荐的通告
        $list=db::table('notice_list')->where([["status",'>','1'],['placeTop','1']])->get();
        foreach($list as $key=>$value){
            $list[$key]->createtime = date('Y-m-d',$value->createtime);
          }
    
        return $list;

    }
    
    
}	
?>