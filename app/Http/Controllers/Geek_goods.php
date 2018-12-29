<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

class Geek_goods extends Controller{
	public function classify(){   //获取商品分类
		$list=db::table('goods_classify')->get();
		return $list;
	}
	public function add_goods(){ //添加商品
		$input=request()->all();
		$input['date']=time();
		$list=db::table('goods')->insert($input);
		if($list){
			return (['state'=>1]);
		}else{
			return (['state'=>0]);
		}
	}	
	 public function display_goods(){   //商品
	 	$list=db::table('goods')->join('goods_classify','goods.classify','goods_classify.id')
	 	  ->select('goods.*', 'goods_classify.classify')->get();
		foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
		return $list;
	 }
	 public function delete_goods(){  //删除商品
	 	$id=request('id');
		$list_img=db::table('goods')->where('id',$id)->value('image');
		$path = public_path();
		$list=db::table('goods')->where('id',$id)->delete();
		unlink ($path.$list_img);
		if($list){
			return '1';
		}else{
			return '0';
		}
	 }
	 public function search_goods(){  //查询商品分类
	 	$input=request('input');
		$type=request('classify');
		if($type==''){
			$list=db::table('goods')->where('name','like',"%".$input."%")->join('goods_classify','goods.classify','goods_classify.id')
	 	  ->select('goods.*', 'goods_classify.classify')->get();
		foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
		return $list;
	     }
		else{
		   $list=db::table('goods')->where([['name','like',"%".$input."%"],['goods.classify',$type]])->join('goods_classify','goods.classify','goods_classify.id')
	 	  ->select('goods.*', 'goods_classify.classify')->get();
		foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
		return $list;
		}
		
	 }
	 public function add_classify(){   //添加分类
	    $input=request('input');
	    db::table('goods_classify')->insert(['classify'=>$input]); 
	 }
	 public function delete_classify(){  //删除分类
	 	  $id=request('id');
		  $list=db::table('goods')->where('classify',$id)->value('id');
		  if($list==''){
		  	db::table('goods_classify')->where('id',$id)->delete();
			return 1;
		  }
		  else{
			return 0;
		  }  
	 }
		
	public function query_modify(){
		 $id=request('id');
		 $list=db::table('goods')->where('id',$id)->get();
		 return $list;
	}
	public function up_goods(){ //添加商品
		$input=request()->all();
		$input['date']=time();
		$list=db::table('goods')->update($input);
		if($list){
			return (['state'=>1]);
		}else{
			return (['state'=>0]);
		}
	}		
		
		
		
		
		
		
		
		
		
		
		
	}
?>
	