<?php
namespace AppMain\data\DB_MASTER;
class article {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'title'=> ['type' => 's', 'value' => null],
			'content'=> ['type' => 's', 'value' => null],
			'pic'=> ['type' => 's', 'value' => null],
			'is_show'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
