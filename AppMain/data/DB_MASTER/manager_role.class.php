<?php
namespace AppMain\data\DB_MASTER;
class manager_role {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'role_name'=> ['type' => 's', 'value' => null],
			'auth'=> ['type' => 's', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
