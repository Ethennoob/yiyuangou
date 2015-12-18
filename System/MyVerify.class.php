<?php
namespace System;
class MyVerify {
	static $verError=null;
	
	
    /**
     * 验证
     * email,mobile,num,ip,url,idCard
     * @param unknown $rule
     * @param unknown $data
     */
    public static function verify($rule,$data){
    	foreach ($rule as $key=>$a){
    		if (is_string($key)&&!isset($data[$key])){
    			if (!isset($a[2]) || $a[2]===true){
                    self::$verError=$key.'验证字段不存在！';
    				return false;
    			}
    			elseif ($a[2]===false){
    				continue;
    			}
    		}
    		elseif (is_numeric($key)){
    			$rt=self::verify($a, $data);
    			if ($rt==false){
    			    return false;
    			}
    			continue;
    		}
    		
    		
			
    		$value=isset($data[$key])?$data[$key]:'';
    		
    		$type=isset($a[0])?$a[0]:'';
    		$rt=false;
    		switch ($type){
    			case 'email' :
    				$rt=self::isEmail($value);
    				break;
    			case 'mobile' :
    				$rt=self::isMobile($value);
    				break;
    			case 'num' :   //大于等于0的整数
    				$rt=(is_numeric($value)&&$value >=0)?true:false;
    				break;
    			case 'egNum' : 	//大于0的整数数字
    			    if (isset($a[1])&&$a[1]!==null){
    				    if (!(intval($value) > 0  && intval($value) < $a[1])){
    				        $rt=false;
    					}
    					else{
    					    $rt=true;
    					}
    					
    				}
    				else{
    					if (is_array($value) || intval($value) < 1){
    					    $rt=false;
    					}
    					else{
    					    $rt=true;
    					}
    				}
    				
    				break;
    			case 'zcode':	
    				$rt=self::isZcode($value);
    				break;
    			case 'domain':	
    				$rt=self::isDomain($value);
    				break;
    			case 'money':	
    				$rt=self::isMoney($value);
    				break;
    			case 'ip' :
    				$rt=self::isIP($value);
    				break;
    			case 'url' :
    				$rt=self::isURL($value);
    				break;
    			case 'idCard' :
    				$rt=self::isIDcard($value);
    				break;
    			case 'in' :
    				$rt=self::in($value, $a[1]);
    				break;
    			case 'reg' :
    				$rt=self::reg($value, $a[1]);
    				break;
    			case 'extra' :
    				$rt=self::multiToOne($a, $data);
    				break;
    			default:
    				if (!empty($value)){
    					$rt=true;
    				}
    				break;
    		}
    		
    		if ($rt==false){
    			self::$verError=$key.'字段验证错误！';
    			return false;
    		}
    	}
    	
    	return true;
    }
    
    /**
     * 验证是否为指定语言,$value传递值;$minLen最小长度;$maxLen最长长度;$charset默认字符类别（en只能英文;cn只能汉字;alb数字;ALL不限制）
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public static function islanguage($value, $charset = 'all', $minLen = 1, $maxLen = 50) {
    	if (!$value)
    		return false;
    	switch ($charset) {
    		case 'en' :
    			$match = '/^[a-zA-Z]{' . $minLen . ',' . $maxLen . '}$/iu';
    			break;
    		case 'cn' :
    			$match = '/^[\x{4e00}-\x{9fa5}]{' . $minLen . ',' . $maxLen . '}$/iu';
    			break;
    		case 'alb' :
    			$match = '/^[0-9]{' . $minLen . ',' . $maxLen . '}$/iu';
    			break;
    		case 'enalb' :
    			$match = '/^[a-zA-Z0-9]{' . $minLen . ',' . $maxLen . '}$/iu';
    			break;
    		case 'all' :
    			$match = '/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]{' . $minLen . ',' . $maxLen . '}$/iu';
    			break;
    			// all限制为：只能是英文或者汉字或者数字的组合
    	}
    	return preg_match($match, $value);
    }
    
    /**
     * 验证eamil,$value传递值;$minLen最小长度;$maxLen最长长度;$match正则方式
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public static function isEmail($value, $minLen = 6, $maxLen = 60, $match = '/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/i') {
    	if (!$value)
    		return false;
    	return (strlen($value) >= $minLen && strlen($value) <= $maxLen && preg_match($match, $value)) ? true : false;
    }
    
    /**
     * 格式化money,$value传递值;小数点后最多2位
     * @param string $value
     * @return boolean
     */
    public static function formatMoney($value) {
    	return sprintf("%1\$.2f", $value);
    }
    
    /**
     * 验证电话号码,$value传递值;$match正则方式
     * @param string $value
     * @return boolean
     */
    public static function isTelephone($value, $match = '/^(0[1-9]{2,3})(-| )?\d{7,8}$/') {
    	// 支持国际版：$match='/^[+]?([0-9]){1,3}?[ |-]?(0[1-9]{2,3})(-| )?\d{7,8}$/'
    	if (!$value)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 验证手机,$value传递值;$match正则方式
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isMobile($value, $match = '/^(0)?1([3|4|5|7|8])+([0-9]){9,10}$/') {
    	// 支持国际版：([0-9]{1,5}|0)?1([3|4|5|8])+([0-9]){9,10}
    	if (!$value)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 验证IP,$value传递值;$match正则方式
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isIP($value, $match = '/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/') {
    	if (!$value)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 验证身份证号码,$value传递值;$match正则方式
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isIDcard($value, $match = '/^\d{6}((1[89])|(2\d))\d{2}((0\d)|(1[0-2]))((3[01])|([0-2]\d))\d{3}(\d|X)$/i') {
    	if (!$value)
    		return false;
    	else if (strlen($value) > 18)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 验证URL,$value传递值;$match正则方式
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isURL($value, $match = '/^(http:\/\/)?(https:\/\/)?([\w\d-]+\.)+[\w-]+(\/[\d\w-.\/?%&=]*)?$/') {
    	$value = strtolower(trim($value));
    	if (!$value)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 验证邮政编码,$value传递值;$match正则方式
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isZcode($value, $match = '/^([0-9]{5})(-[0-9]{4})?$/i') {
    	$value = strtolower(trim($value));
    	if (!$value)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 验证域名,$value传递值;$match正则方式
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isDomain($value, $match = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i') {
    	$value = strtolower(trim($value));
    	if (!$value)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 验证金额,$value传递值;$match正则方式
     * @param string $value
     * @param string $match
     * @return boolean
     */
    public static function isMoney($value, $match = '/^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/') {
    	$value = strtolower(trim($value));
    	if (!$value)
    		return false;
    	return preg_match($match, $value);
    }
    
    /**
     * 包含验证
     * @param mix $value
     * @param array $match
     * @return number
     */
    public static function in($value, $match){
    	return in_array($value, $match);
    }
    
    /**
     * 多个参数其中一个不为空
     * @param mix $value
     * @param array $data
     * @return number
     */
    public static function multiToOne($value, $data){
    	$nullValue=false;
    	$count=count($value);
    	$i=0;
    	foreach ($value as $key=>$a){
    		if(self::verify($value, $data)){
    			$nullValue=true;
    			$i++;
    		}
    	}
    	
    	if ($i==0){
    		self::$verError='字段验证错误！';
    	}
    	elseif($i!=1){
    		$nullValue=false;
    		self::$verError='字段验证错误！';
    	}
    	
    	return $nullValue;
    }
    
    /**
     * 自定义验证
     * @param mix $value
     * @param unknown $match
     * @return number
     */
    public static function reg($value, $match){
    	return preg_match($match, $value);
    }
    

}
