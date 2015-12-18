<?php
namespace AppMain\controller\Task;
use \System\BaseClass;
class CreateDBController extends BaseClass {
	
	public function index(){
		function convertTableToClass($table) {
		    $array = explode("_", $table);
		    for ($i = 0; isset($array[$i]); $i++) {
		        $array[$i] = ucfirst($array[$i]);
		    }
		    return implode("", $array);
		}
		
		$DB=empty($_GET['DB'])?'DB_MASTER':$_GET['DB'];
		
		$conn = \System\database\Database::openConnection($DB);
		$result_table = $conn->query("SHOW TABLES");
		$tables = array();
		while (true) {
		    $row = mysqli_fetch_row($result_table);
		    if ($row) {
		        $tables[] = $row[0];
		    } else {
		        break;
		    }
		}
		mysqli_free_result($result_table);
		
		foreach ($tables as $table) {
		    $result = $conn->query("SHOW COLUMNS FROM $table");
		
		    //$className = convertTableToClass($table);
		    $className = $table;
		    //$string = "<?php class $className extends BaseTable{" . PHP_EOL . "protected function initTable(){ \$this->fields=[" . PHP_EOL;
		    $string = "static public function initTable(){ ".PHP_EOL."		return [" . PHP_EOL;
		    $ai = null;
		
		    if ($result) {
		        while ($row = $result->fetch_row()) {
		        	$type='i';  //默认为字符串
		            if (strpos($row[5], "auto_increment") !== false)
		                $ai = $row[0];
		            if (strpos($row[1], "int") !== false)
		                $type = "i";
		            else if (strpos($row[1], "char") !== false || strpos($row[1], "date") !== false ||strpos($row[1], "text") !== false)
		                $type = "s";
		            else if (strpos($row[1], "decimal") !== false || strpos($row[1], "float") !== false || strpos($row[1], "double") !== false)
		                $type = "d";
		
		            $string .= "			'$row[0]'=> ['type' => '$type', 'value' => null]," . PHP_EOL;
		        }
		        mysqli_free_result($result);
		    }else {
		        echo $table;
		    }
		    
		    $string .= "		];" . PHP_EOL . "	}";//"\$this->tableName = '$table';" . PHP_EOL . "\$this->AIField = '$ai';" . PHP_EOL . "}";
		    $string2="static public function AIField(){ ".PHP_EOL."		return '$ai';" . PHP_EOL.'	}';
		    
		    $filename = "../AppMain/data/$DB/$className.class.php";
		    if (file_exists($filename)) {
		        unlink($filename);
		    } 
		    
		    
		    $create = true;
		    $source = "<?php" . PHP_EOL .
		    'namespace AppMain\data\\'.$DB.';' . PHP_EOL .
		    "class $className {" . PHP_EOL .
		    '	'.$string . PHP_EOL .
		    '	'.$string2 . PHP_EOL .
		    "}" . PHP_EOL;
		    
		    
		    if (!dirExists(dirname($filename))){
		    	die('创建目录失败！');
		    }
		    
		    if (file_put_contents($filename, $source)) {
		        echo '<p>' . $table . ($create ? '生成类成功' : '修改类成功') . '</p>';
		    }
		}
	}
    
    		

}
