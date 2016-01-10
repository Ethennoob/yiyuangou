<?php
namespace AppMain\data\DB_GROUPBUY;
class user {
	static public function initTable(){ 
		return [
			'user_id'=> ['type' => 'i', 'value' => null],
			'username'=> ['type' => 's', 'value' => null],
			'email'=> ['type' => 's', 'value' => null],
			'password'=> ['type' => 's', 'value' => null],
			'level'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'user_id';
	}
}
