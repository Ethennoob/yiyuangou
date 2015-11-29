<?php
namespace System;

class MyLocalCache{
    //Path to cache folder
    public $cachePath = 'cache/';
    //Length of time to cache a file, default 1 day (in seconds)
    public $cacheTime = 86400;
    //Cache file extension
    public $cacheExttension = '.cache';
    public $errorInfo = null;
    
    public function __construct($cachePath = null, $cacheExttension = '.cache') {
        
        if(empty($cachePath)){
            $cachePath=__ROOT__.'/Cache';
            $this->cachePath = $cachePath;
        }
        
        $this->cacheExttension = $cacheExttension;
        if (!file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0777);
        }
    }
    
    /**
     * 增加一对缓存数据
     * @param string $key
     * @param mixed $value
     * @param number $cacheTime
     * @return boolean
     */
    public function set($key, $value,$cacheTime=0) {
        $filename = $this->getCacheFile($key);
        
        $saveValue=[
            'value' => $value ,
            'cacheTime' => $cacheTime
        ];
        
        //写文件, 文件锁避免出错
        if(file_put_contents($filename, serialize($saveValue), LOCK_EX)){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * 删除对应的一个缓存
     * @param string $key
     * @return boolean
     */
    public function delete($key) {
        $filename = $this->getCacheFile($key);
        if(unlink($filename)){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * 获取缓存
     * @param string $key
     * @return Ambigous <boolean, mixed>|boolean
     */
    public function get($key) {
        $value=$this->hasCache($key);
        if ($value) {
            return $value;
        }
        
        return false;
    }
    
    /**
     * 删除所有缓存
     */
    public function flush() {
        $fp = opendir($this->cachePath);
        while(!false == ($fn = readdir($fp))) {
            if($fn == '.' || $fn =='..') {
                continue;
            }
            unlink($this->cachePath . '/' . $fn);
        }
        
        return true;
    }
    
    /**
     * 是否存在缓存
     * @param string $key
     * @return boolean|mixed
     */
    private function hasCache($key) {
        $filename = $this->getCacheFile($key);
        if(file_exists($filename)) {
            $value = file_get_contents($filename);
            if (empty($value)) {
                return false;
            }
            
            $value=unserialize($value);
            if ((filemtime($filename) + $value['cacheTime']) >= time() && $value['cacheTime']!=0){
                return false;
            }
            return $value['value'];
        }
        return false;
    }
    
    /**
     * 验证cache key是否合法，可以自行增加规则
     * @param unknown $key
     * @return boolean
     */
    private function isValidKey($key) {
        if ($key != null) {
            return true;
        }
        return false;
    }
    
    /**
     * @param string $key
     * @return string
     */
    private function safeFilename($key) {
        if ($this->isValidKey($key)) {
            return md5($key);
        }
        //key不合法的时候，均使用默认文件'unvalid_cache_key'，不使用抛出异常，简化使用，增强容错性
        return 'unvalid_cache_key';
    }
    
    /**
     * 拼接缓存路径
     * @param string $key
     * @return string
     */
    private function getCacheFile($key) {
        return $this->cachePath . '/'. $this->safeFilename($key) . $this->cacheExttension;
    }
    
    
}