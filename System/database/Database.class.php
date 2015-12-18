<?php
/**
 * Description of Database
 *
 * @author lzy
 */

namespace System\database;

class Database {

    static public $conn;

    static public function openConnection($db = "DB_MASTER") {
        if (empty(self::$conn[$db])) {
            self::$conn[$db] = new Connection($db);
        }
        return self::$conn[$db]->open();
    }

    static public function closeConnection() {
        //不要关闭连接，留静态变量$conn销毁时关闭
    }

}

class Connection {

	private $conn = null;
	private $host; 
	private $user;
	private $password; 
	private $dbName;

	public function __construct($DB) {
		$DBConfig = \System\Entrance::config($DB);
		$this->host = $DBConfig['host'];
		$this->user = $DBConfig['user'];
		$this->password = $DBConfig['password'];
		$this->dbName = $DBConfig['dbName'];
	}

	public function __destruct() {
		$this->close();
	}

	public function open() {
		if ($this->conn === null) {
			if ($this->user === null && defined('DB_USER'))
				$this->user = DB_USER;
			if ($this->password === null && defined('DB_PSW'))
				$this->password = DB_PSW;

			if ($this->user && $this->password) {
				$this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->dbName);
				if (!mysqli_connect_errno()) {
					mysqli_set_charset($this->conn, "utf8");
				} else {
					$this->conn = null;
				}
			}
		}

		return $this->conn;
	}

	private function close() {
		if ($this->conn) {
			$this->conn->close();
			$this->conn = null;
		}
	}

}

?>
