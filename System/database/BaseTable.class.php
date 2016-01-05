<?php

/**
 * Description of BaseTable
 *
 * @author lzy
 */

namespace System\database;

class BaseTable {

    //static private $_instance;
    protected $error;
    private $tableName;
    private $fields; //包含所有字段的数组，key是字段名称，value是定长数组（第一个元素是数据类型（key为'type'，value为i/s/d/b），第二个元素是字段值（key为'value'，value为任何值））。
    private $AIField; //整数类型的自动增加的字段名称
    private $sqlStmt;
    private $lastSql;
    private $alias=[]; //表别名对应
    
    public $tableMap=[];
    
    private $updateAffectedNum=0;
    
    public $conn;
    public $indexField;  //通过某一字段查询字段名
    public $indexFieldValue; //通过某一字段查询字段值
    private $db = "DB_MASTER";  //数据库
    // 数据库表达式
    protected $comparison = array('eq' => '=', 'neq' => '<>', 'gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'notlike' => 'NOT LIKE', 'like' => 'LIKE', 'in' => 'IN', 'notin' => 'NOT IN', 'not in' => 'NOT IN', 'between' => 'BETWEEN', 'not between' => 'NOT BETWEEN', 'notbetween' => 'NOT BETWEEN');

    private $isDebug=false;
    
    public function __construct($db='DB_MASTER',$debug=false) {
        $this->db=$db;
        $this->conn = Database::openConnection($db);
        $this->isDebug=\System\Entrance::config('IS_DB_DEBUG');
    }

    public function __deconstruct() {
        $this->conn = Database::closeConnection();
    }
    
    /**
     * 初始化sqlStmt数组
     */
    public function initSqlStmt() {
    	$this->sqlStmt = [
    			"whereStmt" => null, //查询条件。支持需要绑定参数的字符串，如id=? and name=?，绑定的参数由$bindParams指定。
    			"bindTypes" => null, //语句中需要绑定的参数的数据类型，取值为i/s/d/b这些表示类型的字符的组合。
    			"bindParams" => null, //语句中要绑定的参数。
    			"orderBy" => null, //排序字段
    			"isDesc" => null, //判断是否降序
    			"begin" => null, //查询的开始行号
    			"size" => null, //查询行数
    			"joinType" => null, //连接方式数组
    			"joinOn" => null,
    			"groupBy" => null, //组合字段
    			"havingStmt" => null, //筛选条件。支持需要绑定参数的字符串，如id=? and name=?，绑定的参数由$bindParams指定。
    			"sqlFunction"=>null,   //sql函数 （传入数组）   ex：['count(*) as num']
    	];
    }

    /**
     * 初始化数据表
     * 初始化表明：tableName = 'test_table'
     * 初始化字段：fields = ['id'=>['type'=>'i', 'value'=>null], ...]
     * 初始化自增字段名：AIField = 'id'
     */
    //abstract protected function initTable();

    public function __set($name, $value) {
        if (array_key_exists($name, $this->fields)) {
            $this->fields[$name]['value'] = $value;
        }
    }

    public function __get($name) {
        return $this->fields[$name]['value'];
    }

    public function getError() {
        return $this->error;
    }
    
    public function setTable($tableName,$isJoinTable=false){
        dump($this->db);
    	//如果不是连表操作
    	if (!$isJoinTable){
    		$this->initSqlStmt();
    		$this->tableName=$tableName;
    	}

    	//建立表别名关联
    	$p = explode(" as ", $tableName);
    	if (count($p) > 1) {
    		$tableName=trim($p[0]);
    		$alias=trim($p[1]);    		
    		$this->alias[$alias]=$tableName;
    	} 
    	
    	if (!array_key_exists($this->db.'-'.$tableName,$this->tableMap)){
    		$class='\\AppMain\\data\\'.$this->db.'\\'.$tableName;
    		if (!class_exists($class)){
    			exit('不存在数据表');
    		}
    		
    		$this->tableMap[$this->db.'-'.$tableName]=[
    				'fields' => $class::initTable(),
    				'AIField' => $class::AIField()
    		];
    	}
    	
    	if (!$isJoinTable){
    		$this->fields=$this->tableMap[$this->db.'-'.$tableName]['fields'];
    		$this->AIField=$this->tableMap[$this->db.'-'.$tableName]['AIField'];
    	}
    	
    	
    	//dump($this->tableMap);
    	//exit('sdfsdf');
    	//dump($this->alias);
    	return true;
    }

    /**
     * 设置sql函数字段
     * @param array $array
     */
    public function sqlFunction($array) {
    	$this->sqlStmt['sqlFunction'] = $array;
    	return $this;
    }
    
    /**
     * 设置groupby条件
     * @param type $where
     */
    public function group($str) {
    	$this->sqlStmt['groupBy'] = $str;
    	return $this;
    }
    
    /**
     * 设置where条件
     * @param array $where
     */
    public function where($where) {
        $this->sqlStmt['whereStmt'] = $this->parseWhere($where);
		return $this;
    }
    
    /**
     * where分析
     * @access protected
     * @param mixed $where
     * @return string
     */
    private function parseWhere($where) {
        $whereStr = '';
        if (is_string($where)) {
            // 直接使用字符串条件
            $whereStr = $where;
        } else { // 使用数组表达式
            $operate = isset($where['_logic']) ? strtoupper($where['_logic']) : '';
            if (in_array($operate, array('AND', 'OR', 'XOR'))) {
                // 定义逻辑运算规则 例如 OR XOR AND NOT
                $operate = ' ' . $operate . ' ';
                unset($where['_logic']);
            } else {
                // 默认进行 AND 运算
                $operate = ' AND ';
            }
            foreach ($where as $key => $val) {
                if (is_numeric($key)) {
                    $key = '_complex';
                }
                if (0 === strpos($key, '_')) {
                    // 解析特殊条件表达式
                    $whereStr .= $this->parseThinkWhere($key, $val);
                } else {
                    // 查询字段的安全过滤
                    if (!preg_match('/^[A-Z_\|\&\-.a-z0-9\(\)\,]+$/', trim($key))) {
                        //E(L('_EXPRESS_ERROR_').':'.$key);
                        echo "错误的输入！";
                    }
                    // 多条件支持
                    $multi = is_array($val) && isset($val['_multi']);
                    $key = trim($key);
                    if (strpos($key, '|')) { // 支持 name|title|nickname 方式定义查询字段
                        $array = explode('|', $key);
                        $str = array();
                        foreach ($array as $m => $k) {
                            $v = $multi ? $val[$m] : $val;
                            $str[] = $this->parseWhereItem($this->parseKey($k), $v);
                        }
                        $whereStr .= '( ' . implode(' OR ', $str) . ' )';
                    } elseif (strpos($key, '&')) {
                        $array = explode('&', $key);
                        $str = array();
                        foreach ($array as $m => $k) {
                            $v = $multi ? $val[$m] : $val;
                            $str[] = '(' . $this->parseWhereItem($this->parseKey($k), $v) . ')';
                        }
                        $whereStr .= '( ' . implode(' AND ', $str) . ' )';
                    } else {
                        $whereStr .= $this->parseWhereItem($this->parseKey($key), $val);
                    }
                }
                $whereStr .= $operate;
            }
            $whereStr = substr($whereStr, 0, -strlen($operate));
        }
        return empty($whereStr) ? '' : ' ' . $whereStr;
    }

    // where子单元分析
    private function parseWhereItem($key, $val) {
        $whereStr = '';
        if (is_array($val)) {
            if (is_string($val[0])) {
                if (preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT)$/i', $val[0])) { // 比较运算
                    $whereStr .= $key . ' ' . $this->comparison[strtolower($val[0])] . ' ' . $this->parseValue($val[1]);
                } elseif (preg_match('/^(NOTLIKE|LIKE)$/i', $val[0])) {// 模糊查找
                    if (is_array($val[1])) {
                        $likeLogic = isset($val[2]) ? strtoupper($val[2]) : 'OR';
                        if (in_array($likeLogic, array('AND', 'OR', 'XOR'))) {
                            $likeStr = $this->comparison[strtolower($val[0])];
                            $like = array();
                            foreach ($val[1] as $item) {
                                $like[] = $key . ' ' . $likeStr . ' ' . $this->parseValue($item);
                            }
                            $whereStr .= '(' . implode(' ' . $likeLogic . ' ', $like) . ')';
                        }
                    } else {
                        $whereStr .= $key . ' ' . $this->comparison[strtolower($val[0])] . ' ' . $this->parseValue($val[1]);
                    }
                } elseif ('exp' == strtolower($val[0])) { // 使用表达式
                    $whereStr .= $key . ' ' . $val[1];
                } elseif (preg_match('/IN/i', $val[0])) { // IN 运算
                    if (isset($val[2]) && 'exp' == $val[2]) {
                        $whereStr .= $key . ' ' . strtoupper($val[0]) . ' ' . $val[1];
                    } else {
                        if (is_string($val[1])) {
                            $val[1] = explode(',', $val[1]);
                        }
                        $zone = implode(',', $this->parseValue($val[1]));
                        $whereStr .= $key . ' ' . strtoupper($val[0]) . ' (' . $zone . ')';
                    }
                } elseif (preg_match('/BETWEEN/i', $val[0])) { // BETWEEN运算
                    $data = is_string($val[1]) ? explode(',', $val[1]) : $val[1];
                    $whereStr .= $key . ' ' . strtoupper($val[0]) . ' ' . $this->parseValue($data[0]) . ' AND ' . $this->parseValue($data[1]);
                } else {
                    //E(L('_EXPRESS_ERROR_').':'.$val[0]);
                    echo "错误的输入！";
                }
            } else {
                $count = count($val);
                $rule = isset($val[$count - 1]) ? (is_array($val[$count - 1]) ? strtoupper($val[$count - 1][0]) : strtoupper($val[$count - 1]) ) : '';
                if (in_array($rule, array('AND', 'OR', 'XOR'))) {
                    $count = $count - 1;
                } else {
                    $rule = 'AND';
                }
                for ($i = 0; $i < $count; $i++) {
                    $data = is_array($val[$i]) ? $val[$i][1] : $val[$i];
                    if ('exp' == strtolower($val[$i][0])) {
                        $whereStr .= $key . ' ' . $data . ' ' . $rule . ' ';
                    } else {
                        $whereStr .= $this->parseWhereItem($key, $val[$i]) . ' ' . $rule . ' ';
                    }
                }
                $whereStr = '( ' . substr($whereStr, 0, -4) . ' )';
            }
        } else {
            //对字符串类型字段采用模糊匹配
//             /* if(C('DB_LIKE_FIELDS') && preg_match('/('.C('DB_LIKE_FIELDS').')/i',$key)) {
//                 $val  =  '%'.$val.'%';
//                 $whereStr .= $key.' LIKE '.$this->parseValue($val);
//             }else { */
			$p=explode('.',$key);
			if (count($p) > 1 ){
				$whereStr .= $p[0].'.`'.$p[1].'` = ' . $this->parseValue($val);
			}
			else{
				$whereStr .= '`'.$key.'` = ' . $this->parseValue($val);
			}
            
            //}
        }
        return $whereStr;
    }

    /**
     * 字段名分析
     * @access protected
     * @param string $key
     * @return string
     */
    private function parseKey(&$key) {
        return $key;
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    private function parseValue($value) {
        if (is_string($value)) {
            $value = '\'' . $this->escapeString($value) . '\'';
        } elseif (isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp') {
            $value = $this->escapeString($value[1]);
        } elseif (is_array($value)) {
            $value = array_map(array($this, 'parseValue'), $value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escapeString($str) {
        return addslashes($str);
    }

    /**
     * 设置插入/更新数据
     * @param array $data
     */
    public function save($data) {
        $this->filterAndSetFields($data);
        return $this->add();
    }

    /**
     * 更新
     * @param array $data
     * @return number
     */
    public function update($data) {
        $fields = $this->filterAndSetFields($data);
        return $this->set($fields);
    }

    /**
     * 设置order
     * @param $order $arr
     */
    public function order($order) {
        $this->sqlStmt['orderBy'] = $order;
        return $this;
    }
    
    /**
     * 设置limit
     * @param $begin int
     * @param $size
     */
    public function limit($begin,$size=null) {
        if ($size===null){
            $size=$begin;
            $begin=0;
        }
        $this->setSqlStmt(array('begin' => $begin, 'size' => $size));
        return $this;
    }
    
    /**
     * 设置join
     * @param $begin int
     * @param $size
     */
    public function join($tableName,$joinOn,$joinType='LEFT JOIN') {
    	$this->sqlStmt['joinOn'][]=$joinOn;
    	$this->sqlStmt['joinType'][]=$joinType;
    	$this->setTable($tableName,true);

    	return $this;
    }
    
    /**
     * 给sqlStmt数组赋值
     * @param string $name sqlStmt数组的KEY值，需要存在于设定的sqlStmt数组中
     * @param mixed $value 需要赋的值
     * @desc array(
      "whereStmt" => null,
      "bindTypes" => null,
      "bindParams" => null,
      "orderBy" => null,
      "isDesc" => null,
      "begin" => null,
      "size" => null,
      "groupBy" => null,
      "havingStmt" => null,
      )
     * @return boolean 成功返回true，false表明sqlStmt不存在此KEY
     */
    public function setSqlStmt($sqlStmt) {
        foreach ($sqlStmt as $k => $v) {
            if (array_key_exists($k, $this->sqlStmt)) {
                $this->sqlStmt[$k] = $v;
            }
        }
        return $this;
    }

    /**
     * 记录数据库错误
     * @param type $err
     */
    protected function logError($err) {
        $this->error = $err;
        error_log($this->error);
        $this->degbugLog();
    }

    /**
     * 把所有类成员变量设为空
     */
    public function clear() {
        foreach ($this->fields as $k => $v) {
            $this->fields[$k]['value'] = null;
        }
        $this->initSqlStmt();
    }

    public function exists_field($name) {
        return array_key_exists($name, $this->fields);
    }

    private function arr2Reference(array &$arr) {
        $rt = [];
        foreach ($arr as $k => $v) {
            $rt[$k] = &$arr[$k];
        }
        return $rt;
    }

    /**
     * 添加一笔数据，对应INSERT 。对已被执行过字段赋值的本对象，执行插入数据表的操作。
     * @return boolean 成功为true，失败为false
     */
    public function add() {
        $rt = false;

        $fields = null;
        $values = null;
        $types = null;
        $params = [];

        foreach ($this->fields as $k => $v) {
            if ($k !== $this->AIField && $v['value'] != null) {
                $fields .= "`$k`,";
                $values .= "?,";
                $types .= $v['type'];
                $params[] = &$this->fields[$k]['value'];
            }
        }
        $fields = trim($fields, ",");
        $values = trim($values, ",");

        $sql = "insert into `$this->tableName` ($fields) values ($values)";

        $this->lastSql = $sql;

        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            call_user_func_array([$stmt, "bind_param"], array_merge([$types], $params));

            if (mysqli_stmt_execute($stmt)) {
                if (strlen($this->AIField) > 0) {
                    $this->fields[$this->AIField]["value"] = mysqli_stmt_insert_id($stmt);
                }
                $rt = $this->fields[$this->AIField]["value"];
            } else {
                $this->logError(mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
        } else {
            $this->logError(mysqli_error($this->conn));
        }
        $this->clear();
        $this->degbugLog();
        return $rt;
    }

    /**
     * 按条件更新数据，对应UPDATE
     * @param array $updateFields 待更新的所有字段，为NULL时则表示更新所有字段为当前对象的值（自增字段除外）。
     * @return int 数据库语句执行失败为0，成功更新为1，语句执行成功但没有更新为2
     */
    public function set(array $updateFields = array(), array $exclude = array()) {
        $rt = 0;
        if ($this->sqlStmt["whereStmt"] === null)
            return $rt;
        $fieldsValue = [];
        if ($updateFields === null || count($updateFields) === 0) {
            foreach ($this->fields as $k => $v) {
                $fieldsValue[$k] = $v['value'];
            }
        } else {
            foreach ($this->fields as $k => $v) {
                if (in_array($k, $updateFields) && !in_array($k, $exclude))
                    $fieldsValue[$k] = $v['value'];
            }
        }

        $fields = null;
        $types = null;
        $params = [];

        foreach ($fieldsValue as $k => $v) {
            if ($k !== $this->AIField) {
                $fields .= "`$k`=?,";
                $types .= $this->fields[$k]['type'];
                $params[] = &$fieldsValue[$k];
            }
        }
        
        if ($this->sqlStmt['sqlFunction']!==null){
            $fields.=$this->sqlStmt['sqlFunction'];
        }
        
        $fields = trim($fields, ",");

        if ($this->sqlStmt["bindTypes"]) {
            $types .= $this->sqlStmt["bindTypes"];
        }

        $sql = "update `$this->tableName` set $fields where " . $this->sqlStmt["whereStmt"];

        $this->lastSql = $sql;

        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            $ps = $this->sqlStmt["bindParams"] === null ? array_merge([$types], $params) : array_merge([$types], $params, $this->arr2Reference($this->sqlStmt["bindParams"]));

            call_user_func_array([$stmt, "bind_param"], $ps);

            if (mysqli_stmt_execute($stmt)) {
            	$affectedRows=mysqli_stmt_affected_rows($stmt);
            	$this->updateAffectedNum=$affectedRows;
                if ($affectedRows > 0)
                    $rt = 1;
                else
                    $rt = 2;
            } else {
                $this->logError(mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
        } else {
            $this->logError(mysqli_error($this->conn));
        }

        $this->initSqlStmt();
        $this->degbugLog();
        
        return $rt;
    }

    /**
     * 按条件修改某个字段的数值，支持加减乘除四种运行。各参数数组顺序要对应
     * @param array $fieldName 需要修改数值的字段名数组。
     * @param array $calcType 运算符数组，字符型，+，-，*，/ 四个中的任一个。
     * @param array $vOffset 跟在运算符后参与运算的那个数值，可以是任何数值。
     * @return int 数据库语句执行失败为0，成功更新为1，语句执行成功但没有更新为2
     */
    public function setFieldCalcValue(array $fieldName, array $calcType, array $vOffset) {
        $rt = 0;
        if ($this->sqlStmt["whereStmt"] === null)
            return $rt;
        $setFields='';
        $fc = count($fieldName);
        for ($i = 0; $i < $fc; $i++) {
            $setFields .= "`" . $fieldName[$i] . "`=(`" . $fieldName[$i] . "` " . $calcType[$i] . " " . $vOffset[$i] . "),";
        }
        $setFields = trim($setFields, ',');
        $sql = "update `" . $this->tableName . "` set $setFields where " . $this->sqlStmt["whereStmt"];
        $this->lastSql = $sql;
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            if ($this->sqlStmt["bindTypes"] && $this->sqlStmt["bindParams"]) {
                call_user_func_array([$stmt, "bind_param"], array_merge([$this->sqlStmt["bindTypes"]], $this->arr2Reference($this->sqlStmt["bindParams"])));
            }

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0)
                    $rt = 1;
                else
                    $rt = 2;
            } else {
                $this->logError(mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
        } else {
            $this->logError(mysqli_error($this->conn));
        }
        $this->initSqlStmt();
        $this->degbugLog();
        return $rt;
    }

    /**
     * 通过ID更新一条计算数据
     * @param array $fieldName 需要修改数值的字段名数组。
     * @param array $calcType 运算符数组，字符型，+，-，*，/ 四个中的任一个。
     * @param array $vOffset 跟在运算符后参与运算的那个数值，可以是任何数值。
     * @return int 数据库语句执行失败为0，成功更新为1，语句执行成功但没有更新为2
     */
    public function setFieldCalcValueById($fieldName, $calcType, $vOffset) {
        $pk = $this->AIField;
        $this->setSqlStmt(["whereStmt" => "$pk = " . $this->$pk]);
        return $this->setFieldCalcValue($fieldName, $calcType, $vOffset);
    }

    /**
     * 按条件删除数据，对应DELETE。
     * @return int 数据库语句执行失败为0，成功删除为1，语句执行成功但没有删除为2
     */
    public function delete() {
        if ($this->sqlStmt["whereStmt"] === null)
            return false;

        $rt = false;

        $sql = "delete from `$this->tableName` where " . $this->sqlStmt["whereStmt"];
        $this->lastSql = $sql;
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            if ($this->sqlStmt["bindTypes"] && $this->sqlStmt["bindParams"]) {
                call_user_func_array([$stmt, "bind_param"], array_merge([$this->sqlStmt["bindTypes"]], $this->arr2Reference($this->sqlStmt["bindParams"])));
            }

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0)
                    $rt = 1;
                else
                    $rt = 2;
            } else {
                $this->logError(mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
        } else {
            $this->logError(mysqli_error($this->conn));
        }
        $this->initSqlStmt();
        $this->degbugLog();
        return $rt;
    }

    /**
     * 
      按条件查询数据，对应SELECT。
     * @param array $fieldsName 需要查询的所有字段，为NULL时则表示查询所有字段。
     * @return array|this 此方法返回查询结果数组。如果结果只有一行，且不指定查询哪些字段，则将结果赋值给当前对象。
     */
    public function get($fieldsName = null, $getOne = false) {
    	//dump($this->tableMap);
    	//dump($this->sqlStmt);//exit;
        if ($getOne) {
            $this->setSqlStmt(array('begin' => 0, 'size' => 1));
        }
        $rt = null;

        $fields = "";
        $ln = count($this->fields);

        if ($fieldsName===false){
        	$fields='';
        	$ln=0;
        }
        elseif ($fieldsName && count($fieldsName) > 0) {
        	$fieldsZ='';
            foreach ($fieldsName as $key=> $fieldname) {
            	
            	$pz = stripos($fieldname, ".");
				$p = stripos($fieldname, " as ");
				
				
                if ($p > 0 && $pz >0) {
                	$fieldsZ= substr($fieldname, 0, $pz) . ".`" . substr($fieldname, $pz+1,$p-$pz-1) . "`". substr($fieldname, $p).",";
					$newFieldName[]=$fieldname;
                } 
				elseif ( $p > 0 ) {
					$fieldsZ= "`" . substr($fieldname, 0, $p) . "`" . substr($fieldname, $p) . ",";
					$newFieldName[]=$fieldname;
				}
				elseif ( $pz >0){
					$fieldsY=substr($fieldname, $pz+1);
					if ($fieldsY === '*'){
						$fieldsNames = array_keys($this->fields);
						foreach ($fieldsNames as $v){
							$fieldsZ .= substr($fieldname, 0, $pz) . ".`$v`,";
							$newFieldName[]=$v;
						}
						
					}
					else{
						$fieldsZ= substr($fieldname, 0, $pz) . ".`" . $fieldsY . "`,";
						$newFieldName[]=$fieldname;
					}
					
				}
				else {
                	$fieldsZ= "`$fieldname`,";
                	$newFieldName[]=$fieldname;
 				}
 				
				$fields .=$fieldsZ;
 			}
 			$fieldsName=$newFieldName;
            $ln = count(explode(',', trim($fields,',')));
        } 
        else {
            $fieldsName = array_keys($this->fields);
            foreach ($fieldsName as $fieldname){
            	$fields .= "`$fieldname`,";
            }
        }
        
        if (!empty($this->sqlStmt["sqlFunction"])){
        	foreach($this->sqlStmt["sqlFunction"] as $a){
        		$fieldsName[]=$a;
        		$fields.=$a.',';
        		$ln++;
        	}
        }

        $fields = trim($fields, ",");
        $results = [];
        for ($i = 0; $i < $ln; $i++) {
            $results[] = 0;
        }
        
        if ($this->sqlStmt['joinType'] === null ){
        	$table="`$this->tableName`";
        }
        else{
        	foreach ($this->alias as $key => $v){
        		$tableArr[]= $v.' as '.$key;
			}
			
			$tableArrCount=count($tableArr);
			$i=0;
			foreach ($tableArr as $key => $v){
				if (($tableArrCount-1) == $key ){
					break;
				}
				
				if ($i==0){
					$table=$v . ' ' .$this->sqlStmt['joinType'][$key] . ' ' . $tableArr[$key+1] . ' on ' .$this->sqlStmt['joinOn'][$key];
				}
				else{
					$table.= ' '.$this->sqlStmt['joinType'][$key]. ' ' . $tableArr[$key+1] . ' on ' .$this->sqlStmt['joinOn'][$key];
				}
				$i++;
			}
		}
        
        $sql = "select $fields from $table";
        //dump($sql);//exit;

        if ($this->sqlStmt["whereStmt"])
            $sql .= " where " . $this->sqlStmt["whereStmt"]; //查询条件
        if ($this->sqlStmt["groupBy"] !== null) {
            $sql .= " group by " . $this->sqlStmt["groupBy"]; //组合
            if ($this->sqlStmt["havingStmt"] !== null)
                $sql .= " having " . $this->sqlStmt["havingStmt"];
        }
        if ($this->sqlStmt["orderBy"] !== null)
            $sql .= " order by " . $this->sqlStmt["orderBy"]; //排序
        if ($this->sqlStmt["begin"] !== null && $this->sqlStmt["size"] !== null)
            $sql .= " limit " . $this->sqlStmt["begin"] . ", " . $this->sqlStmt["size"]; //范围
        $this->lastSql = $sql;
        //dump($sql);
        $stmt = mysqli_prepare($this->conn, $sql);
        //dump($stmt);
        //dump($this->sqlStmt);
        //var_dump(mysqli_stmt_error($stmt));
        if ($stmt) {
            if ($this->sqlStmt["bindTypes"] && $this->sqlStmt["bindParams"]) {
                call_user_func_array([$stmt, "bind_param"], array_merge([$this->sqlStmt["bindTypes"]], $this->arr2Reference($this->sqlStmt["bindParams"])));
            }
            if (mysqli_stmt_execute($stmt)) {
                $bdRs = $this->arr2Reference($results);
                //dump($bdRs);
                //dump($this);
                call_user_func_array([$stmt, "bind_result"], $bdRs);

                $allRows = [];
                while ($stmt->fetch()) {
                    $row = [];

                    for ($i = 0; $i < $ln; $i++) {
                        $row_key = stristr($fieldsName[$i], " as ");
                        if (!$row_key) {
                            $row_key = $fieldsName[$i];
                        } else {
                            $row_key = substr($row_key, 4);
                        }
                        
                        $row_key2=explode(".",$row_key);
                        if (count($row_key2)>1){
                        	$row_key = $row_key2[1];
                        }
                        $row[$row_key] = $bdRs[$i];
                    }

                    $allRows[] = $row;
                }

                if (count($allRows) > 0) {
                    if ($getOne) {
                        $rt = $allRows[0];
                    } else {
                        $rt = $allRows;
                    }
                }
                //}

                $stmt->free_result();
            } else {
                $this->logError(mysqli_stmt_error($stmt));
            }
            //var_dump(mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
        } else {
            $this->logError(mysqli_error($this->conn));
        }
        $this->initSqlStmt();
        $this->degbugLog();
        
        return $rt;
    }
    
    /**
     * sql查询
     * @param string $sql
     * @return multitype:unknown 
     */
    public function query($sql){
    	$result=mysqli_query($this->conn,$sql);
    	$array=null;
    	if ($result) {
    		while (true) {
    			$row = mysqli_fetch_assoc($result);
    			if ($row) {
    				$r = array();
    				foreach ($row as $k => $v) {
    					$r[$k] = $v;
    				}
    				$array[] = $r;
    			} else {
    				break;
    			}
    		}
    	} else {
    		self::logError(mysqli_error($this->conn));
    	}
    	return $array;
    }

    /**
     * 查询符合条件的记录数
     * @return integer 返回记录数
     */
    public function getListLength() {
        $rt = 0;

        if ($this->sqlStmt['joinType'] === null ){
        	$table="`$this->tableName`";
        }
        else{
        	foreach ($this->alias as $key => $v){
        		$tableArr[]= $v.' as '.$key;
        	}
        		
        	$tableArrCount=count($tableArr);
        	$i=0;
        	foreach ($tableArr as $key => $v){
        		if (($tableArrCount-1) == $key ){
        			break;
        		}
        
        		if ($i==0){
        			$table=$v . ' ' .$this->sqlStmt['joinType'][$key] . ' ' . $tableArr[$key+1] . ' on ' .$this->sqlStmt['joinOn'][$key];
        		}
        		else{
        			$table.= ' '.$this->sqlStmt['joinType'][$key]. ' ' . $tableArr[$key+1] . ' on ' .$this->sqlStmt['joinOn'][$key];
        		}
        		$i++;
        	}
        }
        //dump($table);exit;
        if ($this->sqlStmt["groupBy"] !== null) {
            $sql = "select count(DISTINCT(".$this->sqlStmt["groupBy"].")) from $table";
        }
        else{
            $sql = "select count(*) from $table";
        }
        
        if ($this->sqlStmt["whereStmt"])
            $sql .= " where " . $this->sqlStmt["whereStmt"]; //查询条件
        
        $this->lastSql = $sql;
        $stmt = mysqli_prepare($this->conn, $sql);
        if ($stmt) {
            if ($this->sqlStmt["bindTypes"] && $this->sqlStmt["bindParams"]) {
                call_user_func_array([$stmt, "bind_param"], array_merge([$this->sqlStmt["bindTypes"]], $this->arr2Reference($this->sqlStmt["bindParams"])));
            }

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_bind_result($stmt, $rt);

                $stmt->fetch();
                $stmt->free_result();
            } else {
                $this->logError(mysqli_stmt_error($stmt));
            }

            $stmt->close();
        } else {
            $this->logError(mysqli_error($this->conn));
        }

        $this->degbugLog();
        return $rt;
    }

    /**
     * 从提交参数中过滤并赋值给同名字段
     * @param array $data 对应$_REQUEST数组
     */
    public function filterAndSetFields(array $data) {
        $fields = array();
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $this->fields) && $k != $this->AIField) {
                $fields[] = $k;
                $this->fields[$k]['value'] = is_string($v) ? trim($v) : $v;
            }
        }
        return $fields;
    }

    /**
     * 开启事务
     */
    public function startTrans() {
        $this->conn->autocommit(false);
        $this->lastSql='begin';
        $this->degbugLog();
    }

    /**
     * 提交事务
     */
    public function commit() {
        $this->conn->commit();
        $this->conn->autocommit(true);
        $this->lastSql='commit';
        $this->degbugLog();
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        $this->conn->rollback();
        $this->conn->autocommit(true);
        $this->lastSql='rollback';
        $this->degbugLog();
    }

    /**
     * 输出最后提交的sql语句
     * @return integer 返回记录数
     */
    public function getLastSql() {
        return $this->lastSql;
    }

    /**
     * 得到查询条件的SQL ,并且绑定相关数据类型,返回如“ and name like ? and psw = ? ”的查询条件字符串
     * @param array $queryArr 查询条件数组，如 array(array("name", "s","name_val","like"), ... ... );
     *                                          即表示数据类型为 string 的属性“name”作为查询条件，相当于“ name like ? ”;
     * @param type $bindTypes 用于绑定的数据类型，是引用变量，查询条件的数据类型会追加在后面；
     * @param array $bindParams  用于保存属性值的数组，是引用变量，查询条件的属性值会追加在后面；
     * @return String 查询条件 如“ name like ? and psw = ? ”的查询条件字符串
     */
    public function getQuerySql(Array $queryArr, &$bindTypes, Array &$bindParams) {

        $sql = '';
        if (count($queryArr) > 0) {

            foreach ($queryArr as $value) {
                $key = $value[0];
                $type = $value[1];
                $val = $value[2];
                $cmpType = $value[3];

                if (isset($val) && strlen($val) > 0) {
                    $sql .= " and  $key $cmpType ? ";
                    $bindTypes .= $type;
                    $bindParams[] = $val;
                }
            }
        }
        return $sql;
    }
    
    /**
     * 获取更新数量
     * @return int
     */
    public function getUpdateNum(){
    	return $this->updateAffectedNum;
    }
    
    /**
     * debug日志
     * @throws \Exception
     */
    private function degbugLog(){
    	if ($this->isDebug){
    	    sqlDebugLog($this->lastSql,$this->error);   
    	    
    	    if (!empty($this->error)){
    	        throw new \Exception('查询语句：'.$this->lastSql.'     错误提示：'.$this->error);
    	    }
    	}
    }

}

?>
