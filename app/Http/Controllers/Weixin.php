<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

 
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use EasyWeChat;

/**
 * 微信接口类
 */
class Weixin extends Controller
{

    public $successStatus = 200;
    public $user = null;
    function __CONSTRUCT(){
        $this->user = Auth::user();
    }
 
    // 支付
    public function pay(){
        
        $user = Auth::user();
        $user_openid = DB::table("users")
            ->where("id",$user['id'])
            ->value('openid');
        $price = request('price');

        if ($user_openid != '') {
            $time = time();
            $app = EasyWeChat::payment(); // 公众号

            
            $notify['type'] = request('type');//付款类型
            // $notify['type'] = 'vip';//付款类型

            // $notify['shop_id'] = 1;//商品id
            $notify['shop_id'] = request('shop_id');//商品id

            $notify['uid'] = $user['id'];//用户id

            $notify['price'] = $price;//价格

            $notify['rand'] = rand ( 111111111, 999999999 );

            $attach = implode("_",$notify);

            $notify['out_trade_no']  = date('YmdHis',$time).mt_rand(1000,9999);
            $result = $app->order->unify([
                'body' => '极客通告',
                'out_trade_no' => $notify['out_trade_no'],
                // 'total_fee' => 0.01*100,
                'total_fee' => $price*100,
                'attach' => $attach,
                'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
                'openid' => $user_openid,
            ]);
             $appgzh = EasyWeChat::officialAccount(); // 公众号
             $notify['openid'] = $user_openid;
             $notify['attach'] = $attach;
             DB::table("weixin_notify")->insert($notify);
            // $result['prepay_id'] =  "prepay_id=".$result['prepay_id'];
            // $result['time_stamp'] =  (string)$time;

            // $result['gzh'] = $appgzh->jssdk->buildConfig(array('chooseWXPay'), true);
             $ss = $app->jssdk->sdkConfig($result['prepay_id']);
             $result['timestamp'] = $ss['timestamp'];

             
            //  appId: '', // 必填，公众号的唯一标识
            //  timestamp: , // 必填，生成签名的时间戳
            //  nonceStr: '', // 必填，生成签名的随机串
            //  signature: '',// 必填，签名
            
            $result['jsApiList'] = ['chooseWXPay']; 
            $result['nonceStr'] = $result['nonce_str']; 
            $result['signature'] = $result['sign'];
            $result['appId'] = $result['appid'];
             $result['cfg'] = json_encode($result);
           
             $result['paySign'] = $ss['paySign'];
             $result['package'] = $ss['package'];
             
             $result['gzh'] = $result;
            //  $result['cfg'] = $appgzh->jssdk->buildConfig(array('chooseWXPay'),true);

            // return $result;

           return $app->jssdk->bridgeConfig($result['prepay_id']);
        }
        
    }

    public function pay_code()
    {
        
        $app = EasyWeChat::payment();  
        $response = $app->handlePaidNotify(function($notify, $fail){  
             DB::table("weixin_notify")->where("id",1)->update(['attach'=>time()]);
             
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单  
            $order =   DB::table("weixin_notify")->where('out_trade_no',$notify['out_trade_no'])->first(); 

            if (count($order) == 0) { // 如果订单不存在 

                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了  
            }  
            // 如果订单存在  
            // 检查订单是否已经更新过支付状态  
            if ($order->pay_time!='') { // 假设订单字段“支付时间”不为空代表已经支付  

                return true; // 已经支付成功了就不再更新了 

            }  
            
            // 用户是否支付成功  
            if ($notify['return_code'] === 'SUCCESS') { 

                 // 用户是否支付成功
                if (array_get($notify, 'result_code') === 'SUCCESS') {

                     // 不是已经支付状态则修改为已经支付状态  
                    $orderu['pay_time'] = time(); // 更新支付时间为当前时间  
                    $orderu['status'] = 6; //支付成功,  

                    $title =  '';
                    if ($order->type == 'vip') {
                        $title = 'vip开通';
                        $list=db::table('users')->where('id',$order->uid)->update(['member'=>$order->shop_id]);
                    }elseif ($order->type == 'notice_pay') {
                        $title = '发通告支付';
                        $list=db::table('notice_list')->where('id',$order->shop_id)->update(['is_pay'=>1]);

                    }
             
                    db::table('pay_record')->insert([
                    'uid'=>$order->uid,'price'=>$order->price,'title'=>$title,'datetime'=>time()]);
           
                // 用户支付失败
                } elseif (array_get($notify, 'result_code') === 'FAIL') {

                    $orderu['status'] = 2; //待付款  
                }

               

            } else { // 用户支付失败  
                $orderu['status'] = 2; //待付款  
                DB::table("weixin_notify")->where("id",1)->update(['attach'=>'通信失败，请稍后再通知我']);  
            } 
            DB::table("weixin_notify")->where("id",$order->id)->update($orderu);  

            return true; // 返回处理完成  
        });  

    }

    // token验证
    public function token()
    {
        $app = EasyWeChat::officialAccount(); // 公众号
        
        $response = $app->server->serve();
        return $response;
    }

    // 自定义菜单
    public function menu()
    {
        $app = EasyWeChat::officialAccount(); // 公众号
        $buttons = [
            [
                "type" => "view",
                "name" => "关于澳源",
                "url"  => "http://www.aoyuankj.com/AboutUs/"
            ],
            [
                "type" => "view",
                "name" => "官网首页",
                "url"  => "http://www.aoyuankj.com/"
            ],
            [
                "name"       => "项目合作",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "项目合作",
                        "url"  => "http://www.aoyuankj.com/productCenter/"
                    ],
                    [
                        "type" => "view",
                        "name" => "校企合作",
                        "url"  => "http://www.aoyuankj.com/xqhz/"
                    ]
                ],
            ],
        ];
        $app->menu->create($buttons);
    }

    // uauth授权
    
    public function getopenid(Request $request)
    {
        $app = EasyWeChat::officialAccount(); // 公众号
        $response = $app->oauth->scopes(['snsapi_userinfo'])
        ->setRequest($request)
        ->redirect();

        
        return $response;
    }

    // public function gethasopen()
    // {
    //     $app = EasyWeChat::officialAccount(); // 公众号
    //     $wxuser = $app->oauth->user();
         
         
    //     $user = Auth::user();
    //     $user_openid = DB::table("users")
    //         ->where("id",$user['id'])
    //         ->value('openid');
    //     if (empty($user_openid)) {
    //         $res = DB::table("users")
    //         ->where("id",$user['id'])
    //         ->update([
    //             'openid'=>$wxuser->getId(),
    //             'image'=>$wxuser->getAvatar(),
    //             'username'=>$wxuser->getNickname(),
    //         ]);
    //     }    
		
       
    // }
    public function gethasopen()
    {
        
           
        $app = EasyWeChat::officialAccount(); // 公众号
        $wxuser = $app->oauth->user();
         
         
        $user = Auth::user();
       
        if ($user['id']<=0) {
          
             $user_auto = DB::table("users")
                ->where("openid",$wxuser->getId())
                ->first();


              if($user_auto->openid && Auth::attempt(['openid' => $user_auto->openid ])){
                    $user = Auth::user();
                    $success['token'] =  $user->createToken('MyApp')->accessToken;
                     return (['code'=>'0','msg'=>'登录成功','token'=>$success['token'],'type'=>$user['type'],
                    'unreads'=>$user['unreads'],'openid'=>$user['openid']
                    ]);
                }else{
                    return (['code'=>'-1','msg'=>'密码不正确','token'=>'','type'=>'',
                    'unreads'=>'','openid'=>'' ]);
                }
        }else{
         $user_openid = DB::table("users")
                ->where("id",$user['id'])
                ->value('openid');
            if (empty($user_openid)) {
                $res = DB::table("users")
                ->where("id",$user['id'])
                ->update([
                    'openid'=>$wxuser->getId(),
                    'image'=>$wxuser->getAvatar(),
                    'username'=>$wxuser->getNickname(),
                ]);
            } 
        }

          
		
       
    }
    
    
}