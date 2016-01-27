<?php
namespace AppMain\data\DB_MASTER;
class groupbuy_groups {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'goods_id'=> ['type' => 'i', 'value' => null],
			'leader'=> ['type' => 'i', 'value' => null],
			'soft'=> ['type' => 'i', 'value' => null],
			'stool'=> ['type' => 'i', 'value' => null],
			'status'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 's', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
