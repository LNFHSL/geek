<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\User;

class Shop extends Controller{
	    public $user = null;
	    function __CONSTRUCT(){
	        $this->user = Auth::user();
	    }
		 //我的积分在my文件里
		public function getGoods(){  //全部商品
		     $user=Auth::user();
		    $list_score=db::table('users')->where('id',$user['id'])->value('score');
			$list_shopMyGoods=db::table('goods')->where('integral', '<=',$list_score)->get();
			$list_shopAllGood=db::table('goods')->select('id','name','classify','integral','image')->get();
			$list_classify=db::table('goods_classify')->get();
			
			return (['shopMyGoods'=>$list_shopMyGoods,'shopAllGood'=>$list_shopAllGood,'shopDrop'=>$list_classify]);
			
		}
	
	    public function goodsDetail(){  //商品详情
	    	$id=request('id');
	    	$list=db::table('goods')->where('id',$id)->first();
			 return json_encode($list);
	    }
		public function getFilterGoods(){
			$input=request()->all();
			print_r($input['type']);exit();
			
			
			$list=db::table('goods')->where($input)->get();
			return $list;
		}

}
?>