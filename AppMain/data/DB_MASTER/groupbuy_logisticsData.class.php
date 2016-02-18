<?php
namespace AppMain\data\DB_MASTER;
class groupbuy_logisticsData {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'logistics_id'=> ['type' => 'i', 'value' => null],
			'logistics_number'=> ['type' => 'i', 'value' => null],
			'data'=> ['type' => 's', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 's', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
