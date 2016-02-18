<?php

namespace AppMain\controller\Groupbuy;
use \System\BaseClass;

class PayController extends BaseClass {
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
        //开启事务
        $this->V(['bill_id'=>['egNum',null,true]]);
        $id = intval($_POST['bill_id']);
        $data = $this->table('groupbuy_bill')->where(['id'=>$id])->get(['bill_sn','goods_id'],true);
        if (!$data) {
            $this->R('',70009);
        }
        $price = $this->table('groupbuy_goods')->where(['id'=>$data['goods_id']])->get(['price'],true);
        if (!$price) {
            $this->R('',70009);
        }        
        //$this->table()->startTrans();
        $openid = $_SESSION['userInfo']['openid'];
        $data = array(
             'orderSn' => $data['bill_sn'].rand(1,9999),//订单号不能唯一
             'price'   => $price['price']*100,
             'attach' => json_encode(array('bill_sn' => $data['bill_sn'],'price'=>$price['price'])),
            'body' => '反向团购支付',
            'detail' => '反向团购支付服务',
            'notifyUrl'=>gethost().'/Groupbuy/Callback/order',
            );
        //dump($data);exit();
        $wechatPay = $this->H('Wechat')->wechatPay($data,$openid);
        //dump($wechatPay);exit();
        if (!$wechatPay) {
            //$this->table()->rollback();
            $this->R('',40001);
            }
        //$this->table()->commit();
        $this->R(['wechatPay' => $wechatPay]);
        }
    }    
}