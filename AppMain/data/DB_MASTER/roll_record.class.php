<?php
namespace AppMain\data\DB_MASTER;
class roll_record {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'user_id'=> ['type' => 'i', 'value' => null],
			'goods_id'=> ['type' => 'i', 'value' => null],
			'time'=> ['type' => 'i', 'value' => null],
			'shishicai'=> ['type' => 'i', 'value' => null],
			'ms_time'=> ['type' => 's', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
