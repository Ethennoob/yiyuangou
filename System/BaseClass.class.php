<?php
namespace System;

abstract class BaseClass {
	private $cache=null;   //缓存实例
	
    private $pageInfo;
    //private $tableMap = array();  //表名路由
    static $BD=null;
    
    public $mapTable=[];
    
    protected $user = array();
    protected $errcode = '0';
    static $errMap = [];
    protected $error;

    static $functionName=null;
    static $viewDataTemp=null;
    public $viewData=null;
    
    /**
     * 选择表
     * @param string $tableName
     * @param string $db
     * @return \System\database\BaseTable
     */
    public function table($tableName=null, $db = "DB_MASTER") {
       	if (!isset(self::$BD[$db]) || self::$BD[$db] === null){
    		self::$BD[$db]=new \System\database\BaseTable($db);
    	}
    	
    	if ($tableName !== null){
    		self::$BD[$db]->setTable($tableName);
    	}
    	
    	return self::$BD[$db];
    }

    /**
     * 实例化help层
     * @param string $class
     * @return 
     */
    public function H($class) {
    	$class = str_replace(array('.', '#'), array('\\', '.'), $class);
    	$class='\\AppMain\\helper\\'.$class. 'Helper';
        return \System\Router::getClass($class);
    }
    
    /**
     * 使用缓存
     * @param string $type  缓存类型
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
            case 'redis' :   //memcache
                if (empty($this->cache['redis'])){
                	$mem=new MyRedis();
                	$this->cache['redis']=$mem;
                	return $mem;
                }
                else{
                	return $this->cache['redis'];
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
     * @param string $type  缓存类型
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
     * @param string $name  缓存名称
     * @param mixed $value 修改数据
     */
    public function config($name,$value=null){
    	return \System\Entrance::config($name,$value);
    }
    
    /**
     * 转换参数意义
     * @param string $name  配置名称
     * @param mixed $value id
     */
    public function convertId($name,$id=null){
    	return \System\Entrance::convertId($name,$id);
    }
    
    
    /**
     * 查询筛选
     * @param array $filter 原条件数组
     * @param array $mappingTable  对应表
     * @param array $data 数据
     * @param bool $isMatch 是否需要对应
     * @return array
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
     * @param string $errFileName
     * @param string $msg
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
    protected function R($data = "", $errcode = '',$helper=false,$jumpUrl='') {
        if (!empty($errcode)){
            $this->errcode = $errcode;
        }

        if ($this->errcode==0){
            $errmsg='请求成功！';
            $isImportant=0;
        }
        else{
            if (empty(self::$errMap)){
                $errMap=require __ROOT__.'/errorCode.php';
                self::$errMap=$errMap;
            }
            
            if (is_array(self::$errMap[$this->errcode])) {
                $errmsg = self::$errMap[$this->errcode][0];
                $isImportant = self::$errMap[$this->errcode][1];
                $defaultUrl=empty(self::$errMap[$this->errcode][2])?0:self::$errMap[$this->errcode][2];
                $jumpUrl=empty($jumpUrl)?$defaultUrl:$jumpUrl;
            } else {
                $errmsg = self::$errMap[$this->errcode];
                $isImportant = 0;
                $jumpUrl='';
            }
		}
        
        $returnData=[
        		"errcode" => $this->errcode, 
        		"errmsg" => $errmsg, 
        		'data' => $data, 
        		'isImportant' => $isImportant ,
        		'jumpUrl'=>$jumpUrl
        ];
        
        $isView=Router::$isView;
        //$isViewMuti=Router::$isViewMuti;
        
        if (!$helper &&  $isView === false ){
        	ajaxReturn($returnData, "JSON", JSON_UNESCAPED_UNICODE);
        }
        elseif (!$helper && $isView === true ){
        	//错误跳转
        	if ($returnData['errcode'] !=0 && $returnData['isImportant']==1){
        		redirect(getHost().$jumpUrl);
        	}

        	self::$viewDataTemp[self::$functionName]=$returnData;

        	if($returnData['errcode'] !=0){
        		$this->viewError($returnData);
        	}
        	
        	return;
        }

        return $returnData;
    }
    
    /**
     * 调用Api的Controller生成view
     * @param string|array $class Controller位置
     * @param string|array $function
     * @param array $params 参数
     * @param array $extraData 额外参数 
     * @example $this->ApiView('Api.Test','test123',['234234','23434']);
     * @example $this->ApiView(['Api.Test','Api.Test'],['test123','test1234'],[['234234','23434'],['ffff']]);
     */
    protected function ApiView($class,$function,$params=[],$extraData=[]){
    	if (is_string($class)){
    		$dataClass=Router::Controller($class,true,$function);
    		call_user_func_array([$dataClass, $function], (array) $params);
    	}
    	else{
    		foreach ($class as $key=>$v){
    			$dataClass=Router::Controller($v,true,$function[$key]);
    			$mutiParams=empty($params)?[]:$params[$key];
    			call_user_func_array([$dataClass, $function[$key]], (array) $mutiParams);
    		}
    	}
    	
    	if ($extraData){
    		self::$viewDataTemp['extra']=$this->viewExtraData($extraData);
    	}
    	
    	$this->View();
    }
    
    /**
     * 组装apiView数据
     * @param unknown $data
     * @return Ambigous <void, multitype:multitype:string array  number Ambigous <string, string> Ambigous <string, number> Ambigous <string, multitype:, unknown> >
     */
    protected function viewExtraData($data){
    	return $this->R($data,0,true);
    }

    /**
     * 加载模板
     * @param string|array $data
     */
    protected function View($data=null) {
    	if ($data !== null){
    		self::$viewDataTemp = $data;
    	}

    	$requestPath=__ROOT__.'/AppMain/view/'.Entrance::$module.'/'.Entrance::$class.'/'.Entrance::$function.'.php';
		$this->viewData=json_encode(self::$viewDataTemp);
    	define('STATIC_PATH',$this->config('STATIC_PATH'));
        require $requestPath;
        exit;
    }
    
    protected function viewError($data){
    	$errorUrl=getHost().'/Home/Tips/error';
    	$query=[
    		'errorInfo' => 	json_encode($data),
    		'fromUrl' => getHostUrl()
    	];
    	
    	/* if (!empty($_SERVER['HTTP_REFERER'])){
    		$query['preUrl']=urlencode($_SERVER['HTTP_REFERER']);
    	} */
    	
    	$jumpUrl=getHost().'/Home/Tips/error?'.http_build_query($query);
    	redirect($jumpUrl);
    	exit;
    }
    
    /**
     * 用于获取单页列表数据
     * @param pageInfo $pageInfo 页面类，控制页面输出
     * @param object $dataClass 获取数据的Data类
     * @param string $listFunc 用于获取列表的Data类的函数名称
     * @param string $lengFunc 用于获取总行数的Data类的函数名称
     * @param array $params 函数参数数组（$conn除外且默认为第一个参数），默认为空
     * @param array $isHelper 判断$dataClass是否Helper类
     * @return false|array 返回数据列表，失败返回false
     */
    protected function getOnePageData(&$pageInfo, &$dataClass, $listFunc, $lengFunc = null, array $params = null, $isHelper = false) {
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
    		if ($isHelper) {
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
