<?php
namespace AppMain\data\DB_READ;
class role {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'role_name'=> ['type' => 's', 'value' => null],
			'role_status'=> ['type' => 'i', 'value' => null],
			'role_operation'=> ['type' => 'i', 'value' => null],
			'role_remark'=> ['type' => 's', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
