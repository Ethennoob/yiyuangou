<?php
namespace System;
class MyRedis {
    private static $reObj = null;
    
    public function __construct() {
        if (self::$reObj === null) {
            if(!extension_loaded('redis')){
                die('服务器不支持redis扩展！');
        	}
        	
        	$reObj=new \redis();
        	
        	$config=\System\Entrance::config('REDIS_CONFIG');
        	$host=$config['host'];
        	$port=$config['port'];
        	$auth=$config['auth'];
        	if ($config['connectType']=='connect'){
        	    $connect=$reObj->connect($host,$port);
        	}
        	else{
        	    $connect=$reObj->pconnect($host,$port);
        	}
        	
            
        	if (!empty($auth)){
        	    if (!$reObj->auth($auth)){
        	        die('redis密码错误！');
        	    }
        	}
        	
        	if (!$connect){
        	    die('redis服务器连接失败！');
        	}
        	
        	self::$reObj=$reObj;
        }
        return self::$reObj;
    }
    
    /** 
     * 设置值 
     * @param string $key KEY名称 
     * @param string|array $value 获取得到的数据 
     * @param int $timeOut 时间 
     */  
    public function set($key, $value, $timeOut = 0 ,$serialize=true) {  
        if ($serialize){
            $value = serialize($value);
        }
        
        $retRes = self::$reObj->set($key, $value);  
        if ($timeOut > 0) self::$reObj->setTimeout($key, $timeOut);  
        return $retRes;  
    }
    
    /**
     * 同时设置多值
     * 当发现同名的key存在时，用新值覆盖旧值
     * @param array $value 设置的键值和值组成的数组   ['aaa'=>'123'];
     * @param int $timeOut 时间
     */
    public function multiSet($value) {
        foreach ($value as $key=>$a){
            $value[$key]=serialize($a);
        }
        
        $value = serialize($value);
        $retRes = self::$reObj->mset($value);
        return $retRes;
    }
    
    /**
     * 追加字符串
     * @param str $key
     * @param str $valuse
     */
    public function appendStr($key,$value){
        $retRes = self::$reObj->append($key,$value);
        return $retRes;
    }
  
    /** 
     * 通过KEY获取数据 
     * @param string $key KEY名称 
     */  
    public function get($key) {  
        $result = self::$reObj->get($key); 
        
        if (@unserialize($result)){         //判断是否需要反序列
            return unserialize($result);
        }
        else{
            return $result;
        }
    }  
      
    /** 
     * 删除一条数据 
     * @param string｜array $key KEY名称 
     */  
    public function delete($key) {  
        return self::$reObj->delete($key);  
    }  
      
    /** 
     * 清空所有数据库数据 
     */  
    public function flushAll() {  
        return self::$reObj->flushAll();  
    }
    
    /**
     * 清空当前数据库数据
     */
    public function flushDB(){
        return self::$reObj->flushDB();
    }
      
    /** 
     * 数据入队列 
     * @param string $key KEY名称 
     * @param string|array $value 获取得到的数据 
     * @param bool $serialize 是否作序列化处理，默认true
     * @param bool $right 是否插到表尾，默认true
     */  
    public function push($key, $value, $serialize=true,$right = true) {
        if ($serialize){
            $value = serialize($value);
        }
        return $right ? self::$reObj->rPush($key, $value) : self::$reObj->lPush($key, $value);  
    }  
      
    /** 
     * 数据出队列 
     * @param string $key KEY名称 
     * @param bool $left 是否从左边开始出数据 
     */  
    public function pop($key , $left = true) {  
    	$val = $left ? self::$reObj->lPop($key) : self::$reObj->rPop($key);  
        return json_decode($val);  
    }  
    
    /**
     * 数据出队列（监听）
     * @param string $key KEY名称
     * @param int $timeout 超时
     */
    public function blPop($key,$timeout=0){
    	return self::$reObj->blPop($key,$timeout);
    }
      
    /** 
     * 数据自增 
     * @param string $key KEY名称 
     */  
    public function increment($key) {  
        return self::$reObj->incr($key);  
    }  
  
    /** 
     * 数据自减 
     * @param string $key KEY名称 
     */  
    public function decrement($key) {  
        return self::$reObj->decr($key);  
    }  
      
    /** 
     * key是否存在，存在返回ture 
     * @param string $key KEY名称 
     */  
    public function exists($key) {  
        return self::$reObj->exists($key);  
    }  
    
    /**
     * redis服务器信息
     */
    public function info() {
        return self::$reObj->info();
    }
      
    /** 
     * 返回redis对象 
     * redis有非常多的操作方法，我们只封装了一部分 
     * 拿着这个对象就可以直接调用redis自身方法 
     */  
    public function redis() {  
        return self::$reObj;  
    }  

}

?>
