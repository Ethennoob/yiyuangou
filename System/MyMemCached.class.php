<?php
/**
 * Description of MemCached
 *
 * @author lzy
 */
namespace System;
class MyMemCached {

    private static $memObj = null;
	private static $type=null;   //使用memcache客户端状态（）
	
	public $isCompression=false; //是否启用压缩
    
    public function __construct() {
        if (self::$memObj === null) {
        	//为了支持window
        	if (extension_loaded('Memcached')){
        		$class='Memcached';
        		self::$type='Memcached';
        	}
        	elseif (extension_loaded('Memcache')){
        		$class='Memcache';
        		self::$type='Memcache';
        	}
        	else{
        		die('缺乏memcached支持！');
        	}
        	
            self::$memObj = new $class();
            $memConfig=\System\Entrance::config("MEMCACHE_CONFIG");
            
            //添加服务器
            foreach ($memConfig as $key=>$server){
            	self::$memObj->addServer($server['host'], $server['port']);
            }
            
            if ($class=='Memcached' && $this->isCompression==false){
            	self::$memObj->setOption($class::OPT_COMPRESSION, false);  //关闭压缩
            }
        }
        else{
            return self::$memObj;
        }
    }

    /**
     * 获取一个缓存
     * @param string $key
     */
    public function get($key) {
        if (isset(self::$memObj)) {
            return self::$memObj->get($key);
        }
    }

    /**
     * 新增一个缓存
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return boolean|mixed
     */
    public function set($key, $value, $expiration = 0) {
        if (isset(self::$memObj)) {
        	if (self::$type=='Memcached'){
        		$value = self::$memObj->set($key, $value, $expiration);
        	}
        	elseif (self::$type=='Memcache'){
        		if ($this->isCompression){
        			$value = self::$memObj->set($key, $value,MEMCACHE_COMPRESSED, $expiration);
        		}
        		else{
        			$value = self::$memObj->set($key, $value,0,$expiration);
        		}
        	}
        	else{
        		return false;
        	}
        	
            
            return $value;
        }
        return false;
    }

    /**
     * 删除缓存
     * @param string $key
     */
    public function delete($key) {
        if (isset(self::$memObj)) {
            self::$memObj->delete($key);
        }
    }

    /**
     * 获取多个缓存
     * @param string $keys
     * @param mixed $value
     */
    public function getMulti($keys, &$value) {
        if (isset(self::$memObj)) {
            $value = self::$memObj->getMulti($keys);
        }
    }

    /**
     * 增加多个缓存
     * @param unknown $k2vs
     */
    public function setMulti($k2vs) {
        if (isset(self::$memObj)) {
            self::$memObj->setMulti($k2vs);
        }
    }

    /**
     * 删除多个缓存
     * @param unknown $keys
     */
    public function deleteMulti($keys) {
        if (isset(self::$memObj)) {
            self::$memObj->deleteMulti($keys);
        }
    }

    /**
     * 将指定元素的值增加value。如果指定的key 对应的元素不是数值类型并且不能被转换为数值,
     * 会将此值修改为value. 
     * 不会在key对应元素不存在时创建元素。
     * @param type $key
     * @param type $value
     */
    public function increment($key, $value) {
        if (isset(self::$memObj)) {
            self::$memObj->increment($key, $value);
        }
    }
}

?>
