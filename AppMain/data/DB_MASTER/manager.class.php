<?php
namespace AppMain\data\DB_MASTER;
class manager {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'manager_name'=> ['type' => 's', 'value' => null],
			'manager_email'=> ['type' => 's', 'value' => null],
			'manager_phone'=> ['type' => 'i', 'value' => null],
			'manager_pwd'=> ['type' => 's', 'value' => null],
			'manager_level'=> ['type' => 'i', 'value' => null],
			'manager_endlogin'=> ['type' => 'i', 'value' => null],
			'last_ip'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
