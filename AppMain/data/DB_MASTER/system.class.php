<?php
namespace AppMain\data\DB_MASTER;
class system {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'buy_limit'=> ['type' => 'i', 'value' => null],
			'roll_rule'=> ['type' => 'i', 'value' => null],
			'buy_rule'=> ['type' => 'i', 'value' => null],
			'Bvalue'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return '';
	}
}
