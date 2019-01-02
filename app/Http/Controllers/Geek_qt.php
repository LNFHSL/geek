<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

//审核
class Geek_qt extends Controller{
	
	public function wait(){  //待审核   
	   $list=db::table('league')
	   ->where('state',0)->select('id','shopname','contacter','contacttel','students','date','state')
	   ->get();
	   foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
	   return $list;
	}
	public function operation(){ //
		$id=request('id');
		$state=request('state');
		$list=db::table('league')->where('id',$id)->update(['state'=>$state]);
		if($list){
			return 1;
		}else{
			return 2;
		}
	}
	public function league_details(){ //审核详情
		$id=request('id');
		$list=db::table('league')->where('id',$id)->first();
	  	$list->date = date('Y-m-d',$list->date);
	     
		return json_encode($list);
	}
	public function wait_complete(){  //审核完成   
	   $list=db::table('league')
	   ->where('state',">",0)->select('id','shopname','contacter','contacttel','students','date','state')
	   ->get();
	   foreach($list as $key=>$value){
	  	 $list[$key]->date = date('Y-m-d',$value->date);
	     }
	   return $list;
	}
}	
?>