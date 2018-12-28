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
		
		public function getGoods(){  //全部商品
		     $user=Auth::user();
		    $list_score=db::table('users')->where('id',$user['id'])->value('score');
			$list_shopMyGoods=db::table('goods')->where('integral', '<=',$list_score)->get();
			$list_shopAllGood=db::table('goods')->select('id','name','classify','integral','image')->get();
			return (['shopMyGoods'=>$list_shopMyGoods,'shopAllGood'=>$list_shopAllGood]);
			
		}
	
	    public function goodsDetail(){  //商品详情
	    	$id=request('id');
	    	$list=db::table('goods')->where('id',$id)->first();
			 return json_encode($list);
	    }
		
}
?>