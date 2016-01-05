<?php
namespace System\lib\Wechat;
use \System\BaseClass;
class Jssdk extends BaseClass
{
  private $appId;
  private $appSecret;

  public function __construct($options) {
    $this->appId = isset($options['appid'])?$options['appid']:'';
    $this->appSecret = isset($options['appsecret'])?$options['appsecret']:'';
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp =(string)time();
    $nonceStr = $this->createNonceStr();
    //$string = array($nonceStr,$timestamp,$url)
    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      //"jsapiTicket"=>$jsapiTicket,
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
     // "rawString" => $string
    );
    //setcookie("appId",$signPackage["appId"],time()+3600,'/');
    //setcookie("noncestr",$signPackage["nonceStr"],time()+3600,'/');
    //setcookie("timestamp",$signPackage["timestamp"],time()+3600,'/');
    //setcookie("signature",$signPackage["signature"],time()+3600,'/');

    return $signPackage;
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrtuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    $jsapi_ticket = $this->S()->get('jsapi_ticket');
    if (!$jsapi_ticket){
      $accessToken = $this->getAccessToken();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      @$jsapi_ticket = $res->jsapi_ticket;
      if ($jsapi_ticket) {
        $this->S()->set('jsapi_ticket' , serialize($jsapi_ticket),60*60);
        //setcookie("ticket",$ticket,time()+3600,'/');
      }
    }else{
        $jsapi_ticket = unserialize($jsapi_ticket);
       }
    return $jsapi_ticket;
  }

  private function getAccessToken() {
    $access_token = $this->S()->get('access_token');
    if (!$access_token) {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $this->S()->set('access_token' , serialize($access_token),60*60);
        //setcookie("access_token",$access_token,time()+3600,'/');
        }
      }
      else{
        $access_token = unserialize($access_token);
      }
     // @$access_token = $_COOKIE["access_token"];

    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }
  private function get_php_file($filename) {
  return trim(substr(file_get_contents($filename), 15));
  }
  private function set_php_file($filename, $content) {
    $fp = fopen($filename, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
  }
}