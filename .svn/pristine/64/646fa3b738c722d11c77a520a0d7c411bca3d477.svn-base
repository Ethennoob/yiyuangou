<?php
namespace System;

abstract class BaseClass {
	private $cache=null;   //缓存实例
	
    private $pageInfo;
    private $tableMap = array();
    
    public $mapTable=[];
    
    protected $user = array();
    protected $errcode = '0';
    static $errMap = [];
    protected $error;

    public function table($class=null, $db = "DB_MASTER") {
    	if (!array_key_exists($class.'-'.$db, $this->tableMap)) {
    		if ($class!==null){
    			$this->tableMap[$class.'-'.$db] = \System\Router::getClass($class, $db);
    		}
    		else{
    			$this->tableMap[$class.'-'.$db] = new \System\database\InitTable($db);
    		}
    	}	
    	return $this->tableMap[$class.'-'.$db];
 	}

    /**
     * 实例化help层
     * @param str $class
     * @return 
     */
    public function H($class) {
    	$class='\\AppMain\\helper\\'.$class. 'Helper';
        return \System\Router::getClass($class);
    }
    
    /**
     * 使用缓存
     * @param str $type  缓存类型
     */
    public function S($type='memcached'){
        switch ($type){
            case 'memcached' :   //memcache
                if (empty($this->cache['memcachedCache'])){
                    $mem=new MyMemCached();
                    $this->cache['memcachedCache']=$mem;
                    return $mem;
                }
                else{
                    return $this->cache['memcachedCache'];
                }
                break;
            case 'local' :   //本地文件缓存
                if (empty($this->cache['local'])){
                    $mem=new MyLocalCache();
                    $this->cache['local']=$mem;
                    return $mem;
                }
                else{
                    return $this->cache['local'];
                }
                break;
            default:
                throw new \Exception('错误的缓存！');
                break;
        }
    }
    
    
    /**
     * 验证数据
     * @param array $rule 规则
     * @param array $data 要验证的数据
     * @param bool $isR  错误是否直接返回数据给前端
     * @example 
     * $data=[
			'aa'=>'262329131qq.com',
			'bb'=>123
		];
		
		$rule=[
			'aa'=>['email'],
			'bb'=>['num']	
		];
     */
    public function V($rule,$data=null,$isR=true){
    	if (empty($data)){
    		$data=$_POST;
    	}
    	
    	$rt=\System\MyVerify::verify($rule,$data);
    	
    	if ($isR&&!$rt){
    		$this->R(\System\MyVerify::$verError,'40018');
    	}
    	
    	return $rt;
    }
    
    /**
     * 实例化分页类
     * @param str $type  缓存类型
     */
    public function P(){
    	return new PageInfo();
    }
    
    /**
     * 载入系统类
     * @param 文件名(相对于文件夹vendor的php文件)
     * @example $this->Lib('Mail.PHPMailer');
     */
    public function lib($class){
        $class = str_replace(array('.', '#'), array('/', '.'), $class);
        require_once  __ROOT__.'/System/lib/'.$class.'.class.php';
    }
    
    /**
     * 载入第三方类
     * @param 文件名(相对于文件夹vendor的php文件)
     * @example $this->vendor('Excel.PHPExcel');
     */
    public function vendor($class){
    	$class = str_replace(array('.', '#'), array('/', '.'), $class);
    	require_once  __ROOT__.'/System/vendor/'.$class.'.php';
    }
    
    /**
     * 读取\修改配置文件
     * @param str $name  缓存名称
     * @param mixed $value 修改数据
     */
    public function config($name,$value=null){
    	return \System\Entrance::config($name,$value);
    }
    
    /**
     * 转换参数意义
     * @param str $name  配置名称
     * @param mixed $value id
     */
    public function convertId($name,$id=null){
    	return \System\Entrance::convertId($name,$id);
    }
    
    
    /**
     * 查询筛选
     * @param array 原条件数组
     * @param array $mappingTable  对应表
     * @param array $data 数据
     * @param boo; $isMatch 是否需要对应
     * @return unknown
     */
    public function queryFilter($filter,$mappingTable=null,$data=null,$isMatch=false){
    	$data===null?$data=$_POST:$data;
    	$mappingTable===null?$map=false:$map=$mappingTable;
    	$query=[];
    	if ($isMatch===false){
    		foreach ($map as $key=>$a){
    			if (isset($data[$a])){
    				$query[$a]=$data[$a];
    			}
    		}
    	}
    	else{
    		foreach ($map as $key=>$a){
    			if (isset($data[$key])){
    				$query[$a]=$data[$key];
    			}
    		}
    	}
    	
    	$query=array_merge($filter,$query);
    	
    	return $query;
    }
    
    /**
     * 排序
     * @param string $filter 默认条件
     * @param string $mappingTable
     * @param string $data
     * @param string $isMatch
     * @example $this->orderFilter('id Desc',['idDesc'=>'id DESC','idASC'=>'id ASC'],$_GET);
     */
    public function orderFilter($filter,$mappingTable=null,$data=null){
    	$data===null?$data=$_POST:$data;
    	$mappingTable===null?$map=false:$map=$mappingTable;
    	$query=$filter;
    	
    	if (!empty($data['order'])){
    		$order=$data['order'];
    		foreach ($map as $key=>$a){
    			if ($key==$order){
    				return $a;
    			}
    		}
    	}
    	
    	return $query;
    }
    

    /**
     * 错误日志
     * @param unknown $errFileName
     * @param unknown $msg
     */
    public function logErr($errFileName, $msg) {
        file_put_contents(__ROOT__ . "/Cache/" . $errFileName, date("H/m/d H:i:s", time()) . '----' . $msg .PHP_EOL, FILE_APPEND);
    }

    
    /**
     * 返回给前端页面json 
     * @param string|array $data
     * @param string $errcode
     * @param bool $helper   是否helper调用，如果true,则不返回json
     */
    protected function R($data = "", $errcode = '',$helper=false) {
        
        
        if (!empty($errcode))
            $this->errcode = $errcode;
        
        if ($this->errcode==0){
            $errmsg='请求成功！';
            $isImportant=0;
        }
        else{
            if (empty(self::$errMap)){
                $errMap=require '../errorCode.php';
                self::$errMap=$errMap;
            }
            
            if (is_array(self::$errMap[$this->errcode])) {
                $errmsg = self::$errMap[$this->errcode][0];
                $isImportant = self::$errMap[$this->errcode][1];
            } else {
                $errmsg = self::$errMap[$this->errcode];
                $isImportant = 0;
            }
        }
        
        if (!$helper){
        	ajaxReturn(array("errcode" => $this->errcode, "errmsg" => $errmsg, 'data' => $data, 'isImportant' => $isImportant), "JSON", JSON_UNESCAPED_UNICODE);
        }
        else{
        	return array("errcode" => $this->errcode, "errmsg" => $errmsg);
        }
    
    }
    
    /**
     * 用于获取单页列表数据
     * @param pageInfo $pageInfo 页面类，控制页面输出
     * @param instance $dataClass 获取数据的Data类
     * @param string $listFunc 用于获取列表的Data类的函数名称
     * @param string $lengFunc 用于获取总行数的Data类的函数名称
     * @param array $params 函数参数数组（$conn除外且默认为第一个参数），默认为空
     * @param array $isMulti 判断$dataClass是否Helper类
     * @return false|array 返回数据列表，失败返回false
     */
    protected function getOnePageData(&$pageInfo, &$dataClass, $listFunc, $lengFunc = null, array $params = null, $isMulti = false) {
    	$pageInfo->psize = isset($_REQUEST["psize"]) ? $_REQUEST["psize"] : 15;
    	$rt = false;
    	if (null == $lengFunc)
    		$lengFunc = $listFunc . "Length";
    	$pn = isset($_REQUEST["pn"]) ? $_REQUEST["pn"] : 1;
    	if (!$pn)
    		$pn = "1";
    
    	$pageInfo->num = intval($pn);
    
    	$pageInfo->dataSize = call_user_func_array([$dataClass, $lengFunc], (array) $params);
    	if ($pageInfo->dataSize > 0) {
    		$begin = ($pageInfo->num - 1) * $pageInfo->psize; //从第N笔开始检索
    		if ($isMulti) {
    			\System\database\MultiBaseTable::setMultiSqlStmt(["begin" => $begin, "size" => $pageInfo->psize]);
    		} else {
    			$dataClass->setSqlStmt(["begin" => $begin, "size" => $pageInfo->psize]);
    		}
    		$rt = call_user_func_array([ $dataClass, $listFunc], (array) $params);
    		if (!$rt) {
    			$this->error = "无法获取数据";
    		}
    	} else {
    		$this->error = "没有数据";
    	}
    
    	return $rt;
    }

}
