<?php
namespace App\http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\User;

class Users extends Controller{
	//登录
	  public function login(){
        if(Auth::attempt(['mobile' => request('mobile'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
           return (['code'=>'0','msg'=>'登录成功','token'=>$success['token'],'type'=>$user['type'],
           'unreads'=>$user['unreads']
           ]);
        }
        else{
           return (['code'=>'-1','msg'=>'登录失败']);
        }
    }
	  
	//注册 待完善
	 public function register(Request $request)
    {
    	$ValidCode=request('ValidCode');
		$list=db::table('validcode')->where([['mobile',request('mobile')],['ValidCode',request('ValidCode')]])->get();
		
		if ($list->count()){
	        $validator = Validator::make($request->all(), [
	            'mobile' => 'required',
	            'password' => 'required',
	        ]);
	        if ($validator->fails()) {
	           return (['code'=>'-1','msg'=>'验证码错误']);         
	        }
	        $input = $request->all();
	        $input['password'] = bcrypt($input['password']);   //加密密码
	        $user = User::create($input);  
	        $success['token'] =  $user->createToken('MyApp')->accessToken;
	        $success['mobile'] = $user->mobile;
	       db::table('validcode')->where('mobile',request('mobile'))->delete();
	           return (['code'=>'0','msg'=>'注册成功']);
	          }
			
		else{
			return  (['code'=>'-2','msg'=>'验证码错误']);
		}
	}
	 
	 //手机验证码
	 public function getValidCode(){
	 	$mobile=request('mobile');
		$rand=mt_rand(1000,9999);              
	    $list=db::table('users')->where('mobile', $mobile)->get();
		if($list->count()){
		     return (['code'=>'-1','msg'=>'手机号已注册']);
		}
		else{
	 	$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
		$smsConf = array(
		    'key'       => 'd6921e49c1a453c0785c532d2c911deb', //您申请的APPKEY
		    'mobile'    => $mobile , //接受短信的用户手机号码
		    'tpl_id'    => '124906', //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#code#='.$rand.'&#company#=极客艺起' //您设置的模板变量，根据实际情况修改
		);
		$list_two=db::table('validcode')->where('mobile', $mobile)->get();
					
		if($list_two->count()){
		 db::table('validcode')->where('mobile', $mobile)->update(["validcode"=>$rand]);
		}else{
		 db::table('validcode')->insert(["mobile"=>$mobile,"validcode"=>$rand]);
		}
		
		$content = juhecurl($sendUrl,$smsConf,1); //请求发送短信
		
		 
		if($content){
		    $result = json_decode($content,true);
		    $error_code = $result['error_code'];
		    if($error_code == 0){
		        //状态为0，说明短信发送成功
		        echo "短信发送成功,短信ID：".$result['result']['sid'];
		    }else{
		        //状态非0，说明失败
		        $msg = $result['reason'];
		        echo "短信发送失败(".$error_code.")：".$msg;
		    }
		}else{
		    //返回内容异常，以下可根据业务逻辑自行修改
		    echo "请求发送短信失败";
		   }	
		  return (['code'=>'0','msg'=>'发送成功']);
		}
	}

    //站内短消息
    public function getMessage(){
    	$token=input('token');
			
	}

	
	
}        
           //发送消息
         function juhecurl($url,$params=false,$ispost=0){
		    $httpInfo = array();
		    $ch = curl_init();
		    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
		    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
		    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
		    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
		    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
		    if( $ispost )
		    {
		        curl_setopt( $ch , CURLOPT_POST , true );
		        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
		        curl_setopt( $ch , CURLOPT_URL , $url );
		    }
		    else
		    {
		        if($params){
		            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
		        }else{
		            curl_setopt( $ch , CURLOPT_URL , $url);
		        }
		    }
		    $response = curl_exec( $ch );
		    if ($response === FALSE) {
		        //echo "cURL Error: " . curl_error($ch);
		        return false;
		    }
		    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
		    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
		    curl_close( $ch );
		    return $response;
		 }

		   
					   
?>