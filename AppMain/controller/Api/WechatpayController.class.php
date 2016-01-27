<?php

    namespace AppMain\controller\Api;
    use \System\BaseClass;
    /**
    * 微信支付类
    */
    class WechatpayController extends BaseClass {

        /**
        * 微信JSSDK配置
        */
        public function wechatPayConfig(){
             $this->V(['url'=>[]]);
             //微信JS
            $wechat=new \System\lib\Wechat\Wechat($this->config('WEIXIN_CONFIG'));
            //获取jsapi
            $wechatConfig=$wechat->getJsSign($_POST['url']);
            $this->R(['wechatConfig'=>$wechatConfig]);
        }

        /**
        * 微信支付
        */
        public function purchase(){
            if (!isset($_SESSION['userInfo']['openid'])) {
                $this->getOpenID();
                }else{
                    $rule = [
                        'goods_sn' =>[],
                        'price'    =>['egNum',null,true],
                        'goods_id' =>['egNum',null,true],
                        'user_id'  =>['egNum',null,true],
                    ];
                $this->V($rule);
                foreach ($rule as $k => $v) {
                $data[$k] = $_POST[$k];
                    }
                $goods_id    = $data['goods_id'];
                $user_id     = $data['user_id'];
                $num         = $data['price'];
                //是否冻结用户
                $user = $this->table('user')->where(['is_on'=>1,'id'=>$user_id,'is_froze'=>0])->get(['id'],true);
                if(!$user){
                    $this->R('',90008);
                }

                $good = $this->table('goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['limit_num','price'],true);
                if(!$good){
                    $this->R('',90001);
                }
                $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$goods_id])->get(['id'],false);
                $count = count($status);
                $total_num = $good['price'];
                $last_num =$total_num-$count;
                if ($num>$last_num) {
                    $this->R('',90003);
                }
                //判断是否超过限购数
                if ($num>$good['limit_num']) {
                    $this->R('',90001);
                }
                //判断是否卖完了
                $code = $this->table('code')->where(['goods_id'=>$goods_id,'is_use'=>0,'is_get'=>0])->get(['id'],true);
                if(!$code){
                    $this->R('',90003);//90010商品已卖完
                }
                //判断是否已经买过并且超过限购数量
                $limit = $this->table('purchase')->where(['user_id'=>$user_id,'goods_id'=>$goods_id,'is_on'=>1])->get(['id'],false);
                $count = count($limit);
                if ($count+$num>$good['limit_num']) {
                    $this->R('',90001);
                }
                //判断是否超过认购码库存
                $codestock = $this->table('code')->where(['goods_id'=>$goods_id,'is_use'=>0,'is_get'=>0])->get(['id'],false);
                if(!$codestock){
                    $this->R('',70009);
                }
                if ($num>count($codestock)) {
                    $this->R('',90003);//90008选择的数量超过现有库存
                }

            //开启事务
            $this->table()->startTrans();
            //控制临时限定认购码
            $codenum = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'is_get'=>0,'goods_id'=>$goods_id])->order("rand()")->limit($num)->get(['code'],false);
            $count = count($codenum);
            for ($i=0; $i < $count; $i++) { 
                $getcode = $this->table('code')->where(['is_on'=>1,'code'=>$codenum[$i]['code'],'goods_id'=>$goods_id])->update(['is_get'=>$user_id,'update_time'=>time()]);
                if(!$getcode){
                    $this->R('',40001);
                }
            } 
                $openid = $_SESSION['userInfo']['openid'];
                $data = array(
                     'orderSn' => $data['goods_sn'].rand(1,9999),//订单号不能唯一
                     'price'   => $data['price'],
                     'attach' => json_encode(array('goods_sn' => $data['goods_sn'],'price'=>$data['price'])),
                    'body' => '一元购支付',
                    'detail' => '一元购支付服务',
                    'notifyUrl'=>gethost().'/Api/Callback/order',
                    );
                $wechatPay = $this->H('Wechat')->wechatPay($data,$openid);
                if (!$wechatPay) {
                    $this->table()->rollback();
                    $this->R('',40001);
                    }
                $this->table()->commit();
                $this->R(['wechatPay' => $wechatPay]);
            }
        }
        public function unPay(){
             $rule = [
                        'goods_id' =>['egNum',null,true],
                        'user_id'  =>['egNum',null,true],
                    ];
                $this->V($rule);
                foreach ($rule as $k => $v) {
                $data[$k] = $_POST[$k];
                    }
                $goods_id    = $data['goods_id'];
                $user_id     = $data['user_id'];
                $getcode = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'is_get'=>$user_id,'goods_id'=>$goods_id])->update(['is_get'=>0,'update_time'=>time()]);
                if(!$getcode){
                    $this->R('',40001);
                }
                $this->R();
        }
       /**
         * 退款
         */
        public function refund(){
             $this->V(['record_id'=>['egNum',null,true]]);
            $id = intval($_POST['record_id']);
            $this->V(['refund_fee'=>['egNum',null,true]]);
            $refund_fee = intval($_POST['refund_fee']);
            $order = $this->table('record')->where(['is_on'=>1,'id'=>$id])->get(null,true);
            if (!$order) {
                $this->R('','70009');
            }
            //echo phpinfo();exit();
            //echo file_get_contents('/disk2/ftp/buy/www/cert/apiclient_cert.pem');exit();
            $data['total_fee'] = $order['num'];
            $data['out_trade_no'] = $order['wxPay_sn'];
            $data['out_refund_no'] = date("Ymd").rand(100000,999999);
            $data['refund_fee'] = $refund_fee;
            $data['op_user_id'] = 1243036302;
            $result = $this->H('Wechat')->wechatRefund($data);
            if (!$result) {
                    $this->R('',40001);
                    }

                $this->R(['refund' => $result]);

        }
    
        public function record(){
            $this->V(['goods_id'=>['egNum',null,true]]);
            $goods_id = intval($_POST['goods_id']);
            $this->V(['user_id'=>['egNum',null,true]]);
            $user_id = intval($_POST['user_id']);
            $record = $this->table('record')->where(['goods_id'=>$goods_id,'user_id'=>$user_id,'is_on'=>1])->order('add_time desc')->get(['num'],true);
            $goods =$this->table('goods')->where(['id'=>$goods_id])->get(['goods_name','goods_thumb','price'],true);
            $record['goods_name'] = $goods['goods_name'];
            $record['goods_thumb'] = $goods['goods_thumb'];
            $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$goods_id])->get(['id'],false);
            $count = count($status);
            $price = $goods['price'];
            $last_num = $goods['price']-$count;
            $record['last_num'] =$last_num;
            if (!$record) {
                $this->R('70009');
            }
            $this->R(['record'=>$record]);
        }
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
                    'nickname'=>$isUser['nickname'],
                ];

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