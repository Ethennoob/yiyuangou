<?php
namespace AppMain\data\DB_MASTER;
class manager_auth {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'mold'=> ['type' => 's', 'value' => null],
			'mold_name'=> ['type' => 's', 'value' => null],
			'pid'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
