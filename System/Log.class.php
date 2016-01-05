<?php
namespace System;
class Log {   
    
   	/**
   	 * 写文件日志
   	 * @param string $content
   	 * @param string $fileName 文件名，可加入路径，eg:grlend/201111
   	 * @param string $ext 扩展名
   	 */
   	public static function write($content,$fileName='',$ext='txt'){
    	if (empty($fileName)){
    		$fileName= date("Ymd") . $ext;
    	}
    	
    	$fileName = __ROOT__.'/Log/'.$fileName.'.'.$ext;
    
        if (dirExists(dirname($fileName))) {
            file_put_contents($fileName, date("Y/m/d H:i:s") . '----' . PHP_EOL . $content . PHP_EOL, FILE_APPEND);
        }
    }
    
    

}
