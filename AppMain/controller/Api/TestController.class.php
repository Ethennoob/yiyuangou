<?php
    namespace AppMain\controller\Api;
    use \System\BaseClass;
    class TestController extends BaseClass {
    	public function test(){
		$weObj = new \System\lib\Wechat\Template($this->config("WEIXIN_CONFIG"));
		$openid = "o_Oqut6MX4_AenAYe9vTmcMpZYnk";
		$content = "text";
		$weObj->setTemplate($openid,$content);
		if ($weObj) {
			echo "yes!";
		}
	}
}