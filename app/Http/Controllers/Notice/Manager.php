<?php 
/**
 * 公告类接口
 */
namespace App\Http\Controllers\Notice;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Db;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class Manager extends Controller{
    public $user=null;
    public function __construct () {
        $this->user=Auth::user();
    }

    public function save()
    {
        $input = request()->all();
        $input['uid'] = $this->user['id'];
        unset($input['token']);

        $input['create_time'] = time();
        $input['time'] = date('m', $input['end_time']) - date('m', $input['start_time']);
        $juese_list =  $input['juese_list'];
        unset($input['juese_list']);
        $id = DB::table("notice_list")->insert($input);

        $people = 0 ;
       //  添加角色
        foreach ($juese_list as $key => $value) {
              $value['notice_id'] = $id;
              $people += $value['people'] ;
              $id = DB::table("notice_juese")->insert($value);
        }
       //  修改人数
       DB::table("notice_list")->where("id",$id)->update(['people'=>$people]);
    
        return response()->json(['msg'=>'添加成功','code'=>200]);      
    }

    public function edit()
    {
        $input = request()->all();
        $input['uid'] = $this->user['id'];
        unset($input['token']);

        $id = input("id");

        $input['create_time'] = time();
        $input['time'] = date('m', $input['end_time']) - date('m', $input['start_time']);
        $juese_list =  $input['juese_list'];
        unset($input['juese_list']);
        DB::table("notice_list")->where("id",$id)->update($input);

        $people = 0 ;
       //  添加角色
        foreach ($juese_list as $key => $value) {
              $value['notice_id'] = $id;
              $people += $value['people'] ;
              $id = DB::table("notice_juese")->insert($value);
        }
       //  修改人数
       DB::table("notice_list")->where("id",$id)->update(['people'=>$people]);
    
        return response()->json(['msg'=>'添加成功','code'=>200]);      
    }
   

}





 ?>