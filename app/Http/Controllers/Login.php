<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Validator;
use Illuminate\Support\Facades\Storage;

//审核
class Login extends Controller{
    public function login_ht(Request $request){
        return ["code" => 20000,"data"=>["token" => 'admin',"message" => "登录成功"]];
	}
    public function info(Request $request){
        echo 123;



        // return ["code" => 20000,"data"=>["roles"=>"admin","name"=>"admin","avatar"=>"https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif","message" => "登录成功"]];


        // echo '{"code":20000,"data":{"roles":["admin"],"name":"admin","avatar":"https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif"}}';
	}
      
}	
?>