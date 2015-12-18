<?php
namespace System;
class Entrance {
	static $loadConfig=null;
	static $loadConvert=null;
	
	//---美化url参数
	static $module = "";
    static $class = "";
    static $function = "";
	
	
    public static function action() {
        // 注册AUTOLOAD方法
        spl_autoload_register(['self','autoload']);
        define('_DOMAIN_', empty($_SERVER["HTTP_HOST"])?'api.grlend.com':$_SERVER["HTTP_HOST"]);
        
        ini_set('date.timezone', 'Asia/Shanghai'); // 设置时区
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
        header("Content-type:text/html;charset=utf-8");
        header("PowerBy: Han-zi,Liang");
        header("F-Version: 1.0");   //框架版本
        
        //载入防xss和sql注入文件
        require_once 'waf.php';
        
        //载入系统函数
        require_once 'function.php';
        
        startSession();
        
        //启动程序
        self::start();
    }
    
    public static function start(){
    	//美化url
		if(!empty($_SERVER['PATH_INFO'])){
    		$rewrite=explode("/",trim($_SERVER['PATH_INFO'],"/"));
    		if (count($rewrite) >= 3){
    			self::$module = $rewrite[0];
    			self::$class = $rewrite[1];
    			self::$function = $rewrite[2];
    		}
    	}
    	
    	isset($_GET['m'])?self::$module=trim($_GET['m']):self::$module;
    	isset($_GET['c'])?self::$class = trim($_GET['c']):self::$class;
    	isset($_GET['f'])?self::$function = trim($_GET['f']):self::$function;

		//扩展名分析
		if(!empty(self::$function)){
			if(strrpos(self::$function,'.htmlx') !== false){
				self::$function=str_replace('.htmlx','',self::$function);
			}
		}

    	
    	//内部php调用
    	if (isset($_SERVER['argv'][1])){
    		if ($_SERVER['argv'][1]=='task'){
    			Router::Controller("Task." . $_SERVER['argv'][2])->$_SERVER['argv'][3]();
    			exit;
    		}
    	}
    	
        //载入中间件
    	Router::getClass("\\AppMain\\middleware\\HttpMiddleware");
    	
        //载入路由
        Router::router();
     }

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        $filename = str_replace('\\', '/', $class);
        $path= __ROOT__.'/'.$filename . '.class.php';
        
        if (is_file($path)){
        	require_once $path;
        }
        else{
        	return false;
        }
    }

    /**
     * 网站配置参数
     */
    static public function config($name,$value=null) {
    	if (empty(self::$loadConfig)){
			$config=require __ROOT__.'/config.php';
			self::$loadConfig=$config;
    	}
    	else{
    		$config=self::$loadConfig;
    	}   	
		
    	if ($value===null){
    		return $config[$name];
    	}
    	else{
    		$config[$name]=$value;
    		return true;
    	}
    }
	   
    /**
     * 转换参数
     * @param string $name
     * @param string $id
     * @return Ambigous <boolean, unknown>
     */
    static public function convertId($name,$id=null){
    	if (empty(self::$loadConvert)){
    		$config=require __ROOT__.'/convert.php';
    		self::$loadConvert=$config;
    	}
    	else{
    		$config=self::$loadConvert;
    	}
    	
    	if ($id===null){
    		return $config[$name];
    	}
    	else{
    		return $config[$name][$id];
    	}
    }
    
}

