<?php

    namespace AppMain\controller\Api;
    use \System\BaseClass;

    class WechatpayController extends BaseClass {
    	public function purchase(){
            if (!isset($_SESSION['userInfo']['openid'])) {
                $this->getOpenID();
            }else{
                $rule = [
                    'goods_sn' =>[],
                    'price'    =>['egNum',null,true],
                ];
                $this->V($rule);
                foreach ($rule as $k => $v) {
                $data[$k] = $_POST[$k];
                }
            // 'attach' => json_encode(array('orderSn' => $isOrder['order_sn'])),
                //$openid = "o3SK1wKbW5HtmVMit2Ma__vX5bvQ";
                $data = array(
                     'orderSn' => $data['goods_sn'],
                    //'orderSn' => 'FDADFDF',
                     'price'   => $data['price'],
                   // 'price'   => 3,
                    'body' => '一元购支付',
                    'detail' => '一元购支付服务',
                    //'notifyUrl'=>gethost().'/Api/Wechatpay/notifyUrl',
                    'notifyUrl'=>gethost().'/pay/notify.php',
                    );
                $wechatPay = $this->H('Wechat')->wechatPay($data,$_SESSION['userInfo']['openid']);
                if (!$wechatPay) {
                	$this->R('',90007);
                }
                //print_r($wechatPay);
                 $this->R(array('wechatPay' => $_SESSION['userInfo']['openid']));

                 }
    	}
        // public function notifyUrl(){
        //     if (isset($_GET)) {
        //         dump($_GET);
        //     }
        // }
        /**
         * 授权
         */
        private function getOpenID(){
            if (isset($_GET['wechat_refer'])){  //回跳地址
                $_SESSION['wechat_refer']=urldecode($_GET['wechat_refer']);
            }
            $weObj = new \System\lib\Wechat\Wechat($this->config("WEIXIN_CONFIG"));
            $this->weObj = $weObj;
            if (empty($_GET['code']) && empty($_GET['state'])) {
                $callback = getHostUrl();
                $reurl = $weObj->getOauthRedirect($callback, "1");
                redirect($reurl, 0, '正在发送验证中...');
                exit(); 
            } elseif (intval($_GET['state']) == 1) {
                    $accessToken = $weObj->getOauthAccessToken();
                    $isUser=$this->getUserInfo($accessToken);
                   $_SESSION['userInfo']=[
                    'openid'=>$isUser['openid'],
                    'userid'=>$isUser['id'],
                    'nickname'=>$isUser['nickname'],
                    'user_img'=>$isUser['user_img'],
                ];

                    header("LOCATION:".$_SESSION['wechat_refer']);
            } else {
                //用户取消授权
                $this->R('','90006');
                }
            }
            /**
         * 获取用户信息
         */
        private function getUserInfo($user){

            $user_info = $this->weObj->getOauthUserinfo($user['access_token'], $user['openid']);

            if (!$user_info){
                die("系统错误，请稍后再试！");
            }

            //是否关注
            $isFollow=$this->weObj->getUserInfo($user['openid']);
            if ($isFollow['subscribe']==1){
                $user_info['is_follow']=1;
            }
            else{
                $user_info['is_follow']=0;
            }

            return $user_info;
        }

    }