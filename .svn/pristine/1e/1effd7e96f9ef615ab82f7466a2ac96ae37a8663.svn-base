<?php
/**
 * http请求中间件
 */

namespace AppMain\middleware;
use \System\BaseClass;
class HttpMiddleware extends BaseClass {
	static $result=false;
	
	
	public function __construct(){
		$this->isOpenWeb();
	}
	
	/**
	 * 是否开启网站
	 */
	public function isOpenWeb(){
		$isOpen=true;
		if (!$isOpen){
			$isList=$this->whiteList();
			if (!self::$result){
				$this->R('',90000);
			}
		}
	}
	
	/**
	 * 白名单设置
	 * @example 
	 * ['模块','控制器','方法']	
	 * self::pass(['Api','Test','index11']);
	 * self::pass(['Admin']);
	 */
	private function whiteList(){
		//配置
		//['模块','控制器','方法']		
		self::pass(['Api','Callback']);
		self::pass(['Admin']);
	}
	
	/**
	 * 规则
	 * @param array $rule
	 * @return boolean
	 */
	private static function pass($rule){
		if (!self::$result){
			$module = \System\Entrance::$module;
			$class = \System\Entrance::$class;
			$function = \System\Entrance::$function;
			
			if (isset($rule[2])&&$rule[2]!=$function){
				return false;
			}
			if (isset($rule[1])&&$rule[1]!=$class){
				return false;
			}
			if (!(isset($rule[0])&&$rule[0]==$module)){
				return false;
			}
			
			self::$result=true;
		}
	}
	
	
}