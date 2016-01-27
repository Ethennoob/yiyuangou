<?php
namespace AppMain\data\DB_MASTER;
class groupbuy_record {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'bill_id'=> ['type' => 'i', 'value' => null],
			'group_id'=> ['type' => 'i', 'value' => null],
			'goods_id'=> ['type' => 'i', 'value' => null],
			'user_id'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
