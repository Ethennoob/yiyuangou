<?php
namespace System;
class Router{
    
    public static function router(){
        //载入配置
        //require_once  __ROOT__.'/router.php';
        self::defaultConf();

        
   }
    
   public static function test(){
   		echo 'vfdfdgdf';
   }
    
    public static function defaultConf(){
        //美化url
        $module = Entrance::$module;
        $class = Entrance::$class;
        $function = Entrance::$function;
        
        if (strlen($module) > 0 && strlen($class) > 0 && strlen($function) > 0) {
            self::Controller("\\AppMain\\controller\\" . $module . "\\" . $class)->$function();
        } else {
            echo '非法访问';
            exit();
        }
    }
    
    public static function Controller($class) {
        return self::getClass($class . "Controller");
    }
    
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