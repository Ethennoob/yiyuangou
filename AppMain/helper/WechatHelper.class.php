<?php
namespace AppMain\helper;
use System\BaseHelper;
class WechatHelper extends BaseHelper {

    public function setCode() {
        
    }
/**
	 *  支付逻辑
	 *  $data array 传入数据   array("orderSn"=>"","money"=>"","attach"=>"")
	 */
	public function wechatPay($data,$openid){
	
		/**
		 * JS_API支付demo
		 * ====================================================
		 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
		 * 成功调起支付需要三个步骤：
		 * 步骤1：网页授权获取用户openid
		 * 步骤2：使用统一支付接口，获取prepay_id
		 * 步骤3：使用jsapi调起支付
		*/
		//include_once("../WxPayPubHelper/WxPayPubHelper.php");
	
		//使用jsapi接口
		$jsApi = new \System\lib\WxPay\JsApi_pub();
	
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		//$openid=$_SESSION['openid'];
		//echo $openid;
		
		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new \System\lib\WxPay\UnifiedOrder_pub();
		
		if(empty($data['notifyUrl'])) {
		   $data['notifyUrl']= \System\lib\WxPay\WxPayConf_pub::NOTIFY_URL() ;
		}
		
		
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$unifiedOrder->setParameter("openid","$openid");//商品描述
		$unifiedOrder->setParameter("body",$data['body']);//商品描述
		$unifiedOrder->setParameter("detail",$data['detail']);//商品描述
		//自定义订单号，此处仅作举例
		$timeStamp = time();
		$out_trade_no = \System\lib\WxPay\WxPayConf_pub::APPID()."$timeStamp";
		$unifiedOrder->setParameter("out_trade_no",$data['orderSn']);//商户订单号
		$unifiedOrder->setParameter("total_fee",$data['price']);//总金额
		$unifiedOrder->setParameter("notify_url", $data['notifyUrl']);//通知地址
		//$unifiedOrder->setParameter("notify_url", $data['notifyUrl']);//通知地址
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号
		$unifiedOrder->setParameter("attach",$data['attach']);//附加数据
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
		//$unifiedOrder->setParameter("goods_tag",$data['goods_tag']);//商品标记
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id",$data['product_id']);//商品ID
	
		$prepay_id = $unifiedOrder->getPrepayId();
		//dump($unifiedOrder->createXml());
	
		//dump($prepay_id);
		//exit();
		if(!$prepay_id) return false;
		
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);
	
		$jsApiParameters = $jsApi->getParameters();
		//dump($jsApiParameters);
		
		return $jsApiParameters;
	}
	

}
