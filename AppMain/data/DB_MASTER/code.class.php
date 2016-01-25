<?php
namespace AppMain\data\DB_MASTER;
class code {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'goods_id'=> ['type' => 'i', 'value' => null],
			'thematic_id'=> ['type' => 'i', 'value' => null],
			'user_id'=> ['type' => 'i', 'value' => null],
			'code'=> ['type' => 's', 'value' => null],
			'key'=> ['type' => 'i', 'value' => null],
			'is_get'=> ['type' => 'i', 'value' => null],
			'is_use'=> ['type' => 'i', 'value' => null],
			'is_lucky'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
