<?php
 /**
  * 主页接口集成
  */
namespace App\Http\Controllers\HOME;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Db;
use Illuminate\Support\Facades\Input;
 
class HomeInterface extends Controller
{
     public function __construct () {

     }

// 数据库插入
     function SQLInsert($who){
      // 童星邀约
         switch ($who){
            case 'childInvite':
                DB::table('childinvite')->insert(
                        ['contacter' => request('contacter'),'method' => request('method'),'babyid' => request('babyid')]
                 );
            break;
         }
       
     }
// 数据库更新
     function SQLUpdate($who){
         
       
     }
// 数据库删除
     function SQLDelete($who){
         
       
     }
// 数据库查询
     function SQLSelect($who,$where){
         // 参数区别谁调用以便进行不同的查询
       switch ($who){
            // 导航查询
            case 'gethomenav':
                 $gethomenav = DB::table('gethomenav')->get();
                 echo $gethomenav;
            break;
            // 获取推荐童星
            case 'getRecommendChild':
                 $getRecommendChild = DB::table('baby_info')->get();
                 echo $getRecommendChild;
            break; 
               
            break;
            // 获取头条
            case 'getHeadTiao':
                 $getHeadTiao = DB::table('head_tiao')->get();
                 echo $getHeadTiao;
            break;
            // 获取轮播
            case 'getBanner':
                 $getBanner = DB::table('banner')->get();
                 echo $getBanner;
            break;
            // 获取其它童星推荐
            case 'getOhterRecommendChild':

                 $list['data'] = DB::table('baby_info')->get();
                 $list['data1'] = DB::table('baby_info')->get();
                 $list['data2'] = DB::table('baby_info')->get();
                 $list['data3']= DB::table('baby_info')->get();
              
                 echo json_encode($list);
            break;
             
            
         }
     }



// 获取导航
     function gethomenav(){
         $this->SQLSelect('gethomenav',null);
       
     }
// 童星邀约  
     function childInvite(){
        if(request('contacter')&&request('method')&&request('babyid')){
          $this->SQLInsert('childInvite',null);
          echo '邀约成功';
        }else{
          echo '邀约失败';
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
               // $info->lookstyle = explode(",", $info->lookstyle);
               // $info->speciality = explode(",", $info->speciality);
       
               $info->videos = DB::table('baby_video')->select("id","url","createtime")->where("babyid", $info->uid)->get();
               $info->shotexp = DB::table('baby_experience')->where("type",'shot')->where("babyid",$info->uid)->value('content');
               $info->showexp = DB::table('baby_experience')->where("type",'show')->where("babyid",$info->uid)->value('content');
               $info->awardexp = DB::table('baby_experience')->where("type",'award')->where("babyid",$info->uid)->value('content');

               
               $info->arts = DB::table('baby_uploadimage')->where("type",'art')->where("babyid",$info->id)->get(['id','file as url']);
               
               $info->arts = DB::table('baby_uploadimage')->where("type",'art')->where("babyid",$info->id)->get(['id','file as url']);
               
               $info->dramas = DB::table('baby_uploadimage')->where("type",'drama')->where("babyid",$info->id)->get(['id','file as url']);
               $info->cardmodes = DB::table('baby_uploadimage')->where("type",'cardmode')->where("babyid",$info->id)->get(['id','file as url']);
               $info->lifes = DB::table('baby_uploadimage')->where("type",'life')->where("babyid",$info->id)->get(['id','file as url']);
               $info->shot = DB::table('baby_uploadimage')->where("type",'shot')->where("babyid",$info->id)->get(['id','file as url']);
               $info->show = DB::table('baby_uploadimage')->where("type",'show')->where("babyid",$info->id)->get(['id','file as url']);
               $info->award = DB::table('baby_uploadimage')->where("type",'award')->where("babyid",$info->id)->get(['id','file as url']);
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
        $this->SQLSelect('getOhterRecommendChild',null);
          
         
     }
// 获取推荐童星  
     function getRecommendChild(){
        $this->SQLSelect('getRecommendChild',null);
          
         
     }
}