<?php
namespace System; 
class Session{
	static $mem;
	static $maxtime;

	public function __construct($mem){
		self::$mem = $mem;
		self::$maxtime = ini_get('session.gc_maxlifetime');
		session_module_name('user');//session文件保存方式，这个是必须的！除非在Php.ini文件中设置了

		session_set_save_handler(
		array(__CLASS__,'open'),//在运行session_start()时执行
		array(__CLASS__,'close'),//在脚本执行完成或调用session_write_close() 或 session_destroy()时被执行,即在所有session操作完后被执行
		array(__CLASS__,'read'),//在运行session_start()时执行,因为在session_start时,会去read当前session数据
		array(__CLASS__,'write'),//此方法在脚本结束和使用session_write_close()强制提交SESSION数据时执行
		array(__CLASS__,'destroy'),//在运行session_destroy()时执行
		array(__CLASS__,'gc')//执行概率由session.gc_probability 和 session.gc_divisor的值决定,时机是在open,read之后,session_start会相继执行open,read和gc
		);
		session_start();//这也是必须的，打开session，必须在session_set_save_handler后面执行
	}

	public static function open($sid){
		return true;
	}
	
	public static function close(){
		return true;
	}

	public static function read($sid){
		return self::$mem->get($sid);
	}


	public static function write($sid,$data){
		return self::$mem->set($sid,$data,self::$maxtime);
	}

	public static function destroy($sid){
		return self::$mem->delete($sid);
	}

	public static function gc($maxtime){
		return true;
	}
}




?>