<?php 
/**
 * 公告类接口
 */
namespace App\Http\Controllers\Notice;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class Manager extends Controller{
    public $user=null;
    public function __construct () {
        $this->user=Auth::user();
    }

    //添加
    public function save()
    {
        $input = request()->all();
        $input['uid'] = $this->user['id'];
        unset($input['token']);

        $input['createtime'] = time();
        
        // 计算截止还有多少天
        $input['time'] = round(( strtotime($input['endtime']) - $input['createtime']) / 86400);
        
        $juese_list =  $input['juese_list'];
        unset($input['juese_list']);
        unset($input['edit_id']);
        $id = DB::table("notice_list")->insertGetId($input);

        $people = 0 ;
       //  添加角色
        foreach ($juese_list as $key => $value) {
              $value['notice_id'] = $id;
              $value['allAge'] = $value['age']['allAge'];
              $value['ageStar'] = $value['age']['ageStar'];
              $value['ageEnd'] = $value['age']['ageEnd'];
              $value['allHeight'] = $value['heigh']['allHeigh'];
              $value['heightEnd'] = $value['heigh']['heighEnd'];
              $value['heightStar'] = $value['heigh']['heighStar'];
              $value['price'] = $value['money2'];
              unset($value['age']);
              unset($value['money2']);
              unset($value['heigh']);
              $people += $value['people'] ;
              DB::table("notice_juese")->insert($value);
        }
       //  修改人数
       DB::table("notice_list")->where("id",$id)->update(['people'=>$people]);
    
        return response()->json(['msg'=>'添加成功','code'=>200,'id'=>$id]);      
    }

    // 进入编辑查询
    public function edit()
    {
        $getNoticeDetail = DB::table('notice_list')->where(['id'=>request('id')])->first();
        $juese_list = DB::table('notice_juese')->where(['notice_id'=>request('id')])->get();
 
        foreach ($juese_list as $key => $value) { 
            $value->age['allAge'] = $value->allAge>0?true:false;
            $value->age['ageStar'] = $value->ageStar;
            $value->age['ageEnd'] = $value->ageEnd;
            $value->heigh['allHeigh'] = $value->allHeight>0?true:false;
            $value->heigh['heighStar'] = $value->heightStar;
            $value->heigh['heighEnd'] = $value->heightEnd;
            $value->money2 = $value->price;
         
   
            $getNoticeDetail->roleForm[]=$value;
        }

        echo json_encode($getNoticeDetail);
 
    }

    // 更新
    public function update()
    {
        $input = request()->all();
        $input['uid'] = $this->user['id'];

        $id = request("edit_id");
        unset($input['token']);
        unset($input['edit_id']);

        $input['createtime'] = time();
        
        // 计算截止还有多少天
        $input['time'] = round(( strtotime($input['endtime']) - $input['createtime']) / 86400);
     
        $juese_list =  $input['juese_list'];
        unset($input['juese_list']);
        unset($input['id']);

        DB::table("notice_list")->where("id",$id)->update($input);


        $people = 0 ;
       //  添加角色
        foreach ($juese_list as $key => $value) {
              $value['notice_id'] = $id;
              $value['allAge'] = $value['age']['allAge'];
              $value['ageStar'] = $value['age']['ageStar'];
              $value['ageEnd'] = $value['age']['ageEnd'];
              $value['allHeight'] = $value['heigh']['allHeigh'];
              $value['heightEnd'] = $value['heigh']['heighEnd'];
              $value['heightStar'] = $value['heigh']['heighStar'];
              $value['price'] = $value['money2']; 
              unset($value['age']);
              unset($value['money2']);
              unset($value['heigh']);
              $people += $value['people'] ;
              if (isset($value['id'])) {
                 unset($value['id']);
                DB::table("notice_juese")->update($value);
              }else{
                DB::table("notice_juese")->insert($value);
              }
        }
       //  修改人数
       DB::table("notice_list")->where("id",$id)->update(['people'=>$people]);
    
        return response()->json(['msg'=>'添加成功','code'=>200]);      
    }

    //通告列表
	public function lists(){ 
		$uid=$this->user['id'];
        $w_str = []; 
        if (request('type')!='全部') {
            $w_str = ['type'=>request('type')];
        }
		$list=db::table('notice_list')
            ->where(['uid'=>$uid])
            ->where($w_str)
            ->get();
           
		return $list;
	}
   

}





 ?>