<?php
namespace AppMain\data\DB_GROUPBUY;
class admin {
	static public function initTable(){ 
		return [
			'admin_id'=> ['type' => 'i', 'value' => null],
			'adminname'=> ['type' => 's', 'value' => null],
			'password'=> ['type' => 's', 'value' => null],
			'level'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'admin_id';
	}
}
