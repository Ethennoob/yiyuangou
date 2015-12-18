<?php
namespace System\database;

class MultiBaseTable {

    private static $multiSqlStmt = [
        "whereStmt" => null, //查询条件。支持需要绑定参数的字符串，如id=? and name=?，绑定的参数由$bindParams指定。
        "bindTypes" => null, //语句中需要绑定的参数的数据类型，取值为i/s/d/b这些表示类型的字符的组合。
        "bindParams" => null, //语句中要绑定的参数。
        "orderBy" => null, //排序字段
        "isDesc" => null, //判断是否降序
        "begin" => 0, //查询的开始行号
        "size" => null, //查询行数
        "groupBy" => null, //组合字段
        "havingStmt" => null, //筛选条件。支持需要绑定参数的字符串，如id=? and name=?，绑定的参数由$bindParams指定。
        "joinType" => null, //连接方式数组
        "joinOn" => null,
    	"sqlFunction"=>null,	
    ];
    public static $error;
    public static $lastSql;

    /**
     * 给multiSqlStmt数组赋值
     * @param string $name multiSqlStmt数组的KEY值，需要存在于设定的multiSqlStmt数组中
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
      "joinType" => null,
      "joinOn" => null
      )
     * @return boolean 成功返回true，false表明sqlStmt不存在此KEY
     */
    static public function setMultiSqlStmt($multiSqlStmt) {
        foreach ($multiSqlStmt as $k => $v) {
            if (array_key_exists($k, self::$multiSqlStmt)) {
                self::$multiSqlStmt[$k] = $v;
            }
        }
    }

    /**
     * 初始化sqlStmt数组
     */
    static public function initMultiSqlStmt() {
        self::$multiSqlStmt = [
            "whereStmt" => null, //查询条件。支持需要绑定参数的字符串，如id=? and name=?，绑定的参数由$bindParams指定。
            "bindTypes" => null, //语句中需要绑定的参数的数据类型，取值为i/s/d/b这些表示类型的字符的组合。
            "bindParams" => null, //语句中要绑定的参数。
            "orderBy" => null, //排序字段
            "isDesc" => null, //判断是否降序
            "begin" => 0, //查询的开始行号
            "size" => null, //查询行数
            "groupBy" => null, //组合字段
            "havingStmt" => null, //筛选条件。支持需要绑定参数的字符串，如id=? and name=?，绑定的参数由$bindParams指定。
            "joinType" => null, //连接方式数组
            "joinOn" => null,
        	"sqlFunction"=>null,
        ];
    }

    /**
     * 多表查询
     * @param array $fieldsName 查询表项数组，以表名为KEY，以对应字段为VALUE。
     * @return array|this 此方法返回查询结果数组。如果结果只有一行，且不指定查询哪些字段，则将结果赋值给当前对象。
     */
    static public function getMulti(array $fieldsName) {
		$rt = null;

        $tables = null;
        $fieldsWithTable = null;
        $ln = 0;

        $joinInd = 0;
        if ($fieldsName && count($fieldsName) > 0) {
            foreach ($fieldsName as $k => $v) {
                if ($joinInd == 0) {
                    $tables = $k . " ";
                } else {
                    $tables .= (self::$multiSqlStmt["joinType"] == null ? "," : " " . (isset(self::$multiSqlStmt["joinType"][$joinInd - 1]) ? self::$multiSqlStmt["joinType"][$joinInd - 1] : "")
                                    . " " . $k . " " . (isset(self::$multiSqlStmt["joinOn"][$joinInd - 1]) ? "on " . self::$multiSqlStmt["joinOn"][$joinInd - 1] : " "));
                }
                $tableName = self::getAlias($k);
                $fields = explode(",", $v);
                foreach ($fields as $field) {
                		$fieldsWithTable .= $tableName . "." . trim($field) . ",";
                }
                $ln += count($fields);
                $joinInd++;
            }
        } else {
            self::logError("wrong fieldsName:" . var_dump($fieldsName));
            return $rt;
        }
		if (!empty(self::$multiSqlStmt["sqlFunction"])){
			foreach(self::$multiSqlStmt["sqlFunction"] as $a){
					$fieldsWithTable.=$a;
					$ln++;
			}
			
		}
		
        $fieldsWithTable = rtrim($fieldsWithTable, ',');
        $tables = rtrim($tables, ',');
		
        $results = [];
        for ($i = 0; $i < $ln; $i++) {
            $results[] = 0;
        }

        $sql = "select $fieldsWithTable from $tables";
        if (self::$multiSqlStmt["whereStmt"])
            $sql .= " where " . self::$multiSqlStmt["whereStmt"]; //查询条件
        if (self::$multiSqlStmt["groupBy"] !== null) {
            $sql .= " group by " . self::$multiSqlStmt["groupBy"]; //排序
            if (self::$multiSqlStmt["havingStmt"] !== null)
                $sql .= " having " . self::$multiSqlStmt["havingStmt"];
        }
        if (self::$multiSqlStmt["orderBy"] !== null)
            $sql .= " order by " . self::$multiSqlStmt["orderBy"] . (self::$multiSqlStmt["isDesc"] === true ? " desc" : ""); //排序
        if (self::$multiSqlStmt["begin"] !== null && self::$multiSqlStmt["size"] !== null)
            $sql .= " limit " . self::$multiSqlStmt["begin"] . ", " . self::$multiSqlStmt["size"]; //范围
        self::$lastSql = $sql;
        $conn = Database::openConnection();
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            if (self::$multiSqlStmt["bindTypes"] && self::$multiSqlStmt["bindParams"]) {
                call_user_func_array([$stmt, "bind_param"], array_merge([self::$multiSqlStmt["bindTypes"]], self::arr2Reference(self::$multiSqlStmt["bindParams"])));
            }
            if (mysqli_stmt_execute($stmt)) {
                $bdRs = self::arr2Reference($results);
                call_user_func_array([$stmt, "bind_result"], $bdRs);

                $allRows = [];

                while ($stmt->fetch()) {
                    $row = [];
                    $i = 0;
                    foreach ($fieldsName as $k => $v) {
                        $fields = explode(",", $v);
                        foreach ($fields as $field) {
                            $field = trim(self::getAlias($field));
                            $row[$field] = $bdRs[$i++];
                        }
                    }
                    
                    if (!empty(self::$multiSqlStmt["sqlFunction"])){
                    	foreach(self::$multiSqlStmt["sqlFunction"] as $a){
                    		$field = trim(self::getAlias($a));
                    		$row[$field] = $bdRs[$i++];
                    	}
                    }
                    

                    $allRows[] = $row;
                }
                $rt = $allRows;
                $stmt->free_result();
            } else {
                self::logError(mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
        } else {
            self::logError(mysqli_error($conn));
        }
        self::initMultiSqlStmt();
        return $rt;
    }

    static private function getAlias($src) {
        $alias = stristr($src, " as ");
        if (!$alias) {
            $alias = $src;
        } else {
            $alias = trim(substr($alias, 4));
        }
        return $alias;
    }

    /**
     * 查询符合条件的记录数
     * @param array $tableArray 二维数组，KEY值为表名，VALUE为NULL，与getMulti兼容
     * @return integer 返回记录数
     */
    static public function getMultiListLength(array $tableArray, $countStmt = '1') {
        $rt = 0;

        $tables = null;

        $joinInd = 0;
        foreach ($tableArray as $k => $v) {
            if ($joinInd == 0) {
                $tables = $k . " ";
            } else {
                $tables .= (self::$multiSqlStmt["joinType"] == null ? "," : " " . (isset(self::$multiSqlStmt["joinType"][$joinInd - 1]) ? self::$multiSqlStmt["joinType"][$joinInd - 1] : "")
                                . " " . $k . " " . (isset(self::$multiSqlStmt["joinOn"][$joinInd - 1]) ? "on " . self::$multiSqlStmt["joinOn"][$joinInd - 1] : " "));
            }
            $joinInd++;
        }
        $tables = rtrim($tables, ',');

        $sql = "select count(" . $countStmt . ") from " . $tables;
        if (self::$multiSqlStmt["whereStmt"])
            $sql .= " where " . self::$multiSqlStmt["whereStmt"]; //查询条件

        self::$lastSql = $sql;
        $conn = Database::openConnection();
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            if (self::$multiSqlStmt["bindTypes"] && self::$multiSqlStmt["bindParams"]) {
                call_user_func_array([$stmt, "bind_param"], array_merge([self::$multiSqlStmt["bindTypes"]], self::arr2Reference(self::$multiSqlStmt["bindParams"])));
            }

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_bind_result($stmt, $rt);

                $stmt->fetch();
                $stmt->free_result();
            } else {
                self::logError(mysqli_stmt_error($stmt));
            }

            $stmt->close();
        } else {
            self::logError(mysqli_error($conn));
        }
        self::initMultiSqlStmt();
        return $rt;
    }

    static private function arr2Reference(array &$arr) {
        $rt = [];
        foreach ($arr as $k => $v) {
            $rt[$k] = &$arr[$k];
        }
        return $rt;
    }

    static private function logError($err) {
        self::$error = $err;
        error_log(self::$lastSql);
        error_log(self::$error);
    }

    static public function query($query) {
        $conn = Database::openConnection();
        $array = array();
        $result = mysqli_query($conn, $query);
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
            self::logError(mysqli_error($conn));
        }
        return $array;
    }

    /**
     * 得到查询条件的SQL ,并且绑定相关数据类型,返回如“ and name like ? and psw = ? ”的查询条件字符串
     * @param array $queryArr 查询条件数组，如 array(array("name", "s","name_val","like"), ... ... );
     *                                          即表示数据类型为 string 的属性“name”作为查询条件，相当于“ name like ? ”;
     * @param type $bindTypes 用于绑定的数据类型，是引用变量，查询条件的数据类型会追加在后面；
     * @param array $bindParams  用于保存属性值的数组，是引用变量，查询条件的属性值会追加在后面；
     * @return String 查询条件 如“ name like ? and psw = ? ”的查询条件字符串
     */
    static public function getQuerySql(Array $queryArr, &$bindTypes, Array &$bindParams) {

        $sql = '1=1';
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

}
