<?php 
	namespace AppMain\helper;
	use System\BaseHelper;
	/*
    * 优惠券券号生成class
    */
	class CouponHelper extends BaseHelper {
	/** 
	* @param int $no_of_codes//定义一个int类型的参数 用来确定生成多少个优惠码 
	* @param array $exclude_codes_array//定义一个exclude_codes_array类型的数组 
	* @param int $code_length //定义一个code_length的参数来确定优惠码的长度 
	* @return array//返回数组 
	*/ 
	function generate_promotion_code($no_of_codes,$exclude_codes_array='',$code_length = 4) 
	{
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
		$promotion_codes = array();//这个数组用来接收生成的优惠码 
		for($j = 0 ; $j < $no_of_codes; $j++) 
		{ 
		$code = ""; 
		for ($i = 0; $i < $code_length; $i++) 
		{ 
		$code .= $characters[mt_rand(0, strlen($characters)-1)]; 
		} 
		//如果生成的4位随机数不再我们定义的$promotion_codes函数里面 
		if(!in_array($code,$promotion_codes)) 
		{ 
		if(is_array($exclude_codes_array))// 
		{ 
		if(!in_array($code,$exclude_codes_array))//排除已经使用的优惠码 
		{ 
		$promotion_codes[$j] = $code;//将生成的新优惠码赋值给promotion_codes数组 
		} 
		else 
		{ 
		$j--; 
		} 
		} 
		else 
		{ 
		$promotion_codes[$j] = $code;//将优惠码赋值给数组 
		} 
		} 
		else 
		{ 
		$j--; 
		} 
		} 
		return $promotion_codes; 
		}
	}
?> 