<?php
 /**
  * 主页查看接口集成
  */
namespace App\Http\Controllers\Home;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
 
class View extends Controller
{
     public function __construct () {

     }

// 数据库插入
     function SQLInsert($who){
      // 童星邀约
       
       
     }
     
     public function showTt()
     {
          $input=request()->input('id');
          $li=Db::table('headline')->where('id',$input)->first();
          $li->typeName=Db::table('headline_type')->where('id',$li->type)->value('typeName');
          return json_encode($li);
     }
// 数据库更新
     function SQLUpdate($who){
         
       
     }
// 数据库删除
     function SQLDelete($who){
         
       
     }
     public function headlineLi(){
          $input=request()->input('id');
          $li=Db::table('headline')->where('id',$input)->first();
          $li->typeName=Db::table('headline_type')->where('id',$li->type)->value('typeName');
          return json_encode($li);
      }
// 数据库查询
     function SQLSelect($who,$where){
         // 参数区别谁调用以便进行不同的查询
       switch ($who){
            
            // 获取推荐童星
            case 'getRecommendChild':
                 $getRecommendChild = DB::table('baby_info')->orderBy("id","desc")->where('Recommend',1)->get();
                 echo $getRecommendChild;
            break; 
               
            break;
            // 获取头条
            case 'getHeadTiao':
                 $getHeadTiao = DB::table('headline')->orderBy('date','DESC')->orderBy('time','DESC')->limit(5)->get();
                 echo $getHeadTiao;
            break;
            // 获取轮播
            case 'getBanner':
                 $getBanner = DB::table('banner')->get();
                 echo $getBanner;
            break;
            
             
            
         }
     }



// 获取分类
     function getNavCate(){
          $nav_list = DB::table('nav_cate')->get();
              
          $rtn_data_a = [];
          foreach ($nav_list as $key => $value) {
              if ($value->parent_id>0) {
                    //子分类
                    $value->data_name = $value->name;
                    $rtn_data_a[$value->parent_id]->data[] = $value;
              }else{
                    //     一级分类：活动、通告
                    $rtn_data_a[$value->id] = $value;
                 
              }
          }
          return array_values($rtn_data_a);
       
     }

     // 获取子分类
     function getCateChild(){
          $id = request("id");
          $nav_list = DB::table('nav_cate')
               ->where("parent_id",$id)
               ->get();
        
          return $nav_list;
       
     }
// 童星邀约  
     function inviteBaby(){
        if(request('contacter')&&request('method')&&request('babyid')){
          DB::table('baby_invite')->insert(
               [
                    'contacter' => request('contacter'),
                    'method' => request('method'),
                    'babyid' => request('babyid'),
                    'invite_time' => time(),
               ]
        );
          return '邀约成功';
        }else{
          return '邀约失败';
        }
     }
// 获取轮播   
     function getBanner(){
        $this->SQLSelect('getBanner',null);
          
         
     }
// 获取童星萌娃详情
     function getChildDetail(){
         $id=request('id');
         if($id){
               $info = DB::table('baby_info')->where('id', $id)->first();
               $info->lookstyle = explode(",", $info->lookstyle);
               $info->speciality = explode(",", $info->speciality);
       
               $info->videos = DB::table('baby_video')->select("id","url","createtime")->where("babyid", $info->uid)->get();
               $info->shotexp = DB::table('baby_experience')->where("type", 'shot')->where("babyid", $id)->limit(4)->get();
               $info->showexp = DB::table('baby_experience')->where("type", 'show')->where("babyid", $id)->limit(4)->get();
               $info->awardexp = DB::table('baby_experience')->where("type", 'award')->where("babyid", $id)->limit(4)->get();

               
               $info->arts = DB::table('baby_uploadimage')->where("type",'art')->where("babyid",$id)->get(['id','file as url']);
               
               $info->dramas = DB::table('baby_uploadimage')->where("type",'drama')->where("babyid",$id)->get(['id','file as url']);

               $info->cardmodes = DB::table('baby_uploadimage')->where("type",'cardmod')->where("babyid",$id)->get(['id','file as url','cardmode']);
               
               $info->lifes = DB::table('baby_uploadimage')->where("type",'life')->where("babyid",$id)->get(['id','file as url']);
               $info->shot = DB::table('baby_uploadimage')->where("type",'shot')->where("babyid",$id)->get(['id','file as url']);
               $info->show = DB::table('baby_uploadimage')->where("type",'show')->where("babyid",$id)->get(['id','file as url']);
               $info->award = DB::table('baby_uploadimage')->where("type",'award')->where("babyid",$id)->get(['id','file as url']);
               $info->isCollection = 0;
                
               return  response()->json($info);
         }else{
            echo '数据获取失败';
         }
         
          
         
     }
// 获取筛选童星 
     function getFilterChild(){
      if(Input::get()){
        
          $height1 = Input::get('height1')?Input::get('height1'):0;
          $height2 = Input::get('height2')?Input::get('height2'):222;

          $where_eq_a = [];
          if (request('sex')!='') {
               $where_eq_a['sex'] = request('sex')=='男'?1:2;
          }
          if (request('cardmode')!='') {
               $where_eq_a['cardmode'] = 1;
          }
          if (request('video')!='') {
               $where_eq_a['video'] = 1;
          }
          if (request('nationatily')!='') {
               $where_eq_a['nationality'] = request('nationatily');
          }
          $place = '';
          if (count(request('place'))>0) {
               $place = request('place')['province'];
               $place .= request('place')['city'];
               $place .= request('place')['area'];
          }
          
          $getFilterChild = DB::table('baby_info')
               ->whereBetween('height', [$height1, $height2])
               ->where($where_eq_a)
               ->where('place','like',"%$place%")
               ->where('lookstyle','like',"%".request('lookstyle')."%")
               ->where('speciality','like',"%".request('speciality')."%")
               ->get(['id','name','sex','brithday','isAuth','height','weight','headpic']);


         return $getFilterChild;
      }
          
         
     }
// 获取头条
     function getHeadTiao(){
        $this->SQLSelect('getHeadTiao',null);
          
         
     }
 // 获取其他推荐童星
     function getOhterRecommendChild(){
       
          $list['data'] = DB::table('baby_info')->get();
          $list['data1'] = DB::table('baby_info')->get();
          $list['data2'] = DB::table('baby_info')->get();
          $list['data3']= DB::table('baby_info')->get();
       
          return $list;
         
     }
// 获取推荐童星  
     function getRecommendChild(){
        $this->SQLSelect('getRecommendChild',null);
          
         
     }
     // 获取活动列表
    public function getActiveList(){
          $activeArr=DB::table("active_list")
          ->where('time','>','0')
          ->select('id','thumb','title','money','place','time')
          ->orderby("id","desc")
          ->limit(3)
          ->get();
          return $activeArr;
     }
}