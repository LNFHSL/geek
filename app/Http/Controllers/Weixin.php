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

    protected function options(){ //选项设置
		return [
            // 前面的appid什么的也得保留哦
            'app_id' => 'eGjIxh9JjWMzubcXLoyAXomI1wB1redy', //你的APPID
            'secret'  => '92260d5c2656a8ff98bb1fe906fcc6c7',     // AppSecret
            // 'token'   => 'your-token',          // Token
            // 'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
            // ...
            // payment
            'payment' => [
                'merchant_id'        => '1502049251',
                'key'                => 'eGjIxh9JjWMzubcXLoyAXomI1wB1redy',
                // 'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
                // 'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
                'notify_url'         => 'https://www.aoyuankj.com/fenda/public/api/weixin/pay_code',       // 你也可以在下单时单独设置来想覆盖它
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
        ];
	}
    // 支付
    public function pay(){

        return;
        $app = EasyWeChat::officialAccount(); // 公众号
      
        $oauth = $app->oauth;

        // 未登录
        if (empty($_SESSION['wechat_user'])) {

        $_SESSION['target_url'] = 'user/profile';

        return $oauth->redirect();
        // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
        // $oauth->redirect()->send();
        }



    // 已经登录过
    $user = $_SESSION['wechat_user'];

        // $user = Auth::user();
        $id = $user['id'];//用户id
        $price = request('price');
        $type = request('type');
        $detail = '';
        if ( $type == 'price') {
            $detail = '购买会员';
        }
        $mch_id = '1502049251';//你的MCH_ID
            $options = $this->options();
            
  $payment = EasyWeChat::payment(); // 微信支付

  $result = $payment->order->unify([
    'body' => '腾讯充值中心-QQ会员充值',
    'out_trade_no' => '20150806125346',
    'total_fee' => 88,
    'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
    'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
    'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
    'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
]);


            // $app = new Application($options);
            // $payment = $app->payment;
            // $out_trade_no = $mch_id.date("YmdHis"); //拼一下订单号
            // $attributes = [
            //     'trade_type'       => 'APP', // JSAPI，NATIVE，APP...
            //     'body'             => '购买CSDN产品',
            //     'detail'           => $detail, //我这里是通过订单找到商品详情，你也可以自定义
            //     'out_trade_no'     => $out_trade_no,
            //     'total_fee'        => $price*100, //因为是以分为单位，所以订单里面的金额乘以100
            //     // 'notify_url'       => 'http://xxx.com/order-notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            //     // 'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            //     // ...
            // ];
            // $order = new Order($attributes);
            // $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $order_find->out_trade_no = $out_trade_no; //在这里更新订单的支付ID
            $order_find->save();
            // return response()->json(['result'=>$result]);
                $prepayId = $result->prepay_id;
                $config = $payment->configForAppPayment($prepayId);        
                return response()->json($config);
        }
    }

    public function pay_code()
    {
        $options = $this->options();
        $app = new Application($options);
    	$response = $app->payment->handleNotify(function($notify, $successful){
		    // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
		    $order = ExampleOrder::where('out_trade_no',$notify->out_trade_no)->first();
		    if (count($order) == 0) { // 如果订单不存在
		        return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
		    }
		    // 如果订单存在
		    // 检查订单是否已经更新过支付状态
		    if ($order->pay_time) { // 假设订单字段“支付时间”不为空代表已经支付
		        return true; // 已经支付成功了就不再更新了
		    }
		    // 用户是否支付成功
		    if ($successful) {
		        // 不是已经支付状态则修改为已经支付状态
		        $order->pay_time = time(); // 更新支付时间为当前时间
		        $order->status = 6; //支付成功,
		    } else { // 用户支付失败
		        $order->status = 2; //待付款
		    }
		    $order->save(); // 保存订单
		    return true; // 返回处理完成
		});

    }
    
}