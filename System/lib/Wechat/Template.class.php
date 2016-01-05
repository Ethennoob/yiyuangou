<?php
namespace System\lib\Wechat;
use \System\BaseClass;
/**
 * 微信消息模板类
 */
class Template extends BaseClass
{
  public function __construct($options)
  {
    $this->appId = isset($options['appid'])?$options['appid']:'';
    $this->appSecret = isset($options['appsecret'])?$options['appsecret']:'';
    }
  /**
   * 发送消息给用户（需传入用户openid和消息内容）
   */
  public function setTemplate($openid,$content){
    $weObj = new \System\lib\Wechat\Wechat($this->config("WEIXIN_CONFIG"));
        $this->weObj = $weObj;
         $data = array(
                        "touser" => $openid,
                        "msgtype" => "text",
                        "text" => array(
                            "content" => $content,
                        )
                    );
         $accessToken = $this->getAccessToken();
        $result = $this->http_post("https://api.weixin.qq.com/cgi-bin/message/custom/send?".'access_token='.$accessToken,self::json_encode($data));
        if ($result)
            {
                $json = json_decode($result,true);
                if (!$json || !empty($json['errcode'])) {
                    $this->errCode = $json['errcode'];
                    $this->errMsg = $json['errmsg'];
                    return false;
                }
                return $json;
            }
            return false;
  }
  private function getAccessToken() {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
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
  /**
   * POST 请求
   * @param string $url
   * @param array $param
   * @param boolean $post_file 是否文件上传
   * @return string content
   */
  private function http_post($url,$param,$post_file=false){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
      $strPOST = $param;
    } else {
      $aPOST = array();
      foreach($param as $key=>$val){
        $aPOST[] = $key."=".urlencode($val);
      }
      $strPOST =  join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_POST,true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    
    if(intval($aStatus["http_code"])==200){
      return $sContent;
    }else{
      return false;
    }
  }
  /**
   * 微信api不支持中文转义的json结构
   * @param array $arr
   */
  static function json_encode($arr) {
    $parts = array ();
    $is_list = false;
    //Find out if the given array is a numerical array
    $keys = array_keys ( $arr );
    $max_length = count ( $arr ) - 1;
    if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
      $is_list = true;
      for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
        if ($i != $keys [$i]) { //A key fails at position check.
          $is_list = false; //It is an associative array.
          break;
        }
      }
    }
    foreach ( $arr as $key => $value ) {
      if (is_array ( $value )) { //Custom handling for arrays
        if ($is_list)
          $parts [] = self::json_encode ( $value ); /* :RECURSION: */
        else
          $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
      } else {
        $str = '';
        if (! $is_list)
          $str = '"' . $key . '":';
        //Custom handling for multiple data types
        if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
          $str .= $value; //Numbers
        elseif ($value === false)
        $str .= 'false'; //The booleans
        elseif ($value === true)
        $str .= 'true';
        else
          $str .= '"' . addslashes ( $value ) . '"'; //All other things
        // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
        $parts [] = $str;
      }
    }
    $json = implode ( ',', $parts );
    if ($is_list)
      return '[' . $json . ']'; //Return numerical JSON
    return '{' . $json . '}'; //Return associative JSON
  }
}