<?php
namespace System\lib\WxPay;

/**
* 	配置账号信息
*/

class WxPayConf_pub
{
	//=======【基本信息设置】=====================================
	static function APPID(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['appid'];
	}
	
	static function MCHID(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['mchid'];
	}
	
	static function KEY(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['key'];
	}
	
	static function SSLCERT_PATH(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['SSLCERT_PATH'];
	}
	
	static function SSLKEY_PATH(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['SSLKEY_PATH'];
	}
	
	
	static function NOTIFY_URL(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['NOTIFY_URL'];
	}
	
	static function APPSECRET(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['APPSECRET'];
	}
	
	static function JS_API_CALL_URL(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['JS_API_CALL_URL'];
	}
	
	static function CURL_TIMEOUT(){
	    return \System\Entrance::config('WEIXIN_PAY_CONFIG')['CURL_TIMEOUT'];
	}
	
	
	/* //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx69a86bef92d2a0d8';
	//受理商ID，身份标识
	const MCHID = '1228636702';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = '3ae00f86d1b1b07a73ff5903d2cab550';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = 'aafd0a93e65f6ef220cbaf22c9ab39d7';
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	const JS_API_CALL_URL = 'http://jiasudu.ping-qu.com/WechatWeb/Pay/index';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = '/disk2/www/qizi/apiclient_cert.pem';
	const SSLKEY_PATH = '/disk2/www/qizi/apiclient_key.pem';
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = 'http://jiasudu.ping-qu.com/WechatWeb/Pay/callback';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30; */
}
	
?>