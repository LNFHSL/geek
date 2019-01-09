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
			$list->isCollection = false;
			 return json_encode($list);
	    }
		public function getFilterGoods(){
			
			$dropClicks=request('dropClicks');
			$classify=request('fl');

			if($classify=='fl'){
				$list=db::table('goods')->where('classify',$dropClicks)->get();
			    return (['shopAllGoods'=>$list]);
			}
			else if($classify=='jg'){ //低到高
				if($dropClicks== '0'){
					
					$list=db::table('goods')->orderBy('change','asc')->get();
					  return (['shopAllGoods'=>$list]);
				}else{
					$list=db::table('goods')->orderBy('change','desc')->get();
					  return (['shopAllGoods'=>$list]);
				}
			}
			else if($classify=='xl'){
				
				if($dropClicks== '0'){
					$list=db::table('goods')->orderBy('integral','asc')->get();
					  return (['shopAllGoods'=>$list]);
				}else{
					$list=db::table('goods')->orderBy('integral','desc')->get();
					  return (['shopAllGoods'=>$list]);
				}
			}
			
			
		}

}
?>