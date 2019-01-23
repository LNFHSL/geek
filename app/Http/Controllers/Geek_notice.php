<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
class Geek_notice extends Controller{
	public function reports(){ //后台举报
	    $input=request()->all();
		$input['state']=0;
		unset($input['page']);
		$list=db::table('report')->where($input)->paginate(8);
		foreach($list as $key=>$value){
	  	 $list[$key]->time = date('Y-m-d',$value->time);
	     }
		return $list;
		
	}
	public function reports_wc(){ //后台举报完成
	    $input=request()->all();
		unset($input['page']);
		$list=db::table('report')->where([[$input],['state','>',0]])->paginate(8);
		foreach($list as $key=>$value){
	  	 $list[$key]->time = date('Y-m-d',$value->time);
	     }
		return $list;
		
	}
	public function choices(){ //后台举报判断
	    $input=request()->all();
		$list=db::table('report')->where($input)->get();
		foreach($list as $key=>$value){
	  	 $list[$key]->time = date('Y-m-d',$value->time);
	     }
		return $list;
		
	}
	
	public function details(){ //被举报的公告详情
	    $input=request()->all();
	    $list=db::table('notice_list')->where($input)->first();
		return json_encode($list);
	}
	
	
	
	public function template(){ //举报模版
		$type=request('type');
		$list=db::table('info_template')->where('type',$type)->first();
		return json_encode($list);
	}
	public function template_two(){ //被举报模版
		$type=request('type');
		$list=db::table('info_template')->where('type',$type)->first();
		return json_encode($list);
	}
	
	
	public function set_templates(){//上传举报模版
			$input=request()->all();
			$list=db::table('info_template')->where('type',$input['type'])->first();
			if($list){
				$list=db::table('info_template')->where('type',$input['type'])->update($input);
				if($list){
					return 1;
				}else{
					return 2;
				}
			}else{
				$list=db::table('info_template')->insert($input);
				if($list){
					return 1;
				}else{
					return 2;
				}
			}
		}
	public function judge(){ //判断是否违规
	    if(request('state')== 2){
	    	$list=db::table('report')->where('id',request('id'))->update(['state'=>request('state')]);
			return $list; 
	    }else if(request('state')== 1){
	    	$list_j=db::table('info_template')->where('type','jb')->select('info_template','title')->first();
			$list_b=db::table('info_template')->where('type','bjb')->select('info_template','title')->first();
			
			$list_info=db::table('info')   // 发送举报成功信息
			->insert(['uid'=>request('report_id'),
			'title'=>$list_j->title,
			'content'=>$list_j->info_template,
			'createtime'=>time(),
			'isSystem'=>'1'
			]);     
			
			$list_user=db::table('notice_list')->where('id',request('Article_id'))->value('uid');
			
			$list_info_two=db::table('info')   // 发送被举报信息
			->insert(['uid'=>$list_user,
			'title'=>$list_b->title,
			'content'=>$list_b->info_template,
			'createtime'=>time(),
			'isSystem'=>'1'
			]);
			
	    	$list=db::table('report')->where('id',request('id'))->update(['state'=>request('state')]);
			return $list;
	    }
		
	}

	public function an_classify(){
		$nav_list = DB::table('nav_cate')->get();
		$rtn_data_a = [];
		foreach ($nav_list as $key => $value) {
		if ($value->parent_id>0) {
		//子分类
		$rtn_data_a[$value->parent_id]->children[] = $value;
		}else{
		// 一级分类：活动、通告
		$rtn_data_a[$value->id] = $value;
		}
		}
		return array_values($rtn_data_a);
	}
	public function add_notice(){
		$role=request('role');
		$input=request()->all();
		$input['createtime']=time();
		$input['is_pay']='1';
		$input['uid']='0';
		unset($input['role']);
		$list_id=db::table('notice_list')->insertGetId($input);

		if($list_id != ''){
			
			foreach($role as $value){
				$value['notice_id'] =$list_id;
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
				db::table('notice_juese')->insert($value);
			}
			return 1;
		}
		
	}
	
	
}
?>