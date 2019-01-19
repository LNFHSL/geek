<?php
namespace App\Http\Controllers\Admin;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class Manager extends Controller{
	public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');
        //$username = $_POST['username'];
        //$password = $_POST['password'];

        $data = DB::select('select * from manager where name = ? and password = ?',[$username,$password]);

       /* $data = [
            "token" => 'super_admin',
            "code" => 20000,
            "message" => "login",
        ];*/
        if($data)
        {
            $token = $data[0]->token;
            return ["code" => 20000,"data"=>["token" => $token,"message" => "登录成功"]];
        }else{
            return ["code" => 200000,"message" => "请输入正确的用户名和密码"];
        }
    }
    //getinfo
    public function info(Request $request)
    {
        $token =  $request->input('token');
        /*$data = [
            "code" => 20000,
            "roles" => ["super_admin"],
            "name" => "super_admin",
            "avatar" => "http://127.0.0.1:88/meitu/public/1.jpg",
            "message" => "getinfo",
        ];*/
        if($token)
        {
            $data = DB::select('select * from manager where token = ?',[$token]);
            return ["code" => 20000,"data"=>["roles" =>[$data[0]->roles],"name" => $data[0]->name,"id" => $data[0]->id, "avatar" => "/src/assets/avatar.gif"]];
        }else{
            return ["code" => 50008];
        }

	}

	 //logout
	 public function logout()
	 {
		 $data = [
			 "code" => 20000,
			 "data"=>["message" => "logout"]
		 ];
		 return $data;
	 }
	

}

		   
					   
?>