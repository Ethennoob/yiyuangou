<?php
namespace System;
class Router{
    static $isView=false;
    static $isViewMuti=false;
    
	public static function router(){
        //载入配置
        self::defaultConf();
    }

    /**
     * 默认配置
     */
    public static function defaultConf(){
        //美化url
        $module = Entrance::$module;
        $class = Entrance::$class;
        $function = Entrance::$function;
        
        if (strlen($module) > 0 && strlen($class) > 0 && strlen($function) > 0) {
            self::Controller($module . '.' . $class)->$function();
        } else {
            echo '非法访问';
            exit();
        }
    }

    /**
     * 加载控制器
     */
    public static function Controller($class,$isView=false,$isMuti=false,$functionName=null) {
    	$class = str_replace(array('.', '#'), array('\\', '.'), $class);
    	
    	if ($isView){
    		self::$isView=true;
    		self::$isViewMuti=$isMuti;
    	}
    	
    	BaseClass::$functionName=$functionName;
    	
        return self::getClass("\\AppMain\\controller\\" .$class . "Controller");
    }

    /**
     * 加载类
     */
    public static function getClass($class, $db = "") {
        if ($db != "") {
            $class = '\\AppMain\\data\\' . $db . '\\' . $class;
        }
        
        if (class_exists($class)) {
            return new $class($db);
        } else {
            throw new \Exception('找不到类文件：'.$class, 500);
        }
        exit;
    }
    
    
}