<?php
namespace AppMain\data\DB_MASTER;
class logistics_data {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'logistics_number'=> ['type' => 's', 'value' => null],
			'data'=> ['type' => 's', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
